<?php

/**
 * PHP 比赛类
 * 查看赛场 报名参赛 取消报名 人满开赛 定时开赛 计算排行 获取奖励
 * 使用方法
 * class obj {
 *    public $match = null;
 *    public $redis = null;
 *    public $mysql = null;
 *    public function getMatch() {
 *        if ( $this->match === null ) $this->match = match::inst($this->redis, $this->mysql);
 *        return $this->match;
 *    }
 * }
 * $obj = new obj;
 * $match = $obj->getMatch();
 * $md = 3; $channel = 'ali';
 * $res = $match->show($md, $user);
 *
 */
class match
{
    private static $inst = null;
    public $errno = 0;
    public $error = '';
    public $debug = '';
    public $func = '';
    public $errors = [];
    public $elist = [
        '99' => "操作失败，请稍候重试",
        '98' => "操作失败，请重新登录游戏",
        '97' => "操作失败，请联系免费客服电话: \n400-008-5665",
        '96' => "操作失败，请重新登录游戏，\n如果问题仍旧出现，请联系免费客服电话: \n400-008-5665",
        '95' => "您的网络可能不稳定，请重新登录游戏，\n如果问题仍旧出现，请联系免费客服电话: \n400-008-5665",
    ];
    public $state = [
        0 => '时间未到',
        1 => '正在报名',
        2 => '已经报名',
        3 => '已经报名',
        4 => '已经报名',
        5 => '已经报名',
        6 => '已在牌局',
    ];
    /**
     * @var DB
     */
    public $mysql = null;
    public $redis = null;
    public $mds = [3];//
    public $channels = null;
    public $rooms = null;
    public $games = null;
    public $krd_user = KEY_USER_;
    public $krd_room = 'lord_list_room';
    public $krd_table_ = 'lord_table_info_';
    public $kdb_user = 'lord_game_user';
    public $kdb_room = 'lord_game_room';
    public $kdb_record_money_ = 'lord_record_money_';
    public $kdb_record_money_day = 'lord_record_money_day';
    public $kdb_record_money_hour = 'lord_record_money_hour';
    public $kdb_record_money_type = 'lord_record_money_type';
    public $kdb_record_table_ = 'lord_record_table_';
    public $kdb_record_action_ = 'lord_record_action_';

    //私有构造
    private function __construct($redis = null, $mysql = null)
    {
        $this->redis = $redis;
        if ($this->redis === null) $this->getRedis();
        $this->mysql = $mysql;
        if ($this->mysql === null) $this->getMysql();
        $this->confs = include(ROOT . '/conf/confs.php');
        $this->iniRooms();
    }

    //析构
    private function __destruct()
    {
        return true;
        // return $this->insert();
    }

    //析构强制
    public function __close()
    {
        return true;
        // return $this->insert();
    }

    //覆盖克隆
    private function __clone()
    {
        return serr("本类禁止克隆 class=" . __CLASS__);
    }

    //获取单例
    public static function inst($redis = null, $mysql = null)
    {
        if (!(self::$inst instanceof self)) self::$inst = new match($redis, $mysql);
        return self::$inst;
    }

    //连接Mysql
    public function getMysql()
    {
        if ($this->mysql === null) $this->mysql = new DB;
        return $this->mysql;
    }

    //连接Redis
    public function getRedis()
    {
        if ($this->redis === null) $this->redis = new RD;
        return $this->redis;
    }

    //获取当前错误, 并清空当前错误
    //return 		array(array('errno'=>1,'error'=>'错误信息内容','debug'=>'setMine'));
    public function getError()
    {
        $errors = $this->errors;
        $this->errors = [];
        $this->errno = 0;
        $this->error = '';
        return $errors;
    }

    public function getElist()
    {
        return $this->elist;
    }

    private function setFunc($func)
    {
        $this->func = $func;
    }

    //设置错误
    //errno 		错误编号
    //p?			附加参数
    //return 		false //可以直接使用“return $this->setError();”来结束运算并返回false，调用方通过“$class->getError()”可以获取到错误信息数组
    private function setError($errno, $p1 = '', $info = '')
    {
        if (!$errno) return gerr("赛制代码错误");
        if (is_int($errno)) {
            $this->errno = $errno;
            $this->error = isset($this->elist[$errno]) ? sprintf($this->elist[$errno], $p1) : 'unknown';
        } else {
            $this->errno = 100;
            $this->error = $errno;
        }
        if (!is_string($info)) $info = json_encode($info);
        $this->errors[] = ['errno' => $this->errno, 'error' => $this->error, 'debug' => "[{$this->func}] $info"];
        return false;
    }

    //获取渠道ID
    //channel 	str 	渠道英文标记
    //return 	int 	渠道数字ID, 默认0
    public function getChannelid($channel)
    {
        $this->setFunc(__FUNCTION__);
        if (!$channel) return 0;
        if ($this->channels === null) {
            $list = $this->mysql->getData("SELECT * FROM `lord_game_channel`");
            if ($list) {
                $channels = [];
                foreach ($list as $key => $val) {
                    $channels[$val['channel']] = intval($val['id']);
                }
                $this->channels = $channels;
            } else {
                $sql = "CREATE TABLE IF NOT EXISTS `lord_game_channel` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`channel` varchar(16) NOT NULL DEFAULT '' COMMENT '渠道',
					`is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0正常1已删',
					`tmcr` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
					PRIMARY KEY (`id`),
					INDEX `s0` (`channel`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏渠道表' AUTO_INCREMENT=1";
                $res = $this->mysql->runSql($sql);
                if ($res) {
                    $this->channels = [];
                } else {
                    gerr("MYSQL->runSql=$sql");
                    return 0;
                }
            }
        }
        if (isset($this->channels[$channel])) return $this->channels[$channel];
        $sql = "SELECT `id` FROM `lord_game_channel` WHERE `channel` = '$channel'";
        $id = $this->mysql->getVar($sql);
        if ($id) {
            $this->channels[$channel] = $id;
            return $id;
        }
        $lock = 'NEWCHANNEL_' . $channel;
        if (!$this->getRedis()->setLock($lock, 1)) {
            $sql = "SELECT `id` FROM `lord_game_channel` WHERE `channel` = '$channel'";
            $times = 20;
            $id = 0;
            while ($times && !($id = $this->mysql->getVar($sql))) {
                usleep(200000);
                $times--;
            }
            if ($id) {
                $this->channels[$channel] = $id;
                return $id;
            } else {
                return 0;
            }
        }
        $sql = "INSERT INTO `lord_game_channel` (`channel`, `is_del`, `tmcr`) VALUES ('$channel', 0, " . time() . ")";
        $res = $this->mysql->runSql($sql);
        if ($res) {
            $id = $this->mysql->lastId();
            if ($id) {
                $this->channels[$channel] = $id;
                $this->getRedis()->delLock($lock);
                return $id;
            } else {
                gerr("MYSQL->lastId=$sql");
            }
        } else {
            gerr("MYSQL->runSql=$sql");
        }
        $this->getRedis()->delLock($lock);
        return 0;
    }

    //初始化赛制房间列表
    public function iniRooms()
    {
        $this->setFunc(__FUNCTION__);
        $rdc = include(ROOT . '/conf/rooms.php');
        foreach ($rdc as $rd => $v) {
            if (!in_array($v['modelId'], $this->mds)) unset($rdc[$rd]);
        }
        $ext = $this->getRoomsExt();
        foreach ($ext as $rd => $v) {
            $rdc[$rd] = isset($rdc[$rd]) ? array_merge($rdc[$rd], $v) : $v;
        }
        $this->rooms = $rdc;
    }

    //获取数据库里的扩展房间配置
    public function getRoomsExt()
    {
        $this->setFunc(__FUNCTION__);
        $all = $this->redis->hgetall($this->krd_room);
        if (!$all) {
            $sql = "SELECT * FROM `{$this->kdb_room}` WHERE `is_del` = 0 ORDER BY `sort` DESC, `id` DESC";
            $ret = $this->mysql->getData($sql);
            if (!$ret) return [];
            $all = [];
            foreach ($ret as $k => $v) {
                $all[$v['roomId']] = [//待续
                    'isOpen'      => intval($v['isOpen']),
                    'isMobi'      => intval($v['isMobi']),
                    'verMin'      => intval($v['verMin']),
                    'modelId'     => intval($v['modelId']),
                    'mode'        => trim($v['mode']),
                    'roomId'      => intval($v['roomId']),
                    'room'        => trim($v['room']),
                    'name'        => trim($v['name']),
                    'showRules'   => $v['showRules'] && ($tmp = json_decode($v['showRules'], 1)) ? $tmp : [],
                    'baseCoins'   => intval($v['baseCoins']),
                    'rate'        => intval($v['rate']),
                    'rateMax'     => intval($v['rateMax']),
                    'limitCoins'  => intval($v['limitCoins']),
                    'rake'        => intval($v['rake']),
                    'enter'       => trim($v['enter']),
                    'enterLimit'  => intval($v['enterLimit']),
                    'enterLimit_' => intval($v['enterLimit_']),
                    'gameBombAdd' => intval($v['gameBombAdd']),
                    'brief'       => trim($v['brief']),
                    'entry'       => trim($v['entry']),
                    'tips'        => trim($v['tips']),
                    'rules'       => trim($v['rules']),
                    'start'       => intval($v['start']),
                    'entryMoney'  => trim($v['entryMoney']),
                    'entryCost'   => intval($v['entryCost']),
                    'entryTime'   => intval($v['entryTime']),
                    'entryOut'    => intval($v['entryOut']),
                    'entryOsec'   => intval($v['entryOsec']),
                    'entryOmax'   => intval($v['entryOmax']),
                    'entryMax'    => intval($v['entryMax']),
                    'entryMin'    => intval($v['entryMin']),
                    'entryFull'   => intval($v['entryFull']),
                    'entryMore'   => intval($v['entryMore']),
                    'entryLess'   => intval($v['entryLess']),
                    'scoreInit'   => intval($v['scoreInit']),
                    'scoreRate'   => $v['scoreRate'] + 0,
                    'rankRule'    => intval($v['rankRule']),
                    'tableRule'   => intval($v['tableRule']),
                    'outRule'     => intval($v['outRule']),
                    'outValue'    => trim($v['outValue']),
                    'awardRule'   => $v['awardRule'] && ($tmp = json_decode($v['awardRule'], 1)) ? $tmp : [],
                    'apkurl'      => $v['apkurl'],
                    'appid'       => intval($v['appid']),
                    'ver'         => $v['ver'],
                    'vercode'     => intval($v['vercode']),
                    'bytes'       => $v['bytes'] + 0,
                    'desc'        => trim($v['desc']),
                    'md5'         => $v['md5'],
                    'package'     => $v['package'],
                    'sort'        => intval($v['sort']),
                ];
            }
            $this->redis->hmset($this->krd_room, $all);
        }
        foreach ($all as $rd => $v) {
            if (!in_array($v['modelId'], $this->mds)) unset($all[$rd]);
        }
        return $all;
    }

    //获取规则控制下的可用房间列表
    public function getRooms($md, $ch = '', $vercode = 0, $coins = 0, $gold = 0)
    {
        $this->setFunc(__FUNCTION__);
        if (!in_array($md, $this->mds)) return [];
        $rdc = $this->rooms;
        $tmid = time();
        $day0 = strtotime(date('Y-m-d'));
        $wday = date("N");    //周n[1-7]
        $rooms = [];
        foreach ($rdc as $rd => $v) {
            $show = 1;
            if (!$v['isOpen'] || ($vercode && $vercode < $v['verMin'])) continue;
            $rules = isset($v['showRules']) && is_array($v['showRules']) ? $v['showRules'] : [];
            foreach ($rules as $rule) {
                if ($ch && isset($rule['channel']) && $rule['channel'] && is_array($rule['channel']) && !in_array($ch, $rule['channel'])) {
                    $show = 0;
                    continue;
                }
                if ($ch && isset($rule['channot']) && $rule['channot'] && is_array($rule['channot']) && in_array($ch, $rule['channot'])) {
                    $show = 0;
                    continue;
                }
                if ($gold && isset($rule['gold']) && $rule['gold'] && ($golds = explode("|", $rule['gold'])) && !(count($golds) == 2 && $golds[0] <= $gold && ($golds[1] ? $coins < $golds[1] : 1))) {
                    $show = 0;
                    continue;
                }
                if ($coins && isset($rule['coins']) && $rule['coins'] && ($coinss = explode("|", $rule['coins'])) && !(count($coinss) == 2 && $coinss[0] <= $coins && ($coinss[1] ? $coins < $coinss[1] : 1))) {
                    $show = 0;
                    continue;
                }
                if (isset($rule['mixtime']) && $rule['mixtime'] && is_array($rule['mixtime'])) {
                    $show_ = 0;
                    foreach ($rule['mixtime'] as $sets) {
                        $sets = explode("|", $sets);
                        if (count($sets) != 3) continue;
                        $start = explode(" ", $sets[0]);
                        $dateStart = strtotime($start[0] . ' 00:00:00');
                        $todayStart = strtotime(date("Y-m-d " . $start[1]));
                        $end = explode(" ", $sets[1]);
                        $dateEnd = strtotime($end[0] . ' 23:59:59');
                        $todayEnd = strtotime(date("Y-m-d " . $end[1]));
                        $weeks = $sets[2];
                        if ($dateStart <= $day0 && $day0 < $dateEnd && $todayStart <= $tmid && $tmid < $todayEnd && ($weeks ? (strpos($weeks, $wday) !== false) : 1)) {
                            $show_ = 1;
                            break;
                        }
                    }
                    if (!$show_) {
                        $show = 0;
                        continue;
                    }
                }
            }
            if (!$show) continue;
            $rooms[$rd] = $v;
        }
        return $rooms;
    }

    // 显示房间列表
    public function showRooms($md, $U)
    {
        $this->setFunc(__FUNCTION__);
        if (!in_array($md, $this->mds)) return $this->setError(98);
        $rooms = $this->getRooms($md, $U['channel'], $U['vercode'], $U['coins'], $U['gold']);
        if (!$rooms) return $this->setError(98);
        foreach ($rooms as $rd => $R) {
            $show[] = [
                'mode'   => $R['mode'],
                'roomId' => $rd,
                'room'   => $R['room'],
                'brief'  => $R['brief'],
                'entry'  => $R['entry'],
                'sort'   => $R['sort'],
            ];
        }
        return $show;
    }

    // 显示房间数据
    public function showRoom($rd, $U)
    {
        $this->setFunc(__FUNCTION__);
        if (!isset($this->rooms[$rd])) return $this->setError(98);
        $md = $this->rooms[$rd]['modelId'];
        $rooms = $this->getRooms($md, $U['channel'], $U['vercode'], $U['coins'], $U['gold']);
        if (!isset($rooms[$rd])) return $this->setError(98);
        $R = $rooms[$rd];
        $md = $R['modelId'];
        $G = $this->getGame($md, $rd);
        if (!$G) {
            $lock = "MATCH_{$md}_{$rd}";
            if (!setLock($lock)) {
                gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
                return $this->setError(99);
            }
            $G = $this->newGame($R);
            delLock($lock);
        }
        if (!$G) return $this->setError(99);
        $gd = $G['id'];
        $entryMax = $R['entryMax'];
        $entryNum = $G['entryNum'];
        $entryJoin = $this->getEntry($R);
        $entryState = $this->getState($R, $G, $U);
        $award = [];
        foreach ($R['awardRule'] as $k => $v) {
            $award[] = "第 $k 名: " . $v['text'];
        }
        $show = [
            'room'  => $R['room'],
            'brief' => $entryJoin ? ($this->state[$entryState] . " $entryNum / $entryMax") : $R['brief'],
            'entry' => $R['entry'],
            'award' => $award,
            'tips'  => $R['tips'],
            'rules' => $R['rules'],
            'money' => $R['entryMoney'],
            'cost'  => $R['entryCost'],
            'state' => $entryState,
        ];
        return $show;
    }

    //新建一个场次
    private function newGame($R)
    {
        $this->setFunc(__FUNCTION__);
        $rd = $R['roomId'];
        $md = $R['modelId'];
        $mctm = round(microtime(1), 2);
        $G['modelId'] = $md;
        $G['roomId'] = $rd;
        $G['player'] = $G['outer'] = $G['robot'] = $G['table'] = $G['rank'] = $G['award'] = [];
        $G['entryNum'] = $G['entryRob'] = $G['entryPool'] = $G['round'] = $G['starte'] = $G['finish'] = 0;
        $G['outValue'] = $R['outValue'];
        $G['create'] = $G['update'] = $mctm;
        $sql = "INSERT INTO `lord_match_games` (`modelId`, `roomId`, `player`, `outer`, `robot`, `rank`, `award`, `entryNum`, `entryRob`, `entryPool`, `round`, `starte`, `finish`, `create`, `update`) ";
        $sql .= "VALUES ($md, $rd, '" . json_encode($G['player']) . "', '" . json_encode($G['outer']) . "', '" . json_encode($G['robot']) . "', '" . json_encode($G['rank']) . "', '" . json_encode($G['award']) . "', 0, 0, 0, 0, 0, 0, $mctm, $mctm)";
        $ret = $this->mysql->runSql($sql);
        if (!$ret) return gerr("[MYSQL] sql=$sql");
        $gd = $this->mysql->lastId();
        if (!$gd) return gerr("[MYSQL] lastId($sql)");
        $G['id'] = $gd;
        $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
        if (!$ret) return gerr("[REDIS] hset(lord_match_games_{$md}, $gd, data) data=" . json_encode($G));
        return $G;
    }

    // 获取报名时间
    // return		0不可报名 1可以报名
    private function getEntry($R)
    {
        $this->setFunc(__FUNCTION__);
        //随时可以报名
        if (!$R['start'] || !$R['entryTime']) return 1;
        $time = time();
        //每天定时开场
        if (86400 * 0 < $R['start'] && $R['start'] <= 86400 * 1) {
            $gstart = $R['start'] + strtotime(date("Y-m-d"));
            // $period = time() - strtotime(date("Y-m-d"));
        } //每周定时开场
        elseif (86400 * 1 < $R['start'] && $R['start'] <= 86400 * 8) {
            $gstart = $R['start'] + strtotime(date("Y-m-d", time() - (date("N") - 1) * 86400)) + 86400 * 1;
            // $period = time() - strtotime(date("Y-m-d", time() - ( date("N") - 1 ) * 86400)) + 86400 * 1;
        } //每月定时开场
        elseif (86400 * 8 < $R['start'] && $R['start'] <= 86400 * 39) {
            $gstart = $R['start'] + strtotime(date("Y-m-01")) + 86400 * 8;
            // $period = time() - strtotime(date("Y-m-01")) + 86400 * 8;
        } //日期定时开场
        elseif (86400 * 39 < $R['start']) {
            $gstart = $R['start'];
            // $period = time();
        } //开场配置无效
        else return 0;
        //达到报名时间
        if ($gstart - $R['entryTime'] <= $time && $time < $gstart) return 1;
        return 0;
    }

    // 获取开赛时间
    // return 		0马上开赛 n开赛时间
    public function getStart($R, $isGame = 0)
    {
        $this->setFunc(__FUNCTION__);
        if ($isGame) {
            $G = $R;
            $rooms = $this->getRooms($G['modelId']);
            $R = $rooms[$G['roomId']];
        }
        if (!$R['start']) return 0;
        elseif (86400 * 0 < $R['start'] && $R['start'] <= 86400 * 1) return $R['start'] + strtotime(date("Y-m-d"));
        elseif (86400 * 1 < $R['start'] && $R['start'] <= 86400 * 8) return $R['start'] + strtotime(date("Y-m-d", time() - (date("N") - 1) * 86400)) - 86400 * 1;
        elseif (86400 * 8 < $R['start'] && $R['start'] <= 86400 * 39) return $R['start'] + strtotime(date("Y-m-01")) - 86400 * 8;
        elseif (86400 * 39 < $R['start']) return $R['start'];
        else return 0;
    }

    // 获取用户的报名状态
    // return		0不能报名 1没有报名 2已经报名不可取消 3已经报名可以取消 4报名后比赛已经开始 5已经报名其他场次 6已在参与其他牌局
    public function getState($R, $G, $U)
    {
        $this->setFunc(__FUNCTION__);
        $md = $R['modelId'];
        $rd = $R['roomId'];
        $gd = $G['id'];
        if ((isset($U['entry']) && $U['entry']) && (isset($U['gameId']) && $U['gameId'])) {
            if ($md != $U['modelId'] || $rd != $G['roomId']) return 5;
            if ($gd == $U['gameId']) {
                if ($G['starte']) return 4;
                if ($G['player'][$U['uid']][0] + $R['entryOsec'] > time() || ($R['entryOmax'] && $G['entryNum'] >= $R['entryOmax'])) return 2;
                return 3;
            } else {
                $G = $this->getGame($md, $rd, $U['gameId']);
                if (!$G) return 0;
                if ($G['starte']) return 4;
                if ($G['player'][$U['uid']][0] + $R['entryOsec'] > time() || ($R['entryOmax'] && $G['entryNum'] >= $R['entryOmax'])) return 2;
                return 5;
            }
        }
        if ($U['tableId']) return 6;
        return $this->getEntry($R);
    }

//    /**
//     * 为了水果机做比赛场状态判断
//     * 以下代码是他们逼着我写的,我本人表示不以下代码负责
//     * @param $R array 房间
//     * @param $G array 场次
//     * @param $U array 用户
//     * @return int 0不能报名 1没有报名 2已经报名不可取消 3已经报名可以取消 4报名后比赛已经开始 5已经报名其他场次 6已在参与其他牌局
//     */
//    public function getStateForFruit($R, $G, $U)
//    {
//        $this->setFunc(__FUNCTION__);
//        $md = $R['modelId'];
//        $rd = $R['roomId'];
//        $gd = $G['id'];
//        if ((isset($U['entry']) && $U['entry']) && (isset($U['gameId']) && $U['gameId'])) {
//            if ($md != $U['modelId'] || $rd != $G['roomId']) return 5;
//            if ($gd == $U['gameId']) {
//                if ($G['starte']) return 4;
//
//                //1.报名后n秒内不可取消
//                //2.人满后不可取消
//                //3.开赛前10分钟不可取消
//                if (
//                    $G['player'][$U['uid']][0] + $R['entryOsec'] > time() ||
//                    ($R['entryOmax'] && $G['entryNum'] >= $R['entryOmax']) ||
//                    ($R['start'] - 600) >= time()
//                ) {
//                    return 2;
//                }
//                return 3;
//            } else {
//                $G = $this->getGame($md, $rd, $U['gameId']);
//                if (!$G) return 0;
//                if ($G['starte']) return 4;
//                if ($G['player'][$U['uid']][0] + $R['entryOsec'] > time() || ($R['entryOmax'] && $G['entryNum'] >= $R['entryOmax'])) return 2;
//                return 5;
//            }
//        }
//        if ($U['tableId']) return 6;
//        return $this->getEntry($R);
//    }


    // 获取(某渠道ID的)全部赛场房间场次
    public function getGames($md)
    {
        $this->setFunc(__FUNCTION__);
        $games = $this->redis->hgetall("lord_match_games_{$md}");
        if (!$games) return [];
        return $games;
    }

    // 获取赛制房间下的场次数据
    public function getGame($md, $rd, $gd = 0)
    {
        $this->setFunc(__FUNCTION__);
        if ($gd) {
            $game = $this->redis->hget("lord_match_games_{$md}", $gd);
            return $game ? $game : [];
        }
        $games = $this->getGames($md);
        if (!$games) return [];
        $gd = 0;
        $G = [];
        foreach ($games as $id => $v) {
            if ($v['roomId'] != $rd) continue;
            if ($id > $gd) {
                $gd = $id;
                $G = $v;
            }
        }
        return $G;
    }

    // 报名
    public function entry($rd, $U)
    {
        $this->setFunc(__FUNCTION__);
        if ($U['gameId'] || (isset($U['entry']) && $U['entry']) || (isset($U['starte']) && $U['starte']) || $U['tableId']) return $this->setError("请先结束当前的牌局");
        if (!isset($this->rooms[$rd])) return $this->setError(98);
        $md = $this->rooms[$rd]['modelId'];
        $ud = $U['uid'];
        $rooms = $this->getRooms($md, $U['channel'], $U['vercode'], $U['coins'], $U['gold']);
        if (!isset($rooms[$rd])) return $this->setError(98);
        $R = $rooms[$rd];
        $md = $R['modelId'];
        
        $G = $this->getGame($md, $rd);
        if (!$G) {
            return $this->setError(99);
        }
        $gd = $G['id'];
        
        $lock = "MATCH_{$md}_{$rd}_{$gd}";
        if (!setLock($lock)) {
            gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
            return $this->setError(99);
        }
        //校验是否是最新场次
        if($R['entryMax'] == $G['entryNum']){
            $sgin =0;
            $nextG = [];
            while(true){
                $nextG = $this->getGame($md, $rd, $gd+1);
                if($nextG || $sgin >=5)
                {
                        break;
                }
                $sgin++;
                sleep(0.1);
            }
            delLock($lock);
            if($nextG){
                $gd += 1;
                $G = $nextG;
                $lock = "MATCH_{$md}_{$rd}_{$gd}";
                if (!setLock($lock))
                {
                        gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
                        return $this->setError(99);
                }
            }else
            {
                return $this->setError(99);
            }
        }
        $entryCost = $R['entryCost'];
        $entryMoney = $R['entryMoney'];

        //沃城渠道不花钱就能报名
        if ($rd == 3004 && isset($U['vip_channel']) && $U['vip_channel'] == 'wocheng' && isset($U['vip_level']) && $U['vip_level'] >= 1) {
            $entryCost = $entryMoney = 0;
            $sql = sprintf("REPLACE INTO lord_game_user_extra(`uid`,`entry_expenses`) VALUES ('%s','%s');", $U['uid'], 'free');
            $this->mysql->runSql($sql);
        }


        if ($entryCost && $U[$entryMoney] < $entryCost) {
            delLock($lock);
            return $this->setError("报名费用不够");
        }
        $entryState = $this->getState($R, $G, $U);
        if ($entryState != 1) {
            delLock($lock);
            return $this->setError($entryState ? "不能重复报名" : "未到报名时间");
        }
        $mctm = round(microtime(1), 2);
        $G['player'][$ud] = [$mctm, $R['scoreInit'], 0, 0];//entry,score,rank,outtime
        $newU['entry'] = $mctm;
        $newU['score'] = $R['scoreInit'];
        $newU['modelId'] = $md;
        $newU['roomId'] = $rd;
        $newU['gameId'] = $gd;
        if ($entryCost) {
            $newU[$entryMoney] = $U[$entryMoney] - $entryCost;
            $G['entryPool'] += $entryCost;
        }
        $G['entryNum']++;
        $G['update'] = $newU['update'] = $mctm;
        $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
        if (!$ret) {
            delLock($lock);
            return $this->setError(99);
        }
        $ret = $this->redis->hmset(KEY_USER_ . $ud, $newU);
        if (!$ret) {
            delLock($lock);
            return $this->setError(99);
        }
        $this->check($R, $G);
        delLock($lock);
        return ['newU' => $newU, 'room' => $R, 'game' => $G];
    }

    // 检查开始
    public function check($R, $G)
    {
        $this->setFunc(__FUNCTION__);
        if ($G['entryNum'] == $R['entryMax'] && !$this->getStart($R)) {
            $md = $G['modelId'];
            $rd = $G['roomId'];
            $gd = $G['id'];
//             $lock = "MATCH_{$md}_{$rd}_{$gd}";
//             if (!setLock($lock,1)) {
//                 gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
//                 return $this->setError(99);
//             }
            $this->newGame($R);
            $this->start($R, $G);
//             delLock($lock);
        }
        return true;
    }

    // 取消报名
    public function untry($rd, $U)
    {
        $this->setFunc(__FUNCTION__);
        if (!$U['gameId'] || !isset($U['entry']) || !$U['entry']) return $this->setError('您还没有报名');
        if (!isset($this->rooms[$rd])) return $this->setError(98);
        $md = $this->rooms[$rd]['modelId'];
        $ud = $U['uid'];
        $rooms = $this->getRooms($md, $U['channel'], $U['vercode'], $U['coins'], $U['gold']);
        if (!isset($rooms[$rd])) return $this->setError(98);
        $R = $rooms[$rd];
        if (!$R['entryOut']) return $this->setError('本场比赛不能取消');
        $ud = $U['uid'];
        $md = $R['modelId'];
        $G = $this->getGame($md, $rd);
        if (!$G) return $this->setError(99);
        $gd = $G['id'];
        if($gd > $U['gameId'])return $this->setError('您的比赛已开始');
        $lock = "MATCH_{$md}_{$rd}_{$gd}";
        if (!setLock($lock)) {
            gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
            return $this->setError(99);
        }
        $entryCost = $R['entryCost'];
        $entryMoney = $R['entryMoney'];
        $entryState = $this->getState($R, $G, $U);
        if ($entryState != 3) {
            delLock($lock);
            return $this->setError($entryState > 1 ? "现在不能取消" : "您还没有报名");
        }
        $mctm = round(microtime(1), 2);
        $newU['modelId'] = $newU['roomId'] = $newU['gameId'] = $newU['entry'] = $newU['score'] = 0;

        if ($rd == 3004 && $this->mysql->getVar(sprintf("SELECT `entry_expenses` FROM `lord_game_user_extra` WHERE `uid`='%s';", $U['uid'])) == 'free') {
            $this->mysql->runSql(sprintf("UPDATE lord_game_user_extra SET entry_expenses='' WHERE uid='%s';", $U['uid']));
        } else {
            if ($entryCost && $R['entryOut'] == 1) {
                $newU[$entryMoney] = $U[$entryMoney] + $entryCost;
                $G['entryPool'] -= $entryCost;
            }
        }
        $G['entryNum']--;
        $G['update'] = $newU['update'] = $mctm;
        unset($G['player'][$ud]);
        $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
        if (!$ret) {
            delLock($lock);
            return $this->setError(99);
        }
        $ret = $this->redis->hmset(KEY_USER_ . $ud, $newU);
        if (!$ret) {
            delLock($lock);
            return $this->setError(99);
        }
        delLock($lock);
        return ['newU' => $newU, 'room' => $R, 'game' => $G];
    }

    // 比赛定时检查 每分钟
    public function timer($md, $YmdHi)
    {
        $this->setFunc(__FUNCTION__);
        if (!in_array($md, $this->mds)) return false;
        $rooms = $this->getRooms($md);
        foreach ($rooms as $rd => $R) {
            if (!$R['start']) continue;
            $starte = $this->getStart($R);
            if (!$starte) continue;
            $startYmdHi = date('YmdHi', $starte);
            if ($startYmdHi < $YmdHi) continue;
            if ($startYmdHi > $YmdHi) {
                if ($R['entryFull'] == 0) {//提前开赛加开新场
                    $G = $this->getGame($md, $rd);
                    if (!$G || $R['entryMax'] > $G['entryNum']) continue;
                    $this->newGame($R);
                    $this->start($R, $G);
                } elseif ($R['entryFull'] == 1) {//提前开赛不开新场
                    $G = $this->getGame($md, $rd);
                    if (!$G || $R['entryMax'] > $G['entryNum']) continue;
                    $this->start($R, $G);
                } elseif (date('YmdHi', $starte - 60) == $YmdHi) {//一分钟前提前通知
                    $G = $this->getGame($md, $rd);
                    if (!$G || !$G['entryNum']) continue;
                    foreach ($G['player'] as $ud => $player) {
                        $U = $this->getUser($ud);
                        $fd = $U['fd'];
                        if (!$fd) continue;
                        $cmd = 5;
                        $code = 222;
                        $send = ['errno' => 0, 'error' => "您已报名的比赛就快开始，\n请前往比赛场。", 'modelId' => $G['modelId'], 'roomId' => $G['roomId']];
                        sendToFd($fd, $cmd, $code, $send);
                    }
                }
                continue;
            }
            //定时开赛
            $G = $this->getGame($md, $rd);
            if (!$G) continue;
            $gd = $G['id'];
            $lock = "MATCH_{$md}_{$rd}_{$gd}";
            if (!setLock($lock)) {
                gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
                return $this->setError(99);
            }
            if ($R['entryMin'] <= $G['entryNum']) {
                // if ( $R['entryMore'] == 0 ) { 下面的填补机器人逻辑 }
                $num = ($G['entryNum'] - $R['entryMore']) % 3;
                if ($num) {
                    $G = $this->robot($R, $G, $num);
                    if (!$G) {
                        delLock($lock);
                        continue;
                    }
                }
                if ($R['start'] <= 86400 * 39) $this->newGame($R);
                if ($R['entryMore'] > 0) {//1
                    gerr("[MATCH] 暂不支持此配置. entryMore=" . $R['entryMore']);
                }
                $this->start($R, $G);
            } elseif ($G['entryNum']) {
                // if ( $R['entryLess'] == 0 ) { 下面的填补机器人逻辑 }
                $num = $R['entryMin'] - $G['entryNum'];
                if ($num) {
                    $G = $this->robot($R, $G, $num);
                    if (!$G) {
                        delLock($lock);
                        continue;
                    }
                }
                if ($R['start'] <= 86400 * 39) $this->newGame($R);
                if ($R['entryLess'] > 0) {//1 2
                    gerr("[MATCH] 暂不支持此配置. entryLess=" . $R['entryLess']);
                }
                $this->start($R, $G);
            }
            delLock($lock);
        }
        return true;
    }

    // 补充机器人
    public function robot($R, $G, $num)
    {
        $this->setFunc(__FUNCTION__);
        $md = $R['modelId'];
        $rd = $R['roomId'];
        $gd = $G['id'];
        $sql = "SELECT * FROM `lord_game_robot` WHERE `state` = 0 ORDER BY `id` LIMIT $num";//state0
        $robots = $this->mysql->getData($sql);
        if (!$robots || count($robots) != $num) return gerr("[MYSQL] getData($sql) data=" . json_encode($robots));
        if (!isset($G['robot'])) $G['robot'] = [];
        $uids = [];
        foreach ($robots as $v) {
            $ud = $v['uid'];
            $uids[] = $ud;
            $U = ['fd'        => 0, 'modelId' => 0, 'roomId' => 0, 'tableId' => 0, 'seatId' => 0, 'gameId' => 0, 'gamesId' => 0,
                  'uid'       => $v['uid'] + 0, 'cool_num' => $v['cool_num'] . "", 'nick' => $v['nick'] . "", 'word' => $v['word'] . "",
                  'sex'       => $v['sex'] + 0, 'avatar' => $v['avatar'] + 0, 'exp' => $v['exp'] + 0, 'level' => $v['level'] + 0,
                  'gold'      => $v['gold'] + 0, 'coins' => $v['coins'] + (mt_rand(0, 30) * 80), 'coupon' => 0,
                  'play'      => mt_rand(333, 999), 'win' => intval(mt_rand(333, 999) / 3 + mt_rand(10, 77)),
                  'propDress' => ['1' => 1], 'propItems' => [], 'propAcces' => [], 'buff' => [],
                  'giveup'    => 0, 'score' => 0, 'isShowcard' => 0, 'channel' => "robot", 'vercode' => 10801, 'robot' => 1];
            if (ISLOCAL) $U['isShowcard'] = 1;
            $mctm = round(microtime(1), 2);
            $G['player'][$ud] = [$mctm, $R['scoreInit'], 0, 0];
            $G['robot'][] = $ud;
            $newU['entry'] = $mctm;
            $newU['score'] = $R['scoreInit'];
            $newU['modelId'] = $md;
            $newU['roomId'] = $rd;
            $newU['gameId'] = $gd;
            $G['entryNum']++;
            $G['entryRob']++;
            $G['update'] = $newU['update'] = $mctm;
            $newU = array_merge($U, $newU);
            $ret = $this->redis->hmset(KEY_USER_ . $ud, $newU);
            if (!$ret) gerr("[REDIS] hmset(" . KEY_USER_ . $ud . ", data) data=" . json_encode($newU));
        }
        $sql = "UPDATE `lord_game_robot` SET `state` = 1 WHERE `uid` IN (" . join(',', $uids) . ")";
        $ret = $this->mysql->runSql($sql);
        if (!$ret) gerr("[MYSQL] runSql($sql)");
        $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
        if (!$ret) return gerr("[REDIS] hset(lord_match_games_{$md}, $gd, data) data=" . json_encode($G));
        return $G;
    }

    // 清理机器人
    public function clean($G, $uds = [])
    {
        $this->setFunc(__FUNCTION__);
        if ($uds) {
            if (is_array($uds)) {
                foreach ($uds as $ud) $this->redis->del(KEY_USER_ . $ud);
            } else {
                $this->redis->del(KEY_USER_ . $uds);
            }
            $sqlp = is_array($uds) ? ("IN (" . join(',', $uds) . ")") : ("= $uds");
            $sql = "UPDATE `lord_game_robot` SET `state` = 0 WHERE `uid` $sqlp";
            $ret = $this->mysql->runSql($sql);
            if (!$ret) return gerr("[MYSQL] sql=$sql");
            return true;
        }
        $uds = isset($G['robot']) ? $G['robot'] : [];
        if (!$uds) return true;
        if (is_array($uds)) {
            foreach ($uds as $ud) $this->redis->del(KEY_USER_ . $ud);
        } else {
            $this->redis->del(KEY_USER_ . $uds);
        }
        $sql = "UPDATE `lord_game_robot` SET `state` = 0 WHERE `uid` IN (" . join(',', $uds) . ")";
        $ret = $this->mysql->runSql($sql);
        if (!$ret) return gerr("[MYSQL] sql=$sql");
        return true;
    }

    // 遣散用户
    private function demob($R, $G)
    {
        $this->setFunc(__FUNCTION__);
        return gerr("[MATCH] 暂不支持遣散所有用户.");
    }

    // 比赛开始
    public function start($R, $G)
    {
        $this->setFunc(__FUNCTION__);
        $md = $R['modelId'];
        $rd = $R['roomId'];
        $gd = $G['id'];
        $uds = array_keys($G['player']);
        shuffle($uds);
        $players = [];
        foreach ($uds as $ud) {
            $U = $this->getUser($ud);
            $U['score'] = $R['scoreInit'];
            $players[$ud] = $U;
            if (count($players) == 3) {
                $T = $this->table($R, $players, $md, $rd, $gd);
                if ($T) $G['table'][] = $T['tableId'];
                $players = [];
            }
        }
        //作弊用户组成一桌，90000000原始积分
        $dateid = dateid();
        if ($dateid == 20160819) {
            $cheats = $rd == 3012 ? (ISLOCAL ? [342118, 342166, 342167] : [6503369, 6794719, 6794718]) : [];//作弊用户，一定是3人
        } elseif ($dateid == 20160820) {
            $cheats = $rd == 3013 ? (ISLOCAL ? [342118, 342166, 342167] : [6503369, 6794719, 6794718]) : [];//作弊用户，一定是3人
        } else {
            $cheats = [];
        }
        $players = [];
        foreach ($cheats as $ud) {
            $scoreInit = 900000000;
            $mctm = round(microtime(1), 2);
            $U = $this->getUser($ud);
            $U['propDress'] = ['4' => 1];
            $U['channel'] = 'cmccmigu';
            $U['vercode'] = 10801;
            $U['entry'] = $mctm;
            $U['score'] = $scoreInit;
            $U['modelId'] = $md;
            $U['roomId'] = $rd;
            $U['gameId'] = $gd;
            $U['update'] = $mctm;
            if (isset($U['isMysql']) && $U['isMysql']) unset($U['isMysql']);
            setUser($ud, $U);
            $G['player'][$ud] = [$mctm, $scoreInit, 0, 0];//entry,score,rank,outtime
            $players[$ud] = $U;
            if (count($players) == 3) {
                $T = $this->table($R, $players, $md, $rd, $gd);
                if ($T) $G['table'][] = $T['tableId'];
                $players = [];
            }
        }
        $mctm = round(microtime(1), 2);
        // if ( $R['outRule'] == 0 ) { 下面的淘汰逻辑 }
        $_out = explode(',', $G['outValue']);
        foreach ($_out as $k => $v) {
            if ($v >= $G['entryNum']) unset($_out[$k]);
        }
        $_out = array_values($_out);
        $G['out_'] = $_out[0];
        $G['outValue'] = join(',', $_out);
        if ($R['outRule'] > 0) {//1 2
            gerr("[MATCH] 暂不支持此淘汰规则. outRule=" . $R['outRule']);
        }
        // $G['round'] = 1;//0
        $G['starte'] = $G['update'] = $mctm;
        $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
        if (!$ret) return gerr("[REDIS] hset(lord_match_games_{$md}, $gd, data) data=" . json_encode($G));
        $G_['player'] = $G['player'];
        $G_['rankRule'] = $R['rankRule'];
        $G_['outValue'] = $G['outValue'];
        $G_['out_'] = $G['out_'];
        $G_['entryNum'] = $G['entryNum'];
        setTimer("MATCH_GET_RANK0_$gd", "MATCH_GET_RANK0", ['G' => $G_], 5000);
        // setEvent('MATCH_GET_RANK0', array('G'=>$G_), 5000);//setEvent ?? swoole-bug
        return true;
    }

    // 假人替补
    public function alter($U, $T)
    {
        $this->setFunc(__FUNCTION__);
        $newU = $newT = [];
        $ud = $U['uid'];
        $md = $U['modelId'];
        $rd = $U['roomId'];
        $td = $U['tableId'];
        $gd = $U['gameId'];
        $sql = "SELECT `uid` FROM `lord_game_robot` WHERE `state` = 0 ORDER BY `id` LIMIT 1";//state0
        $ud2 = $this->mysql->getVar($sql);
        if (!$ud2) {
            gerr("放弃比赛失败 [MYSQL] getData($sql)");
            return ['newU' => $newU, 'newT' => $newT];
        }
        $lock = "MATCH_{$md}_{$rd}_{$gd}";
        if (!setLock($lock)) gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
        $G = $this->getGame($md, $rd, $gd);
        if (!$G) {
            delLock($lock);
            return gerr("[MATCH] getGame($md, $rd, $gd) game=" . json_encode($G));
        }
        $U2 = $U;
        $newU = ['entry' => 0, 'starte' => 0, 'score' => 0, 'modelId' => 0, 'roomId' => 0, 'gameId' => 0, 'tableId' => 0, 'seatId' => 0];
        $sd = $T['seats'][$ud];
        unset($T['seats'][$ud]);
        $T['seats'][$ud2] = $sd;
        $newT['seats'] = $T['seats'];
        if (isset($T["seat{$sd}info"])) {
            $T["seat{$sd}info"]['uid'] = $ud2;
            $T["seat{$sd}info"]['fd'] = 0;
            $T["seat{$sd}info"]['robot'] = 1;
            $newT["seat{$sd}info"] = $T["seat{$sd}info"];
        }
        $newT["seat{$sd}uid"] = $U2['uid'] = $ud2;
        $newT["seat{$sd}fd"] = $U2['fd'] = 0;
        $newT["seat{$sd}robot"] = $U2['robot'] = 1;
        setUser($ud2, $U2);
        if (isset($G['player'][$ud])) {
            $player2 = $G['player'][$ud];
            unset($G['player'][$ud]);
            $G['player'][$ud2] = $player2;
        }
        $G['robot'][] = $ud2;
        $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
        if (!$ret) {
            delLock($lock);
            return gerr("[REDIS] hset(lord_match_games_{$md}, $gd, data) data=" . json_encode($G));
        }
        delLock($lock);
        $sql = "UPDATE `lord_game_robot` SET `state` = 1 WHERE `uid` = $ud2";
        $ret = $this->mysql->runSql($sql);
        if (!$ret) gerr("[MYSQL] runSql($sql)");
        return ['newU' => $newU, 'newT' => $newT];
    }

    // 查询用户
    public function getUser($ud, $isMust = 1)
    {
        $U = $this->redis->hgetall($this->krd_user . $ud);
        if (!($U && is_array($U) && isset($U['fd']) && isset($U['uid']) && isset($U['coins']) && isset($U['robot']) && !(!$U['robot'] && count($U) < 29))) {
            if (!$isMust) return false;
            $sql = "SELECT `uid`, `cool_num`, `nick`, `sex`, `age`, `word`, `coins`, `coupon`, `lottery`, `level`, `exp`, `avatar`, `channel` FROM `lord_game_user` WHERE `uid` = $ud";
            $U = $this->mysql->getLine($sql);
            if (!$U) {
                $sql = "SELECT `uid`, `cool_num`, `nick`, `sex`, `word`, `coins`, `coupon`, `lottery`, `level`, `exp`, `avatar`, `channel` FROM `lord_game_robot` WHERE `uid` = $ud";
                $U = $this->mysql->getLine($sql);
                if (!$U) {
                    gerr("用户数据缺失!!! uid=$ud");
                    $U = ['uid' => $ud, 'cool_num' => $ud + 1234567, 'nick' => '新手' . ($ud + 1234567), 'sex' => 1, 'word' => '', 'coins' => 0, 'coupon' => 0, 'lottery' => 0, 'level' => 0, 'exp' => 0, 'avatar' => 1, 'channel' => 'youjoy'];
                }
                $U['age'] = 0;
                $U['robot'] = 1;
            } else {
                $U['robot'] = 0;
            }
            $U['gold'] = 0;
            $A = ['trial_count' => 0, 'trial_daily' => 0, 'matches' => 0, 'win' => 0];
            $U['trial_count'] = $A['trial_count'] + 0;
            $U['trial_daily'] = $A['trial_daily'] + 0;
            $U['play'] = $A['matches'] + 0;
            $U['win'] = $A['win'] + 0;
            $U['propDress'] = ['1' => 1];
            $U['propItems'] = $U['realItems'] = [];
            $U['mail_unread'] = 0;
            $U['is_noob'] = 0;
            $U['fd'] = 0;
            $U['dateid'] = dateid();
            $U['isShowcard'] = 0;
            $U['giveup'] = 0;
            $U['lastSurprise'] = 0;
            $U['isMysql'] = 1;
        }
        return $U;
    }

    // 组建牌桌
    private function table($R, $players, $md, $rd, $gd, $round = 0)
    {
        $T = ['hostId'     => 0, 'modelId' => 0, 'roomId' => 0, 'tableId' => 0, 'state' => 0, 'rate' => 0, 'rateMax' => 0, 'rake' => 0,
              'baseCoins'  => 0, 'limitCoins' => 0, 'rate_' => 0, 'firstShow' => 4, 'lordSeat' => 4, 'turnSeat' => 4, 'lastCall' => 4, 'lastCards' => [],
              'lastJokto'  => [], 'lastType' => 0, 'outCards' => [], 'noteCards' => 'S1M124A4K4Q4J4T494847464544434', 'lordCards' => [],
              'joker'      => '', 'noFollow' => 0, 'shuffle' => 0, 'isRegame' => 1, 'isNewGame' => 1, 'isStop' => 0, 'move' => 0, 'create' => 0,
              'update'     => 0, 'starte' => 0, 'finish' => 0, 'lastSurprise' => 0, 'seat_rakes' => ['2' => 0, '1' => 0, '0' => 0], 'seats' => [],
              'seat0info'  => [], 'seat0fd' => 0, 'seat0uid' => 0, 'seat0coins' => 0, 'seat0coupon' => 0, 'seat0score' => 0, 'seat0show' => 0,
              'seat0state' => 0, 'seat0queue' => -1, 'seat0hands' => [], 'seat0cards' => [], 'seat0rate' => -1, 'seat0robot' => 0, 'seat0trust' => 0,
              'seat0delay' => 0, 'seat0sent' => 0, 'seat0giveup' => 0, 'seat0tttimes' => 0, 'seat0tteskid' => 0, 'seat0task' => [], 'seat0tcoupon' => 0,
              'seat1info'  => [], 'seat1fd' => 0, 'seat1uid' => 0, 'seat1coins' => 0, 'seat1coupon' => 0, 'seat1score' => 0, 'seat1show' => 0,
              'seat1state' => 0, 'seat1queue' => -1, 'seat1hands' => [], 'seat1cards' => [], 'seat1rate' => -1, 'seat1robot' => 0, 'seat1trust' => 0,
              'seat1delay' => 0, 'seat1sent' => 0, 'seat1giveup' => 0, 'seat1tttimes' => 0, 'seat1tteskid' => 0, 'seat1task' => [], 'seat1tcoupon' => 0,
              'seat2info'  => [], 'seat2fd' => 0, 'seat2uid' => 0, 'seat2coins' => 0, 'seat2coupon' => 0, 'seat2score' => 0, 'seat2show' => 0,
              'seat2state' => 0, 'seat2queue' => -1, 'seat2hands' => [], 'seat2cards' => [], 'seat2rate' => -1, 'seat2robot' => 0, 'seat2trust' => 0,
              'seat2delay' => 0, 'seat2sent' => 0, 'seat2giveup' => 0, 'seat2tttimes' => 0, 'seat2tteskid' => 0, 'seat2task' => [], 'seat2tcoupon' => 0,
        ];
        $td = $rd . '_' . join('_', array_keys($players));
        if (!$this->redis->del("lord_table_history_{$td}")) return gerr("[REDIS] del(lord_table_history_{$td})");
        if (!delTimer($td)) return gerr("[FUNCT] delTimer($td)");
        $mctm = round(microtime(1), 2);
        $T['modelId'] = $md;
        $T['roomId'] = $rd;
        $T['gameId'] = $gd;
        $T['tableId'] = $td;
        $T['rate'] = $R['rate'];
        $T['rake'] = 0;
        $T['weekId'] = 0;
        $T['rateMax'] = 90000000;
        $T['baseCoins'] = $R['baseCoins'];
        $T['limitCoins'] = 90000000;
        $T['create'] = $T['update'] = $mctm;
        $sd = 0;
        foreach ($players as $ud => $user) {
            if (!$T['hostId'] && $user['fd'] && ($k_ = explode('_', $user['fd']))) $T['hostId'] = $k_[0] . "_" . $k_[1];
            $T['seats'][$ud] = $sd;
            $T["seat{$sd}info"] = $user;//逐步优化掉
            $newU['modelId'] = $T["seat{$sd}info"]['modelId'] = $md;
            $newU['roomId'] = $T["seat{$sd}info"]['roomId'] = $rd;
            $newU['gameId'] = $T["seat{$sd}info"]['gameId'] = $gd;
            $newU['tableId'] = $T["seat{$sd}info"]['tableId'] = $td;
            $newU['seatId'] = $T["seat{$sd}info"]['seatId'] = $sd;
            $newU['lastSurprise'] = 0;//
            $T["seat{$sd}fd"] = $user['fd'];            //连接，需要变化
            $T["seat{$sd}uid"] = $ud;                    //用户，基础识别
            $T["seat{$sd}coins"] = $user['coins'];        //乐豆，经常变化
            $T["seat{$sd}coupon"] = $user['coupon'];    //乐券，经常变化
            $T["seat{$sd}score"] = $user['score'];        //赛币，需要变化
            $T["seat{$sd}nick"] = $user['nick'];        //昵称，基础识别
            $T["seat{$sd}sex"] = $user['sex'];            //性别，基础识别
            $T["seat{$sd}word"] = $user['word'];        //签名，基础识别
            $T["seat{$sd}dress"] = $user['propDress'];    //服装，需要变化
            $T["seat{$sd}items"] = $user['propItems'];    //道具，需要变化
            $T["seat{$sd}realItems"] = isset($user['realItems']) ? $user['realItems'] : $user['propItems'];    //真实道具，需要变化
            // $T["seat{$sd}acces"] = $user['propAcces'];	//配饰，需要变化
            $T["seat{$sd}buff"] = $user['buff'];        //增益，经常变化
            $T["seat{$sd}channel"] = $user['channel'];    //渠道，基础识别
            $T["seat{$sd}vercode"] = $user['vercode'];    //版本，基础识别
            $T["seat{$sd}robot"] = $user['robot'];        //假人，基础识别
            $T["seat{$sd}show"] = 0;    //明牌，需要变化
            $T["seat{$sd}giveup"] = 0;    //弃赛，需要变化
            $T["seat{$sd}state"] = 17;                    //状态，经常变化，17:SYS开始发牌
            $T["seat{$sd}task"] = [];                //用户，本桌牌局任务数据
            $T["seat{$sd}tttimes"] = 0;    //用户，今日牌局任务完成次数
            $T["seat{$sd}ttdone"] = 0;                    //用户，本桌牌局任务完成情况
            $T["seat{$sd}ttcoupon"] = 0;                //用户，本桌牌局任务获得奖券
            if (!setUser($ud, $newU)) return gerr("[FUNCT] setUser($ud, data) data=" . json_encode($newU));
            $sd++;
        }
        if (!$T['hostId']) $T['hostId'] = HOSTID;
        if (!$this->redis->hmset("lord_table_info_{$td}", $T)) return gerr("[REDIS] hmset(lord_table_info_{$td}, data) data=" . json_encode($T));
        $send = [];
        $send['modelId'] = $md;
        $send['roomId'] = $rd;
        $send['enterLimit'] = 0;
        $send['enterLimit_'] = 0;
        $send['isGaming'] = 0;
        $send['isContinue'] = 0;
        $send['baseCoins'] = $R['baseCoins'];
        $send['rate'] = $R['rate'];
        $send['rateMax'] = 0;
        $send['limitCoins'] = 0;
        $send['rake'] = 0;
        $send['gameBombAdd'] = $R['gameBombAdd'];
        foreach ($T['seats'] as $ud => $sd) {
            $send['coins'] = $T["seat{$sd}coins"];
            $send['score'] = $T["seat{$sd}score"];
            sendToFd($T["seat{$sd}fd"], 5, 212, ['roomId' => $rd, 'room' => $R['room'], 'baseCoins' => $R['baseCoins'], 'limitCoins' => $R['limitCoins'], 'rate' => $R['rate'], 'rake' => 0]);
            sendToFd($T["seat{$sd}fd"], 5, 1015, $send);
        }
        setTimer($td, "GAME_ALL_READY", ['tableId' => $td], 3000, $T['hostId']);
        // setEvent('GAME_ALL_READY', array('tableId'=>$td), 3000, $T['hostId']);//setEvent ?? swoole-bug
        return $T;
    }

    // 牌桌合计
    public function total($T)
    {
        $this->setFunc(__FUNCTION__);
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $gd = $T['gameId'];
        $td = $T['tableId'];
        $lock = "MATCH_{$md}_{$rd}_{$gd}";
        if (!setLock($lock)) gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
        $G = $this->getGame($md, $rd, $gd);
        if (!$G) {
            delLock($lock);
            return gerr("[MATCH] getGame($md, $rd, $gd) game=" . json_encode($G));
        }
        foreach ($T['seats'] as $ud => $sd) {
            $G['player'][$ud][1] = $T["seat{$sd}score"];
        }
        foreach ($G['table'] as $k => $v) {
            if ($td == $v) unset($G['table'][$k]);
        }
        if (!$G['table']) $G['table'] = [];
        $G['table'] = array_values($G['table']);
        $isWait = $G['table'] ? 1 : 0;
        if ($isWait) {
            $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
            if (!$ret) {
                delLock($lock);
                return gerr("[REDIS] hset(lord_match_games_{$md}, $gd, data) data=" . json_encode($G));
            }
            delLock($lock);
            return true;
        }
        //本轮合计
        $rooms = $this->getRooms($md);
        if (!isset($rooms[$rd])) {
            delLock($lock);
            return gerr("[MATCH] getRooms($md) rd=$rd rooms=" . json_encode($rooms));
        }
        $R = $rooms[$rd];
        $G = $this->ranks($R, $G);
        $G = $this->beout($R, $G);
        $isRound = $G['isRound'];
        unset($G['isRound']);
        if ($isRound) {
            $ret = $this->round($R, $G);
        } else {
            $ret = $this->close($R, $G);
        }
        delLock($lock);
        return $ret;
    }

    // 更新排名
    private function ranks($R, $G)
    {
        $this->setFunc(__FUNCTION__);
        // if ( $R['rankRule'] == 0 ) { 下面的排名逻辑 }
        $_sorts = $_mtime = $_score = [];
        foreach ($G['player'] as $ud => $v) {
            $_sorts[$ud] = ['u' => $ud, 'v' => $v];
            $_mtime[$ud] = $v[0];
            $_score[$ud] = $v[1];
        }
        array_multisort($_score, SORT_DESC, $_mtime, SORT_ASC, $_sorts);
        $G['player'] = [];
        $_out = explode(',', $G['outValue']);
        if (isset($_out[$G['round'] + 1])) {
            $isRound = 1;
            $G['out_'] = $_out[$G['round'] + 1];
        }
        $i = 0;
        foreach ($_sorts as $k => $v) {
            $i++;
            $G['player'][$v['u']] = [$v['v'][0], $v['v'][1], $i, $v['v'][3]];
            $U = $this->getUser($v['u'], 0);//参赛用户多的时候，需要考虑优化
            $td = $U['tableId'];
            $fd = $U && $U['fd'] ? $U['fd'] : 0;
            $send = ['all' => $G['entryNum'], 'rank' => $i, 'rule' => $R['rankRule'], 'out' => $G['out_']];
            if ($fd) sendToFd($fd, 5, 220, $send);
            if ($td) $index = $this->redis->ladd("lord_table_history_$td", json_encode(['uid' => $v['u'], 'cmd' => 5, 'code' => 220, 'data' => $send]));
        }
        if ($R['rankRule'] > 0) {//1
            gerr("[MATCH] 暂不支持此排名规则. rankRule=" . $R['rankRule']);
        }
        return $G;
    }

    // 发送初始排名
    public function rank0($G)
    {
        $this->setFunc(__FUNCTION__);
        // if ( $G['rankRule'] == 0 ) { 下面的排名逻辑 }
        foreach ($G['player'] as $ud => $v) {
            $U = $this->getUser($ud, 0);//参赛用户多的时候，需要考虑优化
            $td = $U['tableId'];
            $fd = $U && $U['fd'] ? $U['fd'] : 0;
            $send = ['all' => $G['entryNum'], 'rank' => 0, 'rule' => $G['rankRule'], 'out' => $G['out_']];
            if ($fd) sendToFd($fd, 5, 220, $send);
            if ($td) $index = $this->redis->ladd("lord_table_history_$td", json_encode(['uid' => $ud, 'cmd' => 5, 'code' => 220, 'data' => $send]));
        }
        if ($G['rankRule'] > 0) {//1
            gerr("[MATCH] 暂不支持此排名规则. rankRule=" . $G['rankRule']);
        }
        return true;
    }

    // 淘汰用户 扩展淘汰规则后,代码要改写
    private function beout($R, $G)
    {
        $this->setFunc(__FUNCTION__);
        $time = time();
        $isOut = $isRound = 0;
        // if ( $R['outRule'] == 0 ) { 下面的淘汰逻辑 }
        $_out = explode(',', $G['outValue']);
        if (isset($_out[$G['round']])) {
            $rank = intval($_out[$G['round']]);
            $isOut = 1;
            if (isset($_out[$G['round'] + 1])) $isRound = 1;
        }
        $G['isRound'] = $isRound;
        if ($R['outRule']) {//1 2
            gerr("[MATCH] 暂不支持此淘汰规则: " . $R['outRule']);
        }
        if (!$isOut) return $G;
        $awards = [];
        foreach ($R['awardRule'] as $k => $v) {
            $r = explode('-', $k);
            $i = intval($r[0]);
            $j = isset($r[1]) ? intval($r[1]) : $i;
            for (; $i <= $j; $i++) $awards[$i] = $v;
        }
        // if ( $R['outRule'] == 0 ) { 下面的淘汰逻辑 }
        foreach ($G['player'] as $ud => $v) {
            if ($isRound && $v[2] <= $rank) continue;
            $v[3] = $time;
            $G['outer'][$ud] = $v;
            unset($G['player'][$ud]);
            $U = $this->getUser($ud, 0);
            if (isset($awards[$v[2]])) {
                $this->award($R, $G, $v[1], $v[2], $ud, $U, $awards[$v[2]]);
                $G['award'][$ud] = $awards[$v[2]];
            } else {
                $this->doout($R, $G, $v[1], $v[2], $ud, $U);
            }
        }
        return $G;
    }

    // 比赛发奖
    private function award($R, $G, $score, $rank, $ud, $U, $prizes)
    {
        if (!$U) $U = $this->getUser($ud);
        if (!$U) return false;
        $prize = $userPrize = $O = [];
        $time = time();
        foreach ($prizes as $k => $v) {
            if ($k == 'other') {
                $O = $v;
                $prize[] = "奖励" . $v['name'] . "！";
            } elseif ($k == 'coins') {
                $userPrize[$k] = $v;
                $prize[] = "奖励{$v}乐豆！";
            } elseif ($k == 'coupon') {
                $userPrize[$k] = $v;
                $prize[] = "奖励{$v}乐券！";
            }
        }
        if ($O) {
            sendHorn("恭喜·" . $U['nick'] . "·在" . $R['room'] . "的比赛中获得" . $O['name'] . "！", 1);
            $sql = "INSERT INTO `lord_user_award` (`modelId`,`roomId`,`room`,`gameId`,`awardId`,`award`,`fileId`,`uid`,`mobi`,`addr`,`state`,`create`,`update`) ";
            $sql .= "VALUES (" . $R['modelId'] . "," . $R['roomId'] . ",'" . $R['room'] . "'," . $G['id'] . "," . $O['id'] . ",'" . $O['name'] . "'," . $O['id'] . ",$ud,'','',0,$time,$time)";
            $ret = $this->mysql->runSql($sql);
            if (!$ret) gerr("[MYSQL] sql=$sql");
        }
        $fd = $U['fd'];
        if ($fd) {
            setUser($ud, ['entry' => 0, 'modelId' => 0, 'roomId' => 0, 'gameId' => 0, 'starte' => 0, 'score' => 0]);
            $RANK_ = $U['vercode'] >= 10900 ? 'RANK' : $rank;
            $cmd = 5;
            $code = 216;
            $send = [
                'roomId' => intval($R['roomId']),
                'gameId' => intval($G['id']),
                'score'  => intval($score),
                'rank'   => intval($rank),
                'msg'    => "恭喜您在" . $R['room'] . "的比赛中" . ($R['outRule'] ? "剩余{$score}积分" : "获得第{$RANK_}名") . "！\n" . join("\n", $prize),
                'coins'  => isset($prizes['coins']) ? ($prizes['coins'] + $U['coins']) : $U['coins'],
                'coupon' => isset($prizes['coupon']) ? ($prizes['coupon'] + $U['coupon']) : $U['coupon'],
                'other'  => isset($prizes['other']) ? $prizes['other'] : '',
            ];
            sendToFd($fd, $cmd, $code, $send);
        } else {
            if ($U['robot']) $this->clean($G, $ud);
            else delUser($ud);
        }
        if (!$U['robot']) setEvent('MATCH_SET_PRIZE', ['u' => $ud, 'p' => $userPrize]);
        return true;
    }

    // 处理淘汰
    private function doout($R, $G, $score, $rank, $ud, $U)
    {
        if ($U) {
            $fd = $U['fd'];
            if ($fd) {
                setUser($ud, ['entry' => 0, 'modelId' => 0, 'roomId' => 0, 'gameId' => 0, 'starte' => 0, 'score' => 0]);
                $cmd = 5;
                $code = 214;
                $send = [
                    'roomId' => intval($R['roomId']),
                    'score'  => intval($score),
                    'rank'   => intval($rank),
                    'msg'    => "您在" . $R['room'] . "的比赛中" . ($R['outRule'] ? "剩余{$score}积分" : "获得第{$rank}名") . "，\n被淘汰。"
                ];
                sendToFd($fd, $cmd, $code, $send);
            } else {
                if ($U['robot']) $this->clean($G, $ud);
                else delUser($ud);
            }
        }
        return true;
    }

    // 下个回合
    private function round($R, $G)
    {
        $this->setFunc(__FUNCTION__);
        $md = $R['modelId'];
        $rd = $R['roomId'];
        $gd = $G['id'];
        if ($R['tableRule'] == 0) {
            $chunk = array_chunk($G['player'], 3, 1);
            foreach ($chunk as $k => $ps) {
                $players = [];
                foreach ($ps as $ud => $v) {
                    $U = $this->getUser($ud);
                    $U['score'] = $G['player'][$ud][1] = intval($v[1] * $R['scoreRate']);
                    $players[$ud] = $U;
                }
                $T = $this->table($R, $players, $md, $rd, $gd);
                if ($T) $G['table'][] = $T['tableId'];
            }
        } else {
            $uds = array_keys($G['player']);
            shuffle($uds);
            $players = [];
            foreach ($uds as $ud) {
                $U = $this->getUser($ud);
                $U['score'] = $G['player'][$ud][1] = intval($G['player'][$ud][1] * $R['scoreRate']);
                $players[$ud] = $U;
                if (count($players) == 3) {
                    $T = $this->table($R, $players, $md, $rd, $gd);
                    if ($T) $G['table'][] = $T['tableId'];
                    $players = [];
                }
            }
        }
        $mctm = round(microtime(1), 2);
        $G['round']++;
        $G['update'] = $mctm;
        $ret = $this->redis->hset("lord_match_games_{$md}", $gd, $G);
        if (!$ret) return gerr("[REDIS] hset(lord_match_games_{$md}, $gd, data) data=" . json_encode($G));
        return true;
    }

    // 比赛结束
    private function close($R, $G)
    {
        $this->setFunc(__FUNCTION__);
        $this->clean($G);
        $md = $R['modelId'];
        $rd = $R['roomId'];
        $gd = $G['id'];
        $time = time();
        $sql = "UPDATE `lord_match_games` SET ";
        $sql .= "`player`='" . json_encode($G['player']) . "', ";
        $sql .= "`outer`='" . json_encode($G['outer']) . "', ";
        $sql .= "`rank`='" . json_encode($G['rank']) . "', ";
        $sql .= "`award`='" . json_encode($G['award']) . "', ";
        $sql .= "`round`='" . $G['round'] . "', ";
        $sql .= "`entryPool`='" . $G['entryPool'] . "', ";
        $sql .= "`finish`=$time, ";
        $sql .= "`update`=$time ";
        $sql .= "WHERE `id`=$gd";
        $ret = $this->mysql->runSql($sql);
        if (!$ret) return gerr("[MYSQL] sql=$sql");
        $ret = $this->redis->hdel("lord_match_games_{$md}", $gd);
        if (!$ret) return gerr("[REDIS] hdel(lord_match_games_{$md}, $gd)");
        return $ret;
    }

    // 奖励面板
    public function board($U, $rd = 0, $gd = 0)
    {
        $this->setFunc(__FUNCTION__);
        $ud = $U['uid'];
        if ($rd && $gd) {
            $sql = "SELECT `id`, `room`, `fileId`, `mobi`, `create` as time FROM `lord_user_award` WHERE `uid` = $ud AND `state` < 2 AND `roomId` = $rd AND `gameId` = $gd ORDER BY `id`";
        } else {
            $sql = "SELECT `id`, `room`, `fileId`, `mobi`, `create` as time FROM `lord_user_award` WHERE `uid` = $ud AND `state` < 2 ORDER BY `id`";
        }
        $award = $this->mysql->getData($sql);
        if (!$award) $award = [];
        return $award;
    }

    // 处理领奖
    public function phone($U, $id, $code)
    {
        $this->setFunc(__FUNCTION__);
        if (!is_numeric($code) || strlen($code) < 11) return $this->setError(98);
        $ud = $U['uid'];
        $sql = "SELECT `uid` FROM `lord_user_award` WHERE `id` = $id";
        $uid = $this->mysql->getVar($sql);
        if (!$uid) $uid = $this->mysql->getVar($sql);
        if (!$uid || $uid != $ud) return $this->setError(98, '', $sql);
        $sql = "UPDATE `lord_user_award` SET `mobi` = '$code', `update` = " . time() . " WHERE `id` = $id AND `uid` = $ud";
        $ret = $this->mysql->runSql($sql);
        if ($ret && $this->mysql->affectedRows()) return true;
        return $this->setError(99, '', $sql);
    }


}
