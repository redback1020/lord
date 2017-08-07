<?php

//严重警告，下面的所有注释文字或代码，务必保留，不许删除

$root = dirname(ROOT);
require $root . "/lib/util.php";
if (ISLOCAL) {
    define("LOG_LEVEL", 75);
    define("DEBUG_LOG", ROOT . "/log/sweety.log");//game.log
    define("GAME_LOG", ROOT . "/log/sweety.log");
    define("GAME_ERR", ROOT . "/log/sweety.log");
    define("SERVER_LOG", ROOT . "/log/sweety.log");
    define("SERVER_ERR", ROOT . "/log/sweety.log");
    require $root . "/lib/class.log.php";
} else {
    // function gerr($str){	global $socket; return fwrite($socket, LOGTAG.' [ERROR]['.date("Y-m-d H:i:s").']'.$str); }//抵达时间对比
    function gerr($str)
    {
        global $socket;
        fwrite($socket, LOGTAG . ' [ERROR] ' . $str);
        return false;
    }//game error log
    function glog($str)
    {
        global $socket;
        fwrite($socket, LOGTAG . ' [INFOR] ' . $str);
        return true;
    }//game infor log
    function debug($str)
    {
        if (!ISDEBUG) {
            return false;
        }
        global $socket;
        fwrite($socket, LOGTAG . ' [DEBUG] ' . $str);
        return true;
    }//debug log
    function serr($str)
    {
        global $socket;
        fwrite($socket, LOGTAG . ' [SSERR] ' . $str);
        return false;
    }//sweety error log
    function slog($str)
    {
        global $socket;
        fwrite($socket, LOGTAG . ' [SSINF] ' . $str);
        return true;
    }//sweety infor log
    $socErr = $socTmo = 1;
    $socStr = HOSTID . '_' . LOGTAG;
    $socket = stream_socket_client('udg:///dev/log', $socErr, $socStr, $socTmo);
    // fclose($socket);//永久打开，不可关闭
    $hid = HOSTID;
    $wid = "000";
    $pid = sprintf("%1$05d", getmypid());
    slog("[LOGLOG] W=$wid H=$hid P=$pid 远程日志启动");
}

class Sweety
{
    public $ver = SWOOLE_VERSION;
    public $hid = HOSTID;
    public $wid = 0;
    public $pid = 0;
    public $srv = null;
    public $redis = null;
    public $mysql = null;
    public $root = "";
    public $scfd = [];
    public $fdi = [];
    public $desc = "";
    public $sctimerId = 0;
    public $scconnect = 0;
    public $cron = null;
    private $srv_hosts = "srv_hosts";    //host数组(redis-hash结构)
    private $srv_locks = "srv_locks";    //lock数组串行化，存放上次fixLock()时的已知锁
    private $srv_lock_ = "srv_lock_";    //.$lockId: 事务锁(int)
    private $srv_bind_ = "srv_bind_";        //.$fdx: FDU数据(redis-hash结构)
    private $srv_user_ = KEY_USER_;        //.$uid: USER数据(redis-hash结构)
    private $srv_mysql = "srv_mysql";    //sql后置处理(redis-list结构)
    private $srv_route_ = "srv_route_";    //.$hostId协议转发用队列(redis-list结构/POP后执行)
    private $srv_event_ = "srv_event_";    //.$hostId一次性事件任务(redis-list结构/POP后执行)
    private $srv_timer_ = "srv_timer_";    //.$hostId重复性定时任务(redis-hash结构/执行前销毁)

    function __construct()
    {
        global $root;
        $this->root = $root;
    }

    public function start()
    {
        $this->srv = new swoole_server("0.0.0.0", PORT, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->srv->set([
            //'daemonize' => 1,			//启用进程守护
            'timeout'                  => 3,                //设置epoll溢出时间
            'open_cpu_affinity'        => 1,    //启用CPU亲和设置
            'open_tcp_nodelay'         => 1,    //启用TCP即时发送
            'socket_buffer_size'       => 2400000,
            'pipe_buffer_size'         => 128 * 1024 * 1024,
            'buffer_output_size'       => 64 * 1024 * 1024,
            'enable_unsafe_event'      => 1,
            'max_conn'                 => SW_CONNECT,        //最大连接数
            'backlog'                  => SW_BACKLOG,        //最大排队数
            'worker_num'               => SW_WORKER,        //协议进程数
            'task_worker_num'          => SW_TASKER,    //任务进程数
            'max_request'              => SW_REQUEST,    //进程回收数
            'dispatch_mode'            => SW_DISPATCH,    //FD分配模式
            'log_file'                 => SW_LOG,
            'heartbeat_check_interval' => SW_INTERVAL,
            'heartbeat_idle_time'      => SW_IDLETIME,
            // 'open_eof_check' => 0,		//关闭
            // 'package_eof' => "\r\n\r\n",
            'open_length_check'        => 1,    //开启
            'package_length_type'      => 'N',
            'package_length_offset'    => 0,
            'package_body_offset'      => 4,
            'package_max_length'       => 2048000,
        ]);
        $this->srv->on('Start', [$this, 'onServerStart']);
        $this->srv->on('Shutdown', [$this, 'onServerClose']);
        $this->srv->on('ManagerStart', [$this, 'onManageStart']);
        $this->srv->on('ManagerStop', [$this, 'onManageClose']);
        $this->srv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->srv->on('WorkerStop', [$this, 'onWorkerClose']);
        $this->srv->on('WorkerError', [$this, 'onWorkerError']);
        $this->srv->on('pipeMessage', [$this, 'onPipeMessage']);
        $this->srv->on('Task', [$this, 'onTaskRun']);
        $this->srv->on('Finish', [$this, 'onTaskEnd']);
        $this->srv->on('Connect', [$this, 'onFdStart']);
        $this->srv->on('Close', [$this, 'onFdClose']);
        $this->srv->on('Receive', [$this, 'onReceive']);
        $this->srv->start();
    }

    // ON 服务进程启动成功
    public function onServerStart($srv)
    {
        $pid = getmypid();
        $f = $this->root . '/master.pid';
        touch($f);
        $h = fopen($f, 'a');
        fwrite($h, $pid . "\n");
        fclose($h);
        $hid = $this->hid;
        $pid = sprintf("%1$05d", $pid);
        slog("[SWEETY] W=000 H=$hid P=$pid 核心服务启动:$this->ver");
        require($this->root . "/lib/class.redis.php");
        $this->redis = $this->getRedis();
        $this->redis->hset($this->srv_hosts, $this->hid, ["start" => microtime(1), "master" => intval(ISMASTER && PORT == 9000), "ver" => $this->ver]);
        $this->redis->close();
    }

    // ON 服务进程开始关闭 (平滑关闭:kill -15)
    public function onServerClose($srv)
    {
        $f = $this->root . '/master.pid';
        file_put_contents($f, '');
        $hid = $this->hid;
        $pid = sprintf("%1$05d", getmypid());
        slog("[SWEETY] W=000 H=$hid P=$pid 核心进程关闭:$this->ver");
        require_once($this->root . "/lib/class.redis.php");
        $this->redis = $this->getRedis();
        $this->redis->hdel($this->srv_hosts, $this->hid);
        $this->redis->close();
    }

    // ON 管理进程启动成功
    public function onManageStart($srv)
    {
        $hid = $this->hid;
        $pid = sprintf("%1$05d", getmypid());
        slog("[SWEETY] W=000 H=$hid P=$pid 管理进程启动");
    }

    // ON 管理进程开始关闭
    public function onManageClose($srv)
    {
        $hid = $this->hid;
        $pid = sprintf("%1$05d", getmypid());
        slog("[SWEETY] W=000 H=$hid P=$pid 管理进程关闭");
    }

    // ON 事件进程启动成功
    public function onWorkerStart($srv, $wid)
    {
        $this->srv = $srv;
        $hid = $this->hid;
        $wid = $this->wid = sprintf("%1$03d", $wid);
        $pid = $this->pid = sprintf("%1$05d", $srv->worker_pid);
        $tag = $wid < SW_WORKER ? "协议" : "任务";
        slog("[SWEETY] W=$wid H=$hid P=$pid {$tag}进程启动");
        if ($wid >= SW_WORKER) {
            require($this->root . "/lib/class.mysql.php");
            //类重载bug 这里做个判断
            //require($this->root . "/lib/class.redis.php");
            !class_exists('RD',false) &&  require($this->root . "/lib/class.redis.php");
            require($this->root . "/game/class.gamer.php");

            $this->redis = $this->getRedis(1);
            $this->mysql = $this->getMysql(1);
            $this->gamer = new gamer($this->redis, $this->mysql);
        }
        if ($wid == 0) {
            //真跨服任务 预留
        } //W01 常规延时任务
        elseif ($wid == 1) {
            //类重载bug 这里做个判断
            //require($this->root . "/lib/class.redis.php");
            !class_exists('RD',false) &&  require($this->root . "/lib/class.redis.php");
            $this->redis = $this->getRedis(1);
            $tick = 100;
            $cb = function ($timerId) {
                while ($data = $this->redis->lpop($this->srv_event_ . $this->hid)) {
                    $data['_'] = 'runEvent';
                    if (isset($data['mtime']) && isset($data['delay'])) {
                        $nmtime = microtime(1);
                        $emtime = intval($data['mtime'] * 1000 + $data['delay']) / 1000;
                        $delay = $emtime > $nmtime ? intval(($emtime - $nmtime) * 1000) : 0;
                    } else {
                        $delay = 0;
                    }
                    if ($delay) {
                        $cc = function () use ($data) {
                            $this->srv->task($data);
                        };
                        swoole_timer_after($delay, $cc);
                        continue;
                    }
                    $this->srv->task($data);
                }
            };
            swoole_timer_tick($tick, $cb);
        } //W02 定时脚本任务
        elseif ($wid == 2) {
            $tick = 60000;
            $cb = function ($timerId) {
                $data = [];
                $data['_'] = 'runCront';
                $this->srv->task($data);
            };
            swoole_timer_tick($tick, $cb);
        } //W03 跨服转发任务
        elseif ($wid == 3) {
            $tick = 100;
            $cb = function ($timerId) {
                $data = [];
                $data['_'] = 'runRoute';
                $this->srv->task($data);
            };
            swoole_timer_tick($tick, $cb);
        } //W04 异步数据任务
        elseif ($wid == 4) {
            $tick = 1000;
            $cb = function ($timerId) {
                $data = [];
                $data['_'] = 'runMysql';
                $this->srv->task($data);
            };
            swoole_timer_tick($tick, $cb);
        } //W05 定时场景任务
        elseif ($wid == 5) {
            $tick = 100;
            $cb = function ($timerId) {
                $data = [];
                $data['_'] = 'runTimer';
                $this->srv->task($data);
            };
            swoole_timer_tick($tick, $cb);
        } //W06 定时闹钟任务
        elseif ($wid == 6) {
            $cb = function ($timerId, $data) {
                $data['_'] = 'runEvent';
                $data['params'] = ['time' => time()];
                $this->srv->task($data);
            };
            $ticks = ROOT . "/conf/timers.php";
            if (!is_file($ticks)) return true;
            $ticks = include $ticks;
            foreach ($ticks as $k => $v) {
                if (!isset($v['act']) || !$v['act'] || !isset($v['delay']) || !$v['delay']) continue;
                if (isset($v['isMaster']) && $v['isMaster'] && !(ISMASTER && PORT == 9000)) continue;
                $tick = max($v['delay'], 1000);
                swoole_timer_tick($tick, $cb, $v);
            }
        } //W07 定时修锁任务
        elseif ($wid == 7 && (ISMASTER && PORT == 9000)) {
            $tick = 10000;
            $cb = function ($timerId) {
                $data = [];
                $data['_'] = 'runLocks';
                $this->srv->task($data);
            };
            swoole_timer_tick($tick, $cb);
        } elseif ($wid == 8) {
            /*
             * 本地测试关掉 牛牛
             * $this->cowBankRobot = new CowBankerRobot();
            $this->cowBankRobot->setMachine($this->gamer->cowMachine,$this->gamer->mysql, $this->gamer->redis)->getRobotList();
            $this->cowBankRobot->setMachine($this->gamer->cowMachine,$this->gamer->mysql, $this->gamer->redis)->throwRobot();
            $this->cowPlayerRobot = new CowPlayerRobot();
            $this->cowPlayerRobot->setMachine($this->gamer->cowMachine,$this->gamer->mysql, $this->gamer->redis)->getRobotList();
            $cb = function ($timerId) {
                if ($this != null && $this->gamer != null && isset($this->gamer->cowMachine)) {
                    $machine = $this->gamer->cowMachine;
                    $machine->onCallBack();
                    $this->cowBankRobot->onCallBack();
                    $this->cowPlayerRobot->onCallBack();
                }
            };
            swoole_timer_tick(1000, $cb);*/
        }
    }

    // ON 事件进程开始关闭
    public function onWorkerClose($srv, $wid)
    {
        $this->gamer = null;
        $this->redis = null;
        $this->mysql = null;
        $hid = $this->hid;
        $wid = sprintf("%1$03d", $wid);
        $pid = sprintf("%1$05d", $srv->worker_pid);
        $tag = $wid < SW_WORKER ? "协议" : "任务";
        slog("[SWEETY] W=$wid H=$hid P=$pid {$tag}进程关闭");
    }

    // ON 事件进程运行异常
    public function onWorkerError($srv, $wid, $pid, $errno)
    {
        $hid = $this->hid;
        $wid = sprintf("%1$03d", $wid);
        $pid = sprintf("%1$05d", $pid);
        $tag = $wid < SW_WORKER ? "协议" : "任务";
        serr("[SWEETY] W=$wid H=$hid P=$pid {$tag}进程异常:$errno");
    }

    // ON 管道消息开始执行
    public function onPipeMessage($srv, $src_wid, $data)
    {
    }

    // ON 异步任务开始执行
    public function onTaskRun($srv, $taskid, $wid, $data)
    {
        $data_ = $data;
        switch ($data['_']) {
            case 'runEvent' :
                $sttime = microtime(1);
                $lock = isset($data['lockId']) && $data['lockId'] ? ($this->srv_lock_ . $data['lockId']) : 0;
                if ($lock && !$this->redis->setLock($lock)) gerr("[LOCKON][EVENT] L=$lock D=" . json_encode($data_));
                glog("[EVENTS] W=$this->wid D=" . json_encode($data));
                $this->gamer->runEvent($data['act'], $data['params']);
                $lock && $this->redis->delLock($lock);
                $ustime = number_format(microtime(1) - $sttime, 2);
                if ($ustime > ROE_EVENT) gerr("[++++++][EVENT] W=$this->wid time=$ustime D=" . json_encode($data_));
                break;
            case 'runCront' :
                $time = time();
                $d = explode("-", date("i-G-j-n-N", $time));
                $cron = include ROOT . "/conf/crontab.php";
                if ($cron && is_array($cron)) {
                    $this->cron = $cron;
                } elseif ($this->cron) {
                    $cron = $this->cron;
                } else {
                    $this->cron = $cron = [];
                }
                foreach ($cron as $v) {
                    if (!isset($v['act']) || !isset($v['date']) || (isset($v['isMaster']) && $v['isMaster'] && !(ISMASTER && PORT == 9000))) continue;
                    $t = explode(' ', $v['date']);
                    if (!$t || count($t) != 5) continue;
                    if (($t[0] == '*' || $t[0] == intval($d[0])) && ($t[1] == '*' || $t[1] == $d[1]) && ($t[2] == '*' || $t[2] == $d[2]) && ($t[3] == '*' || $t[3] == $d[3]) && ($t[4] == '*' || $t[4] == $d[4])) {
                        $sttime = microtime(1);
                        glog("[CRONTS] W=$this->wid D=" . json_encode($data));
                        $this->gamer->runCrontab($v['act'], ['time' => $time]);
                        $ustime = number_format(microtime(1) - $sttime, 2);
                        if ($ustime > ROE_CRONTAB) gerr("[++++++][CRONT] W=$this->wid time=$ustime D=" . json_encode($v));
                    }
                }
                break;
            case 'runRoute' :
                while ($data = $this->redis->lpop($this->srv_route_ . $this->hid)) {
                    switch ($data['act']) {
                        case 'SEND' : //发送
                            $this->sendFd($data['fd'], $data['cmd'], ['code' => $data['code'], 'data' => $data['data']]);
                            break;
                        case 'KICK' : //踢掉
                            if ($fd = intval(str_replace($this->hid . '_', '', $data['fd']))) {
                                $this->desc = $data['desc'];
                                $srv->close($fd);
                            }
                            break;
                        case "HORN" : //广播
                            if ($data['fds']) {
                                $this->sendClient($data['fds'], $data['cmd'], ['code' => $data['code'], 'data' => $data['data']]);
                                break;
                            }
                            foreach ($srv->connections as $fd) {
                                $this->sendClient($fd, $data['cmd'], ['code' => $data['code'], 'data' => $data['data']]);
                            }
                            break;
                        default:
                            break;
                    }
                }
                break;
            case 'runMysql' :
                while ($sql = $this->redis->lpop($this->srv_mysql)) {
                    // $res = $this->mysql->runSqls($sql);//批量处理异步sql 预留
                    $res = $this->mysql->runSql($sql);
                    if (!$res) {
                        $sqls = include ROOT . "/conf/mysql.php";
                        if ($sqls && is_array($sqls)) {
                            foreach ($sqls as $k => $v) {
                                $this->mysql->runSql($v);
                            }
                            $res = $this->mysql->runSql($sql);
                        }
                    }
                }
                break;
            case 'runTimer' :
                $events = $this->redis->hgetall($this->srv_timer_ . $this->hid);//1000~2000
                if (!$events) break;
                $events_mtime = [];
                foreach ($events as $k => $v) {
                    $ntime = $v['mtime'] + $v['delay'] / 1000;
                    $events[$k]['ntime'] = $events_mtime[$k] = $ntime;
                }
                array_multisort($events_mtime, $events);
                foreach ($events as $sceneId => $data) {
                    if ($data['ntime'] > microtime(1)) break;
                    $lock = $this->srv_lock_ . "TIMER_" . $sceneId;
                    if (!$this->redis->setLock($lock)) {
                        gerr("[LOCKON] W=$this->wid L=$lock D=" . json_encode($data));
                        continue;
                    }
                    $event = $this->redis->hget($this->srv_timer_ . $this->hid, $sceneId);
                    if (!$event || $event['mtime'] != $data['mtime'] || $event['delay'] != $data['delay'] || $event['stop'] != $data['stop']) {
                        slog("[TIMERD] W=$this->wid D=" . json_encode($data) . " dnew=" . json_encode($event));
                        $this->redis->delLock($lock);
                        continue;
                    }
                    $res = $this->redis->hdel($this->srv_timer_ . $this->hid, $sceneId);
                    if ($data['stop']) {
                        slog("[TIMERD] W=$this->wid D=" . json_encode($data) . " dnew=" . json_encode($event));
                        $this->redis->delLock($lock);
                        continue;
                    }
                    $this->redis->delLock($lock);
                    $res = $this->redis->ladd($this->srv_event_ . $this->hid, $event);
                }
                break;
            case 'runLocks' :
                $res = $this->redis->fixLock($this->srv_lock_ . '*', $this->srv_locks);
                break;
            case 'runReg' :
                    $sttime = microtime(1);
                    $ccn = $data['ccn'];
                    $fdx = $data['fdx'];
                    $cmd = $data['cmd'];
                    $code = $data['code'];
                    $data = $data['data'];
                    $lockUU = $this->srv_lock_ . 'USER_' . $fdx;
                    if (!$this->redis->setLock($lockUU)) gerr("[LOCKON][REG] F=$fdx L=$lockUU D=" . json_encode($data));
                    $this->gamer->runReg($fdx, $cmd, $code, $data);
                    $this->redis->delLock($lockUU);
                    break;                
            case 'runLogin' :
                $sttime = microtime(1);
                $ccn = $data['ccn'];
                $fdx = $data['fdx'];
                $cmd = $data['cmd'];
                $code = $data['code'];
                $data = $data['data'];
                $lockUU = $this->srv_lock_ . 'USER_' . $fdx;
                if (!$this->redis->setLock($lockUU)) gerr("[LOCKON][LOGIN] F=$fdx L=$lockUU D=" . json_encode($data));
                $uid = $this->gamer->runLogin($fdx, $cmd, $code, $data);
                $uid = $uid > 0 ? intval($uid) : 0;
                if ($uid) {
                    $res = $this->setBind($fdx, ['uid' => $uid]);
                    $res = $this->setUser($uid, ['fd' => $fdx, 'last_action' => 'LOGIN_GUEST', 'last_time' => $sttime]);
                }
                $this->redis->delLock($lockUU);
                $ustime = number_format(microtime(1) - $sttime, 2);
                if ($ustime > ROE_LOGIN) gerr("[++++++][LOGIN] W=$this->wid time=$ustime D=" . json_encode($data));
                break;
            case 'runLogout' :
                $sttime = microtime(1);
                //$fd = $data['fd'];
                //$fdx = "{$this->hid}_{$fd}";
                $fdx = $data['fdx'];
                $lockUU = $this->srv_lock_ . 'USER_' . $fdx;
                if (!$this->redis->setLock($lockUU)) gerr("[LOCKON][LOGOU] F=$fdx L=$lockUU D=" . json_encode($data));
                $uid = 0;
                $user = $this->getUserByFd($fdx);
                if ($user) {
                    $uid = $user['uid'] = $user['uid'] > 0 ? intval($user['uid']) : 0;
                    if ($uid) {
                        $user['last_action'] = $newU['last_action'] = 'LOGOUT';
                        $user['last_time'] = $newU['last_time'] = $sttime;
                        $res = $this->setUser($uid, $newU);
                    } else {
                        $user = [];
                    }
                } else {
                    $user = [];
                }
                $res = $this->delBind($fdx);
                $res = $this->gamer->runLogout($fdx, $user, 1);
                $this->redis->delLock($lockUU);
                $ustime = number_format(microtime(1) - $sttime, 2);
                if ($ustime > ROE_LOGOUT) gerr("[++++++][LOGOU] W=$this->wid time=$ustime F=$fdx");
                break;
            case 'runJumpin' :
                $sttime = microtime(1);
                $ccn = $data['ccn'];
                $fdx = $data['fdx'];
                $cmd = $data['cmd'];
                $code = $data['code'];
                $data = $data['data'];
                gerr("[功能暂时缺失] F=$fdx D=$data_");
                $ustime = number_format(microtime(1) - $sttime, 2);
                if ($ustime > ROE_JUMPIN) gerr("[++++++][JUMPI] W=$this->wid time=$ustime D=" . json_encode($data));
                break;
            case 'runJumpout' :
                $sttime = microtime(1);
                $ccn = $data['ccn'];
                $fdx = $data['fdx'];
                $cmd = $data['cmd'];
                $code = $data['code'];
                $data = $data['data'];
                gerr("[功能暂时缺失] F=$fdx D=$data_");
                $ustime = number_format(microtime(1) - $sttime, 2);
                if ($ustime > ROE_JUMPOUT) gerr("[++++++][JUMPO] W=$this->wid time=$ustime D=" . json_encode($data));
                break;
            case 'runAction' :
                $sttime = microtime(1);
                $ccn = $data['ccn'];
                $fdx = $data['fdx'];
                $cmd = $data['cmd'];
                $code = $data['code'];
                $data = $data['data'];
                $reqs = include ROOT . "/conf/action.php";
                $act = isset($reqs[$cmd][$code]) ? $reqs[$cmd][$code] : [];
                if (!isset($act['act']) || empty($act['act'])) return $this->closeToFd($fdx, "$ccn 无效协议编号 D=" . json_encode($data));
                $lockUU = isset($act['locku']) && $act['locku'] ? ($this->srv_lock_ . 'USER_' . $fdx) : false;
                if ($lockUU && !$this->redis->setLock($lockUU)) gerr("[LOCKON][ACTIO] F=$fdx L=$lockUU D=" . json_encode($data));
                $uid = 0;
                $user = [];
                if (isset($act['user']) && $act['user'] > 0) {
                    $user = $this->getUserByFd($fdx);
                    if (!$user || !isset($user['uid']) || !($user['uid'] > 0) || !$this->gamer->runCheck($user)) {
                        $lockUU && $this->redis->delLock($lockUU);
                        return $this->closeToFd($fdx, "$ccn 无效协议用户 D=" . json_encode($data) . " user=" . json_encode($user));
                    }
                    $uid = $user['uid'] = intval($user['uid']);
                }
                $lockCC = $uid && isset($act['lock']) && $act['lock'] && isset($user[$act['lock']]) && $user[$act['lock']] ? ($this->srv_lock_ . $user[$act['lock']]) : false;
                if ($lockCC && !$this->redis->setLock($lockCC)) gerr("[LOCKON][ACTIO] F=$fdx L=$lockCC D=" . json_encode($data_));
                $res = $this->gamer->runAction($fdx, $cmd, $code, $act['act'], $data, $user);
                if ($uid) {
                    $newU = ['last_action' => $act['act'], 'last_time' => $sttime];
                    $res = $this->setUser($uid, $newU);
                }
                $lockCC && $this->redis->delLock($lockCC);
                $lockUU && $this->redis->delLock($lockUU);
                $ustime = number_format(microtime(1) - $sttime, 2);
                if ($ustime > ROE_ACTION) gerr("[++++++] $ccn W=$this->wid time=$ustime D=" . json_encode($data));
                break;
            default:
                gerr("[PIPERR] W=$this->wid A=" . $data['_']);
                break;
        }
        return true;
    }

    // ON 异步任务执行完毕
    public function onTaskEnd($srv, $taskid, $data)
    {
        return true;
    }

    // ON 事件进程用户连接
    public function onFdStart($srv, $fd, $rid)
    {
        glog("[FDCONN] W=$this->wid F={$fd} 用户连接成功 " . json_encode($srv->connection_info($fd)));
    }

    // ON 事件进程用户断开
    public function onFdClose($srv, $fd, $rid)
    {
        unset($this->fdi[$fd]);
        $fdx = "{$this->hid}_{$fd}";
        $isKick = !($this->desc === "");
        if ($isKick) {
            glog("[FDKICK] W=$this->wid F=$fdx 用户被踢开始:" . $this->desc);
        } else {
            glog("[FDCLOS] W=$this->wid F=$fdx 用户断开开始");
        }
        $this->desc = "";
        //return $this->srv->task(['_' => 'runLogout', 'fd' => $fd]);
        return $this->srv->task(['_' => 'runLogout', 'fdx' => $fdx]);
    }

    // ON 事件进程接收数据
    public function onReceive($srv, $fd, $rid, $data)
    {
        $sttime = microtime(1);
        $fdx = "{$this->hid}_{$fd}";
        $info = $srv->connection_info($fd);
        if (!$info || !isset($info['remote_ip'])) {
            glog("[-----<] W=$this->wid F=$fdx info=" . json_encode($info));
            return false;
        }
        // $len = UInt32Binary2Int(array_values(unpack("C*",substr($data, 0, 4))));
        $cmd = UInt32Binary2Int(array_values(unpack("C*", substr($data, 4, 4))));//cmd
        $cme = UInt32Binary2Int(array_values(unpack("C*", substr($data, 8, 4))));//扩展
        $cmf = UInt32Binary2Int(array_values(unpack("C*", substr($data, 12, 4))));//扩展
        $data_ = substr($data, 16);
        $data = @json_decode($data_, 1);
        if (!is_array($data)) return gerr("[RECEIV] W=$this->wid F=$fdx D=" . $data_);
        if ($cmd && !isset($data['t'])) {
            gerr("[----<<][{$cmd}????] W=$this->wid F=$fdx D=" . $data_);
            $srv->close($fd);
            return false;
        }
        $code = $data['t'];
        $ccn = $cmd ? strval($cmd * 10000 + $code) : sprintf("%1$05d", $code);
        //用户登录IP附加
        if ($ccn == ACT_LOGIN || $ccn == ACT_REG) {
            $data['ip'] = $info['remote_ip'];
        } else {
            //踢出闲置心跳
            $this->fdi[$fd]['HEART'] = !isset($this->fdi[$fd]['HEART']) ? 1 : ($this->fdi[$fd]['HEART'] * intval(!$cmd) + intval(!$cmd));
            glog("[<<<<<<]".json_encode($data)."   ".$this->fdi[$fd]['HEART']);
            if ($this->fdi[$fd]['HEART'] >= 225) {
                glog("[<<<<<<] $ccn W=$this->wid F=$fdx HEART=" . $this->fdi[$fd]['HEART']);
                $srv->close($fd);
                return true;
            }
            //处理正常心跳
            if ($ccn == ACT_HEART) {
                $isResponse = 1;
                if ($isResponse) {
                    return $this->sendClient($fd, $cmd, ['code' => 0, 'data' => []]);
                } else {
                    return glog("[------] $ccn W=$this->wid F=$fdx");
                }
            }
            //踢出异常刷新
            if (strpos(ACT_SPEED, $ccn) !== false) {
                if (!isset($this->fdi[$fd]['SPEED'])) {
                    $this->fdi[$fd]['SPEED'] = $sttime;
                } elseif (($sttime - $this->fdi[$fd]['SPEED']) < 1) {
                    glog("[---<<<] $ccn W=$this->wid F=$fdx SPEED=$sttime");
                    $srv->close($fd);
                    return true;
                } else {
                    $this->fdi[$fd]['SPEED'] = $sttime;
                }
            }
        }
        unset($data['t']);
        glog("[<<<<<<] $ccn F=$fdx D=$data_");
        switch ($ccn) {
            case ACT_REG:
                $srv->task(['_' => 'runReg', 'ccn' => $ccn, 'fdx' => $fdx, 'cmd' => $cmd, 'code' => $code, 'data' => $data]);
                break;
            case ACT_LOGOUT :
                break;
            case ACT_LOGIN :
                $srv->task(['_' => 'runLogin', 'ccn' => $ccn, 'fdx' => $fdx, 'cmd' => $cmd, 'code' => $code, 'data' => $data]);
                break;
            case ACT_JUMPIN :
                $srv->task(['_' => 'runJumpin', 'ccn' => $ccn, 'fdx' => $fdx, 'cmd' => $cmd, 'code' => $code, 'data' => $data]);
                break;
            case ACT_JUMPOUT :
                $srv->task(['_' => 'runJumpout', 'ccn' => $ccn, 'fdx' => $fdx, 'cmd' => $cmd, 'code' => $code, 'data' => $data]);
                break;
            default:
                $srv->task(['_' => 'runAction', 'ccn' => $ccn, 'fdx' => $fdx, 'cmd' => $cmd, 'code' => $code, 'data' => $data]);
                break;
        }
        return true;
    }

    private function sendFd($fdx, $cmd, $data)
    {
        if (is_int($fdx)) {
            $fd = $fdx;
            $fdx = $this->hid . '_' . $fd;
        } else {
            $fd = intval(str_replace($this->hid . '_', '', $fdx));
        }
        //下面的判断是斗地主独有特殊逻辑？ //怎么在发送给用户的时候做更多标记(比如牌桌用户UID)？ 目前临时使用 预留
        if (isset($data['data']['log'])) {
            $ud = $data['data']['log']['ud'];
            $td = $data['data']['log']['td'];
            unset($data['data']['log']);
        } else {
            $ud = $td = '';//uid/tableId
        }
        $res = $this->sendClient($fd, $cmd, $data);
        $ccn = $cmd ? ($cmd * 10000 + $data['code']) : sprintf("%1$05d", $data['code']);
        glog("[" . ($res ? ">>>>>>" : ">>>>>-") . "] $ccn W=$this->wid F=$fdx U=$ud T=$td D=" . json_encode($data['data']));
    }

    private function sendClient($fds, $cmd, $msg = [])
    {
        $msg = is_array($msg) ? json_encode($msg) : $msg;
        $len = num2UInt32Str(strlen($msg) + 12);
        $cmd = num2UInt32Str(intval($cmd));
        $cme = num2UInt32Str(0);
        $cmf = num2UInt32Str(0);
        $pack = $len . $cmd . $cme . $cmf . $msg;
        if (is_array($fds)) {
            foreach ($fds as $fd) {
                $this->srv->send($fd, $pack);
            }
            return true;
        }
        return $this->srv->send($fds, $pack);
    }

    // REDIS 获取Redis对象
    public function getRedis($isKeep = false, $isThrow = false)
    {
        if (!is_null($this->redis)) return $this->redis;
        $this->redis = new RD($isKeep, $isThrow);
        if ($this->redis) return $this->redis;
        serr("[REDIS] new failed.");
        return null;
    }

    // MYSQL 获取MySQL对象
    public function getMysql($isKeep = false, $isThrow = false)
    {
        if (!is_null($this->mysql)) return $this->mysql;
        $this->mysql = new DB($isKeep, $isThrow);
        if ($this->mysql) return $this->mysql;
        serr("[MYSQL] new failed.");
        return null;
    }

    // HOSTS 获取所有HOST_PORT的进程数据
    public function getHosts()
    {
        return $this->redis->hgetall($this->srv_hosts);
    }

    // LOCK 事务加锁
    public function setLock($lock, $isOnly = 0)
    {
        return $this->redis->setLock($this->srv_lock_ . $lock, $isOnly);
    }

    // LOCK 事务解锁
    public function delLock($lock)
    {
        return $this->redis->delLock($this->srv_lock_ . $lock);
    }

    // LOCK 修复事务锁
    public function fixLock($pattern)
    {
        return $this->redis->fixLock($pattern, $this->srv_locks);
    }

    // 异步SQL sql入队
    public function bobSql($sql, $now = 0)
    {
        return $this->redis->ladd($this->srv_mysql, $sql);
        //预留 下面代码预留 批量处理异步SQL
        static $num = 0;
        static $time = 0;
        static $sqls = [];
        $num++;
        $res = -1;
        if ($sql && !$now) $sqls[] = $sql;
        // if ( $now || count($sqls) > 30 || $time + 300 < time() ) {
        if ($now || count($sqls) > 10 || $time + 10 < time()) {
            if (count($sqls) > 0) {
                $res = $this->redis->ladd($this->srv_mysql, join(";", $sqls) . ";");
                $num = 0;
                $time = time();
                $sqls = [];
            }
        }
        // if ( $now ) debug("测试故意重载结果" . json_encode($res));
        return true;
    }

    // FD绑定 获取
    public function getBind($fdx)
    {
        return $this->redis->hgetall($this->srv_bind_ . $fdx);
    }

    // FD绑定 设置
    public function setBind($fdx, $bind)
    {
        return $this->redis->hmset($this->srv_bind_ . $fdx, $bind);
    }

    // FD绑定 删除
    public function delBind($fdx)
    {
        return $this->redis->del($this->srv_bind_ . $fdx);
    }

    // 用户数据 通过UID获取
    public function getUser($uid)
    {
        return $this->redis->hgetall($this->srv_user_ . $uid);
    }

    // 用户数据 设置
    public function setUser($uid, $user)
    {
        return $this->redis->hmset($this->srv_user_ . $uid, $user);
    }

    // 用户数据 增减
    public function incUser($uid, $key, $num)
    {
        return $this->redis->hincrby($this->srv_user_ . $uid, $key, $num);
    }

    // 用户数据 删除
    public function delUser($uid)
    {
        return $this->redis->del($this->srv_user_ . $uid);
    }

    // 用户数据 通过FDX获取
    public function getUserByFd($fdx)
    {
        $fdx = trim($fdx);
        if (empty($fdx)) return false;
        $info = $this->getBind($fdx);
        if (!$info || !is_array($info) || !isset($info['uid'])) return false;
        return $this->getUser($info['uid']);
    }

    // SEND 发送全服/部分广播
    public function sendHorn($msg, $level = 0, $fdxs = [])
    {
        $msg = trim($msg);
        if (!$msg) return false;
        $level = intval($level);
        if (!is_array($fdxs)) $fdxs = [];
        $hosts = $this->getHosts();
        if (!$hosts) return false;
        $cmd = intval(ACT_HORN / 10000);
        $code = ACT_HORN % 10000;
        if ($fdxs) {
            $fdss = [];
            foreach ($fdxs as $k => $v) {
                $v_ = explode('_', $v);
                $fdss[$v_[0] . '_' . $v_[1]][] = $v_[2];
            }
            foreach ($fdss as $hostId => $fds) {
                $route = ['act' => "HORN", 'fds' => $fds, 'cmd' => $cmd, 'code' => $code, 'data' => ['msg' => $msg, 'level' => $level]];
                $this->setRoute($route, $hostId);
            }
        } else {
            $route = ['act' => "HORN", 'fds' => $fdxs, 'cmd' => $cmd, 'code' => $code, 'data' => ['msg' => $msg, 'level' => $level]];
            foreach ($hosts as $hostId => $v) {
                $this->setRoute($route, $hostId);
            }
        }
        return true;
    }

    // SEND 发送数据到FD
    public function sendTofd($fdx, $cmd, $code, $info)
    {
        $fd_ = explode("_", $fdx);
        if (!$fd_ || !is_array($fd_) || count($fd_) !== 3 || !($fd = intval($fd_[2]))) return false;
        $hostId = $fd_[0] . "_" . $fd_[1];
        if ($hostId == $this->hid) {
            $data['code'] = $code;
            $data['data'] = $info;
            return $this->sendFd($fd, $cmd, $data);
        }
        $route = ['act' => "SEND", 'fd' => $fdx, 'cmd' => $cmd, 'code' => $code, 'data' => $info];
        return $this->setRoute($route, $hostId);
    }

    // CLOSE 关闭FD连接
    public function closeToFd($fdx, $desc = '')
    {
        $fd_ = explode("_", $fdx);
        if (!$fd_ || !is_array($fd_) || count($fd_) !== 3 || !($fd = intval($fd_[2]))) return false;
        $desc = is_array($desc) && $desc ? json_encode($desc) : ($desc ? $desc : '');
        $hostId = $fd_[0] . "_" . $fd_[1];
        if ($hostId == $this->hid) {
            if ($desc) $this->desc = $desc;
            return $this->srv->close($fd);
        }
        $route = ['act' => "KICK", 'fd' => $fdx, 'desc' => $desc];
        return $this->setRoute($route, $hostId);
    }

    // ROUTE 追加跨服转发事件
    public function setRoute($data, $hostId = null)
    {
        if (!$data) return false;
        if (is_null($hostId)) {
            if (isset($data['fd']) && $data['fd']) {
                $fd_ = explode("_", $data['fd']);
                if (!$fd_ || !is_array($fd_) || count($fd_) !== 3 || !($fd = intval($fd_[2]))) return false;
                $hostId = $fd_[0] . "_" . $fd_[1];
            } else {
                $hostId = $this->hid;
            }
        }
        return $this->redis->ladd($this->srv_route_ . $hostId, $data);
    }

    // EVENT 设置一个常规或者延时事件
    public function setEvent($act, $params = [], $delay = 0, $hostId = HOSTID)
    {
        $act = trim($act);
        if (!$act) return false;
        $delay = intval($delay);
        if ($delay < 0) $delay = 0;
        $event = ['act' => $act, 'params' => $params, 'delay' => $delay, 'mtime' => microtime(1)];
        if ($hostId == "ALL") {
            $hosts = $this->getHosts();
            if (!$hosts) return false;
            foreach ($hosts as $hostId => $v) {
                $this->redis->ladd($this->srv_event_ . $hostId, $event);
            }
            return true;
        }
        return $this->redis->ladd($this->srv_event_ . $hostId, $event);
    }

    // TIMER 设置一个场景轮次定时器事件
    public function setTimer($sceneId, $act, $params = [], $delay = 0, $hostId = HOSTID)
    {
        $sceneId = trim($sceneId);
        $act = trim($act);
        $delay = intval($delay);
        if (!$sceneId || !$act) return false;
        if ($delay < 0) {
            $stop = 1;
            $delay *= 1000;
        } else {
            $stop = 0;
        }
        $event = ['act' => $act, 'params' => $params, 'lockId' => $sceneId, 'delay' => $delay, 'stop' => $stop, 'mtime' => microtime(1)];
        $lock = $this->srv_lock_ . "TIMER_" . $sceneId;
        if ($hostId === "ALL") {
            $hosts = $this->getHosts();
            if (!$hosts) return false;
            if (!$this->redis->setLock($lock)) return gerr("[LOCKON] W=$this->wid L=$lock event=" . json_encode($event));
            foreach ($hosts as $hostId => $v) {
                $this->redis->hset($this->srv_timer_ . $hostId, $sceneId, $event);
            }
            $this->redis->delLock($lock);
            return true;
        }
        if (!$this->redis->setLock($lock)) return gerr("[LOCKON] W=$this->wid L=$lock event=" . json_encode($event));
        $res = $this->redis->hset($this->srv_timer_ . $hostId, $sceneId, $event);
        $this->redis->delLock($lock);
        return $res;
    }

    // TIMER 修改一个场景轮次定时器事件的执行时间
    public function updTimer($sceneId, $params, $delay, $hostId)
    {
        if ($delay < 0) {
            $stop = 1;
            $delay *= 1000;
        } else {
            $stop = 0;
        }
        $lock = $this->srv_lock_ . "TIMER_" . $sceneId;
        if (!$this->redis->setLock($lock)) return gerr("[LOCKON] W=$this->wid L=$lock delay=$delay params=" . json_encode($params));
        $event = $this->redis->hget($this->srv_timer_ . $hostId, $sceneId);
        if (!$event) {
            $this->redis->delLock($lock);
            return false;
        }
        if ($params) {
            foreach ($params as $k => $v) {
                if (!isset($event['params'][$k]) || $event['params'][$k] != $v) {
                    $this->redis->delLock($lock);
                    return false;
                }
            }
        }
        $event['delay'] = $delay;
        $event['mtime'] = microtime(1);
        $res = $this->redis->hset($this->srv_timer_ . $hostId, $sceneId, $event);
        $this->redis->delLock($lock);
        return $res;
    }

    // TIMER 删除一个场景轮次定时器事件
    public function delTimer($sceneId, $hostId)
    {
        $lock = $this->srv_lock_ . "TIMER_" . $sceneId;
        if ($hostId == "ALL") {
            $hosts = $this->getHosts();
            if (!$hosts) return false;
            if (!$this->redis->setLock($lock)) return gerr("[LOCKON] W=$this->wid L=$lock hostid=" . $hostId);
            foreach ($hosts as $hostId => $v) {
                $this->redis->hdel($this->srv_timer_ . $hostId, $sceneId);
            }
            $this->redis->delLock($lock);
            return true;
        }
        if (!$this->redis->setLock($lock)) return gerr("[LOCKON] W=$this->wid L=$lock hostid=" . $hostId);
        $res = $this->redis->hdel($this->srv_timer_ . $hostId, $sceneId);
        $this->redis->delLock($lock);
        return $res;
    }

}

//获取所有服务器关联数据
function getHosts()
{
    global $sweety;
    return $sweety->getHosts();
}

//获取一个REDIS连接对象
function getRedis()
{
    global $sweety;
    return $sweety->getRedis(1);
}

//获取一个MYSQL连接对象
function getMysql()
{
    global $sweety;
    return $sweety->getMysql(1);
}

//设置一个并发锁
// lock		string 	必须 		并发锁ID
// isOnly 	num 	默认0 		0忙等锁1互斥锁
// return 	bool 	false|true
function setLock($lock, $isOnly = 0)
{
    $lock = trim($lock);
    if (empty($lock)) return false;
    global $sweety;
    return $sweety->setLock($lock, intval(!!$isOnly));
}

//销毁一个并发锁
// lock		string 	必须 		并发锁ID
// return 	bool 	false|true
function delLock($lock)
{
    $lock = trim($lock);
    if (empty($lock)) return false;
    global $sweety;
    return $sweety->delLock($lock);
}

//检查修复某种指定的并发锁，系统底层有并发锁自动修复功能，除非特殊情况不建议使用
// pattern	string 	必须 		redis锁的key的匹配表达式
// return 	bool 	false|true
function fixLock($pattern)
{
    $pattern = trim($pattern);
    if (empty($pattern)) return false;
    global $sweety;
    return $sweety->fixLock($pattern);
}

//追加一条MySQL异步操作
// sql		string 	必须 		MySQL查询字串
// return 	bool 	false|true
function bobSql($sql)
{
    $sql = trim($sql);
    if (empty($sql)) return false;
    global $sweety;
    return $sweety->bobSql($sql);
}

//获取连接绑定
// fdu		string 	必须 		连接标示
// return 	mix 	false|array('uid'=>111,...)
function getBind($fdx)
{
    $fdx = trim($fdx);
    if (empty($fdx) || !$fdx) return false;
    global $sweety;
    return $sweety->getBind($fdx);
}

//设置连接绑定
// fdu		string 	必须 		连接标示
// data		array 	必须 		连接数据
// return 	bool 	false|true
function setBind($fdx, $data)
{
    $fdx = trim($fdx);
    if (empty($fdx) || !$fdx || !is_array($data) || !$data) return false;
    global $sweety;
    return $sweety->setBind($fdx, $data);
}

//删除连接绑定
// fdu		string 	必须 		连接标示
// return 	bool 	false|true
function delBind($fdx)
{
    $fdx = trim($fdx);
    if (empty($fdx) || !$fdx) return false;
    global $sweety;
    return $sweety->delBind($fdx);
}

//获取用户数据
// uid		int 	必须 		用户UID
// return 	mix 	false|array('uid'=>111,...)
function getUser($uid)
{
    $uid = intval($uid);
    if ($uid < 1) return false;
    global $sweety;
    return $sweety->getUser($uid);
}

//设置用户数据
// uid		int 	必须 		用户UID
// data		array 	必须 		用户数据
// return 	bool 	false|true
function setUser($uid, $data)
{
    $uid = intval($uid);
    if ($uid < 1 || !is_array($data) || !$data) return false;
    global $sweety;
    return $sweety->setUser($uid, $data);
}

//增减用户属性
// uid		int 	必须 		用户UID
// key		string 	必须 		用户属性
// num 		numeric	必须 		增减数值
// return 	numeric 增减后的新数值
function incUser($uid, $key, $num)
{
    $uid = intval($uid);
    $key = trim($key);
    $num += 0;
    if ($uid < 1 || empty($key)) return false;
    global $sweety;
    return $sweety->incUser($uid, $key, $num);
}

//删除用户数据
// uid		int 	必须 		用户UID
// return 	bool 	false|true
function delUser($uid)
{
    $uid = intval($uid);
    if ($uid < 1) return false;
    global $sweety;
    return $sweety->delUser($uid);
}

//通过FDU获取用户数据
// fdu		string 	必须 		连接标示
function getUserByFd($fdx)
{
    $fdx = trim($fdx);
    if (empty($fdx) || !$fdx) return false;
    global $sweety;
    return $sweety->getUserByFd($fdx);
}

//发送全服广播
// msg		string 	必须 		广播的文本内容
// level 	int 	默认0 		广播优先级
// fdus 	array 	默认空数组 	空数组时发送到全服；否则发送到指定的部分fdu
function sendHorn($msg, $level = 0, $fdxs = [])
{
    $msg = trim($msg);
    $level = intval($level);
    if (empty($msg) || $level < 0 || !is_array($fdxs)) return false;
    global $sweety;
    return $sweety->sendHorn($msg, $level, $fdxs);
}

//发送数据
// fdu		string 	必须 		连接标示
// cmd 		int 	必须 		协议族
// code 	int 	必须 		协议号
// info 	array 	必须 		数据
function sendToFd($fdx, $cmd, $code, $info)
{
    $fdx = trim($fdx);
    $cmd = intval($cmd);
    $code = intval($code);
    if (empty($fdx) || !$fdx || $cmd < 0 || $code < 0 || !is_array($info)) return false;
    global $sweety;
    return $sweety->sendTofd($fdx, $cmd, $code, $info);
}

//断开连接
// fdu		string 	必须 		连接标示。
// hostId 	string 	默认空字串 	描述信息，会写入日志
function closeToFd($fdx, $desc = "")
{
    $fdx = trim($fdx);
    if (empty($fdx) || !$fdx || !is_string($desc)) return false;
    global $sweety;
    return $sweety->closeToFd($fdx, $desc);
}

//添加常规延迟事件
// act		string 	必须 		事件执行时的方法
// params 	array 	必须 		事件执行时的参数
// delay 	int 	默认0		延迟指定毫秒数后，开始执行事件。<0立即执行
// hostId 	string 	默认HOSTID 	在某个服务器上增加。ALL在所有服务器上增加
function setEvent($act, $params, $delay = 0, $hostId = null)
{
    $act = trim($act);
    $delay = intval($delay);
    $hostId = is_null($hostId) ? HOSTID : trim($hostId);
    if (empty($hostId) || empty($act) || !is_array($params)) return false;
    global $sweety;
    return $sweety->setEvent($act, $params, $delay, $hostId);
}

//添加动态闹钟事件
// sceneId	string 	必须 		场景ID＝闹钟ID
// act		string 	必须 		事件执行时的方法
// params 	array 	必须 		事件执行时的参数
// delay 	int 	必须			延迟指定毫秒数后，开始执行事件。<0未执行的定时器停止
// hostId 	string 	默认HOSTID 	在某个服务器上增加。ALL在所有服务器上增加
function setTimer($sceneId, $act, $params, $delay, $hostId = null)
{
    $sceneId = trim($sceneId);
    $act = trim($act);
    $delay = intval($delay);
    $hostId = is_null($hostId) ? HOSTID : trim($hostId);
    if (empty($sceneId) || empty($hostId) || empty($act) || !is_array($params)) return false;
    global $sweety;
    return $sweety->setTimer($sceneId, $act, $params, $delay, $hostId);
}

//校验后修改动态闹钟事件的执行时间
// sceneId	string 	必须 		场景ID＝闹钟ID
// params 	array 	必须			校验参数。array()不做校验，否则如果校验用项与原参数某项不符合，不执行修改
// delay 	int 	必须			延迟指定毫秒数后，开始执行事件。<0未执行的定时器停止
// hostId 	string 	必须 		在某个服务器上的闹钟。
function updTimer($sceneId, $params, $delay, $hostId)
{
    $sceneId = trim($sceneId);
    $delay = intval($delay);
    $hostId = trim($hostId);
    if (empty($sceneId) || empty($hostId) || !is_array($params)) return false;
    global $sweety;
    return $sweety->updTimer($sceneId, $params, $delay, $hostId);
}

//删除动态闹钟事件
// sceneId	string 	必须 		场景ID＝闹钟ID
// hostId 	string 	默认HOSTID 	在某个服务器上删除。ALL在所有服务器上删除
function delTimer($sceneId, $hostId = null)
{
    $sceneId = trim($sceneId);
    $hostId = is_null($hostId) ? HOSTID : trim($hostId);
    if (empty($sceneId) || empty($hostId)) return false;
    global $sweety;
    return $sweety->delTimer($sceneId, $hostId);
}

//进程重载
function srvReload()
{
    global $sweety;
    return $sweety->srv->reload();
}
