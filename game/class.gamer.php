<?php

// $fd : $fd         //长连接
// $F  : $fdinfo     //连接数据
// $t  : $open_type  //登录类型
// $d  : $device     //设备编号
// $c  : $channel 	 //用户渠道
// $e  : $extend  	 //设备扩展
// $vs : $version    //字串版本号1.1.0
// $vc : $vercode    //数字版本号10100
// $ip : $ipaddress  //IP地址
// $ch : $channel 	 //用户渠道
// $ud : $uid        //用户ID
// $U  : $user       //用户数据
// $hd : $hostId     //伺服ID
// $md : $modelId    //模式ID
// $rd : $roomId     //房间ID
// $td : $TId    //牌桌ID
// $gd : $gameId     //赛场ID
// $mrwg : $gamesId  //赛场IDx
// $T  : $T      //牌桌数据
// $sd : $seatId     //席位ID
// $gd : $goodsId    //商品ID
// $G  : $goods      //商品数据
// $cd : $categoryId //分类ID(商品|道具|其他等的分类)
// $pd : $propId     //道具ID
// $P  : $prop       //道具数据
// $wd : $weekId     //星期ID intval(date("Ymd"))
// $dd : $dateid     //日期ID intval(date("Ymd"))
require_once(ROOT . "/class.model.php");
require_once(ROOT . "/class.task.php");
require_once(ROOT . "/class.tesk.php");
require_once(ROOT . "/class.card.php");
require_once(ROOT . "/class.prop.php");
require_once(ROOT . "/class.match.php");
require_once(ROOT . "/class.record.php");
require_once(ROOT . "/class.fruit.php");

if (ISLOCAL) {
    require_once(ROOT . "/class.cow.php");
}

class gamer
{
    public $is_freshtask = 0;
    public $god_uids = array();
    
    function __construct($redis = null, $mysql = null)
    {
        $this->redis = $redis;
        $this->mysql = $mysql;
        $this->model = new model($redis, $mysql);
        $this->record = $this->model->getRecord();
        $this->match = match::inst($this->redis, $this->mysql);
        $this->confs = include(ROOT . '/conf/confs.php');
        $this->rooms = include(ROOT . '/conf/rooms.php');
        $ext = $this->model->listGetRoom();
        foreach ($ext as $k => $v) {
            $this->rooms[$v['roomId']] = $v;
        }


        $this->fruitMachine = new DDZFruitMachine();
        $this->fruitMachine->fill_ratio = 0.001;
        $this->fruitMachine->setGamer($this);
        $this->fruitMachine->setConfig();

        $this->cowMachine = new DDZCowMachine();
        $this->cowMachine->setGamer($this)->setRedis($redis);
    }

    function __destruct()
    {
        $this->model = null;
        $this->redis = null;
        $this->mysql = null;
        return true;
    }

    
    //执行注册
    function runReg($fd, $cmd, $code, $params)
    {
        $type = $params['type'];
        $open_id = $params['d'] = isset($params['d']) ? $params['d'] : '';    //设备 原始设备号md5( $uuid . $sign )
        $extend = $params['e'] = isset($params['e']) ? $params['e'] : '';    //设备 扩展设备号，用做串号校验
        $open_type = $params['sdkid'] = isset($params['sdkid']) ? $params['sdkid'] : 'device';    //sdkid
        if ( in_array($open_type, array('','0','name')) )$params['sdkid'] = $open_type = 'device';//强制矫正
        $version = $params['v'] = isset($params['v']) ? $params['v'] : '';    //版本
        $channel = $params['c'] = isset($params['c']) ? $params['c'] : '';    //渠道
        $ip = isset($params['ip']) ? $params['ip'] : '';                    //IP
        if($type == "guest")
        {
            $uuid = $params['u'] = isset($params['u']) ? $params['u'] : '';    //设备 原始设备号
            $password = strval(mt_rand(100000, 999999));//随机密码
        }
        elseif($type == "third")
        {
            $uuid = $params['a'] = isset($params['a']) ? $params['a'] : '';    //sdkid
            $password = $params['p'] = isset($params['p']) ? $params['p'] : '';    //密码
        }
        
        if ( $type == "guest" ) 
        {    //第三方使用不加密的uuid作为open_id并进入到游戏登陆
             $result = $this->model->regDev($uuid, $open_type, $open_id, $extend, $channel, $version, $password, $ip);
        }
        elseif($type == "third") 
        {
             $result = $this->model->reg3rd($uuid, $open_type, $uuid, $extend, $channel, $version, $password, $ip);
        }
        $result["data"]["type"] = $type;
        $result["data"]["password"] = $result["password"];
        unset($result["password"]);
        debug("玩家注册 ".json_encode($params));
        $res = sendToFd($fd, $cmd, $code, $result);
        
        return ;
    }
    
    //校验用户 返回 true|false
    //只在协议执行前判断用户的基础通用数据的可靠性。不可靠时底层断开用户连接，并终止执行。
    function runCheck($U)
    {
        return ($U && is_array($U) && isset($U['fd']) && isset($U['uid']) && isset($U['coins']) && isset($U['robot']) && !(!$U['robot'] && count($U) < 29));
    }

    //执行登录 返回 UID|false
    function runLogin($fd, $cmd, $code, $params)
    {
        $dd = intval(date("Ymd"));
        $date = date("Y-m-d");
        $datm = date("Y-m-d H:i:s");
        $time = time();
        //整理登录数据
        $d = $params['d'] = isset($params['d']) ? $params['d'] : '';    //设备 原始设备号md5( $uuid . $sign )
        $e = $params['e'] = isset($params['e']) ? $params['e'] : '';    //设备 扩展设备号，用做串号校验
        $f = $params['f'] = isset($params['f']) ? $params['f'] : "device";//设备 支付设备号，SDKUID不会串号
        $p = $params['p'] = isset($params['p']) ? $params['p'] : '';    //密码
        $v = $params['v'] = isset($params['v']) ? $params['v'] : '';    //版本
        $c = $params['c'] = isset($params['c']) ? $params['c'] : '';    //渠道
        $wn = $params['wn'] = isset($params['wn']) ? $params['wn'] : '';    //第三方昵称
        $i = isset($params['ip']) ? $params['ip'] : '';                    //IP
        $robot = isset($params['robot']) ? intval($params['robot'] && $c == 'robot') : 0;
        if ($robot) $c = 'robot';//外置机器人
        if (!$d || !$p) {
            $this->record->action(10000, 0, 0, 0, [], [], ['params' => $params]);
            return gerr("登录参数无效 F=$fd U=? V=$v I=$i C=$c P=" . json_encode($params));
        }
        //检测登录帐号
        $code = $this->model->userCheck($f, $d, $e, $p);
        if (is_numeric($code)) {
            sendToFd($fd, 1, $code, []);
            $this->record->action(10000, 0, 0, 0, [], [], ['params' => $params, 'ret' => $code]);
            return gerr("登录检查无效 F=$fd U=? V=$v I=$i C=$c P=" . json_encode($params));
        }
        $ud = intval($code['id']);
        $lockUU = 'USER_' . $ud;
        if (!setLock($lockUU)) gerr("[LOCKLL][LOGIN] F=$fd lock=$lockUU P=" . json_encode($params));
        //处理掉线信息
        $last = $this->model->getUserInfo($ud);
        if ($last) {
            //核对连接通道
            if ($last['fd']) {
                if ($last['fd'] != $fd) {
                    debug("登录账户冲突 F=$fd U=$ud V=$v I=$i C=$c O=" . $last['fd']);
                    if (!in_array($c, ['shiboyun', 'xiaomibox'])) {
                        sendToFd($last['fd'], 4, 902, ['errno' => 1, 'error' => "您的帐号在其他设备登录。\n若非本人操作，请退出后重新登录!", 'newdate' => $datm, 'olddate' => date("Y-m-d H:i:s", $last['Ltime']), 'uid' => $ud, 'ip' => $i, 'channel' => $c, 'version' => $v, 'newfd' => $fd]);
                    }
                    delBind($last['fd']);//释放旧连接
                    $last['fd'] = 0;
                } else {
                    $fdinfo = getBind($last['fd']);
                    if($fdinfo && isset($fdinfo['uid']) && $fdinfo['uid'] != $ud){
                        delBind($last['fd']);//释放旧连接
                    }
                    if ($fdinfo && isset($fdinfo['uid']) && $fdinfo['uid'] == $ud) {
                        debug("登录多次重复 F=$fd U=$ud V=$v I=$i C=$c D=$datm");
                        $this->record->action(10000, 0, 0, 0, [], [], ['params' => $params, 'ret' => 'repeats']);
                        delLock($lockUU);
                        return false;//退出运算，记录ERROR日志，不向客户端回馈
                    }
                }
            }
            //核对牌桌数据
            if ($last['tableId']) {
                $lock = $td = $last['tableId'];
                $res = setLock($lock);
                $T = $this->model->getTableInfo($td);
                if (!$T) {
                    debug("登录牌桌失效 F=$fd U=$ud V=$v I=$i C=$c T=$td");
                    $this->model->desUserInfo($ud, $last, __LINE__, 0, 1);//
                    $last = false;
                } else {
                    $newT['seat'.$last['seatId'].'fd'] = $fd;
                    $this->model->setTableInfo($td, $newT); 
                    unset($newT);
                }
                delLock($lock);
            }
            //核对参赛信息
            if ($last && $last['modelId'] && $last['gameId']) {
                $md = $last['modelId'];
                $gd = $last['gameId'];
                if ($md == 1) {
                    $rd = 0;//$last['roomId'];
                    $wd = $last['weekId'];
                    $mrwg = $last['gamesId'];
                    $lock = 'GAMESID_' . $mrwg;
                    $res = setLock($lock);
                    $game = $this->model->getModelGame($md, $rd, $wd, $gd);
                    if (!$game) {
                        debug("登录竞技结束 F=$fd U=$ud V=$v I=$i C=$c G=" . $last['gamesId']);
                        $this->model->desUserInfo($ud, $last, __LINE__, 0, 1);//
                        $last = false;
                    } elseif ($game['gameStart'] && ($time - $game['gameStart']) > 1200) {
                        gerr("登录竞技失效 F=$fd U=$ud V=$v I=$i C=$c G=" . json_encode($game));
                        $this->model->delModelGame($md, $mrwg);
                        $this->model->desUserInfo($ud, $last, __LINE__, 0, 1);//
                        $last = false;
                    }
                    delLock($lock);
                } else {
                    $rd = $last['roomId'];
                    $lock = "MATCH_{$md}_{$rd}_{$gd}";
                    if (!setLock($lock)) gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
                    $G = $this->match->getGame($md, $rd, $gd);
                    if (!$G) {
                        debug("登录赛事结束 F=$fd U=$ud V=$v I=$i C=$c G=$gd");
                        $this->model->desUserInfo($ud, $last, __LINE__, 0, 1);
                        $last = false;
                    }
                    delLock($lock);
                }
            }
        }
        //获取基础数据
        $code = $this->model->userLogin($ud, $d, $e, $v, $c, $i, $wn, $robot, $datm);
        if (is_numeric($code)) {
            sendToFd($fd, 1, $code, []);
            $this->record->action(10000, 0, 0, 0, [], [], ['params' => $params, 'ret' => $code]);
            delLock($lockUU);
            return gerr("登录运算错误 F=$fd U=$ud V=$v I=$i C=$c P=" . json_encode($params));
        }
        $ud = intval($code['uid']);
        $U['uid'] = $ud;
        $U['cool_num'] = strval($code['cool_num']);
        $U['nick'] = trim($code['nick']);
        $U['sex'] = max(1, intval($code['sex']));//默认为1
        $U['age'] = intval($code['age']);
        $U['word'] = trim($code['word']);
        $U['gold'] = intval($code['gold']);//向后兼容
        $U['coins'] = intval($code['coins']);
        $U['coupon'] = intval($code['coupon']);
        $U['lottery'] = intval($code['lottery']);
        $U['level'] = intval($code['level']);
        $U['exp'] = intval($code['exp']);
        $U['avatar'] = intval($code['avatar']);
        $U['check_code'] = strval($code['check_code']);
        $U['propDress'] = $this->model->getDbUserDress($ud);
        $U['propItems'] = $this->model->getDbUserItems($ud, 1);
        $U['realItems'] = $this->model->getDbUserItems($ud);
        if (isset($U['propItems']['8'])) unset($U['propItems']['8']);
        if (isset($U['realItems']['8'])) unset($U['realItems']['8']);
        $U['buff'] = $this->model->getDbUserBuff($ud);
        $U['mail_unread'] = $this->model->getMailNewNum($ud);
        $U['is_noob'] = $is_noob = intval($code['is_noob']);
        $A = $code['analyse'];
        $dayfirst = $code['first'];
        //合并用户信息
        if ($last) {
            $rd = $last['roomId'];
            $td = $last['tableId'];
            debug("用户重新登入 F=$fd U=$ud R=$rd T=$td V=$v I=$i C=$c");
            $info = array_merge($last, ['fd' => $fd]);
        } else {
            $rd = $td = 0;
            debug("用户全新登入 F=$fd U=$ud R=$rd T=$td V=$v I=$i C=$c");
            $info = array_merge($U, ['fd' => $fd]);
            $info['roomId'] = $info['tableId'] = $info['seatId'] = $info['modelId'] = $info['weekId'] = $info['gameId'] = $info['joinTime'] = $info['score'] = $info['giveup'] = $info['gameStart'] = 0;
            $info['gamesId'] = $info['gameplayId'] = '';
        }
        if ($info['coins'] < 0) {
            gerr("登入乐币负值 F=$fd U=$ud R=$rd T=$td V=$v I=$i C=$c coins=" . $info['coins']);
            $info['coins'] = 0;
            $sql = "UPDATE `lord_game_user` SET `coins` = 0 WHERE `uid` = $ud";
            $this->mysql->runSql($sql);
        }
        $info['dateid'] = $dd;
        $info['robot'] = 0;//1内置机器人
        $info['giveup'] = 0;//
        $info['isShowcard'] = 0;
        $info['lastSurprise'] = 0;
        $info['ip'] = $i;
        $info['channel'] = $c;
        $info['vercode'] = $vercode = intval(str_replace('.', '0', isset($v) ? trim($v) : "0"));
        $info['trial_count'] = $A['trial_count'] + 0;//兼容100版
        $info['trial_daily'] = $A['trial_daily'] + 0;//兼容100版
        $info['play'] = $info['Lplay'] = $A['matches'] + 0;
        $info['win'] = $info['Lwin'] = $A['win'] + 0;
        $info['Ltime'] = $time;
        $info['Lgold'] = $info['gold'] + 0;
        $info['Lcoins'] = $info['coins'] + 0;
        $info['Lcoupon'] = $info['coupon'] + 0;
        $info['Llottery'] = $info['lottery'] + 0;
        $info['login_got_coupon'] = 0; //用户登陆获取的乐券
        $info['lastRoomId'] = 0;  //用户最后进入房间
        $res = setUser($ud, $info);
        foreach ($U as $key => $val) {
            if (isset($info[$key])) $U[$key] = $info[$key];
        }
        unset($U['realItems']);
        //用户任务
        $newU = [];
        $Utask = $this->model->getUserTask($ud);
        $teski = $this->model->getlistTesk($info);
        $Utesk = $teski['usertesk'];
        $tesks = $teski['tesklist'];
        $task1_unaward = $task2_unaward = $task3_unaward = 0;
        foreach ($Utesk as $k => $val) {
            $k_ = explode('_', $k);
            if ($k_[0] == 'teskstate' && $val == 2 && isset($tesks[$k_[1]]['type'])) {
                ${"task" . ($tesks[$k_[1]]['type'] + 1) . "_unaward"}++;
            }
        }
        $newU['ttdateid'] = $Utesk['ttdateid'];
        $newU['tttimes'] = $Utesk['tttimes'];
        //发送消息 用户登录成功
        $cmd = 1;
        $code = 10;
        $data = $U;//只发送基础用户信息即可
        $data['isDev'] = ISLOCAL;
        $data['task1_unaward'] = $newU['task1_unaward'] = $info['task1_unaward'] = $task1_unaward;
        $data['task2_unaward'] = $newU['task2_unaward'] = $info['task2_unaward'] = $task2_unaward;
        $data['task3_unaward'] = $newU['task3_unaward'] = $info['task3_unaward'] = $task3_unaward;
        $data['checkin_undo'] = $newU['checkin_undo'] = $checkin_undo = intval(!$Utask['login_day5_got'] || $Utask['login_this_dateid'] != $dd);//今日的签到奖励可领取状态0不可领取1可领取
        $data['gameData']['matches'] = $info['play'];//下一版本用usertask里面的
        $data['gameData']['win'] = $info['win'];//下一版本用usertask里面的
        $data['n_reward_list'] = $this->model->getNRewardList($ud, $vercode);
        //首冲逻辑 修改下面配置逻辑，需要全项目搜索关键字“ 修改首冲逻辑 ”，防止漏掉。乐币到乐豆的每日首冲/每周首冲/用户首冲
        //keep $data['charge_rate'] = ! $Utask['gold_all'] ? 100 : (! $Utask['gold_week'] ? 20 : (! $Utask['gold_day'] ? 20 : 0));
        //keep $data['charge_rate'] = ! $Utask['gold_all'] ? 100 : (! $Utask['gold_day'] ? 20 : 0);
        //keep $data['charge_rate'] = strtotime('2016-04-28') < $time && $time < strtotime('2016-05-05') ? (! $Utask['gold_all'] ? 100 : (! $Utask['gold_day'] ? 100 : 0)) : $data['charge_rate'];
        //keep $data['charge_rate'] = ISTESTS && $time < strtotime('2016-05-05') ? (! $Utask['gold_all'] ? 100 : (! $Utask['gold_day'] ? 100 : 0)) : $data['charge_rate'];
        $data['charge_rate'] = !$Utask['gold_day'] ? 100 : 0;
        //公司信息
        include(ROOT . '/include/conf_game_base.php');//这里面有$data变量
        //素材版本
        $vers = $this->model->getVersion('', $vercode);
        $data = array_merge($data, $vers);
        //预留功能 导航控制
        // $data['navi'] = array('user_index'=>1,'user_inbox'=>2,'mall_index'=>3,'topic_index'=>4,'topic_lucky'=>5,'list_gold'=>6,'task_check'=>7,'topic_activity'=>8,'user_wechat'=>9,'setting'=>10,'help'=>11);//大厅菜单栏目//>0:排序|0:不显示
        $data['room'] = $this->getRooms($c, $U['gold'], $U['coins'], $vercode, null , $info['channel']);//房间控制
        //向后兼容//县官套装乐娃套装 新道具针对旧版本的呈现兼容逻辑 20160229 给客户端擦屁股
        if (!$robot && $vercode < 10600) {
            $_propIds = [6];
            foreach ($_propIds as $_propId) {
                if (isset($data['propDress'][$_propId])) {
                    unset($data['propDress'][$_propId]);
                    if (!$data['propDress'] || !in_array(1, $data['propDress'])) $data['propDress']['1'] = 1;
                }
            }
        }
        if (!$robot && $vercode < 10900) {
            $_propIds = [9];
            foreach ($_propIds as $_propId) {
                if (isset($data['propDress'][$_propId])) {
                    unset($data['propDress'][$_propId]);
                    if (!$data['propDress'] || !in_array(1, $data['propDress'])) $data['propDress']['1'] = 1;
                }
            }
        }
        $res = sendToFd($fd, $cmd, $code, $data);
        //通知 热门活动
        $cmd = 4;
        $code = 404;
        $send = ['errno' => 0, 'error' => '', 'list' => $this->model->ListGetTopicLobby($c)];
        $res = sendToFd($fd, $cmd, $code, $send);
        $goods = [];
        //通知 大厅礼包
        $teskYiYuanLiBaoId = 10001;//一元礼包任务ID
        if (!isset($Utesk["teskdone_$teskYiYuanLiBaoId"]) || !$Utesk["teskdone_$teskYiYuanLiBaoId"]) {
            $send = $this->model->getGoodsCtrl('datinglibao', $c);
            if (!$goods) $goods = $this->model->getlistGoods('', 1);
            foreach ($send as $k => $conf) {
                if (!isset($goods[$conf['id']])) {
                    unset($send[$k]);
                    continue;
                }
                $send[$k]['price'] = $goods[$conf['id']]['price'];
                $send[$k]['anim'] = 0;
            }
            if ($send) {
                $cmd = 4;
                $code = 164;
                $send = array_values($send);
                $this->model->sendToFd($fd, $cmd, $code, $send);
            }
        }
        //用户综合信息
        $U = $info;
        $td = $U['tableId'] ? $U['tableId'] : '';
        $rd = $U['roomId'];
        $newut = [];
        //更新登录信息
        $addut['login_all_times'] = 1;
        $Utask['login_all_times'] += 1;
        $addut['login_day_times'] = 1;
        $Utask['login_day_times'] += 1;
        $newut['login_last_dateid'] = $Utask['login_last_dateid'] = $Utask['login_this_dateid'];
        $newut['login_this_dateid'] = $Utask['login_this_dateid'] = $dd;
        if (!$Utask['login_day5_day']) $newut['login_day5_day'] = $Utask['login_day5_day'] = 1;
        
        //今日首次登录
        if($Utask['login_last_dateid'] !== $dd)
        {
            $newU['lottery'] = $U['lottery'] = 0;//清零抽奖次数
            if (!$checkin_undo) {
                $newU['checkin_undo'] = $U['checkin_undo'] = 1;
                //通知 可以签到
                $cmd = 4;
                $code = 110;
                $send = ['checkin_undo' => 1];
                $this->model->sendToFd($fd, $cmd, $code, $send);
            }
            if ($Utask['login_last_dateid'] == date("Ymd",time()-86400)) {    // 次日首登 数值加1
                $addut['login_day5_day'] = 1;
                $Utask['login_day5_day'] += 1;
            } else {            // 多日首登 重置为1
                $newut['login_day5_day'] = $Utask['login_day5_day'] = 1;
            }
            $newut['login_day5_got'] = $Utask['login_day5_got'] = 0;
            //修改 变量名错误的bug
            if($Utask['dateid'] == $dd)
            {   // 新手初登
            }elseif($Utask['dateid'] == date("Ymd",time()-86400))
            {   // 次日首登
                $newut['first_day2'] = $Utask['first_day2'] = 1;
            }elseif($Utask['dateid'] == date("Ymd",time()-2*86400))
            {   // 三日首登
                $newut['first_day3'] = $Utask['first_day3'] = 1;
            }elseif($Utask['dateid'] == date("Ymd",time()-6*86400))
            {   // 七日首登
                $newut['first_day7'] = $Utask['first_day7'] = 1;
            }
        }
        //更新登录信息
        $res = setUser($ud, $newU);
        unset($newU);
        $res = $this->model->incUserTask($ud, $addut);
        unset($addut);
        if ($res && isset($res['login_day5_day']) && ($vercode >=10903 ? $res['login_day5_day'] > 7:$res['login_day5_day'] > 5)) $newut['login_day5_day'] = $Utask['login_day5_day'] = 1;
        $res = $this->model->setUserTask($ud, $newut);
        unset($newut);
        //TESK Start
        $accode = 10000;
        $action = 'LOGIN_GUEST';
        $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
        $Utesk = [];
        $teskparam = intval(date("Ymd"));
        $tesktable = [];
        if ($addU = $tesk->execute('user_login', $U, $Utesk, $teskparam, $tesktable)) {
            foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $ud, $U);
            if (($res = $this->model->incUserInfo($ud, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
        }
        //TESK End
        $hour = date("H");
        $hasDuoChongLiBao = 0;//多重礼包占用新手礼包的面板，优先呈现多重礼包时
        $teskDuoChongLiBaoGd = 34;//第一个多重礼包ID，逐个＋1购买
        $teskDuoChongLiBaoId = 10242;//多重礼包任务ID，逐个＋1呈现
        $teskXinShouLibaoId = 10200;//新手礼包任务ID
        
        //固定任务 新手初登，奖励一次抽奖机会
        if ($U['is_noob']) {
            $taskid = 1;
            $tasker = new task($this->model, $taskid, 1, 0, $this->is_freshtask);
            $res = $tasker->run($U, $Utask);
            if ($res) {
                debug("任务新手初登 F=$fd U=$ud R=$rd T=$td taskid=$taskid");
                $U = array_merge($U, isset($res[$taskid]['userinfo']) ? $res[$taskid]['userinfo'] : []);
                $Utask = array_merge($Utask, isset($res[$taskid]['usertask']) ? $res[$taskid]['usertask'] : []);
            }
        }
        
        //签到面板
//         $sql = "select `extend` from user_login where `uid`=$ud LIMIT 0,1";
//         $deviceID = $this->mysql->getVar($sql);
//         $rt = $this->redis->redis->hget("login_got_$dd",$deviceID);
        if(!$Utask['login_day5_got'])
        {
            $this->ACT_LOGIN_DAY0($U, $Utask);
        }
        
        //用户登录完成
        if (!$dayfirst) {
            //多重礼包
            if ($c == 'letv') {
                for ($ii = 0; $ii < 4; $ii++) {
                    $teskDuoChongLiBaoGd += 1;
                    $teskDuoChongLiBaoId += 1;
                    if (isset($Utesk["teskdone_$teskDuoChongLiBaoId"]) && $Utesk["teskdone_$teskDuoChongLiBaoId"]) $this->model->setUserTesk($ud, ["teskdone_$teskDuoChongLiBaoId" => 0]);
                }
            }
            //新手礼包
            if (!$hasDuoChongLiBao && (!isset($Utesk["teskdone_$teskXinShouLibaoId"]) || !$Utesk["teskdone_$teskXinShouLibaoId"])) {
                $conf = $this->model->getGoodsCtrl('xinshoulibao', $c);
                if (!$goods) $goods = $this->model->getlistGoods('', 1);
                if ($conf && isset($goods[$conf['id']])) {
                    $cmd = 4;
                    $code = 334;
                    $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId']];
                    $this->model->sendToFd($fd, $cmd, $code, $send);
                }
            }
            debug("用户登入成功 F=$fd U=$ud R=$rd T=$td V=$v I=$i C=$c");
            //离线消息
            $this->model->exeUserMsg($ud);
            $this->record->action(10000, $rd, $td, $ud, $U);

            //水果机断线重连
            $this->fruitMachine->setUser($U);
            $this->fruitMachine->escape();

            delLock($lockUU);

            return $ud;
        }
        //今日首登
        //多重礼包
        if ($c == 'letv') {
            if (strtotime("2016-04-14") < $time && $time < strtotime("2016-04-18")) {
                for ($ii = 0; $ii < 4; $ii++) {
                    if (!isset($Utesk["teskdone_$teskDuoChongLiBaoId"]) || !$Utesk["teskdone_$teskDuoChongLiBaoId"]) {
                        $conf = $this->model->getGoodsCtrl('duochonglibao', $c, $teskDuoChongLiBaoGd);
                        if (!$goods) $goods = $this->model->getlistGoods('', 1);
                        if ($conf && isset($goods[$conf['id']])) {
                            $hasDuoChongLiBao = 1;
                            $cmd = 4;
                            $code = 334;
                            $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId']];
                            $this->model->sendToFd($fd, $cmd, $code, $send);
                        }
                        break;
                    }
                    $teskDuoChongLiBaoGd += 1;
                    $teskDuoChongLiBaoId += 1;
                }
            } else {
                for ($ii = 0; $ii < 4; $ii++) {
                    $teskDuoChongLiBaoGd += 1;
                    $teskDuoChongLiBaoId += 1;
                    if (isset($Utesk["teskdone_$teskDuoChongLiBaoId"]) && $Utesk["teskdone_$teskDuoChongLiBaoId"]) $this->model->setUserTesk($ud, ["teskdone_$teskDuoChongLiBaoId" => 0]);
                }
            }
        }
        //新手礼包
        if (!$hasDuoChongLiBao && (!isset($Utesk["teskdone_$teskXinShouLibaoId"]) || !$Utesk["teskdone_$teskXinShouLibaoId"])) {
            $conf = $this->model->getGoodsCtrl('xinshoulibao', $c);
            if (!$goods) $goods = $this->model->getlistGoods('', 1);
            if ($conf && isset($goods[$conf['id']])) {
                $cmd = 4;
                $code = 334;
                $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId']];
                $this->model->sendToFd($fd, $cmd, $code, $send);
            }
        }
        //包月俸禄
        $mcard = $this->model->getMcard($ud);
        if ($mcard == 1) {
            $conf = $this->model->getGoodsCtrl('baoyuelibao', $c);
            if (!$goods) $goods = $this->model->getlistGoods('', 1);
            if ($conf && isset($goods[$conf['id']])) {
                $mtem = $this->model->getuserItem($ud, 1);
                $pd = 7;
                $pdSec = 0;
                foreach ($mtem as $k => $vv) {
                    if ($vv['state'] < 2 && $vv['pd'] == $pd) {
                        $pdSec += intval($vv['sec'] > 0 ? $vv['sec'] : max(0, $vv['end'] > 0 ? ($vv['end'] - $time) : 0));
                    }
                }
                $cmd = 4;
                $code = 224;
                $send = ['errno' => 0, 'error' => "", 'isPush' => 1, 'state' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'sec' => $pdSec];
                $res = sendToFd($fd, $cmd, $code, $send);
            }
        }
        //包月礼包
        if (isset($Utesk["teskdone_$teskXinShouLibaoId"]) && $Utesk["teskdone_$teskXinShouLibaoId"] && !$mcard) {
            $conf = $this->model->getGoodsCtrl('baoyuelibao', $c);
            if (!$goods) $goods = $this->model->getlistGoods('', 1);
            if ($conf && isset($goods[$conf['id']])) {
                $cmd = 4;
                $code = 224;
                $send = ['errno' => 0, 'error' => "", 'isPush' => 1, 'state' => 0, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'sec' => 0];
                $res = sendToFd($fd, $cmd, $code, $send);
            }
        }
        //签到面板
        $this->ACT_LOGIN_DAY0($U, $Utask);
        debug("用户今日首登 F=$fd U=$ud R=$rd T=$td V=$v I=$i C=$c");
        //离线消息
        $this->model->exeUserMsg($ud);
        $this->record->action(10000, $rd, $td, $ud, $U);
        delLock($lockUU);
        return $ud;
    }

    //断开用户 返回 void
    function runLogout($fd, $U)
    {
        
        $ud = (is_array($U) && isset($U['uid'])) ? intval($U['uid']) : 0;
        $vc = isset($U['vercode']) ? $U['vercode'] : 0;
        $ip = isset($U['ip']) ? $U['ip'] : 0;
        $ch = isset($U['channel']) ? $U['channel'] : 0;
        $lo = isset($U['logout']) ? $U['logout'] : 0;
        $rd = isset($U['roomId']) ? $U['roomId'] : 0;
        $td = isset($U['tableId']) ? $U['tableId'] : 0;
        
        if (!$U || !is_array($U)) {
            debug("连接已经断开 F=$fd U=$ud R=$rd T=$td");
            $this->record->action(10009, $rd, $td, $ud, $U, [], ['ret' => 'repeats', 'user' => $U]);
            return false;
        } elseif (count($U) < 29) {    //并发或异步情形下，非原子性数据的改写操作，会产生用户残留信息
            debug("用户残留登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo user=" . json_encode($U));
            //清理用户
            $res = $this->model->desUserInfo($ud, $U, __LINE__);
            //不再继续处理
            $this->record->action(10009, $rd, $td, $ud, $U, [], ['ret' => 'residue', 'user' => $U]);
            return false;
        } elseif (!$U['fd']) {    //swoole-bug、close机制，会产生用户掉线保留后再次掉线
            gerr("用户连续登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
            //不再继续处理
            $this->record->action(10009, $rd, $td, $ud, $U, [], ['ret' => 'repeats', 'user' => $U]);
            return false;
        } elseif ($U['fd'] && $U['fd'] != $fd) {    //swoole-bug、close机制，会产生这个状况：服务器不确定用户是否失联->之后->用户换线重登->之后->原线断开再次影响到此用户
            gerr("用户换线登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo newfd=" . $U['fd']);
            //不再继续处理
            $this->record->action(10009, $rd, $td, $ud, $U, [], ['ret' => 'another', 'fd' => $fd, 'user' => $U]);
            return false;
        } elseif (!$ud) {    //
            gerr("用户不明登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo user=" . json_encode($U));
            //清理用户
            $res = $this->model->desUserInfo($ud, $U, __LINE__);
            //不再继续处理
            $this->record->action(10009, $rd, $td, $ud, $U, [], ['ret' => 'unknown', 'ud' => $ud, 'user' => $U]);
            return false;
        }
        
        $this->fruitMachine->setUser($U);
        $U = $this->fruitMachine->escape(false);
        
        $this->cowMachine->linesGone($ud);
        //修改调用方法名不存在的bug  原 ddaJoinTrioNew
        $this->model->ddaJoinTrio($ud, $rd);
        
        $rd = $U['roomId'];
        $td = $U['tableId'];
        $sd = $U['seatId'];
        $gd = $U['gameId'];
        if($fd == $U['fd'])
        $U['fd'] = $newU['fd'] = 0;
        $res = setUser($ud, $newU);
        unset($newU);
        if ($gd) {    //赛场状态 保留用户 后续逻辑中会清理用户
            debug("用户赛场登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
            if ($td) {
                $lock = $td;
                if (!setLock($lock)) gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
                if ($T = $this->model->getTableInfo($td)) {
                    //重设牌桌玩家fd
                    $newT["seat{$sd}fd"] = $T["seat{$sd}fd"] = 0;
                    $newT["seat{$sd}giveup"] = $T["seat{$sd}giveup"] = 1;
                    $res = $this->model->setTableInfo($td, $newT);
                    if (in_array($T['state'], [3, 4, 5, 6])) {    //正在游戏 执行托管 保留用户 散桌过程中会清理用户
                        $res = $this->model->updUserInfo($ud, $U);
                        debug("用户赛场托管 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
                        $res = $this->USER_ENTRUST($fd, $T, $sd, 4);
                    }
                }
                delLock($lock);
            } else {
                //
            }
            $U['giveup'] = $newU['giveup'] = 1;
            $res = setUser($ud, $newU);
            unset($newU);
            $res = $this->model->updUserInfo($ud, $U);
        } elseif ($td) {    //在桌状态
            $lock = $td;
            if (!setLock($lock)) gerr("[LOCKON] lock=$lock func=" . __FUNCTION__);
            $T = $this->model->getTableInfo($td);
            if (!$T) {
                debug("用户桌号登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
                $res = $this->model->desUserInfo($ud, $U, __LINE__);
            } else {
                //重设牌桌玩家fd
                $newT["seat{$sd}fd"] = $T["seat{$sd}fd"] = 0;
                $res = $this->model->setTableInfo($td, $newT);
                if (in_array($T['state'], [3, 4, 5, 6])) {    //正在游戏 执行托管 保留用户 散桌过程中会清理用户
                    $res = $this->model->updUserInfo($ud, $U);
                    debug("用户托管登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
                    $res = $this->USER_ENTRUST($fd, $T, $sd, 4);
                } elseif (in_array($T['state'], [1, 2])) {    //普通在桌 执行散桌 保留用户 散桌过程中会清理用户
                    $res = $this->model->updUserInfo($ud, $U);
                    debug("用户散桌登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
                    $res = $this->TABLE_BREAK($T, 1);
                } else {    //其他状态 不做处理 保留用户 散桌过程中会清理用户
                    debug("用户在桌登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
                }
            }
            delLock($lock);
        } elseif (isset($U['gameStart']) && $U['gameStart']) {    //凑桌状态 保留用户 后续逻辑中会清理用户
            debug("用户凑桌登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
        } else {    //空闲状态 清理用户
            debug("用户普通登出 F=$fd U=$ud R=$rd T=$td lov=$vc loi=$ip loc=$ch lod=$lo");
            $res = $this->model->desUserInfo($ud, $U, __LINE__,0,$fd);
        }
        $this->record->action(10009, $rd, $td, $ud, $U);


        return true;
    }

    //执行跳入
    function runJumpin()
    {
        //
    }

    //执行跳出
    function runJumpout()
    {
        //
    }

    //执行协议
    function runAction($fd, $cmd, $code, $action, $params = [], $user = [])
    {
        // glog("[RUNACTION][$fd] CMD=$cmd CODE=$code ACTION=$action PARAMS=".json_encode($params)." USER=".json_encode($user));
        // return true;

        $accode = $cmd ? ($cmd * 10000 + $code) : sprintf("%1$05d", $code);
        $action = strtolower($action);
        $file = ROOT . "/action/{$accode}.{$action}.php";

        if (!file_exists($file)) return closeToFd($fd, "无效协议文件 file=$file");
        $param = $params;
        require $file;
    }

    //执行事件
    function runEvent($act, $params = [])
    {
        if (method_exists($this, $act)) return $this->$act($params);
        gerr("[EVENT] 无效事件方法 act=$act params=" . json_encode($params));
        return false;
    }

    //执行定时
    function runCrontab($act, $params = [])
    {
        $file = ROOT . "/cron/cron_" . strtolower($act) . ".php";
        if (!file_exists($file)) return gerr("[CRONT] 无效定时脚本 file=$file");
        require $file;
    }

    function getRooms($ch = '', $gold = 0, $coins = 0, $vercode = 0, $md = null, $channel=null)
    {
        $isMobi = intval(strpos($ch, 'sj') === 0);
        $tmid = time();
        $day0 = strtotime(date('Y-m-d'));
        $wday = date("N");    //周n[1-7]
        $rooms = $models = [];
        foreach ($this->rooms as $rd => $v) {
            if (isset($v['isOpen']) && !$v['isOpen']) continue;
            if (isset($v['isMobi']) && !$v['isMobi'] && $isMobi) continue;
            if (isset($v['verMin']) && $v['verMin'] > $vercode) continue;
            if ($isMobi && $vercode < 10800 && $v['modelId'] == 2) continue;
            if ($vercode < 10700 && $v['modelId'] == 2) continue;
            if ($vercode < 10800 && $v['roomId'] == 1094) continue;
            if ($v['roomId'] == 1096 && ($isMobi || $vercode < 10800)) continue;
            if ($v["modelId"] == 5 && $vercode < 10902) continue;
            if ($v["modelId"] == 6 && $vercode < 10903) continue;
            
            $show = 1;//默认显示
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
                    $show_ = 0;//默认隐藏
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
            if ($md === null && isset($v['isModel']) && $v['isModel'] && in_array($v['modelId'], $models)) continue;
            $models[] = $v['modelId'];
            if ($md !== null) {
                if ($md == $v['modelId']) {
                    $room = $v;
                } else {
                    continue;
                }
            } else {
                $room = [];
                $room['modelId'] = $v['modelId'];
                $room['roomId'] = $v['roomId'];
                $room['sort'] = $v['sort'];
                if (in_array($v['modelId'], [0, 1, 2])) {//经典场竞技场赖子场
                    $room['baseCoins'] = $v['baseCoins'];
                    $room['rate'] = $v['rate'];
                    $room['rateMax'] = $v['rateMax'];
                    $room['limitCoins'] = $v['limitCoins'];
                    $room['rake'] = $v['rake'];
                    $room['enter'] = $v['enter'];
                    $room['enterLimit'] = $v['enterLimit'];
                    $room['enterLimit_'] = $v['enterLimit_'];
                    $room['gameBombAdd'] = $v['gameBombAdd'];
                }
                if ($v['modelId'] == 1) {//竞技场
                    $room['gameInCoins'] = $v['gameInCoins'];
                }
                if ($v['modelId'] == 91) {//广告位
                    $room['apkurl'] = $v['apkurl'];
                    $room['isForce'] = isset($v['isForce']) ? intval($v['isForce']) : 0;
                    $room['appid'] = $v['appid'];
                    $room['ver'] = $v['ver'];
                    $room['vercode'] = $v['vercode'];
                    $room['bytes'] = $v['bytes'];
                    $room['desc'] = $v['desc'];
                    $room['md5'] = $v['md5'];
                    $room['package'] = $v['package'];
                }
                if (isset($v['trial'])) {
                    $room['trial'] = $v['trial'];
                }
                //安徽电信
                if(isset($v['trial']) && $channel = "ahtelecom")
                {
                    $room['trial'] = $this->rooms[1001]['trial'];
                }
            }


            $rooms[] = $room;
        }

        //水果机房间
        if ($vercode >= 10902 && $channel != "ahtelecom") {
            $room['baseCoins'] = 0;
            $room['enter'] = "";
            $room['enterLimit'] = 0;
            $room['gameBombAdd'] = 0;
            $room['limitCoins'] = 0;
            $room['modelId'] = 5;
            $room['rake'] = 0;
            $room['rate"'] = 0;
            $room['rateMax"'] = 0;
            $room['roomId'] = 9999;
            $room['sort'] = 1010;
            $rooms[] = $room;
        }

        //百人牛牛
        if ($vercode >= 10903 && $channel != "ahtelecom") {
            $room['baseCoins'] = 0;
            $room['enter'] = "";
            $room['enterLimit'] = 0;
            $room['gameBombAdd'] = 0;
            $room['limitCoins'] = 0;
            $room['modelId'] = 6;
            $room['rake'] = 0;
            $room['rate"'] = 0;
            $room['rateMax"'] = 0;
            $room['roomId'] = 9999;
            $room['sort'] = 1010;
            $rooms[] = $room;
        }

        return $rooms;
    }

    //事件 - 10秒钟主服单独处理
    function TIMERM_10S_ROOMS()
    {
        $tmid = time();
        $night = intval(!($tmid > strtotime(date("Y-m-d 08:00:00")) && $tmid < strtotime(date("Y-m-d 23:59:59"))));
        $isTests = $isRobot = 0;
        if (ISROBOT) $isRobot = 1;    //正式服机器人	//$night;
        if (ISTESTS) $isTests = 1;    //开启测试模式
        //if (ISLOCAL) $isRobot = 1;    //测试服机器人
        //if (ISLOCAL) $this->MODEL_ROBOT_ENROLL();//测试服竞技场开关 机器人参与报名
        $hosts = getHosts();
        if (!$hosts) return gerr("getHosts()=" . json_encode($hosts));
        $hosts = array_keys($hosts);
        $rooms = $this->rooms;
        foreach ($rooms as $rd => $rdc) {
            if (!$rdc['isOpen']) continue;
            // $isRdRobot = $isRobot && ( $isTests || $rd < 1002 );//房间机器人开关
            $isRdRobot =  intval($isRobot || $isTests);//房间机器人开关
            $this->MAKE_TABLE($rd, [], $isRdRobot, $rdc);//向后兼容，附加新版新版开桌
        }
    }

    //事件 - 10秒钟全服各自处理
    function TIMERA_10S_OTHER($params = [])
    {
        //事件 - API
        while ($task = $this->model->popAPIS()) {
            if (!is_array($task) || !isset($task['act']) || !isset($task['data']) || !method_exists($this, $task['act'])) continue;
            $act = $task['act'];
            $this->$act($task['data']);
        }
        //事件 - 推送
        while ($data = $this->model->popPUSH()) {
            $type = $data['type'];
            unset($data['type']);
            switch ($type) {
                case 'mail':    //推送邮件
                    $this->PUSH_USR_MAILS($data);
                    break;
                // case '??':
                // 	$this->PUSH_???($data);
                // 	break;
                default:
                    break;
            }
        }
        //事件 - 文件
        while ($data = $this->model->popFILE()) {
            $type = $data['_type'];
            unset($data['_type']);
            $file = $data['_file'];
            unset($data['_file']);
            $class = $data['_class'];
            unset($data['_class']);
            $this->FILE_OPERATION($file, $class, $type, $data);
        }
    }

    //事件 - 每分钟主服单独处理
    function TIMERM_1M($params = [])
    {
        // 处理新赛制定时开赛
        $this->match->timer(3, intval(date("YmdHi", isset($params['time']) ? $params['time'] : time())));
        // 1 处理过期邮件
        $tmid = time() - 86400 * 30;
        $sql = "SELECT * FROM `lord_user_inbox` WHERE `is_del` = 1 OR `create_time` < $tmid ORDER BY `id` ASC LIMIT 100";
        $res = $this->mysql->getData($sql);
        if (!$res) $res = [];
        $del = [];
        foreach ($res as $k => $v) {
            $sql = "INSERT INTO `lord_user_unbox` (`id`,`type`,`fromUid`,`uid`,`subject`,`content`,`items`,`fileid`,`is_read`,`is_del`,`sort`,`create_time`,`update_time`) VALUES "
                . "(" . $v['id'] . "," . $v['type'] . "," . $v['fromuid'] . "," . $v['uid'] . ",'" . addslashes($v['subject']) . "','" . addslashes($v['content']) . "','" . addslashes($v['items']) . "'," . $v['fileid'] . "," . $v['is_read'] . "," . $v['is_del'] . "," . $v['sort'] . "," . $v['create_time'] . "," . $v['update_time'] . ")";
            $res = $this->mysql->runSql($sql);
            if (!$res) gerr("MYSQL->runSql($sql)");
            if ($res) $del[] = $v['id'];
        }
        if ($del) {
            $sql = "DELETE FROM `lord_user_inbox` WHERE `id` IN (" . join(',', $del) . ")";
            $res = $this->mysql->runSql($sql);
            if (!$res) gerr("MYSQL->runSql($sql)");
        }
        // 2 5分钟统计基础在线 待优化 待转移
        if (!(intval(date("i")) % 5)) {
            $date5 = date("Y-m-d H:i:00");
            $date5_ = date("Y-m-d H:i:00", time() - 300);
            $num = $playing = 0;
            $sql = "SELECT `num`,`playing` FROM `lord_game_online` WHERE `add_time` >= '" . $date5_ . "' AND `add_time` < '" . $date5 . "'";
            $res = $this->mysql->getData($sql);
            if ($res) {
                foreach ($res as $k => $v) {
                    $num += $v['num'];
                    $playing += $v['playing'];
                }
                $num = intval($num / count($res));
                $playing = intval($playing / count($res));
            }
            $sql = "INSERT INTO `lord_online` (`add_time`, `num`, `playing`) VALUES ('$date5', $num, $playing)";
            $res = $this->mysql->runSql($sql);
            if (!$res) gerr("MYSQL->runSql($sql)");
        }
        // 3 存储用户任务记录 待优化 待转移
        $sql = "INSERT INTO `lord_user_taskrecord` (`uid`,`taskid`,`dateid`,`days`,`times`,`gold`,`coupon`,`coins`,`exp`,`lottery`,`propid`,`props`,`ut_create`,`ut_update`) VALUES ";
        $sqli = 0;
        while ($sqli < 50 && ($a = $this->redis->lpop('lord_user_taskrecord'))) {
            $uid = isset($a['uid']) ? intval($a['uid']) : 0;
            $taskid = isset($a['taskid']) ? intval($a['taskid']) : 0;
            if (!$uid || !$taskid) continue;
            $dd = isset($a['dateid']) ? intval($a['dateid']) : 0;
            $days = isset($a['days']) ? intval($a['days']) : 0;
            $times = isset($a['times']) ? intval($a['times']) : 0;
            $gold = isset($a['gold']) ? intval($a['gold']) : 0;
            $coupon = isset($a['coupon']) ? intval($a['coupon']) : 0;
            $coins = isset($a['coins']) ? intval($a['coins']) : 0;
            $exp = isset($a['exp']) ? intval($a['exp']) : 0;
            $lottery = isset($a['lottery']) ? intval($a['lottery']) : 0;
            $propid = isset($a['propid']) ? intval($a['propid']) : 0;
            $props = isset($a['props']) && is_array($a['props']) && $a['props'] ? addslashes(json_encode($a['props'])) : '[]';
            $ut_create = isset($a['ut_create']) ? intval($a['ut_create']) : 0;
            $ut_update = isset($a['ut_update']) ? intval($a['ut_update']) : $ut_create;
            $sql .= " ( $uid, $taskid, $dd, $days, $times, $gold, $coupon, $coins, $exp, $lottery, $propid, '$props', $ut_create, $ut_update ),";
            $sqli++;
        }
        if ($sqli) $res = $this->mysql->runSql(trim($sql, ','));
        // if ( $sqli ) $res = bobSql(trim($sql, ','));
    }

    //事件 - 每分钟全服各自处理
    function TIMERA_1M($params = [])
    {
        return true;
    }

    //事件 - 每分钟主服单独处理
    function TIMERM_1M_ONLINE($params = [])
    {
        return true;
    }

    //事件 - 用户重新登录之后的延迟消息
    // $data['act'] = 'USER_MESSAGE';
    // $data['cmd'] = 4;
    // $data['code'] = 111;
    // $data['var'] = 'val';
    // $res = $this->model->insUserMsg($uid, $data);
    function USER_MESSAGE($data)
    {
        $U = $this->model->getUserInfo($data['uid']);
        unset($data['uid']);
        if (!$U) return false;
        $fd = $U['fd'];
        $cmd = $data['cmd'];
        unset($data['cmd']);
        $code = $data['code'];
        unset($data['code']);
        return sendToFd($fd, $cmd, $code, $data);
    }

    //事件 - 用户弹窗
    function USER_ALERT($data)
    {
        $U = $this->model->getUserInfo($data['uid']);
        unset($data['uid']);
        if (!$U) return false;
        $fd = $U['fd'];
        $cmd = $data['cmd'];
        unset($data['cmd']);
        $code = $data['code'];
        unset($data['code']);
        $data['coins'] = $U['coins'];
        $data['props'] = $U['propDress'];
        return sendToFd($fd, $cmd, $code, $data);
    }


    //事件 - 凑成新桌
    function MAKE_TABLE($rd, $uids = [], $isRobot = 0, $rdc = [])
    {
        //主服触发
        if (!$uids) $uids = $this->model->getJoinTrio($rd);
        //用户触发
        $players = [];
        $player_ids = [];
        foreach ($uids as $ud) {
            $U = $this->model->getUserInfo($ud);
            if ($U) 
            {
                $players[$ud] = $U;
                $player_ids[] = $ud;
            }
            
            if (count($players) != 3) continue;
            /*
            //经典高级 癞子中级
            if($rd == 1003 || $rd == 1009)
            {
                $unavailable_ids = $lastTable1 = $lastTable0 = array();
                if(isset($players[$player_ids[0]]["lastTableId"]))
                {
                    $lastTable0 = explode("_",$players[$player_ids[0]]["lastTableId"]);
                    unset($lastTable0[0]);
                }
                if(isset($players[$player_ids[1]]["lastTableId"]))
                {
                    $lastTable1 = explode("_",$players[$player_ids[1]]["lastTableId"]);
                    unset($lastTable1[0]);
                }
                
                if(in_array($player_ids[1],$lastTable0))
                {
                    $this->model->addJoinTrio($players[$player_ids[1]], $rd);
                    unset($players[$player_ids[1]]);
                    $unavailable_ids[] = 1;
                }
                if(in_array($player_ids[2],$lastTable0))
                {
                    $this->model->addJoinTrio($players[$player_ids[2]], $rd);
                    unset($players[$player_ids[2]]);
                    $unavailable_ids[] = 2;
                }
                if(!in_array(1,$unavailable_ids) && !in_array(2,$unavailable_ids) && in_array($player_ids[2],$lastTable1))
                {
                    $this->model->addJoinTrio($players[$player_ids[2]], $rd);
                    unset($players[$player_ids[2]]);
                    $unavailable_ids[] = 2;
                }
                for($o=0;$o<count($unavailable_ids); $o++)
                {
                    unset($player_ids[$unavailable_ids[$o]]);
                }
                $player_ids = array_merge(array(),$player_ids);
                if($unavailable_ids) continue;
            }
            */
            //凑够三人，组建新桌
            if ($T = $this->model->iniTableInfo($rd, $players)) {
                $players = [];
                $player_ids = [];
                //事件 牌桌开始
                $sceneId = $T['tableId'];
                $act = "GAME_ALL_READY";
                $params = ['tableId' => $sceneId];
                $delay = 0;
                $hd = $T['hostId'];
                setTimer($sceneId, $act, $params, $delay, $hd);//setEvent ?? swoole-bug
                continue;
            }
            //凑桌失败，再回队列
            gerr("组建新桌失败1 R=$rd players=" . json_encode($players));
            foreach ($players as $k => $v) {
                $this->model->addJoinTrio($v, $rd);
            }
            $players = [];
        }
        if (!$players) return true;
        if (!$isRobot) {
            foreach ($players as $k => $v) {
                $this->model->addJoinTrio($v, $rd);
            }
            return true;
        }
        $nums = 3 - count($players) % 3;
        $seed = mt_rand(1, 60000);
        $AND = $rdc['enterLimit_'] > 0 ? (" AND coins <=" . $rdc['enterLimit_']) : '';
        $sql = "SELECT * FROM lord_game_robot WHERE id >= " . $seed . " AND coins > " . $rdc['enterLimit'] . "$AND AND state = 0 LIMIT " . $nums;//临时使用state=0作为机器人值班状态的识别
        $robots = $this->mysql->getData($sql);
        if (!$robots) {
            gerr("MYSQL->getData($sql)");
            $robots = [];
        }
        foreach ($robots as $v) {
            $U = ['fd'        => 0, 'modelId' => 0, 'roomId' => 0, 'tableId' => 0, 'seatId' => 0, 'gameId' => 0, 'gamesId' => 0,
                  'uid'       => $v['uid'] + 0, 'cool_num' => $v['cool_num'] . "", 'nick' => $v['nick'] . "", 'word' => $v['word'] . "",
                  'sex'       => $v['sex'] + 0, 'avatar' => $v['avatar'] + 0, 'exp' => $v['exp'] + 0, 'level' => $v['level'] + 0,
                  'gold'      => $v['gold'] + 0, 'coins' => $v['coins'] + (mt_rand(0, 30) * 80), 'coupon' => 0,
                  'play'      => mt_rand(333, 999), 'win' => intval(mt_rand(333, 999) / 3 + mt_rand(10, 77)),
                  'propDress' => ['1' => 1], 'propItems' => [], 'propAcces' => [], 'buff' => [],
                  'giveup'    => 0, 'score' => 0, 'isShowcard' => 0, 'channel' => "robot", 'vercode' => 10801, 'robot' => 1];
            if (ISLOCAL) $U['isShowcard'] = 1;
            $res = setUser($U['uid'], $U);
            if (!$res) {
                gerr("setUser U=" . $U['uid'] . " user=" . json_encode($U));
                break;
            }
            $players[$U['uid']] = $U;
            //更新机器人值班状态
            $sql = "UPDATE lord_game_robot SET state = 1 WHERE uid = " . $U['uid'];
            $res = $this->mysql->runSql($sql);
            if (!$res) gerr("MYSQL->runSql($sql)");
        }
        //凑够三人，组建新桌
        if (count($players) == 3) {
            if ($T = $this->model->iniTableInfo($rd, $players)) {
                $players = [];
                //事件 牌桌开始
                $sceneId = $T['tableId'];
                $act = "GAME_ALL_READY";
                $params = ['tableId' => $sceneId];
                $delay = 0;
                $hd = $T['hostId'];
                setTimer($sceneId, $act, $params, $delay, $hd);//setEvent ?? swoole-bug
                return true;
            }
            gerr("组建新桌失败2 R=$rd players=" . json_encode($players));
        }
        //销毁假人，落单用户||出错用户，扔回队列
        foreach ($players as $k => $v) {
            if ($v['robot']) {
                $this->model->desRobot($v['uid'], __LINE__);
            } else {
                $this->model->addJoinTrio($v, $rd);
            }
        }
        return true;
    }


    //事件 - 凑成新桌 已经废弃，暂时保留
    function GAME_NEW_TABLE($rd)
    {
        $rd = is_array($rd) && isset($rd['roomId']) ? intval($rd['roomId']) : intval($rd);
        //加事务锁 凑桌互斥锁
        $lock = "NEWTABLE" . $rd;
        if (!setLock($lock, 1)) return false;
        //尝试凑桌
        while ($this->model->countRoomPlayer($rd) > 2) {
            $players = $this->model->getRoomPlayer($rd, 3);
            if (!$players) $players = [];
            //不够三人，重回凑桌
            if (count($players) < 3) {
                foreach ($players as $k => $v) {
                    $res = $this->model->addRoomPlayer($v);
                }
                continue;
            }
            //凑够三人，组建新桌
            $T = $this->model->iniTableInfo($rd, $players);
            if ($T) {
                $this->GAME_ALL_READY($T, 1);
                continue;
            }
            //凑桌失败，踢掉用户
            foreach ($players as $k => $v) {
                setUser($v['uid'], ['gameStart' => 0]);
                $this->model->closeToUid($v['uid'], "牌桌凑桌失败 iniTableInfo($rd, " . json_encode($players) . ")");
            }
        }
        //解事务锁
        delLock($lock);
        return true;
    }

    //事件 - 准备开打
    function GAME_ALL_READY($T, $isTable = 0)
    {
        $state = 3;
        $newT = [];
        if ($isTable) {    //直接执行
            $td = $T['tableId'];
        } else {            //事件执行
            $td = $T['tableId'];
            $T = $this->model->getTableInfo($td);
            if (!$T) return gerr("牌桌开打无效 T=$td");
            //更新用户连接
            foreach ($T['seats'] as $uid => $sid) {
                $fd = ($U = $this->model->getUserInfo($uid)) ? $U['fd'] : 0;
                if ($T["seat{$sid}fd"] != $fd) $newT["seat{$sid}fd"] = $T["seat{$sid}fd"] = $fd;
            }
        }
        $md = $T['modelId'];
        $rd = $T['roomId'];
        //清除打牌历史
        $this->model->delTableHistory($td);
        //更新牌桌状态及是否新局
        $newT['state'] = $T['state'] = $state;
        $newT['gameStart'] = $T['gameStart'] = time();
        $res = $this->model->setTableInfo($td, $newT);
        if (!$res) return gerr("牌桌开打失败 T=$td newT=" . json_encode($newT));
        //比赛模式下主动推送 已进凑桌
        if ($md == 1 || $md == 3) {
            foreach ($T['seats'] as $uid => $sid) {
                $player = ['uid' => $uid, 'fd' => $T["seat{$sid}fd"], 'tableId' => $td];
                $cmd = 5;
                $code = 1001;
                $send = [];
                $res = $this->model->sendToUser($player, $cmd, $code, $send);
            }
        }
        debug(($T['isNewGame'] ? ($md == 1 ? "竞技三人准备" : ($md == 3 ? "比赛三人准备" : "牌桌三人凑齐")) : "牌桌三人准备") . " T=$td");
        //通知牌桌: 正式开始
        $cmd = 5;
        $code = 1004;
        $data = [];
        if ($T['isNewGame']) $data['isNewGame'] = $T['isNewGame'];//是否新局，一定要这么写，源于最初客户端中的协议设计
        $data['modelId'] = $md;
        $data['roomId'] = $rd;
        $data['tableId'] = $td;
        $data['0'] = [
            'seatId'    => 0,
            'roomId'    => $T['roomId'],
            'tableId'   => $T['tableId'],
            'uid'       => $T['seat0uid'],
            'coin'      => $T['seat0coins'],//某个客户端的旧版本用的是这个属性
            'coins'     => $T['seat0coins'],
            'score'     => $T['seat0score'],
            'sex'       => $T['seat0sex'],
            'nick'      => $T['seat0nick'],
            'word'      => $T['seat0word'],
            'propDress' => $T['seat0dress'],
            'buff'      => $T['seat0buff'],
        ];
        $data['1'] = [
            'seatId'    => 1,
            'roomId'    => $T['roomId'],
            'tableId'   => $T['tableId'],
            'uid'       => $T['seat1uid'],
            'coin'      => $T['seat1coins'],
            'coins'     => $T['seat1coins'],
            'score'     => $T['seat1score'],
            'sex'       => $T['seat1sex'],
            'nick'      => $T['seat1nick'],
            'word'      => $T['seat1word'],
            'propDress' => $T['seat1dress'],
            'buff'      => $T['seat1buff'],
        ];
        $data['2'] = [
            'seatId'    => 2,
            'roomId'    => $T['roomId'],
            'tableId'   => $T['tableId'],
            'uid'       => $T['seat2uid'],
            'coin'      => $T['seat2coins'],
            'coins'     => $T['seat2coins'],
            'score'     => $T['seat2score'],
            'sex'       => $T['seat2sex'],
            'nick'      => $T['seat2nick'],
            'word'      => $T['seat2word'],
            'propDress' => $T['seat2dress'],
            'buff'      => $T['seat2buff'],
        ];
        $min_ver = isset($T['version'])?min($T['version']):0;
        $min_ver >= $this->confs['time_auto_play_new_version']?$data["time"]=$this->confs['time_client']:'';
        
        foreach ($T['seats'] as $uid => $sid) {
            //县官套装乐娃套装 新道具针对旧版本的呈现兼容逻辑 20160130 因为客户端没有做好向后兼容
            if (!$T["seat{$sid}robot"] && $T["seat{$sid}vercode"] < 10600) {
                $_propIds = [6];
                foreach ($_propIds as $_propId) {
                    if (isset($data['0']['propDress'][$_propId])) {
                        unset($data['0']['propDress'][$_propId]);
                        if (!$data['0']['propDress'] || !in_array(1, $data['0']['propDress'])) $data['0']['propDress']['1'] = 1;
                    }
                    if (isset($data['1']['propDress'][$_propId])) {
                        unset($data['1']['propDress'][$_propId]);
                        if (!$data['1']['propDress'] || !in_array(1, $data['1']['propDress'])) $data['1']['propDress']['1'] = 1;
                    }
                    if (isset($data['2']['propDress'][$_propId])) {
                        unset($data['2']['propDress'][$_propId]);
                        if (!$data['2']['propDress'] || !in_array(1, $data['2']['propDress'])) $data['2']['propDress']['1'] = 1;
                    }
                }
            }
            if (!$T["seat{$sid}robot"] && $T["seat{$sid}vercode"] < 10900) {
                $_propIds = [9];
                foreach ($_propIds as $_propId) {
                    if (isset($data['0']['propDress'][$_propId])) {
                        unset($data['0']['propDress'][$_propId]);
                        if (!$data['0']['propDress'] || !in_array(1, $data['0']['propDress'])) $data['0']['propDress']['1'] = 1;
                    }
                    if (isset($data['1']['propDress'][$_propId])) {
                        unset($data['1']['propDress'][$_propId]);
                        if (!$data['1']['propDress'] || !in_array(1, $data['1']['propDress'])) $data['1']['propDress']['1'] = 1;
                    }
                    if (isset($data['2']['propDress'][$_propId])) {
                        unset($data['2']['propDress'][$_propId]);
                        if (!$data['2']['propDress'] || !in_array(1, $data['2']['propDress'])) $data['2']['propDress']['1'] = 1;
                    }
                }
            }
            $data['seatId'] = $sid;
            $player = ['uid' => $uid, 'fd' => $T["seat{$sid}fd"], 'tableId' => $td];
            $this->model->sendToPlayer($player, $cmd, $code, $data);
        }
        //执行洗牌发牌
        $res = $this->GAME_SHUFFLE($T);
        return true;
    }

    //[SYS]	洗牌发牌
    function GAME_SHUFFLE($T)
    {
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $td = $T['tableId'];
        $rdc = $this->rooms[$rd];
        debug("牌桌开始发牌[$td]");
        //洗牌
        if ($md == 1 || $md == 3) {
            //竞技场比赛场 旧版加炸运算
            $cardPool = Card::newCardPool(1, $rdc['gameBombAdd']);//[0][1][2][lord]
            //提升底牌倍率
            if (Card::cardsRate($cardPool['lord']) < 2) $cardPool = Card::newCardPool(1, $rdc['gameBombAdd']);
        } else {
            //经典场癞子场 新版加炸运算
            $bombs = $rdc['bombs'];
            $addBomb = 0;
            $step = 0;
            $needle = mt_rand(1, array_sum($bombs));
            foreach ($bombs as $num => $prob) {
                if ($needle > $step && $needle <= $prob + $step) {
                    $addBomb = $num;
                    break;
                }
                $step += $prob;
            }
            // $addBomb = 3;//调试用
            //用户加炸权重
            $rules = ['miss' => []];
            foreach ($T['seats'] as $uid => $sid) {
                $rules['miss'][$sid] = $this->model->getMissBomb($uid);
            }
            // debug("加炸用户权重 bomb=$addBomb A".json_encode($rules));//调试用
            $cardPool = Card::newCardPool(1, $addBomb, $rules);//[0][1][2][lord][miss]
            //提升底牌倍率
            if (Card::cardsRate($cardPool['lord']) < 2) $cardPool = Card::newCardPool(1, $addBomb, $rules);
            if ($cardPool['miss']) {
                foreach ($T['seats'] as $uid => $sid) {
                    $res = $this->model->setMissBomb($uid, $cardPool['miss'][$sid]);
                }
                // debug("加炸用户权重 bomb=$addBomb E".json_encode($cardPool['miss']));//调试用
            }
        }
        // //客户端特殊指定牌面调试代码，务必保留
        // $cardPool['0'] = array(
        // 	"01","42","32","3d","1d","1c","3b","2b","39","29","19","46","36","26","44","34","14"
        // );
        // $cardPool['1'] = array(
        // 	"22","31","11","3c","3a","2a","1a","18","49","16","45","35","25","15","24","23","13"
        // );
        // $cardPool['2'] = array(
        // 	"12","21","2d","4d","4c","2c","4b","1b","4a","48","38","28","47","37","27","17","43"
        // );
        // $cardPool['lord'] = array(
        // 	"00","41","33"
        // );
        //初始倍率
        $new['baseCoins'] = $T['baseCoins'] = $rdc['baseCoins'];
        $new['rate'] = $new['rate_'] = $T['rate'] = $T['rate_'] = $rdc['rate'];
        $new['limitCoins'] = $T['limitCoins'] = $rdc['limitCoins'];
        $new['rake'] = $T['rake'] = $rdc['rake'];
        $new['shuffle'] = ++$T['shuffle'];
        //通知牌桌: 初始倍率
        $cmd = 5;
        $code = 1021;
        $data = [];
        $data['rateId'] = 0;
        $data['rate_num'] = 1;
        $data['rate'] = $T['rate'];
        $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        //确定谁先叫庄
        $new['turnSeat'] = $T['turnSeat'] = ($T['turnSeat'] == 4 ? mt_rand(0, 2) : $T['turnSeat']);
        //确定底牌(地主牌)
        $new['lordCards'] = $T['lordCards'] = $cardPool['lord'];
        //设置各家手牌，判断明牌
        $rate_showcard = 0;
        foreach ($T['seats'] as $uid => $sid) {
            //设置各自手牌
            $new["seat{$sid}cards"] = $T["seat{$sid}cards"] = $cardPool[$sid];
            //如果有人明牌开始
            if ($T["seat{$sid}show"]) {
                //明牌倍率
                $rate_showcard = $this->confs['rate_showcard'];
                //通知牌桌: 有人明牌
                $cmd = 5;
                $code = 1019;
                $data = [];
                $data['rate'] = $rate_showcard;
                $data['showCardId'] = $sid;
                $data['showCardInfo'] = $cardPool[$sid];
                $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);

                $data_['showCard'][$sid] = 1;
                $data_['showCardInfo'][$sid] = $cardPool[$sid];
                //更新牌桌倍率
                if ($rate_showcard) {
                    $new['rate'] = $new['rate_'] = $T['rate'] = $T['rate_'] = $this->TABLE_NEW_RATE($T, $sid, $rate_showcard);
                }
                continue;
            }
            //如果没有明牌
            $data_['showCard'][$sid] = 0;
            $data_['showCardInfo'][$sid] = [];
        }
        //更新牌桌数据
        $res = $this->model->setTableInfo($td, $new);
        if (!$res) return gerr("牌桌发牌失败[$td] new=" . json_encode($new));
        //通知用户: 牌桌发牌
        $cmd = 5;
        $code = 1005;
        $god_sids = array();
        $showCardInfo = array(array(),array(),array());
        $isGod = array_intersect($this->god_uids, array_keys($T['seats']));
        foreach ($T['seats'] as $uid => $sid) {
            if (ISPRESS && $T["seat{$sid}robot"]) {    //压测/外挂专用代码: 三家牌面发到用户
                $data_['seat0cards'] = $T['seat0cards'];
                $data_['seat1cards'] = $T['seat1cards'];
                $data_['seat2cards'] = $T['seat2cards'];
            }
            if($isGod)
            {
                $showCardInfo[$sid] = $T["seat{$sid}cards"];
                if(in_array($uid,$this->god_uids)){
                    $god_sids[$uid] = $sid;
                    continue;
                }
            }
            $data_['myCard'] = $T["seat{$sid}cards"];
            $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
            $res = $this->model->sendToPlayer($player, $cmd, $code, $data_);
        }
        if($god_sids){
            foreach($god_sids as $uid=>$sid)
            {
                $data_["showCard"] = array(1,1,1);
                $data_["showCardInfo"] = $showCardInfo;
                $data_['myCard'] = $T["seat{$sid}cards"];
                $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
                $res = $this->model->sendToPlayer($player, $cmd, $code, $data_);
            }
        }
        //非比赛场
        if ($md != 1 && $md != 3) {
            //牌局任务 发布
            // $coupon_rate = ( (ISTESTS && strtotime('2016-04-25') < time()) || (! ISTESTS && strtotime('2016-04-28') < time() && time() < strtotime('2016-05-05') ) ) ? 2 : 1;
            $coupon_rate = 1;
            $newT = [];
            $data_tteskrate_list = [];
            include(ROOT . '/include/data_tteskrate_list.php');
            $data_ttesksource_list = [];
            include(ROOT . '/include/data_ttesksource_list.php');
            $data_ttesk_list = [];
            include(ROOT . '/include/data_ttesk_list.php');
            foreach ($T['seats'] as $uid => $sid) {
                if ($T["seat{$sid}robot"]) continue;
                $Utask = $this->model->getUserTask($uid);
                if ($Utask && $Utask['normal_all_play'] <= 10) {
                    $tttype = 0;//0正向匹配1反向误导
                } else {
                    $tttimes = $T["seat{$sid}tttimes"];
                    $ttrate = [];
                    foreach ($data_tteskrate_list as $k => $v) {
                        if ($v['times'] <= $tttimes) $ttrate = $v;
                    }
                    if (!$ttrate) $ttrate = ['id' => '3', 'times' => '0', 'prob' => '15000', 'miss' => '5000'];
                    $gold_all = $Utask ? $Utask['gold_all'] : 0;
                    $coupon_all = $Utask ? $Utask['coupon_all'] : 0;
                    $ttrate['prob'] = max(0, $ttrate['prob'] + $gold_all * 100 - $coupon_all);
                    $ttrate['miss'] = max(1, $ttrate['miss']);
                    $tttype = intval(mt_rand(0, $ttrate['prob'] + $ttrate['miss']) > $ttrate['prob']);
                }
                $ttlist = [];
                foreach ($data_ttesk_list as $k => $v) {
                    if ($v['typeid'] != $tttype || !$v['prob']) continue;
                    if ($v['users'] && !in_array(strval($uid), $v['users'])) continue;
                    if ($v['rooms'] && !in_array(strval($rd), $v['rooms'])) continue;
                    if ($v['channels'] && !in_array($T["seat{$sid}channel"], $v['channels'])) continue;
                    $ttlist[$k] = $v;
                }
                // debug("ERR牌局任务".($tttype?"误导":"可行")."模式 进入运算 ".json_encode(array_merge($cardPool[$sid], $cardPool['lord'])));
                if ($tttype) {
                    $task = Card::getMissTask($cardPool[$sid], $cardPool['lord'], $ttlist, $data_ttesksource_list);
                } else {
                    $task = Card::getProbTask($cardPool[$sid], $cardPool['lord'], $ttlist, $data_ttesksource_list);
                }
                $newT["seat{$sid}task"] = $T["seat{$sid}task"] = $task;
                if ($task) {
                    // debug("ERR牌局任务".($tttype?"误导":"可行")."模式 输出任务 ".json_encode($task));
                    $cmd = 5;
                    $code = 1022; //触发牌局任务
                    $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
                    $data = ['table_task' => $task['name'], 'coupon' => intval($task['coupon'] * $coupon_rate), 'is_done' => 0];
                    $res = $this->model->sendToPlayer($player, $cmd, $code, $data);
                }
            }
            //发牌时抽水
            if ($T['shuffle'] < 4 && $T['rake']) {
                $coins = [];
                foreach ($T['seats'] as $uid => $sid) {
                    if ($T["seat{$sid}robot"]){
                        $coins["$sid"]=["coins"=>$T["seat{$sid}coins"]>=$T['rake']?$T["seat{$sid}coins"]-$T['rake']:0];
                        continue;
                    }
                    $ret = $this->model->incUserInfo($uid, ['coins' => -$T['rake']]);
                    if (!$ret) continue;
                    $newT["seat{$sid}coins"] = $T["seat{$sid}coins"] = $ret['info']['coins'];
                    $newT['seat_rakes'][$sid] = $T['seat_rakes'][$sid] += $T['rake'];
                    $coins["$sid"]=$ret['send'];
                    sendToFd($T["seat{$sid}fd"], 4, 110, $ret['send']);
                    $this->record->money('牌局抽水', 'coins', $T['rake'], $uid, $this->model->getTableUser($T, $sid));
                }
                foreach ($T['seats'] as $uid => $sid) {
                    if ($T["seat{$sid}robot"]) continue;
                    $cmd = 5;
                    $code = 430;
                    sendToFd($T["seat{$sid}fd"], $cmd, $code, $coins);
                }
            }
            $newT && $this->model->setTableInfo($td, $newT);
        }
        //牌局任务 完毕
        //事件 - 邀请叫庄
        $sceneId = $td;
        $act = "TURN_CALL_LORD";
        $params = ['tableId' => $td];
        $delay = $this->confs['time_invite_belord'] * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
        // //事件 - 邀请叫庄
        // $act = "TURN_CALL_LORD";
        // $params = array('tableId'=>$td);
        // $delay = $this->confs['time_invite_belord'] * 1000;
        // $hd = $T['hostId'];
        // setEvent($act, $params, $delay, $hd);
        return true;
    }

    //[SYS]	轮到叫庄
    function TURN_CALL_LORD($T, $isTable = 0)
    {
        $state = 4;
        //直接执行
        if ($isTable) {
            $td = $T['tableId'];
        } //执行事件
        else {
            $td = $T['tableId'];
            $T = $this->model->getTableInfo($td);
            if (!$T) {
                gerr("轮叫牌桌无效[?|?|$td|?]");
                return false;
            }
            $newT = [];
            foreach ($T['seats'] as $uid => $sid) {
                $U = $this->model->getUserInfo($uid);
                $fd = $U ? $U['fd'] : 0;
                if ($T["seat{$sid}fd"] != $fd) {
                    $newT["seat{$sid}fd"] = $T["seat{$sid}fd"] = $fd;
                }
            }
            $newT && $this->model->setTableInfo($td, $newT);
        }
        $rd = $T['roomId'];
        $sd = $T['turnSeat'];
        $fd = $T["seat{$sd}fd"];
        $ud = $T["seat{$sd}uid"];
        //更新牌桌状态
        if ($T['state'] != $state) {
            $T['state'] = $state;
            $res = $this->model->setTableState($td, $state);
            if (!$res) {
                gerr("轮叫状态失败 F=$fd U=$ud R=$rd T=$td setTableState=$state");
                return false;
            }
            debug("牌桌开始叫庄 F=$fd U=$ud R=$rd T=$td");
            foreach ($T['seats'] as $uid => $sid) {
                if (!$T["seat{$sid}fd"] && !$T["seat{$sid}robot"]) {
                    $this->USER_ENTRUST($T["seat{$sid}fd"], $T, $sid, 3);//掉线用户自动托管
                }
            }
        }
        debug("牌桌轮到叫庄 F=$fd U=$ud R=$rd T=$td");
        //闹钟 - 到期叫庄
        $sceneId = $td;
        $act = "AUTO_CALL_LORD";
        $params = ['tableId' => $td, 'seatId' => $sd];
        $delay = ($T["seat{$sd}trust"] || $T["seat{$sd}robot"] ? $this->confs['time_trust_play'] : $this->confs['time_auto_lord']) * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
        //通知牌桌: 轮到叫庄
        $cmd = 5;
        $code = 1008;
        $send = ['callId' => $sd];
        $res = $this->model->sendToTable($T, $cmd, $code, $send);
        return true;
    }

    //[SYS]	自动叫庄
    function AUTO_CALL_LORD($params, $U = [], $beLord = 0)
    {
        $state = 4;
        //直接执行
        if ($U) {
            $rd = $U['roomId'];
            $td = $U['tableId'];
            $sd = $U['seatId'];
            $fd = $U['fd'];
            $ud = $U['uid'];
        } //事件执行
        else {
            //参数校验
            if (!isset($params['tableId']) || !isset($params['seatId'])) {
                gerr("叫庄参数无效 F=? U=? R=? T=? params=" . json_encode($params));
                return false;
            }
            $rd = '?';
            $td = $params['tableId'];
            $sd = $params['seatId'];
            $fd = '?';
            $ud = '?';
            $beLord = mt_rand(1, 3) > 2 ? 2 : mt_rand(1, 2);//三分之一
            if (ISTESTS) $beLord = 1;
        }
        //获取牌桌
        $T = $this->model->getTableInfo($td);
        if (!$T) {
            gerr("叫庄牌桌无效 F=$fd U=$ud R=$rd T=$td");
            return false;
        }
        $rd = $T['roomId'];
        $fd = $U ? $U['fd'] : $T["seat{$sd}fd"];
        $ud = $U ? $U['uid'] : $T["seat{$sd}uid"];
        if ($T['state'] != $state || $T['turnSeat'] != $sd) {
            debug("叫庄网络延迟 F=$fd U=$ud R=$rd T=$td state$state=" . $T['state'] . " turn" . $T['turnSeat'] . "=" . $sd);
            return false;
        }
        $old_rate_ = $T['rate_'];
        //执行叫庄，获取最新牌桌数据
        $TOld = $T;
        $T = $this->model->call_lord($TOld, $beLord);
        if (!$T) {
            gerr("叫庄执行失败 F=$fd U=$ud R=$rd T=$td call_lord( " . json_encode($TOld) . ", $beLord )");
            return false;
        }
        $text = $U ? ($beLord == 1 ? '用户选择叫庄' : '用户放弃叫庄') : ($beLord == 1 ? '自动选择叫庄' : '自动放弃叫庄');
        debug("{$text} F=$fd U=$ud R=$rd T=$td beLord=$beLord");
        //通知牌桌: 叫庄/不叫
        $cmd = 5;
        $code = 1006;
        $send = ['beLordId' => $sd, 'beLordInfo' => $beLord];
        $res = $this->model->sendToTable($T, $cmd, $code, $send);
        //通知牌桌: 有倍率变化
        if ($T['rate_'] > $old_rate_) {
            $cmd = 5;
            $code = 1021;
            $data = [
                'rateId'   => $sd,
                'rate_num' => intval($T['rate_'] / $old_rate_),
                'rate'     => $T['rate_'],
            ];
            $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        }
        //检查牌桌状态
        if ($T['state'] == 3) {
            debug("叫庄再次发牌 F=$fd U=$ud R=$rd T=$td");
            //再次发牌
            $res = $this->GAME_SHUFFLE($T);
        } elseif ($T['state'] == 4) {
            //轮到叫庄
            $res = $this->TURN_CALL_LORD($T, 1);
        } elseif ($T['state'] == 5) {
            //轮到抢庄
            $res = $this->TURN_GRAB_LORD($T);
        } elseif ($T['state'] == 6) {
            //确认地主
            $res = $this->GAME_LORD_DONE($T);
        } else {
            gerr("叫庄执行失败 F=$fd U=$ud R=$rd T=$td table＝" . json_encode($T));
        }
        return true;
    }

    //[SYS]	轮到抢庄
    function TURN_GRAB_LORD($T)
    {
        $state = 5;
        $rd = $T['roomId'];
        $td = $T['tableId'];
        $sd = $T['turnSeat'];
        $fd = $T["seat{$sd}fd"];
        $ud = $T["seat{$sd}uid"];
        //更新牌桌状态
        if ($T['state'] != $state) {
            $T['state'] = $state;
            $res = $this->model->setTableState($td, $state);
            if (!$res) {
                gerr("轮抢执行失败 F=$fd U=$ud R=$rd T=$td state=$state");
                return false;
            }
            debug("牌桌开始抢庄 F=$fd U=$ud R=$rd T=$td");
        }
        debug("牌桌轮到抢庄 F=$fd U=$ud R=$rd T=$td");
        //闹钟 - 自动抢庄
        $sceneId = $td;
        $act = "AUTO_GRAB_LORD";
        $params = ['tableId' => $td, 'seatId' => $sd];
        $delay = ($T["seat{$sd}trust"] || $T["seat{$sd}robot"] ? $this->confs['time_trust_play'] : $this->confs['time_auto_lord']) * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
        //通知牌桌: 轮到抢庄
        $cmd = 5;
        $code = 1011;
        $data = [
            'callId' => $sd,
        ];
        $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        return true;
    }

    //[SYS]	自动抢庄
    function AUTO_GRAB_LORD($params, $U = [], $beLord = 0)
    {
        $state = 5;
        //直接执行
        if ($U) {
            $td = $U['tableId'];
            $sd = $U['seatId'];
        } //事件执行
        else {
            //校验参数
            if (!isset($params['tableId']) || !isset($params['seatId'])) {
                gerr("抢庄参数无效[?|?|?|?] params=" . json_encode($params));
                return false;
            }
            $td = $params['tableId'];
            $sd = $params['seatId'];
            $beLord = mt_rand(1, 3) > 2 ? 2 : mt_rand(1, 2);//三分之一
            if (ISTESTS) $beLord = 1;
        }
        //获取牌桌
        $T = $this->model->getTableInfo($td);
        if (!$T) {
            $fd = $U ? $U['fd'] : "?";
            $ud = $U ? $U['uid'] : "?";
            $rd = $U ? $U['roomId'] : "?";
            gerr("抢庄牌桌无效 F=$fd U=$ud R=$rd T=$td");
            return false;
        }
        $rd = $T['roomId'];
        $fd = $U ? $U['fd'] : $T["seat{$sd}fd"];
        $ud = $U ? $U['uid'] : $T["seat{$sd}uid"];
        if ($T['state'] != $state || $T['turnSeat'] != $sd) {
            debug("抢庄网络延迟 F=$fd U=$ud R=$rd T=$td state$state=" . $T['state'] . " turn" . $T['turnSeat'] . "=" . $sd);
            return false;
        }
        $old_rate_ = $T['rate_'];
        //执行抢庄，获取最新牌桌数据
        $TOld = $T;
        $T = $this->model->grab_lord($TOld, $beLord);
        if (!$T) {
            gerr("抢庄执行失败 F=$fd U=$ud R=$rd T=$td grab_lord( " . json_encode($TOld) . ", $beLord )");
            return false;
        }
        if ($U) {
            if ($beLord == 1) {
                $text = "用户选择抢庄";
            } else {
                $text = "用户放弃抢庄";
            }
        } else {
            if ($beLord == 1) {
                $text = "自动选择抢庄";
            } else {
                $text = "自动放弃抢庄";
            }
        }
        debug("{$text} F=$fd U=$ud R=$rd T=$td beLord=$beLord");
        //通知牌桌: 抢庄/不抢
        $cmd = 5;
        $code = 1016;
        $data = [
            'grabLordId'   => $sd,
            'grabLordInfo' => $beLord,
        ];
        $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        //通知牌桌: 有倍率变化
        if ($T['rate_'] > $old_rate_) {
            $cmd = 5;
            $code = 1021;
            $data = [
                'rateId'   => $sd,
                'rate_num' => intval($T['rate_'] / $old_rate_),
                'rate'     => $T['rate_'],
            ];
            $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        }
        //检查牌桌状态
        if ($T['state'] == 5) {
            //轮到抢庄
            $res = $this->TURN_GRAB_LORD($T);
        } elseif ($T['state'] == 6) {
            //确认地主
            $res = $this->GAME_LORD_DONE($T);
        } else {
            gerr("抢庄执行失败 F=$fd U=$ud R=$rd T=$td table＝" . json_encode($T));
        }
        return true;
    }

    //[SYS]	敲定地主
    function GAME_LORD_DONE($T)
    {
        $state = 6;
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $td = $T['tableId'];
        $sd = $T['turnSeat'];
        $fd = $T["seat{$sd}fd"];
        $ud = $T["seat{$sd}uid"];
        //更新牌桌状态
        $T['state'] = $state;
        $res = $this->model->setTableState($td, $state);
        if (!$res) return gerr("定庄执行失败 F=$fd U=$ud R=$rd T=$td table＝" . json_encode($T));
        debug("牌桌敲定地主 F=$fd U=$ud R=$rd T=$td");
        //临时叫抢倍率扶正
        $T['rate'] = $T['rate_'];
        if (!($md == 0 || $md == 2)) $T['noteCards'] = '';
        //通知牌桌: 有人成为地主
        $cmd = 5;
        $code = 1007;
        $data = [];
        $data['lordId'] = $sd;
        $data['lordCard'] = $T['lordCards'];
        $data['lordBonus'] = Card::cardsRate($T['lordCards']);
        $data['noteCards'] = $T['noteCards'];
        
        $isGod = array_intersect($this->god_uids, array_keys($T['seats']));
        $god_sids = array();
        
        foreach ($T['seats'] as $uid => $sid) {
            if (isset($data['myCard'])) {
                unset($data['myCard']);
            }
            if (isset($data['lordShowCard'])) {
                unset($data['lordShowCard']);
            }
            //如果是地主本人: 把地主所有手牌给他
            if ($sd == $sid) {
                $data['myCard'] = $T["seat{$sd}cards"];
            } //如果非地主本人，但地主明牌了: 把地主所有手牌亮给他
            elseif ($sd != $sid && $T["seat{$sd}show"] == 1) {
                $data['lordShowCard'] = $T["seat{$sd}cards"];
            }
            
            if(in_array($uid, $isGod))
            {
                $god_sids[$uid] = $sid;
                continue;
            }
            
            $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
            $res = $this->model->sendToPlayer($player, $cmd, $code, $data);
        }
        
        foreach ($god_sids as $uid=>$sid)
        {
            $data['lordShowCard'] = $T["seat{$sd}cards"];
            $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
            $res = $this->model->sendToPlayer($player, $cmd, $code, $data);
        }
        
        // 赖子场
        //确定赖子牌旧版无花色十六进制牌值joker='c' 重排用户牌面 重发用户牌面 并需要后面代码中把地主第一次打牌的时效延长
        //用户打牌时，给到的数据是sendCards=array(原牌面=旧版十六进制牌值'2b'),jokto=array('c'),其中c代表赖子牌扮演的旧版无花色十六进制牌值
        if ($md == 2) {
            $isJokerA = $this->model->checkTableVersion($T, 10800);//10800版本之后才能使用赖子A
            $newT['joker'] = $T['joker'] = $joker = Card::newJoker($isJokerA ? [] : ['12', '13', '14', '15', '16', '17', '18', '19', '1a', '1b', '1c', '1d']);
            // //客户端特殊牌面调试代码，务必保留
            // $newT['joker'] = $T['joker'] = $joker = '1';
            //通知牌桌: 确定赖子牌面
            $cmd = 5;
            $code = 1002;
            $send = ['joker' => $joker];
            foreach ($T['seats'] as $uid => $sid) {
                $newT["seat{$sid}cards"] = $T["seat{$sid}cards"] = Card::preJoker($T["seat{$sid}cards"], $joker);
                $send['showCard'][$sid] = $T["seat{$sid}show"];
                $send['showCardInfo'][$sid] = $T["seat{$sid}show"] ? $T["seat{$sid}cards"] : [];
            }
            foreach ($T['seats'] as $uid => $sid) {
                $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
                $send['myCard'] = $T["seat{$sid}cards"];
                $this->model->sendToPlayer($player, $cmd, $code, $send);
            }
        }
        //牌局任务 检查
        if ($md != 1) {
            // $coupon_rate = ( (ISTESTS && strtotime('2016-04-25') < time()) || (! ISTESTS && strtotime('2016-04-28') < time() && time() < strtotime('2016-05-05') ) ) ? 2 : 1;
            $coupon_rate = 1;
            foreach ($T['seats'] as $uid => $sid) {
                if ($ud != $uid || $T["seat{$sid}robot"]) continue;
                $task = $T["seat{$sid}task"];
                if (!$task || (isset($task['is_done']) && $task['is_done'])) continue;
                $task = Card::checkTaskBcards($T['lordCards'], $task);
                if (!$task['is_new']) continue;
                $newT["seat{$sid}task"] = $T["seat{$sid}task"] = $task;
                $task['coupon'] = intval($task['coupon'] * $coupon_rate);
                if (isset($task['is_done']) && $task['is_done']) {
                    $newT["seat{$sid}tttimes"] = $T["seat{$sid}tttimes"] = $T["seat{$sid}tttimes"] + 1;
                    $newT["seat{$sid}ttdone"] = $T["seat{$sid}ttdone"] = 1;
                    $newT["seat{$sid}task"] = $T["seat{$sid}task"] = [];
                    $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
                    $cmd = 5;
                    $code = 1022;
                    $send = ['table_task' => $task['name'], 'coupon' => $task['coupon'], 'is_done' => 1];
                    $res = $this->model->sendToPlayer($player, $cmd, $code, $send);
                    $res = $this->model->incUserTask($uid, ['coupon_all' => $task['coupon']]);
                    $res = $this->model->incUserTesk($uid, ['tttimes' => 1]);
                    $res = $this->model->incUserInfo($uid, ['coupon' => $task['coupon'], 'tttimes' => 1]);
                    $cmd = 4;
                    $code = 110;
                    $send = $res['send'];
                    $this->model->sendToPlayer($player, $cmd, $code, $send);
                    $newT["seat{$sid}coupon"] = $T["seat{$sid}coupon"] = $T["seat{$sid}coupon"] + $task['coupon'];
                    $newT["seat{$sid}ttcoupon"] = $T["seat{$sid}ttcoupon"] = $task['coupon'];
                    $user = $this->model->getTableUser($T, $sid);
                    $this->record->money('牌局任务', 'coupon', $task['coupon'], $uid, $user);
                }
            }
        }
        //牌局任务 完毕
        //通知牌桌: 底牌导致倍率变更(即使倍率没变化也要通知)
        $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $sd, $data['lordBonus']);
        //更新牌桌信息
        $res = $this->model->setTableInfo($td, $newT);
        if (!$res) {
            gerr("定庄执行失败 F=$fd U=$ud R=$rd T=$td gamer-" . __LINE__ . " rate=" . $T['rate']);
            return false;
        }
        // TESK任务
        if ($md != 1 && $fd && !$T["seat{$sd}robot"]) {
            $accode = 0;
            $action = 'GAME_LORD_DONE';
            $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
            $user = $this->model->getUserInfo($ud);
            $Utesk = [];
            $teskparam = 1;
            if ($addU = $tesk->execute('be_lord', $user, $Utesk, $teskparam, $T)) {
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $user);
                if (($res = $this->model->incUserInfo($ud, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
            }
        }

        if ($md == 2) {
            //事件 轮到打牌
            $sceneId = $td;
            $act = "TURN_PLAY_CARD";
            if ($this->model->checkTableVersion($T, 10800)) $act = "DOUBLE_ENABLED";//允许双倍
            $params = ['tableId' => $td];
            $delay = $this->confs['time_invite_belord'] * 1000;//使用邀请叫庄时间4秒，提供给客户端动画
            $hd = $T['hostId'];
            setTimer($sceneId, $act, $params, $delay, $hd);
            // //事件 邀请叫庄
            // $act = "TURN_PLAY_CARD";
            // $params = array('tableId'=>$td);
            // $delay = $this->confs['time_invite_belord'] * 1000;
            // $hd = $T['hostId'];
            // setEvent($act, $params, $delay, $hd);
        } elseif ($this->model->checkTableVersion($T, 10800)) {
            //允许双倍
            $res = $this->DOUBLE_ENABLED($T);
        } else {
            //轮到打牌
            $res = $this->TURN_PLAY_CARD($T);
        }

        return true;
    }

    //[SYS]	允许双倍
    function DOUBLE_ENABLED($T)
    {
        if (count($T) == 1 && isset($T['tableId'])) {
            $td = $T['tableId'];
            $T = $this->model->getTableInfo($td);
            if (!$T) return gerr("双倍牌桌无效 F=? U=? T=$td");
        }
        $td = $T['tableId'];
        //通知 允许双倍
        $cmd = 5;
        $code = 1030;
        $send = ['errno' => 0, 'error' => '', 'can_double' => 1];
        $res = $this->model->sendToTable($T, $cmd, $code, $send);
        //事件 轮到打牌
        $sceneId = $td;
        $act = "TURN_PLAY_CARD";
        $params = ['tableId' => $td];
        $delay = (isset($T['version'])?min($T['version']):0)>=$this->confs['time_auto_play_new_version']?($this->confs['time_client']['double']+1)*1000:$this->confs['time_check_double_rate'] * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
    }

    //[SYS]	轮到打牌
    function TURN_PLAY_CARD($T)
    {
        if (count($T) == 1 && isset($T['tableId'])) {
            $td = $T['tableId'];
            $T = $this->model->getTableInfo($td);
            if (!$T) return gerr("轮打牌桌无效 F=? U=? T=$td");
        }
        $td = $T['tableId'];
        $sd = $T['turnSeat'];
        $fd = $T["seat{$sd}fd"];
        $ud = $T["seat{$sd}uid"];
        // debug("牌桌轮到打牌 F=$fd U=$ud R=$rd T=$td");
        //通知 轮到打牌
        $cmd = 5;
        $code = 1009;
        $send = ['callId' => $sd];
        $res = $this->model->sendToTable($T, $cmd, $code, $send);
        //事件 到期打牌
        $sceneId = $td;
        $act = "AUTO_PLAY_CARD";
        $params = ['tableId' => $td, 'seatId' => $sd];
        //$delay = ($T["seat{$sd}trust"] || $T["seat{$sd}robot"] ? $this->confs['time_trust_play'] : min($T['version'])>=$this->confs['time_auto_play_new_version']?$this->confs['time_auto_play_new']:$this->confs['time_auto_play']) * 1000;
        $delay = ($T["seat{$sd}trust"] || $T["seat{$sd}robot"] ? $this->confs['time_trust_play'] : (($min_ver = isset($T['version'])?min($T['version']):0)>=$this->confs['time_auto_play_new_version']?$this->confs['time_client']['send']+1:$this->confs['time_auto_play'])) * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
        return true;
    }

    //[SYS]	自动打牌(出牌跟牌过牌)
    function AUTO_PLAY_CARD($params)
    {
        $state = 6;
        //校验参数
        if (!isset($params['tableId']) || !isset($params['seatId'])) return gerr("机打参数无效 params=" . json_encode($params));
        $td = $params['tableId'];
        $sd = $params['seatId'];
        //获取牌桌
        $T = $this->model->getTableInfo($td);
        if (!$T) return gerr("机打牌桌无效 F=? U=? T=$td");
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $gd = $T['gameId'];
        $fd = $T["seat{$sd}fd"];
        $ud = $T["seat{$sd}uid"];
        //校验牌桌状态、席位轮流
        if ($T['state'] != $state || $T['turnSeat'] != $sd) {
            debug("机打网络延迟 F=$fd U=$ud R=$rd T=$td state" . $T['state'] . "=$state turn" . $T['turnSeat'] . "=$sd");
            return false;
        }
        //校验手牌
        if (!$T['seat0cards'] || !$T['seat1cards'] || !$T['seat2cards']) {
            return gerr("机打手牌无效 F=$fd U=$ud R=$rd T=$td table=" . json_encode($T));
        }
        //自动托管
        if (!$T["seat{$sd}trust"] && !$T["seat{$sd}robot"]) {
            $newT["seat{$sd}delay"] = ++$T["seat{$sd}delay"];
            // //第一次延迟出牌，就自动托管
            // if ( $T["seat{$sd}delay"] == 2 )
            // {
            $newT["seat{$sd}delay"] = $T["seat{$sd}delay"] = 0;
            //执行托管
            debug("机打自动托管 F=$fd U=$ud R=$rd T=$td");
            $T = $this->USER_ENTRUST($fd, $T, $sd, 2);//2自动托管
            // }
        }
        //新打牌机器人 start
        $_table['joker'] = $joker = $T['joker'];
        $_table['jokerDec'] = Card::jokerToNewDec($joker);
        $_table['lord'] = $T['lordSeat'];
        $_table['outs'] = $T['outCards'] ? Card::cardsToNew($T['outCards']) : [];
        $_table['call'] = $T['lastCall'];
        $_table['card'] = $T['lastCards'] ? Card::cardsToNew($T['lastCards']) : [];
        $_table['type'] = $T['lastType'];
        $_table['jokto'] = $T['lastJokto'];
        $sid = $sd;
        $_mine['posi'] = $sid;
        $_mine['hand'] = Card::cardsToNew($T["seat{$sid}cards"]);
        $sid = $sid + 1;
        $sid = $sid == 3 ? 0 : $sid;
        $_prev['posi'] = $sid;
        $_prev['hand'] = Card::cardsToNew($T["seat{$sid}cards"]);
        $sid = $sid + 1;
        $sid = $sid == 3 ? 0 : $sid;
        $_next['posi'] = $sid;
        $_next['hand'] = Card::cardsToNew($T["seat{$sid}cards"]);
        if ($md == 2 && $joker && Card::hasJoker($T["seat{$sd}cards"], $joker)) {
            $_outs = Card::jokerLogic($_table, $_prev, $_next, $_mine);
            $jokto = $_outs ? $_outs['jokto'] : [];
            $_outs = $_outs ? $_outs['plays'] : [];
        } else {
            $jokto = [];
            $_outs = Card::playLogic($_table, $_prev, $_next, $_mine);
        }
        $_outs = $_outs ? $_outs : [];
        //新打牌机器人 end
        //有牌打出
        if ($_outs) {
            //解析牌组
            $cardsRes = Card::cardsParse(Card::cardsToOld($_outs), $jokto, $joker);
            $cardsType = intval($cardsRes['t']);//牌型编号
            $cardsLen = $cardsRes['l'];//牌组长度
            $cardsValue = $cardsRes['v'];//牌组取值
            $cardsPlay = $cardsRes['plays'];//扮演牌组
            $cardsReal = $cardsRes['reals'];//实际牌组
            $jokto = $cardsRes['jokto'];//赖子角色
            //记牌器
            if ($md == 0 || $md == 2) {
                $noteC = str_split($T['noteCards']);
                $cVF = ['f' => 'S', 'e' => 'M', 'd' => '2', 'c' => 'A', 'b' => 'K', 'a' => 'Q', '9' => 'J', '8' => 'T', '7' => '9', '6' => '8', '5' => '7', '4' => '6', '3' => '5', '2' => '4', '1' => '3'];
                $cFI = array_values($cVF);
                foreach ($_outs as $k => $v) {
                    $noteC[array_search($cVF[$v[1]], $cFI) * 2 + 1] -= 1;
                }
                $noteCards = $newT['noteCards'] = join($noteC);
            } else {
                $noteCards = '';
            }
            debug("机打自动出牌 F=$fd U=$ud R=$rd T=$td notes=$noteCards cards=" . join($cardsReal));
            //通知牌桌: 某人出牌
            $cmd = 5;
            $code = 1017;
            $send = ['callId' => $sd, 'sendCards' => $cardsReal, 'cardType' => $cardsType, 'noteCards' => $noteCards];
            if ($md == 2) $send['jokto'] = $jokto;
            $this->model->sendToTable($T, $cmd, $code, $send);
            //更新牌型倍率
            $rate = isset($this->confs['rate_cardstype' . $cardsType]) ? $this->confs['rate_cardstype' . $cardsType] : 1;
            if ($rate > 1) $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $sd, $rate);
            //增加牌桌废牌
            $newT['outCards'] = $T['outCards'] = is_array($T['outCards']) ? array_merge($T['outCards'], $cardsReal) : $cardsReal;
            //减少用户手牌
            $newT["seat{$sd}cards"] = $T["seat{$sd}cards"] = array_values(array_diff($T["seat{$sd}cards"], $cardsReal));
            //累加出牌次数
            $newT["seat{$sd}sent"] = ++$T["seat{$sd}sent"];
            //轮转下家席位
            $newT['turnSeat'] = $T['turnSeat'] = nextSeat($sd);
            //当前叫牌席位
            $newT['lastCall'] = $T['lastCall'] = $sd;
            //当前叫牌内容
            $newT['lastCards'] = $T['lastCards'] = $cardsPlay;
            //当前叫牌扮演
            $newT['lastJokto'] = $T['lastJokto'] = $jokto;
            //当前叫牌牌型
            $newT['lastType'] = $T['lastType'] = $cardsType;
            //重设不跟次数
            $newT['noFollow'] = $T['noFollow'] = 0;
            //更新牌桌信息
            if (!$this->model->setTableInfo($td, $newT)) return gerr("机打执行失败 F=$fd U=$ud R=$rd T=$td new=" . json_encode($newT));
            //检测手牌出完 执行GAME_OVER，并return
            if (!count($T["seat{$sd}cards"])) {
                if ($md == 3) $res = $this->MATCH_GAME_OVER($T);
                elseif ($md == 1) $res = $this->MODEL_GAME_OVER($T);
                else $res = $this->TABLE_GAME_OVER($T);
                return true;
            }
            //轮到下家打牌
            $res = $this->TURN_PLAY_CARD($T);
            return true;
        }
        //无牌打出
        debug("机打自动不跟 F=$fd U=$ud R=$rd T=$td");
        //通知牌桌: 某人不跟
        $cmd = 5;
        $code = 1018;
        $send = ['callId' => $sd];
        $this->model->sendToTable($T, $cmd, $code, $send);
        //累加不跟次数
        $newT['noFollow'] = ++$T['noFollow'];
        //重设叫牌模式
        if ($T['noFollow'] == 2) {
            $newT['noFollow'] = $T['noFollow'] = 0;
            $newT['lastCards'] = $T['lastCards'] = [];
            $newT['lastJokto'] = $T['lastJokto'] = [];
            $newT['lastType'] = $T['lastType'] = 0;
        }
        //轮转下家席位
        $newT['turnSeat'] = $T['turnSeat'] = nextSeat($sd);
        //更新牌桌信息
        if (!$this->model->setTableInfo($td, $newT)) return gerr("机打执行失败 F=$fd U=$ud R=$rd T=$td new=" . json_encode($newT));
        //轮到下家打牌
        $res = $this->TURN_PLAY_CARD($T);
        return true;
    }

    //计算倍率，广播到牌桌，并返回新的$T['rate']
    function TABLE_NEW_RATE($T, $sd, $rate, $ext = 0)
    {
        $td = $T['tableId'];
        $md = $T['modelId'];
        $T['rate'] = $T['rate'] ? $T['rate'] : 1;    //基础设置
        $rate_num = ($rate >= 1 && $rate <= 5) ? $rate : 1;        //翻倍限制
        $rate = $T['rate'] = $T['rate'] * $rate_num;    //翻倍结果
        // TESK任务
        if ($rate >= 200) {
            $accode = 0;
            $action = __FUNCTION__;
            $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
            foreach ($T['seats'] as $uid => $sid) {
                if ($T["seat{$sid}robot"]) continue;
                $user = $this->model->getUserInfo($uid);
                $user['tableId'] = $td;
                $utesk = [];
                $param = $rate;
                if ($addU = $tesk->execute('table_rate', $user, $utesk, $param, $T)) {
                    foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $user);
                    if (($res = $this->model->incUserInfo($ud, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
                }
            }
        }
        //牌局任务 检查
        if ($md != 1) {
            // $coupon_rate = ( (ISTESTS && strtotime('2016-04-25') < time()) || (! ISTESTS && strtotime('2016-04-28') < time() && time() < strtotime('2016-05-05') ) ) ? 2 : 1;
            $coupon_rate = 1;
            $newT = [];
            foreach ($T['seats'] as $uid => $sid) {
                if ($T["seat{$sid}robot"]) continue;
                $task = $T["seat{$sid}task"];
                if (!$task || (isset($task['is_done']) && $task['is_done'])) continue;
                $i = $isNew = 0;
                foreach ($task['conds'] as $k => $cond) {
                    if (isset($cond['is_done']) && $cond['is_done']) {
                        $i++;
                    } elseif ((!isset($cond['is_done']) || !$cond['is_done']) && $cond['id'] == 28 && $cond['value'] <= $rate) {
                        $task['conds'][$k]['is_done'] = $isNew = 1;
                        $i++;
                    }
                }
                if ($i == count($task['conds'])) $task['is_done'] = $isNew = 1;
                if (!$isNew) continue;
                $newT["seat{$sid}task"] = $T["seat{$sid}task"] = $task;
                $task['coupon'] = intval($task['coupon'] * $coupon_rate);
                if (isset($task['is_done']) && $task['is_done']) {
                    $newT["seat{$sid}tttimes"] = $T["seat{$sid}tttimes"] = $T["seat{$sid}tttimes"] + 1;
                    $newT["seat{$sid}ttdone"] = $T["seat{$sid}ttdone"] = 1;
                    $newT["seat{$sid}task"] = $T["seat{$sid}task"] = [];
                    $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
                    $cmd = 5;
                    $code = 1022;
                    $send = ['table_task' => $task['name'], 'coupon' => $task['coupon'], 'is_done' => 1];
                    $res = $this->model->sendToPlayer($player, $cmd, $code, $send);
                    $res = $this->model->incUserTask($uid, ['coupon_all' => $task['coupon']]);
                    $res = $this->model->incUserTesk($uid, ['tttimes' => 1]);
                    $res = $this->model->incUserInfo($uid, ['coupon' => $task['coupon'], 'tttimes' => 1]);
                    $cmd = 4;
                    $code = 110;
                    $send = $res['send'];
                    $this->model->sendToPlayer($player, $cmd, $code, $send);
                    $newT["seat{$sid}coupon"] = $T["seat{$sid}coupon"] = $T["seat{$sid}coupon"] + $task['coupon'];
                    $newT["seat{$sid}ttcoupon"] = $T["seat{$sid}ttcoupon"] = $task['coupon'];
                    $user = $this->model->getTableUser($T, $sid);
                    $this->record->money('牌局任务', 'coupon', $task['coupon'], $uid, $user);
                }
            }
            $newT && $this->model->setTableInfo($td, $newT);
        }
        //广播到牌桌: 倍率变更
        $cmd = 5;
        $code = 1021;
        $send = ['rateId' => $sd, 'rate_num' => $rate_num, 'rate' => $rate, 'ext' => $ext];
        $res = $this->model->sendToTable($T, $cmd, $code, $send);
        return $rate;
    }

    //[SYS]	桌局结束
    function TABLE_GAME_OVER($T)
    {
        $state = 7;
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $gd = $T['gameId'];
        $td = $T['tableId'];
        $dd = dateid();
        $rdc = $this->rooms[$rd];
        $winner = $T['lastCall'];
        $lord = $T['lordSeat'];
        //更新牌桌状态 开始结算
        $newT['state'] = $T['state'] = $state;
        $res = $this->model->setTableState($td, $state);
        if (!$res) return gerr("牌桌结算失败[$td] state=$state table＝" . json_encode($T));
        debug("牌桌开始结算[$td] lord=$lord winner=$winner");
        //地主赢/农民赢、春天/反春 	//变更牌桌倍率
        $isLordwin = $isLordspring = $isBoorspring = 0;
        if ($winner == $lord) {
            $isLordwin = 1;
            $isLordspring = intval(!$T['seat' . nextSeat($winner) . 'sent'] && !$T['seat' . nextSeat(nextSeat($winner)) . 'sent']);
            if ($isLordspring) $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $winner, $this->confs['rate_lordspring']);
        } else {
            $isLordwin = 0;
            $isBoorspring = intval($T['seat' . $lord . 'sent'] == 1);// && in_array(0, [$T['seat0sent'], $T['seat1sent'], $T['seat2sent']]));
            if ($isBoorspring) $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $winner, $this->confs['rate_boorspring']);
        }
        //新版结算
        $data['modelId'] = $md;
        $data['baseCoins'] = $T['baseCoins'];    //底分
        $data['rate'] = $T['rate'];                //倍率
        $data['rateMax'] = $T['rateMax'];        //顶倍
        $data['rake'] = $T['rake'];                //门票
        $data['rake'] = $T['rake'] = 0;            //门票已经在前面扣除了
        //输赢上限
        $rate = $T['rateMax'] <= 0 ? $T['rate'] : min($T['rate'], $T['rateMax']);
        $total = $T['baseCoins'] * $rate;
        $next = nextSeat($lord);
        $prev = nextSeat($next);
        $T['seat' . $lord . 'coins'] = abs($T['seat' . $lord . 'coins']);
        $T['seat' . $next . 'coins'] = abs($T['seat' . $next . 'coins']);
        $T['seat' . $prev . 'coins'] = abs($T['seat' . $prev . 'coins']);
        if ($isLordwin) {
            $wins = min(2 * $total, $T['seat' . $lord . 'coins']);
            $winp = round($wins / 2, 1);
            $losa = min($T['seat' . $next . 'coins'], $winp);
            $losb = min($T['seat' . $prev . 'coins'], $winp);
            $smla = $smlb = 0;
            if ($losa < $winp) $smla = 1;
            if ($losb < $winp) $smlb = 1;
            if ($smla && !$smlb) {
                $losb = min(min($T['seat' . $prev . 'coins'], $total), $wins - $losa);
            }
            if ($smlb && !$smla) {
                $losa = min(min($T['seat' . $next . 'coins'], $total), $wins - $losb);
            }
            $data['total'][$lord] = intval($losa + $losb);
            $data['total'][$next] = -1 * intval($losa);
            $data['total'][$prev] = -1 * intval($losb);
        } else {
            $lose = min(2 * $total, $T['seat' . $lord . 'coins']);
            $losp = round($lose / 2, 1);
            $wina = min($T['seat' . $next . 'coins'], $losp);
            $winb = min($T['seat' . $prev . 'coins'], $losp);
            $smla = $smlb = 0;
            if ($wina < $losp) $smla = 1;
            if ($winb < $losp) $smlb = 1;
            if ($smla && !$smlb) {
                $winb = min(min($T['seat' . $prev . 'coins'], $total), $lose - $wina);
            }
            if ($smlb && !$smla) {
                $wina = min(min($T['seat' . $next . 'coins'], $total), $lose - $winb);
            }
            $data['total'][$lord] = -1 * intval($wina + $winb);
            $data['total'][$next] = intval($wina);
            $data['total'][$prev] = intval($winb);
        }
        //幸运牌局buff 赢了双倍输了不扣
        foreach ($T['seats'] as $uid => $sid) {
            $data['isWinner'][$sid] = intval(($isLordwin && $sid == $lord) || (!$isLordwin && $sid != $lord));
            if (isset($T["seat{$sid}buff"]['8']) && $T["seat{$sid}buff"]['8']) {
                if ($data['isWinner'][$sid]) {
                    $data['buff8'][$sid] = 2;
                } else {
                    $data['buff8'][$sid] = 0;
                }
            } else {
                $data['buff8'][$sid] = 1;
            }
        }
        $users = [];
        //谁输谁赢 现金多少 考虑门票
        foreach ($T['seats'] as $uid => $sid) {
            $users[$uid] = $this->model->getUserInfo($uid);
            $data['coins'][$sid] = max(0, $T["seat{$sid}coins"] + intval($data['total'][$sid] * $data['buff8'][$sid]) - $T['rake']);
            if(!$data['buff8'][$sid]) $data['coins'][$sid]= 0;
            $data['buff8'][$sid] = intval($data['total'][$sid] * ($data['buff8'][$sid] - 1));
            // $seat_rakes[$sid] = ($T["seat{$sid}coins"] + $data['total'][$sid]) >= $T['rake'] ? $T['rake'] : ($T["seat{$sid}coins"] + $data['total'][$sid]);
        }
        //压测模式 金币不变
        if (ISPRESS) $data['coins'] = ["2" => $T['seat2coins'], "1" => $T['seat1coins'], "0" => $T['seat0coins']];
        //剩牌多少
        $data['cards'] = ["2" => $T['seat2cards'], "1" => $T['seat1cards'], "0" => $T['seat0cards']];
        //转为对象
        $seat_winer = $data['isWinner'];
        krsort($data['isWinner']);
        $T['seat_winer'] = $data['isWinner'];
        $seat_coins = $data['coins'];
        krsort($data['coins']);
        $T['seat_coins'] = $data['coins'];
        $seat_total = $data['total'];
        krsort($data['total']);
        $T['seat_total'] = $data['total'];
        $seat_buff8 = $data['buff8'];
        krsort($data['buff8']);
        $T['seat_buff8'] = $data['buff8'];
        // $T['seat_rakes'] = $seat_rakes;
        //通知 牌桌结算
        $cmd = 5;
        $code = 1014;
        $this->model->sendToTable($T, $cmd, $code, $data);
        //牌局任务 检查
        if ($md != 1) {
            // $coupon_rate = ( (ISTESTS && strtotime('2016-04-25') < time()) || (! ISTESTS && strtotime('2016-04-28') < time() && time() < strtotime('2016-05-05') ) ) ? 2 : 1;
            $coupon_rate = 1;
            foreach ($T['seats'] as $uid => $sid) {
                if ($T["seat{$sid}robot"]) continue;
                $task = $T["seat{$sid}task"];
                if (!$task || (isset($task['is_done']) && $task['is_done'])) continue;
                $i = $isNew = 0;
                foreach ($task['conds'] as $k => $cond) {
                    if (isset($cond['is_done']) && $cond['is_done']) {
                        $i++;
                    } elseif ((!isset($cond['is_done']) || !$cond['is_done']) && $cond['id'] == 29 && $seat_winer[$sid]) {
                        $task['conds'][$k]['is_done'] = $isNew = 1;
                        $i++;
                    } elseif ((!isset($cond['is_done']) || !$cond['is_done']) && $cond['id'] == 30 && $lord == $sid && $isLordspring) {
                        $task['conds'][$k]['is_done'] = $isNew = 1;
                        $i++;
                    } elseif ((!isset($cond['is_done']) || !$cond['is_done']) && $cond['id'] == 31 && $lord != $sid && $isBoorspring) {
                        $task['conds'][$k]['is_done'] = $isNew = 1;
                        $i++;
                    }
                }
                if ($i == count($task['conds'])) $task['is_done'] = $isNew = 1;
                if (!$isNew) continue;
                $newT["seat{$sid}task"] = $T["seat{$sid}task"] = $task;
                $task['coupon'] = intval($task['coupon'] * $coupon_rate);
                if (isset($task['is_done']) && $task['is_done']) {
                    $newT["seat{$sid}tttimes"] = $T["seat{$sid}tttimes"] = $T["seat{$sid}tttimes"] + 1;
                    $newT["seat{$sid}ttdone"] = $T["seat{$sid}ttdone"] = 1;
                    $newT["seat{$sid}task"] = $T["seat{$sid}task"] = [];
                    $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
                    $cmd = 5;
                    $code = 1022;
                    $send = ['table_task' => $task['name'], 'coupon' => $task['coupon'], 'is_done' => 1];
                    $res = $this->model->sendToPlayer($player, $cmd, $code, $send);
                    $res = $this->model->incUserTask($uid, ['coupon_all' => $task['coupon']]);
                    $res = $this->model->incUserTesk($uid, ['tttimes' => 1]);
                    $res = $this->model->incUserInfo($uid, ['coupon' => $task['coupon'], 'tttimes' => 1]);
                    $cmd = 4;
                    $code = 110;
                    $send = $res['send'];
                    $this->model->sendToPlayer($player, $cmd, $code, $send);
                    $newT["seat{$sid}coupon"] = $T["seat{$sid}coupon"] = $T["seat{$sid}coupon"] + $task['coupon'];
                    $newT["seat{$sid}ttcoupon"] = $T["seat{$sid}ttcoupon"] = $task['coupon'];
                    $user = $this->model->getTableUser($T, $sid);
                    $this->record->money('牌局任务', 'coupon', $task['coupon'], $uid, $user);
                }
            }
        }
        //牌局任务 完毕
        if ($newT) $this->model->setTableInfo($td, $newT);
        //用户补豆，数据落地
        $isRegame = 1;
        $data = $addT = $newT = $goods = [];
        $tasker = null;
        $coins = [];
        foreach ($seat_coins as $sid => $val) {
            $fd = $T["seat{$sid}fd"];
            $uid = $T["seat{$sid}uid"];
            $win = intval($seat_winer[$sid] == 1);        //输赢次数增加
            $add = $val - $T["seat{$sid}coins"];    //实际乐豆加减
            $los = $T["seat{$sid}coins"] - $val;
            $ilordwin = intval($win && $sid == $lord);    //我是地主赢
            $ipoorwin = intval($win && $sid != $lord);    //我是农民赢
            $coinswin = $win ? $add : 0;
            $newU = [];
            $user = $users[$uid];
            $stwin = $stwinold = $this->model->redis->hget('lord_stwin_' . $dd, $uid);
            $stlos = $stlosold = $this->model->redis->hget('lord_stlos_' . $dd, $uid);
            
            if ($win) {
                $stwin = $stwin * 1 + 1;
            } else {
                $stwin = $stwin * 0 + 0;
                $stlos = $stlos + 1;//实际上是累败，不是连败
            }
            if ($stwin != $stwinold) $this->model->redis->hset('lord_stwin_' . $dd, $uid, $stwin);
            if ($stlos > $stlosold) $this->model->redis->hset('lord_stlos_' . $dd, $uid, $stlos);
            
            //加减乐豆
            if ($add) {
                $addU = ['coins' => $add];
                $addT["seat{$sid}coins"] = $add;
                $res = $this->model->incUserInfo($uid, $addU);
                unset($addU);
                $users[$uid]['coins'] = $user['coins'] = $res['info']['coins'];
                $this->model->coinsStat($uid, $add);
                $coins[$sid]=["coins"=>$res['info']['coins']];
            }
            //是否再桌
            if ($T["seat{$sid}robot"]) {        //机器人 在钱数合适时，有一半的机会继续再桌。且不处理后续结算统计逻辑
                $isRegame = min(($val < $rdc['enterLimit'] || ($rdc['enterLimit_'] > 0 && $val > $rdc['enterLimit_'])) ? 0 : (ISLOCAL ? 1 : mt_rand(0, 1)), $isRegame);
                continue;
            } elseif ($T["seat{$sid}trust"]) {//托管人 不再桌
                $isRegame = 0;
            } elseif (!$T["seat{$sid}fd"]) {    //离桌人 不再桌
                $isRegame = 0;
            }
            //货币记录
            if ($seat_total[$sid] > 0) $this->record->money('牌局赢豆', 'coins', $seat_total[$sid], $uid, $user);
            else $this->record->money('牌局输豆', 'coins', abs($seat_total[$sid]), $uid, $user);
            // $this->record->money('牌局抽水', 'coins', $seat_rakes[$sid], $uid, $user);
            if ($seat_buff8[$sid]) {
                $this->record->money('幸运牌局', 'coins', abs($seat_buff8[$sid]), $uid, $user);
                $newU['buff'] = $user['buff'] = $this->model->ddaBuff($uid, 8, $user);
            }
            //用户补豆 获取用户即时信息
            $user = $users[$uid] = $this->model->checkUserCoins($uid);
            if (!$user) {                            //无效人 不再桌 且不处理后续逻辑
                $isRegame = 0;
                gerr("结算用户无效[$fd|$uid|$td|$sid]");
                continue;
            } elseif (!$user['fd']) {                //掉线人 不再桌
                $isRegame = 0;
            } else {                                    //正常人 可再桌
                $isRegame = min($isRegame, 1);
            }
            //V10000前后版本兼容
            if ($user['vercode'] == 10000) {
                if ($user['isSend']) {
                    $addT["seat{$sid}coins"] = $add + $user['sendCoins'];
                }
                unset($user['isSend']);
                unset($user['sendCoins']);
                unset($user['sendCoinsTimesToday']);
                unset($user['sendCoinsTimes']);
            }
            //数据落地 历史遗留原因 待优化 待续
            $sql = "UPDATE `lord_game_analyse` SET `matches` = `matches` + 1, `win` = `win` + $win WHERE `uid` = $uid";
            bobSql($sql);
            if (isset($user['normal_all_play'])) {
                $addU['normal_all_play'] = 1;
                $user['normal_all_play'] += 1;
                $addU['normal_all_win'] = $win;
                $user['normal_all_win'] += $win;
            } else {
                $addU['play'] = 1;
                $user['play'] += 1;
                $addU['win'] = $win;
                $user['win'] += $win;
            }
            $newU['gameStart'] = $user['gameStart'] = $users[$uid]['gameStart'] = 0;
            $addU && $this->model->incUserInfo($uid, $addU);
            unset($addU);
            $newU && setUser($uid, $newU);
            unset($newU);
            //重设牌桌用户变动信息
            $fd = $newT["seat{$sid}fd"] = $T["seat{$sid}fd"] = $user['fd'];
            $newT["seat{$sid}coins"] = $T["seat{$sid}coins"] = $user['coins'];
            $newT["seat{$sid}coupon"] = $T["seat{$sid}coupon"] = $user['coupon'];
            $newT["seat{$sid}dress"] = $T["seat{$sid}dress"] = $user['propDress'];
            $newT["seat{$sid}items"] = $T["seat{$sid}items"] = $user['propItems'];
            $newT["seat{$sid}buff"] = $T["seat{$sid}buff"] = isset($user['buff']) ? $user['buff'] : [];
            // $newT["seat{$sid}acces"] = $T["seat{$sid}acces"] = $user['propAcces'];
            // TESK任务 Start
            $accode = 0;
            $action = 'GAME_OVER';
            $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
            $Utesk = [];
            //n 局送乐券是否达成
            $is_finish = 0;
            if ($addU = $tesk->execute('game_over', $user, $Utesk, $win, $T)) {
                if (isset($addU['teskdones']) && array_intersect($addU['teskdones'], [10266, 10267, 10268, 10269, 10295, 10270, 10271, 10272, 10273, 10296])) {
                    unset($addU['teskdones']);
                    $is_finish = 1;
                    $send = ['errno' => 0, 'error' => '', 'is_finish' => $is_finish, 'coupon' => intval($addU['coupon']), 'reward_list' => $this->model->getNRewardList($uid,$user['vercode'])];
                    sendToFd($fd, 4, 236, $send);
                }
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $user);
                if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
            }
            if (!$is_finish) {
                $send = ['errno' => 0, 'error' => '', 'is_finish' => $is_finish, 'coupon' => 0, 'reward_list' => $this->model->getNRewardList($uid,$user['vercode'])];
                sendToFd($fd, 4, 236, $send);
            }
            if ($ilordwin) {
                if ($addU = $tesk->execute('lord_win', $user, $Utesk, $ilordwin, $T)) {
                    foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $user);
                    if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
                }
            } elseif ($ipoorwin) {
                if ($addU = $tesk->execute('poor_win', $user, $Utesk, $ipoorwin, $T)) {
                    foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $user);
                    if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
                }
            }
            if ($coinswin) {
                if ($addU = $tesk->execute('coins_win', $user, $Utesk, $coinswin, $T)) {
                    foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $user);
                    if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
                }
            }
            // TESK任务 End
            // 开始用户统计
            $Utask = $this->model->getUserTask($uid);
            $addUT = $newUT = [];
            // 普通场游戏次数
            $addUT['normal_all_play'] = 1;
            $addUT['normal_week_play'] = 1;
            $addUT['normal_day_play'] = 1;
            // 累加入榜 普通场游戏次数榜单
            $res = $this->model->zNormalPlay($uid, 1);
            if ($win) {    // 普通场胜局次数
                $addUT['normal_all_win'] = 1;
                $addUT['normal_week_win'] = 1;
                $addUT['normal_day_win'] = 1;
                // 累加入榜 普通场胜局次数榜单
                $res = $this->model->zNormalWin($uid, 1);
            }
            $earn = $add;
            if (0 < $earn) {    // 普通场赢钱累计
                $addUT['normal_all_earn'] = $earn;
                $addUT['normal_week_earn'] = $earn;
                $addUT['normal_day_earn'] = $earn;
                // 累加入榜 普通场赢钱累计榜单
                $res = $this->model->zNormalEarn($uid, $earn);
                if ($Utask['normal_day_maxearn'] < $earn) {    // 普通场每日最大赢钱数
                    $newUT['normal_day_maxearn'] = $earn;
                    // 暂不入榜
                    if ($Utask['normal_week_maxearn'] < $earn) {    // 普通场每周最大赢钱数
                        $newUT['normal_week_maxearn'] = $earn;
                        // 暂不入榜
                        if ($Utask['normal_all_maxearn'] < $earn) {    // 普通场全部最大赢钱数
                            $newUT['normal_all_maxearn'] = $earn;
                            // 暂不入榜
                        }
                    }
                }
                // 倍率入榜
                $rate = $T['rate'];
                if ($Utask['normal_day_maxrate'] < $rate) {    // 普通场每日最大倍率数
                    $newUT['normal_day_maxrate'] = $rate;
                    // 更新入榜 普通场最大倍率榜单 日
                    $res = $this->model->zNormalDayMaxrate($uid, $rate);
                    if ($Utask['normal_week_maxrate'] < $rate) {    // 普通场每周最大倍率数
                        $newUT['normal_week_maxrate'] = $rate;
                        // 暂不入榜
                        if ($Utask['normal_all_maxrate'] < $rate) {    // 普通场全部最大倍率数
                            $newUT['normal_all_maxrate'] = $rate;
                            // 暂不入榜
                        }
                    }
                }
            }
            // 更新用户统计
            $addUT && $this->model->incUserTask($uid, $addUT);
            $newUT && $this->model->setUserTask($uid, $newUT);
            $Utask = $this->model->getUserTask($uid);
            // 结束用户统计
            // 开始执行任务: 今日N次普通场，奖励一次抽奖机会
            $is_taskdone = 0;
            if ($Utask['normal_day_play'] == 100) {
                $taskid = 2;
                if ($tasker === null) $tasker = new task($this->model, $taskid, 0, 0, $this->is_freshtask);
                if ($res = $tasker->run($user, $Utask)) {
                    $is_taskdone = 1;
                    debug("任务普场日满[$fd|$uid|$td|$sid] taskid=$taskid");
                    $user = array_merge($user, isset($res[$taskid]['userinfo']) ? $res[$taskid]['userinfo'] : []);
                    $Utask = array_merge($Utask, isset($res[$taskid]['usertask']) ? $res[$taskid]['usertask'] : []);
                }
            }
            // 结束执行任务
            //钱多散桌 钱少不散
            if ($rdc['enterLimit_'] > 0 && $user['coins'] > $rdc['enterLimit_']) {
                $isRegame = 0;
            }
            $isRegame = 0   ;
            //单次记牌器
            $propId = 5;
            $propItems = $T["seat{$sid}items"];
            $realItems = $T["seat{$sid}realItems"];
            if (!($realItems && isset($realItems[$propId])) && $this->model->getUserNumItem($uid, $propId) > 0) {
                $res = $this->model->addUserNumItem(['uid' => $uid, 'propItems' => $propItems], $propId, -1);
                $propItems = $res['propItems'];
                $send = ["propId" => $propId, "propNum" => $res["propNum"], "propItems" => $propItems, 'coins' => $T["seat{$sid}coins"], 'propPrice' => 0, "errno" => 0, "error" => ""];
                $fd && sendToFd($fd, 6, 112, $send);
            }
            if (!$fd) continue;
            $user = getUser($uid);
            $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
            //刷新数据
            sendToFd($fd, 4, 110, ['coins' => $user['coins'], 'coupon' => $user['coupon'], 'lottery' => $user['lottery'], "propItems" => $propItems]);
            //各种礼包
            $noLibao = $noJiuji = 1;
            //乐豆超出
            if ($rdc['enterLimit_'] > 0 && $user['coins'] > $rdc['enterLimit_']) {
                $cmd = 5;
                $code = 1027;
                $send = ['isSmall' => 0, 'coins' => $user['coins'], 'roomId' => $rd, 'type' => 'lose',"newRoomId"=>0,"msg"=>""];//?lose?
                // sendToFd($fd, $cmd, $code, $send);
                $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                $noLibao = 0;
            }
            if ($win) {    //赢
                //连胜礼包
                if ($noLibao && in_array($stwin, $rdc['stwin']) && $user["channel"]!="ahtelecom") {
                    $conf = $this->model->getGoodsCtrl('lianshenglibao', $T["seat{$sid}channel"], 0, $rd, $stwin);
                    if (!$goods) $goods = $this->model->getlistGoods('', 1);
                    if ($conf && isset($goods[$conf['id']])) {
                        $cmd = 4;
                        $code = 168;
                        $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => $conf['anim'], 'goto' => $conf['goto']];
                        // $this->model->sendToFd($fd, $cmd, $code, $send);
                        $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                        $noLibao = 0;
                        //加入礼包面板 向高覆盖
                        $isModify = $isTitle = 0;
                        if ($tmp = $this->model->redis->hget('lord_libao_' . $dd, $uid)) {
                            foreach ($tmp as $k => $v) {
                                if ($v['title'] == $send['title']) {
                                    $isTitle = 1;
                                    if ($v['price'] < $send['price'] || ($v['price'] == $send['price'] && $v['id'] < $send['id'])) {
                                        unset($tmp[$k]);
                                        $tmp[$send['title'] . '_' . $send['price'] . '_' . $send['id']] = $send;
                                        $isModify = 1;
                                    }
                                }
                            }
                            if (!$isTitle) {
                                $tmp[$send['title'] . '_' . $send['price'] . '_' . $send['id']] = $send;
                                $isModify = 1;
                            }
                        } else {
                            $tmp = [$send['title'] . '_' . $send['price'] . '_' . $send['id'] => $send];
                            $isModify = 1;
                        }
                        $isModify && $this->model->redis->hset('lord_libao_' . $dd, $uid, $tmp);//
                    }
                }
                //幸运牌局
                $chance = mt_rand(0, 9999) < 999;
                if ($noLibao && $chance && $this->model->redis->hget('lord_libao_xingyun_times_' . $dd, $uid) < 3 && $user["channel"]!="ahtelecom") {
                    $conf = $this->model->getGoodsCtrl('xingyunpaiju', $T["seat{$sid}channel"], 0, $rd);
                    if (!$goods) $goods = $this->model->getlistGoods('', 1);
                    if ($conf && isset($goods[$conf['id']])) {
                        $cmd = 4;
                        $code = 168;
                        $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => $conf['anim'], 'goto' => $conf['goto']];
                        // $this->model->sendToFd($fd, $cmd, $code, $send);
                        $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                        $noLibao = 0;
                        //加入礼包面板 并列
                        $tmp = $this->model->redis->hget('lord_libao_' . $dd, $uid);
                        if (!$tmp) $tmp = [];
                        $tmp[$send['title'] . '_' . $send['price'] . '_' . $send['id']] = $send;
                        $this->model->redis->hset('lord_libao_' . $dd, $uid, $tmp);//
                        $this->model->redis->hadd('lord_libao_xingyun_times_' . $dd, $uid);
                    }
                }
            } else {        //输
                //乐豆不足
                if ($user['coins'] < $rdc['enterLimit']) {
                    //版本兼容
                    if ($T["seat{$sid}vercode"] < 10800) {
                        $cmd = 5;
                        $code = 1027;
                        $send = ['isSmall' => 1, 'coins' => $user['coins'], 'roomId' => $rd, 'type' => 'lose'];//?lose?
                        // sendToFd($fd, $cmd, $code, $send);
                        $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                        $libao = [];
                        $noLibao = $noJiuji = 0;
                    }
                    //用户破产
                    if ($user['coins'] < 1000) {
                        //获取今日已领取次数、冷却秒数
                        $trial = $this->model->getdataUserTrial($uid);

                        $send = ['errno' => 0, 'error' => '', 'trial_count' => $trial['trial_count'], 'trial_cooldown' => $trial['trial_cooldown']];
                        //获取本次领取的基数倍率列表
                        $list = $this->model->getlistTrialCoins($T["seat{$sid}channel"], $trial['trial_count']);
                        foreach ($list as $k => $v) {
                            unset($v['probability']);
                            $list[$k] = $v;
                        }
                        $send['trial_list'] = $list;
                        //救济面板＋新手礼包
                        if ($noLibao && !$Utesk['teskdone_10200']) {
                            $conf = $this->model->getGoodsCtrl('xinshoulibao', $T["seat{$sid}channel"]);
                            if (!$goods) $goods = $this->model->getlistGoods('', 1);
                            if ($conf && isset($goods[$conf['id']])) {
                                $libao = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => 0, 'goto' => $conf['goto']];
                                $noLibao = $noJiuji = 0;
                            }
                        }
                        //救济面板＋连败礼包
                        if ($noLibao && in_array($stlos, $rdc['stlos'])) {
                            $conf = $this->model->getGoodsCtrl('lianbailibao', $T["seat{$sid}channel"], 0, $rd, $stlos);
                            if (!$goods) $goods = $this->model->getlistGoods('', 1);
                            if ($conf && isset($goods[$conf['id']])) {
                                $libao = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => 0, 'goto' => $conf['goto']];
                                $noLibao = $noJiuji = 0;
                            }
                        }
                        //救济面板＋礼包
                        if (!$noLibao && $libao) {
                            $cmd = 5;
                            $code = 152;
                            $send = array_merge($send, $libao);
                            // sendToFd($fd, $cmd, $code, $send);
                            $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                            $noLibao = $noJiuji = 0;
                        }
                        
                        //扫码下载手机版
                        if (!in_array($user['channel'], ['sjyoujoy', 'sjyishiteng', 'sjiosappstore', 'sjzimo', 'sjiosxy', 'sjaiyouxi', 'sjmigu', 'sjweichat', 'sjiosappstore', 'sjyybweikandian'])) {
                            if ($trial['trial_count'] >= 2) {
                                sendToFd($fd, 4, 120, ['id' => 111, 'price' => 0, 'goto' => 0, 'msg' => "", 'title' => '扫码下载手机版', 'sub' => '领取10000乐豆', 'type' => 0, 'img' => 'http://gt2.youjoy.tv/ddzgamefile/qrcode/2.png']);
                            }
                        }
                    }
                }
                //免责金牌
                if ($noLibao && $los >= $rdc['mianze'] && $this->model->redis->hget('lord_libao_mianze_times_' . $dd, $uid) < 3 && $user["channel"]!="ahtelecom") {
                    $conf = $this->model->getGoodsCtrl('mianzejinpai', $T["seat{$sid}channel"], 0, $rd);
                    if (!$goods) $goods = $this->model->getlistGoods('', 1);
                    if ($conf && isset($goods[$conf['id']])) {
                        $cmd = 4;
                        $code = 168;
                        $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => $conf['anim'], 'goto' => $conf['goto'], 'coins' => $los];
                        // $this->model->sendToFd($fd, $cmd, $code, $send);
                        $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                        $noLibao = 0;
                        //加入礼包面板 向高覆盖
                        $isModify = $isTitle = 0;
                        if ($tmp = $this->model->redis->hget('lord_libao_' . $dd, $uid)) {
                            foreach ($tmp as $k => $v) {
                                if ($v['title'] == $send['title']) {
                                    $isTitle = 1;
                                    if ($v['price'] < $send['price'] || $v['coins'] < $send['coins']) {
                                        unset($tmp[$k]);
                                        $tmp[$send['title'] . '_' . $send['price'] . '_' . $send['id']] = $send;
                                        $isModify = 1;
                                        break;
                                    }
                                }
                            }
                            if (!$isTitle) {
                                $tmp[$send['title'] . '_' . $send['price'] . '_' . $send['id']] = $send;
                                $isModify = 1;
                            }
                        } else {
                            $tmp = [$send['title'] . '_' . $send['price'] . '_' . $send['id'] => $send];
                            $isModify = 1;
                        }
                        $isModify && $this->model->redis->hset('lord_libao_' . $dd, $uid, $tmp);//
                        $this->model->redis->hadd('lord_libao_mianze_times_' . $dd, $uid);
                    }
                }
                //连败礼包
                if ($noLibao && in_array($stlos, $rdc['stlos']) && $user["channel"]!="ahtelecom") {
                    $conf = $this->model->getGoodsCtrl('lianbailibao', $T["seat{$sid}channel"], 0, $rd, $stlos);
                    if (!$goods) $goods = $this->model->getlistGoods('', 1);
                    if ($conf && isset($goods[$conf['id']])) {
                        $cmd = 4;
                        $code = 168;
                        $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => $conf['anim'], 'goto' => $conf['goto']];
                        // $this->model->sendToFd($fd, $cmd, $code, $send);
                        $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                        $noLibao = 0;
                    }
                }
                //幸运牌局
                if ($noLibao && mt_rand(0, 9999) < 999 && $this->model->redis->hget('lord_libao_xingyun_times_' . $dd, $uid) < 3 && $user["channel"]!="ahtelecom") {
                    $conf = $this->model->getGoodsCtrl('xingyunpaiju', $T["seat{$sid}channel"], 0, $rd);
                    if (!$goods) $goods = $this->model->getlistGoods('', 1);
                    if ($conf && isset($goods[$conf['id']])) {
                        $cmd = 4;
                        $code = 168;
                        $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => $conf['anim'], 'goto' => $conf['goto']];
                        // $this->model->sendToFd($fd, $cmd, $code, $send);
                        $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                        $noLibao = 0;
                        //加入礼包面板 并列
                        $tmp = $this->model->redis->hget('lord_libao_' . $dd, $uid);
                        if (!$tmp) $tmp = [];
                        $tmp[$send['title'] . '_' . $send['price'] . '_' . $send['id']] = $send;
                        $this->model->redis->hset('lord_libao_' . $dd, $uid, $tmp);//
                        $this->model->redis->hadd('lord_libao_xingyun_times_' . $dd, $uid);
                    }
                }
                //世博云
                if ($noLibao && $T["seat{$sid}channel"] === 'shiboyun') {
                    if ($sid != $lord && (!isset($Utesk['teskdone_10253']) || !$Utesk['teskdone_10253']) && $this->model->redis->hget('lord_libao_dznm_times_' . $dd, $uid) < 5) {
                        $conf = $this->model->getGoodsCtrl('nongminlibao', $T["seat{$sid}channel"]);
                        if (!$goods) $goods = $this->model->getlistGoods('', 1);
                        if ($conf && isset($goods[$conf['id']])) {
                            $cmd = 4;
                            $code = 168;
                            $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => $conf['anim'], 'goto' => $conf['goto']];
                            // $this->model->sendToFd($fd, $cmd, $code, $send);
                            $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                            $this->model->redis->hadd('lord_libao_dznm_times_' . $dd, $uid);
                            $noLibao = 0;
                        }
                    }
                    if ($sid == $lord && (!isset($Utesk['teskdone_10252']) || !$Utesk['teskdone_10252']) && $this->model->redis->hget('lord_libao_dznm_times_' . $dd, $uid) < 5) {
                        $conf = $this->model->getGoodsCtrl('dizhulibao', $T["seat{$sid}channel"]);
                        if (!$goods) $goods = $this->model->getlistGoods('', 1);
                        if ($conf && isset($goods[$conf['id']])) {
                            $cmd = 4;
                            $code = 168;
                            $send = ['isPush' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'title' => $conf['title'], 'bar' => $conf['bar'], 'anim' => $conf['anim'], 'goto' => $conf['goto']];
                            // $this->model->sendToFd($fd, $cmd, $code, $send);
                            $this->model->sendToPlayer($player, $cmd, $code, $send, 0);
                            $this->model->redis->hadd('lord_libao_dznm_times_' . $dd, $uid);
                            $noLibao = 0;
                        }
                    }
                }
            }//结束输赢礼包逻辑
            //非手机版用户，用户在没有点击“不再提示”按钮的情况下，经典场赖子场的非新手场5局后，如果没有弹出任何礼包，则必须弹出一个二维码。
            if ( !in_array($rd,array(1000,1007)) && ! ( strpos($user['channel'], 'sj') === 0 ) ) {
            	$qrcodeid = 0;
            	$qrcode = $this->model->redis->hadd("lord_libao_qrcode{$qrcodeid}_times_{$dd}", $uid);
            	if ( $noLibao && $noJiuji && $qrcode >= 5 ) {
            		$this->model->redis->hset("lord_libao_qrcode{$qrcodeid}_times_{$dd}",$uid,0);
            		$cmd = 4; $code = 120; $send = array('type'=>$qrcodeid,'id'=>111,'title'=>'可领取10000乐豆！','sub'=>'微信扫码下载手机版','img'=>'http://gt2.youjoy.tv/ddzgamefile/qrcode/003.png');
            		$this->model->sendToPlayer($player, $cmd, $code, $send, 0);
            	}
            }
            //比赛场可以在报名后继续玩经典场癞子场游戏，但要提醒用户入场
            if ($user['modelId'] == 3 && $user['gameId']) {
                $isRegame = 0;
                if ($G = $this->match->getGame($user['modelId'], 0, $user['gameId'])) {
                    $getStart = $this->match->getStart($G, 1);
                    if (!$getStart || $getStart - time() < $this->confs['time_match_before_start']) {
                        $cmd = 5;
                        $code = 222;
                        $send = ['errno' => 0, 'error' => "您已报名的比赛就快开始，\n请前往比赛场。", 'modelId' => $G['modelId'], 'roomId' => $G['roomId']];
                        sendToFd($fd, $cmd, $code, $send);
                    }
                }
            }
        }//End foreach
        //牌桌记录
        $this->record->table($T);
        //同步牌桌玩家乐豆信息
        foreach ($T['seats'] as $uid => $sid) {
            if ($T["seat{$sid}robot"]) continue;
            $cmd = 5;
            $code = 430;
            sendToFd($T["seat{$sid}fd"], $cmd, $code, $coins);
        }
        //重设牌桌信息
        $newT['state'] = 2;
        $newT['rate'] = $this->rooms[$rd]['rate'];
        $newT['rate_'] = 0;
        $newT['firstShow'] = 4;
        $newT['lordSeat'] = 4;
        $newT['turnSeat'] = 4;
        $newT['lastCall'] = 4;
        $newT['lastCards'] = [];
        $newT['outCards'] = [];
        $newT['lordCards'] = [];
        $newT['noFollow'] = 0;
        $newT['isRegame'] = $isRegame;//是否再来一局
        $newT['isNewGame'] = 0;
        $newT['noteCards'] = "S1M124A4K4Q4J4T494847464544434";
        $newT['seat_rakes'] = ['2' => 0, '1' => 0, '0' => 0];
        $newT['shuffle'] = 0;
        foreach ($T['seats'] as $uid => $sid) {
            $newT["seat{$sid}show"] = 0;
            $newT["seat{$sid}state"] = 47;//SYS结算
            $newT["seat{$sid}queue"] = -1;
            $newT["seat{$sid}cards"] = [];
            $newT["seat{$sid}hands"] = [];
            $newT["seat{$sid}rate"] = -1;
            $newT["seat{$sid}trust"] = $T["seat{$sid}trust"] == 4 ? 4 : 0;//待续优化
            $newT["seat{$sid}delay"] = 0;
            $newT["seat{$sid}sent"] = 0;
            $newT["seat{$sid}task"] = [];
            $newT["seat{$sid}ttdone"] = 0;
            $newT["seat{$sid}ttcoupon"] = 0;
        }
        $addT && $this->model->incTableInfo($td, $addT);
        $newT && $this->model->setTableInfo($td, $newT);
        //闹钟 - 到期重来
        setTimer($td, "WAIT_TOTAL_DONE", ['tableId' => $td, 'isRegame' => $isRegame], $this->confs['time_table_total'] * 1000, $T['hostId']);
        return true;
    }

    function WAIT_TOTAL_DONE($params)
    {
        if ($params['isRegame'] || (ISPRESS && !ISROBOT)) {
            $res = $this->ROBOT_READY($params['tableId']);
        } else {
            $res = $this->TABLE_BREAK($params);
        }
        return true;
    }

    // 新版赛制结算
    function MATCH_GAME_OVER($T)
    {
        $state = 7;
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $gd = $T['gameId'];
        $td = $T['tableId'];
        $winner = $T['lastCall'];
        $lord = $T['lordSeat'];
        debug("赛桌结算开始 M=$md R=$rd G=$gd T=$td");
        //更新牌桌状态
        $T['state'] = $state;
        $this->model->setTableState($td, $state);
        //地主赢/农民赢、春天/反春
        $isLordwin = $isLordspring = $isBoorspring = 0;
        if ($winner == $lord) {
             $isLordwin = 1;
            $isLordspring = intval(!$T["seat" . nextSeat($winner) . "sent"] && !$T["seat" . nextSeat(nextSeat($winner)) . "sent"]);
        } else {
            $isLordwin = 0;
            $isBoorspring = intval($T["seat{$lord}sent"] == 1);// && in_array(0, [$T['seat0sent'], $T['seat1sent'], $T['seat2sent']]));
        }
        if ($isLordspring) $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $winner, $this->confs['rate_lordspring']);
        if ($isBoorspring) $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $winner, $this->confs['rate_boorspring']);
        $total = $T['baseCoins'] * $T['rate'];
        //计算座位输赢及分值状况
        $data['modelId'] = $md;
        $data['total'] = ["0" => 0, "1" => 0, "2" => 0];
        if ($isLordwin) {    //地主赢
            $next = nextSeat($lord);
            $data['total'][$next] = -1 * $total;
            $prev = nextSeat($next);
            $data['total'][$prev] = -1 * $total;
            $data['total'][$lord] = 2 * $total;
        } else {            //农民赢
            $data['total'][$lord] = -2 * $total;
            $next = nextSeat($lord);
            $data['total'][$next] = $total;
            $prev = nextSeat($next);
            $data['total'][$prev] = $total;
        }
        foreach ($T['seats'] as $uid => $sid) {
            $data['isWinner'][$sid] = intval(($isLordwin && $sid == $lord) || (!$isLordwin && $sid != $lord));
            $data['score'][$sid] = max(0, $T["seat{$sid}score"] + $data['total'][$sid]);
        }
        $data['cards'] = ["0" => $T['seat0cards'], "1" => $T['seat1cards'], "2" => $T['seat2cards']];
        krsort($data['isWinner']);
        krsort($data['total']);
        krsort($data['score']);
        krsort($data['cards']);
        $seat_score = $data['score'];
        $this->model->sendToTable($T, 5, 1014, $data);
        //处理牌桌玩家数据
        foreach ($seat_score as $sid => $val) {
            $uid = $T["seat{$sid}uid"];
            $win = intval($data['isWinner'][$sid] == 1);
            setUser($uid, ['score' => $val]);
            $newT["seat{$sid}score"] = $T["seat{$sid}score"] = $val;
        }
        $newT['seat_score'] = $T['seat_score'] = $seat_score;
        $this->model->setTableInfo($td, $newT);
        unset($newT);
        //事件 - 场赛结束
        setTimer($td, "MATCH_GAME_DONE", ['td' => $td], $this->confs['time_match_table_total'] * 1000, $T['hostId']);
        return true;
        // setEvent('MATCH_GAME_DONE', array('td'=>$td), $this->confs['time_table_total'] * 1000, $T['hostId']);
        // return true;
    }

    // 新版赛制结算完毕
    function MATCH_GAME_DONE($ps)
    {
        $td = $ps['td'];
        $T = $this->model->getTableInfo($td);
        if (!$td || !$T) return gerr("赛结牌桌无效[$td] table=" . json_encode($T));
        $this->TABLE_BREAK($T, 1);
        $this->match->total($T);
    }

    // 新版赛制处理奖品
    function MATCH_SET_PRIZE($ps)
    {
        $ud = intval($ps['u']);
        $U = $this->model->getUserInfo($ud);
        return $this->model->userPrize($ud, $ps['p'], $U, '新赛发奖');
    }

    // 新版赛制初始排名
    function MATCH_GET_RANK0($ps)
    {
        return $this->match->rank0($ps['G']);
    }

    //[SYS]	竞技结束
    function MODEL_GAME_OVER($T)
    {
        $state = 7;
        //参数整理
        $tmid = time();
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $gd = $T['gameId'];
        $mrwg = $T['gamesId'];
        $td = $T['tableId'];
        $winner = $T['lastCall'];
        $lord = $T['lordSeat'];
        //更新牌桌状态
        $newT['state'] = $T['state'] = $state;
        $res = $this->model->setTableState($td, $state);
        debug("赛桌结算开始[$mrwg|$td]");
        //地主赢/农民赢、春天/反春
        $isLordwin = $isLordspring = $isBoorspring = 0;
        if ($winner == $lord) {
            $isLordwin = 1;
            $isLordspring = intval(!$T["seat" . nextSeat($winner) . "sent"] && !$T["seat" . nextSeat(nextSeat($winner)) . "sent"]);
        } else {
            $isLordwin = 0;
            $isBoorspring = intval($T["seat{$lord}sent"] == 1);// && in_array(0, [$T['seat0sent'], $T['seat1sent'], $T['seat2sent']]));
        }
        //变更牌桌倍率
        if ($isLordspring) {
            $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $winner, $this->confs['rate_lordspring']);
        } elseif ($isBoorspring) {
            $newT['rate'] = $T['rate'] = $this->TABLE_NEW_RATE($T, $winner, $this->confs['rate_boorspring']);
        }
        //竞技场不存在输光因素
        $total = $T['baseCoins'] * $T['rate'];
        //计算座位输赢及分值状况
        $data['modelId'] = $md;
        $data['total'] = ["0" => 0, "1" => 0, "2" => 0];
        $data['coins'] = ["0" => $T["seat0coins"], "1" => $T["seat1coins"], "2" => $T["seat2coins"]];
        if ($isLordwin) {    //地主赢
            $next = nextSeat($lord);
            $data['scoreTotal'][$next] = -1 * $total;
            $prev = nextSeat($next);
            $data['scoreTotal'][$prev] = -1 * $total;
            $data['scoreTotal'][$lord] = 2 * $total;
            //处理竞技场逃跑事件
            $lord_giveup = $T["seat{$lord}giveup"];
            $next_giveup = $T["seat{$next}giveup"];
            $prev_giveup = $T["seat{$prev}giveup"];
            if ($lord_giveup) {                            //只要地主逃跑
                $data['scoreTotal'][$lord] = 0;
                $data['scoreTotal'][$next] = 0;
                $data['scoreTotal'][$prev] = 0;
            } elseif ($next_giveup && !$prev_giveup) {    //下家农民逃跑
                $data['scoreTotal'][$next] = -2 * $total;
                $data['scoreTotal'][$prev] = 0;
            } elseif ($prev_giveup && !$next_giveup) {    //上家农民逃跑
                $data['scoreTotal'][$prev] = -2 * $total;
                $data['scoreTotal'][$next] = 0;
            }
        } else {            //农民赢
            $data['scoreTotal'][$lord] = -2 * $total;
            $next = nextSeat($lord);
            $data['scoreTotal'][$next] = $total;
            $prev = nextSeat($next);
            $data['scoreTotal'][$prev] = $total;
            //处理竞技场逃跑事件
            $lord_giveup = $T["seat{$lord}giveup"];
            $next_giveup = $T["seat{$next}giveup"];
            $prev_giveup = $T["seat{$prev}giveup"];
            if ($next_giveup && $prev_giveup) {            //农民全部逃跑
                $data['scoreTotal'][$lord] = 0;
                $data['scoreTotal'][$next] = 0;
                $data['scoreTotal'][$prev] = 0;
            } elseif ($next_giveup && !$prev_giveup) {    //下家农民逃跑
                $data['scoreTotal'][$lord] = -1 * $total;
                $data['scoreTotal'][$next] = 0;
            } elseif ($prev_giveup && !$next_giveup) {    //上家农民逃跑
                $data['scoreTotal'][$lord] = -1 * $total;
                $data['scoreTotal'][$prev] = 0;
            }
        }
        foreach ($T['seats'] as $uid => $sid) {
            $data['isWinner'][$sid] = intval(($isLordwin && $sid == $lord) || (!$isLordwin && $sid != $lord));
            $data['score'][$sid] = max(0, $T["seat{$sid}score"] + $data['scoreTotal'][$sid]);
            //处理竞技场逃跑事件
            $giveup = $T["seat{$sid}giveup"];
            if ($giveup) {
                $data['score'][$sid] = 0;
            }
        }
        $data['cards'] = ["0" => $T['seat0cards'], "1" => $T['seat1cards'], "2" => $T['seat2cards']];//剩下的牌
        $data['giveup'] = ["0" => $T['seat0giveup'], "1" => $T['seat1giveup'], "2" => $T['seat2giveup']];//竞技逃跑
        krsort($data['isWinner']);
        krsort($data['total']);
        krsort($data['coins']);
        krsort($data['scoreTotal']);
        krsort($data['score']);
        krsort($data['cards']);
        krsort($data['giveup']);
        $seat_score = $data['score'];
        //通知牌桌: 开始结算
        $cmd = 5;
        $code = 1014;
        $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        //处理牌桌玩家数据
        foreach ($seat_score as $sid => $val) {
            $uid = $T["seat{$sid}uid"];
            $win = intval($data['isWinner'][$sid] == 1);
            $newU['score'] = $val;
            // $newU['gameStart'] = $U['gameStart'] = 0;
            $newU && setUser($uid, $newU);
            unset($newU);
            $newT["seat{$sid}score"] = $T["seat{$sid}score"] = $val;
        }
        $newT['seat_score'] = $T['seat_score'] = $seat_score;
        $newT && $this->model->setTableInfo($td, $newT);
        unset($newT);
        //事件 - 场赛结束
        $sceneId = $td;
        $act = "MODEL_GAME_DONE";
        $params = ['tableId' => $td];
        $delay = $this->confs['time_table_total'] * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
        return true;
    }

    //[SYS]	竞技结束
    function MODEL_GAME_DONE($params)
    {
        $td = $params['tableId'];
        $T = $this->model->getTableInfo($td);
        if (!$td || !$T) {
            gerr("赛结牌桌无效[$td] table=" . json_encode($T));
            return false;
        }
        $cmd = 5;//
        $tmid = time();
        $md = $T['modelId'];
        $rd = $T['roomMock'];
        $wd = $T['weekId'];
        $gd = $T['gameId'];
        $mrwg = $T['gamesId'];
        $td = $T['tableId'];
        $winner = $T['lastCall'];
        $seat_score = $T['seat_score'];
        //执行散桌
        $this->TABLE_BREAK($T, 1);
        //加事务锁 场次变动
        $lock = 'GAMESID_' . $mrwg;
        $res = setLock($lock);
        if (!$res) {
            gerr("[LOCKON] lockId=$lock func=" . __FUNCTION__);
            return false;
        }
        //获取赛场信息
        $gameNew = 0;
        $game = $this->model->getModelGame($md, $rd, $wd, $gd);
        if (isset($game['tableIds'][$td])) {
            $gameNew = 1;
            unset($game['tableIds'][$td]);
        }
        //判断是否还有下一局
        $isNextGame = 1;
        if (($game['gameStart'] + $game['gameEndTime']) <= $tmid || $game['gamePlay'] <= $game['gameWinner']) {
            $isNextGame = 0;
        }
        foreach ($seat_score as $sid => $val) {
            $uid = $T["seat{$sid}uid"];
            $U = $this->model->getUserInfo($uid);
            if (!$U) {
                $T["seat{$sid}fd"] = 0;
                $T["seat{$sid}info"]["fd"] = 0;
                $U = $T["seat{$sid}info"];
            } else {
                $T["seat{$sid}fd"] = $U['fd'];
                $T["seat{$sid}info"]["fd"] = $U['fd'];
            }
            $users[$uid] = $U;//
            if ($T["seat{$sid}giveup"] || $users[$uid]['giveup'] || ($isNextGame && $val < $game['gameScoreOut'])) {
                $gameNew = 1;
                $game['gamePlay']--;
            }
        }
        if ($gameNew) {
            if (isset($game['thisWeekRank'])) unset($game['thisWeekRank']);
            if (isset($game['lastWeekRank'])) unset($game['lastWeekRank']);
            $this->model->setModelGame($md, $rd, $wd, $gd, $game);
        }
        //解事务锁 场次变动
        delLock($lock);
        if ($game['gamePlay'] <= $game['gameWinner']) {    //判断剩余人数
            $isNextGame = 0;
        }
        //处理继续或淘汰
        $len = 0;
        foreach ($seat_score as $sid => $val) {
            $uid = $T["seat{$sid}uid"];
            $fd = $users[$uid]['fd'];
            $gameplay = $this->model->getModelGamePlay($mrwg, $uid);
            $gameplay['fd'] = $fd;
            $gameplay['score'] = $val;
            if ($T["seat{$sid}giveup"] || $users[$uid]['giveup']) {    //放弃
                debug("赛场用户放弃[$mrwg|$uid]");
                $seat_score[$sid] = 0;
                $gameplay['score'] = $val;
                $gameplay['giveup'] = 1;
                $gameplay['deadTime'] = $tmid;
                $gameplay['update_time'] = $tmid;
                //通知用户: 已被淘汰
                $cmd = 5;
                $code = 112;
                $data = [];
                $data['errno'] = 0;
                $data['error'] = "本局您被淘汰，不会获得排名和奖励；\n请再接再厉！";
                $data['modelId'] = $md;
                $data['gameId'] = $gd;
                $data['score'] = 0;
                $res = sendToFd($fd, $cmd, $code, $data);
            } elseif ($isNextGame && $val >= $game['gameScoreOut']) {    //有且可以参加下一局: 重设用户赛场数据，加入新桌队列
                debug("赛场用户继续[$mrwg|$uid]");
                $gameplay['update_time'] = $tmid;
                $res = $this->model->addModelGoonPlay($mrwg, $gameplay);
                $len = $res + 1;
            } elseif (($isNextGame && $val < $game['gameScoreOut'])) {    //淘汰
                debug("赛场用户淘汰[$mrwg|$uid]");
                $gameplay['deadTime'] = $tmid;
                $gameplay['update_time'] = $tmid;
                //通知用户: 已被淘汰
                $cmd = 5;
                $code = 112;
                $data = [];
                $data['errno'] = 0;
                $data['error'] = "很遗憾您的竞技豆目前低于" . $game['gameScoreOut'] . "；\n本局您被淘汰，不会获得排名和奖励；\n请再接再厉！";
                $data['modelId'] = $md;
                $data['gameId'] = $gd;
                $data['score'] = ($T["seat{$sid}giveup"] || $users[$uid]['giveup']) ? 0 : $val;
                $res = sendToFd($fd, $cmd, $code, $data);
            } else {    //正常结束
                debug("赛场用户结束[$mrwg|$uid]");
                $gameplay['overTime'] = $tmid;
                $gameplay['update_time'] = $tmid;
                //通知用户: 本场结束
                $cmd = 5;
                $code = 114;
                $data_ = [];
                $data['errno'] = 0;
                $data['error'] = "本场比赛结束，恭喜您还剩余" . $val . "竞技豆；\n如果您的竞技豆排名能进入前九榜单，\n那么对应的名次奖励将稍后通知；\n请继续游戏哦~";//等待其他牌桌打完
                $data['modelId'] = $md;
                $data['gameId'] = $gd;
                $data['score'] = $val;
                $res = sendToFd($fd, $cmd, $code, $data);
            }
            $res = $this->model->setModelGamePlay($mrwg, $uid, $gameplay);
            //清理掉非继续的用户的参赛信息
            if (!($isNextGame && $val >= $game['gameScoreOut']) || $T["seat{$sid}giveup"] || $users[$uid]['giveup']) {
                if ($users[$uid]['robot']) {
                    $this->model->desRobot($uid, __LINE__);
                } elseif ($fd) {
                    $newU = ['gameplayId' => '', 'gamesId' => '', 'modelId' => 0, 'roomId' => 0, 'weekId' => 0, 'gameId' => 0, 'joinTime' => 0, 'score' => 0];
                    if ($T["seat{$sid}giveup"]) $newU['giveup'] = 0;
                    $res = setUser($uid, $newU);
                    unset($newU);
                } else {
                    $U = $users[$uid];
                    $res = $this->model->desUserInfo($uid, $U, __LINE__);
                }
            }
        }
        //处理下一局
        if ($isNextGame) {
            if ($len >= 3) {
                //事件 - 赛场轮桌
                $act = "MODEL_GAME_GOON";
                $params = ['gamesId' => $game['gamesId'], 'roomReal' => $game['roomReal']];
                $delay = 0;//待续
                setEvent($act, $params, $delay);
            }
            debug("赛场还有新局[$mrwg|$td]");
            return true;
        }
        //处理待完局
        if ($game['tableIds']) {
            debug("赛场还有老局[$mrwg|$td] " . json_encode($game['tableIds']));
            return true;
        }
        //没有下一局，没有待完局，处理结束及发奖
        debug("赛场开始结算[$mrwg|$td]");
        //清理再来队列
        while ($gameplays = $this->model->getModelGoonPlay($mrwg, 1)) {
            $gameplay = $gameplays[0];//多维数组
            $val = $gameplay['score'];
            $uid = $gameplay['uid'];
            $U = $this->model->getUserInfo($uid);
            if ($U && $U['fd']) {
                $newU = ['gameplayId' => '', 'gamesId' => '', 'modelId' => 0, 'roomId' => 0, 'weekId' => 0, 'gameId' => 0, 'joinTime' => 0, 'score' => 0];
                $res = setUser($uid, $newU);
                unset($newU);
                debug("赛场用户清出[$mrwg|$uid]");
                //通知用户: 本场结束
                $cmd = 5;
                $code = 114;
                $data_ = [];
                $data_['errno'] = 0;
                $data_['error'] = "本场比赛结束，恭喜您还剩余" . $val . "竞技豆；\n如果您的竞技豆排名能够进入前九榜单，\n那么对应的名次奖励将稍后通知；\n请继续游戏哦~";//等待其他牌桌打完
                $data_['modelId'] = $md;
                $data_['gameId'] = $gd;
                $data_['score'] = $val;
                $res = $this->model->sendToUser($U, $cmd, $code, $data_);
            } elseif ($U && $U['robot']) {
                $res = $this->model->desRobot($uid, __LINE__);
            } else {
                $res = $this->model->desUserInfo($uid, $U, __LINE__);
            }
            // $res = $this->model->delUserModel($uid);
            $gameplay['overTime'] = $tmid;
            $gameplay['update_time'] = $tmid;
            $res = $this->model->setModelGamePlay($mrwg, $uid, $gameplay);
        }
        //处理赛场结束
        $gamePlayAll = $this->model->getModelGamePlayAll($mrwg);
        $gamePlayer = $gameScore = $gameJoin_ = $gameRobots = [];
        foreach ($gamePlayAll as $k => $v) {
            if (isset($v['giveup']) && $v['giveup']) continue;
            $uid = $v['uid'];
            $gamePlayer[$uid] = $v;
            $gameScore[$uid] = $v['score'];
            $gameJoin_[$uid] = $v['joinTime'];
            if ($v['robot']) {
                $gameRobots[] = $v['uid'];
                continue;
            }
            // 开始用户统计
            $Utask = $this->model->getUserTask($uid);
            // 竞技场游戏次数
            $addUT['match_all_play'] = 1;
            $addUT['match_week_play'] = 1;
            $addUT['match_day_play'] = 1;
            // 累加入榜 竞技场参与次数榜单 周、日
            // $res = $this->model->zMatchPlay($uid, 1);// 暂不入榜
            // 更新用户统计信息
            $res = $this->model->incUserTask($uid, $addUT);
            // 下面三行代码没用，所以注释掉
            // $Utask['match_all_play']++;
            // $Utask['match_week_play']++;
            // $Utask['match_day_play']++;
            // 结束用户统计
        }
        unset($gamePlayAll);
        array_multisort($gameScore, SORT_DESC, $gameJoin_, SORT_DESC, $gamePlayer);
        $gamePlayert = $gamePlayer;//排序结果丢失key了
        $gamePlayer = $gameNick = $gameScore = $gameJoin_ = $gameRobot = [];
        foreach ($gamePlayert as $k => $v) {
            $gamePlayer[$v['uid']] = $v;
            $gameScore[$v['uid']] = $v['score'];
            $gameJoin_[$v['uid']] = $v['joinTime'];
            $gameNick[$v['uid']] = $v['nick'];
            $gameRobot[$v['uid']] = $v['robot'];
        }
        arsort($gameScore);//再次高低排序
        //处理奖项数据
        $prizePoint = [];
        foreach ($game['gamePrizePoint'] as $k => $v) {
            $r = explode('-', $k);
            $i = $r[0];
            $j = isset($r[1]) ? $r[1] : $i;
            for (; $i <= $j; $i++) {
                $prizePoint[$i] = $v;
            }
        }
        $prizeCoins = [];
        foreach ($game['gamePrizeCoins'] as $k => $v) {
            $r = explode('-', $k);
            $i = $r[0];
            $j = isset($r[1]) ? $r[1] : $i;
            for (; $i <= $j; $i++) {
                $prizeCoins[$i] = $v;
            }
        }
        $prizeCoupon = [];
        foreach ($game['gamePrizeCoupon'] as $k => $v) {
            $r = explode('-', $k);
            $i = $r[0];
            $j = isset($r[1]) ? $r[1] : $i;
            for (; $i <= $j; $i++) {
                $prizeCoupon[$i] = $v;
            }
        }
        $prizeProps = [];
        foreach ($game['gamePrizeProps'] as $k => $v) {
            $r = explode('-', $k);
            $i = $r[0];
            $j = isset($r[1]) ? $r[1] : $i;
            for (; $i <= $j; $i++) {
                $prizeProps[$i] = $v;
            }
        }
        //处理赛场奖励
        $i = 1;
        $gamePrize = $gameRank = $gamePrizeCoins = $gamePrizeCoupon = $gamePrizePoint = $gamePrizeProps = [];
        foreach ($gameScore as $uid => $v) {
            if ($i > $game['gameWinner'] || $v < $game['gameScoreOut']) break;
            if (isset($prizeCoins[$i])) $gamePrizeCoins[$uid] = $gamePrize[$uid]['coins'] = $prizeCoins[$i];
            if (isset($prizeCoupon[$i])) $gamePrizeCoupon[$uid] = $gamePrize[$uid]['coupon'] = $prizeCoupon[$i];
            if (isset($prizePoint[$i])) $gamePrizePoint[$uid] = $gamePrize[$uid]['point'] = $prizePoint[$i];
            if (isset($prizeProps[$i])) $gamePrizeProps[$uid] = $gamePrize[$uid]['items'] = $prizeProps[$i];
            $gameRank[$uid] = $i;
            if ($i == 1) $gameNick = $gameNick[$uid];
            $i++;
        }
        //处理赛场结束
        $game['gameRank'] = $gameRank;
        $game['gamePrize'] = $gamePrize;
        $game['gameScore'] = $gameScore;
        $game['gameNick'] = $gameNick;//第一名
        $game['gameRobot'] = $gameRobot;
        $game['gamePrizeCoins'] = $gamePrizeCoins;
        $game['gamePrizeCoupon'] = $gamePrizeCoupon;
        $game['gamePrizePoint'] = $gamePrizePoint;
        $game['gamePrizeProps'] = $gamePrizeProps;
        $game['gameOver'] = $tmid;
        //入库赛场信息		//入库赛场用户
        $res = $this->model->insModelGame($md, $rd, $wd, $gd, $game);
        $res = $this->model->insModelGamePlay($mrwg, $gamePlayer);
        debug("赛场发奖稍候[$mrwg|$td]");
        //事件 - 场赛发奖
        $sceneId = $td;
        $act = "MODEL_GAME_PRIZE";
        $params = $game;
        $delay = $this->confs['time_' . strtolower($act)] * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
        $gameRanknum = $game['gameRanknum'];
        $weekPeriod = $game['weekPeriod'];
        unset($game);
        unset($params);
        //进行周赛数据及用户任务数据更新
        debug("赛周数据更新[$mrwg|$td]");
        //加事务锁 周赛变动
        $lock2 = 'WEEKID_' . $wd;
        $res = setLock($lock2);
        if (!$res) {
            gerr("[LOCKON] lockId2=$lock2 gamesId=$mrwg TD=$td");
            return false;
        }
        //更新赛周用户积分
        $tasker = null;
        foreach ($gamePrizePoint as $uid => $v) {
            $data = $this->model->getModelWeekPlay($md, $rd, $wd, $uid);
            if (!$data) {
                $data = [
                    'weekplayId'     => $md . '_' . $rd . '_' . $wd . '_' . $uid,
                    'modelId'        => $md,
                    'roomId'         => $rd,
                    'weekId'         => $wd,
                    'uid'            => $uid,
                    'nick'           => isset($gamePlayer[$uid]) ? $gamePlayer[$uid]['nick'] : '',
                    'cool_num'       => isset($gamePlayer[$uid]) ? $gamePlayer[$uid]['cool_num'] : 0,
                    'weekPoint'      => 0,
                    'weekRank'       => 0,
                    'weekPrizeExp'   => 0,
                    'weekPrizeCoins' => 0,
                    'weekPrizeProps' => [],
                    'create_time'    => $tmid,
                    'update_time'    => 0,
                ];
            }
            $data['weekPoint'] += $v;
            $data['update_time'] = $tmid;
            $res = $this->model->setModelWeekPlay($md, $rd, $wd, $uid, $data);
            if (in_array($uid, $gameRobots)) continue;
            // 开始用户统计
            $Utask = $this->model->getUserTask($uid);
            // 竞技场积分数量
            $addUT['match_all_point'] = $v;
            $addUT['match_week_point'] = $v;
            $addUT['match_day_point'] = $v;
            // 累加入榜 竞技场积分数量榜单 周、日
            $res = $this->model->zMatchPoint($uid, $v);
            // 更新用户统计信息
            $res = $this->model->incUserTask($uid, $addUT);
            $Utask['match_all_point'] += $v;
            $Utask['match_week_point'] += $v;
            $Utask['match_day_point'] += $v;
            // 结束用户统计
            $U = $this->model->getUserInfo($uid);
            if (!$U) {
                $U = $this->model->getUserData($uid);
                if (!$U) continue;
                $U = array_merge($U, ['modelId' => 0, 'weekId' => 0, 'gameId' => 0, 'gamesId' => '', 'gameplayId' => '', 'joinTime' => 0, 'score' => 0, 'roomId' => 0, 'tableId' => 0, 'seatId' => 0]);
                $U = array_merge($U, ['fd' => 0, 'dateid' => 0, 'isShowcard' => 0, 'robot' => 0, 'trial_count' => 0, 'trial_daily' => 0, 'play' => 0, 'win' => 0, 'Lcoins' => $U['coins'], 'Lcoupon' => $U['coupon'], 'Lgold' => $U['gold'], 'Ltime' => 0, 'last_action' => '', 'last_time' => 0]);
            }
            // 开始执行任务: 今日N竞技分，奖励一次抽奖机会
            if ($Utask['match_day_point'] >= 20) {
                $taskid = 3;
                if ($tasker === null) $tasker = new task($this->model, $taskid, 0, 0, $this->is_freshtask);
                if ($res = $tasker->run($U, $Utask)) {
                    debug("任务赛场日满[$fd|$uid|$td|$sid] taskid=$taskid");
                    $U = array_merge($U, isset($res[$taskid]['userinfo']) ? $res[$taskid]['userinfo'] : []);
                    $Utask = array_merge($Utask, isset($res[$taskid]['usertask']) ? $res[$taskid]['usertask'] : []);
                }
                if (!$U['fd']) {
                    $sql = "UPDATE `lord_game_user` SET `lottery` = `lottery` + 1 WHERE `uid` = $uid";
                    bobSql($sql);
                }
            }
            // 结束执行任务:
        }
        //刷新本周积分排名
        //待优化 使用redis榜单
        $weekPlayAll = $this->model->getModelWeekPlayAll($md, $rd, $wd);
        $weekPoint = $weekRank = [];
        foreach ($weekPlayAll as $k => $v) {
            if (!$v['weekPoint']) continue;
            $weekPoint[$v['weekPoint']] = ['uid' => $v['uid'], 'nick' => $v['nick'], 'point' => $v['weekPoint']];
        }
        unset($weekPlayAll);
        if ($weekPoint) {
            krsort($weekPoint);
            $weekRank = array_slice($weekPoint, 0, $gameRanknum);
            unset($weekPoint);
            foreach ($weekRank as $k => $v) {
                $weekRank[$k]['rank'] = $k + 1;
            }
            $weekRank = array_values($weekRank);
        }
        $week = $this->model->getModelWeek($md, $rd, $wd);
        if (!$week) {
            $date = str_split($wd, 2);
            $weekStart = strtotime($date[0] . $date[1] . '-' . $date[2] . '-' . $date[3] . ' 00:00:00');
            $week = [
                'weeksId'        => $md . '_' . $rd . '_' . $wd,
                'modelId'        => $md,
                'roomId'         => $rd,
                'weekId'         => $wd,
                'weekPool'       => 0,
                'weekRank'       => [],
                'weekPrizeCoins' => [],
                'weekPrizeProps' => [],
                'weekStart'      => $weekStart,
                'weekEnd'        => $weekStart + intval($weekPeriod * 86400) - 1,
            ];
        }
        // $week['weekPool'] -= array_sum($gamePrizeCoins);//V10500
        $week['weekRank'] = $weekRank;
        $res = $this->model->setModelWeek($md, $rd, $wd, $week);
        //解事务锁 周赛变动
        delLock($lock2);
        return true;
    }

    //[SYS]	某人托管
    function USER_ENTRUST($fd, $T, $sd, $state)
    {
        $td = $T['tableId'];
        $ud = $T["seat{$sd}uid"];
        //通知牌桌: 有人托管
        $cmd = 5;
        $code = 1028;
        $data = [];
        $data['giveup'] = 0;
        //设置托管状态		//state: 0没有托管1主动托管2延时托管3退房托管4掉线托管
        $newT["seat{$sd}trust"] = $T["seat{$sd}trust"] = $state;
        if ($state == 3 || $state == 4) {    //退房托管或掉线托管时，不向用户发送牌桌消息
            $newT["seat{$sd}fd"] = $T["seat{$sd}fd"] = 0;
            if ($state == 4) {
                $newT["seat{$sd}giveup"] = $T["seat{$sd}giveup"] = 0;
                $data['giveup'] = 1;
            }
        }
        //更新牌桌信息
        $res = $this->model->setTableInfo($td, $newT);
        if (!$res) {
            gerr("用户托管失败 F=$fd U=$ud R=$rd T=$td state=" . $T["seat{$sd}trust"] . "->" . $state);
            return false;
        }
        $data['trustId'] = $sd;
        $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        //修改牌桌上对应座位计时器 - 不管什么任务
        //闹钟 - 修改时间
        $sceneId = $td;
        $params = ['seatId' => $sd];
        $delay = $this->confs['time_trust_play'] * 1000;
        $hd = $T['hostId'];
        updTimer($sceneId, $params, $delay, $hd);
        return $T;
    }

    //[SYS]	某人解除托管
    function USER_DETRUST($fd, $T, $sd, $state)
    {
        $td = $T['tableId'];
        $ud = $T["seat{$sd}uid"];
        //通知牌桌: 有人解除托管
        $cmd = 5;
        $code = 1029;
        $send = ['trustId' => $sd];
        $res = $this->model->sendToTable($T, $cmd, $code, $send, __LINE__);
        //更新牌桌
        $newT["seat{$sd}fd"] = $fd;
        $newT["seat{$sd}trust"] = $state;
        $newT["seat{$sd}giveup"] = 0;
        //更新牌桌信息
        $res = $this->model->setTableInfo($td, $newT);
        if (!$res) return gerr("用户解托失败 F=$fd U=$ud R=$rd T=$td state=$state");
        return true;
    }

    //[SYS]	机器人自动准备
    function ROBOT_READY($td)
    {
        $T = $this->model->getTableInfo($td);
        if (!$td || !$T || $T['state'] != 2) return false;
        $md = $T['modelId'];
        $rd = $T['roomId'];
        debug("牌桌自动准备[$td]");
        $isShowcard = 0;//机器人不明牌
        foreach ($T['seats'] as $uid => $sid) {
            if (!$T['seat' . $sid . 'robot']) {
                if ($T['seat' . $sid . 'trust'] > 2 || !isset($T['seat' . $sid . 'fd']) || !$T['seat' . $sid . 'fd']) {
                    $fd = isset($T['seat' . $sid . 'fd']) ? $T['seat' . $sid . 'fd'] : '';
                    debug("机备牌桌散桌[$fd|$uid|$td|$sid]");
                    $res = $this->TABLE_BREAK($T, 1);
                    return false;
                }
                $U = $this->model->getUserInfo($uid);
                if (!$U) {
                    $fd = isset($U['fd']) ? $U['fd'] : '';
                    debug("机备用户散桌[$fd|$uid|$td|$sid]");
                    $res = $this->TABLE_BREAK($T, 1);
                    return false;
                }
                continue;
            }
            //设置机器人准备
            $T['seat' . $sid . 'state'] = 16;
            $T = $this->model->setSeatReady($T, $sid, $isShowcard);
            if (!$T) {
                gerr("机备位态失败[$td] gamer-" . __LINE__ . " setSeatReady( table, $sid, $isShowcard )<<table=" . json_encode($T));
                return false;
            }
            //通知牌桌: 我已准备
            $cmd = 5;
            $code = 1003;
            $data['readyId'] = $sid;
            $res = $this->model->sendToTable($T, $cmd, $code, $data, __LINE__);
        }
        if ($T['seat0state'] == 16 && $T['seat1state'] == 16 && $T['seat2state'] == 16) {    // 全部准备
            $res = $this->GAME_ALL_READY($T, 1);
        } else {    // 等待其他准备
            //闹钟 - 到期散桌
            $sceneId = $td;
            $act = "TABLE_BREAK";
            $params = ['tableId' => $td];
            $delay = 60 * 1000;// 一分钟之内没有人开始的话，自动散桌
            $hd = $T['hostId'];
            setTimer($sceneId, $act, $params, $delay, $hd);
        }
        return true;
    }

    //[SYS]	自动执行散桌
    function TABLE_BREAK($T, $isTable = 0)
    {
        if ($isTable) {    //直接执行
            $td = $T['tableId'];
        } else {            //执行事件
            $td = $T['tableId'];
            $T = $this->model->getTableInfo($td);
            if (!$T) {
                debug("散桌牌桌失效[$td]");
                return false;
            }
        }
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $state = $T['state'];
        if (isset($T['isStop']) && $T['isStop']) {
            debug("散桌牌桌终止[$td]");
        } elseif (!in_array($state, [2, 7])) {
            debug("散桌状态失效[$td] state27=$state");
            return false;
        }
        //清除牌桌任务
        $res = delTimer($td, $T['hostId']);
        //清除牌桌信息
        $res = $this->model->delTableInfo($td);
        //清除打牌历史
        $res = $this->model->delTableHistory($td);
        //处理牌桌用户
        foreach ($T['seats'] as $uid => $sid) {
            //牌桌机器人
            if ($T["seat{$sid}robot"]) {
                if ($md != 1 && $md != 3) {    // 非竞技 销毁假人
                    $this->model->desRobot($uid, __LINE__);
                } else {            // 竞技场 暂不处理
                }
            } //牌桌掉线托管用户
            elseif ($T["seat{$sid}trust"] == 4) {
                $U = $this->model->getUserInfo($uid);
                if ($U && $U['fd']) {    //当前在线用户
                    $newU['lastTableId'] = $U['tableId'];
                    $newU['tableId'] = $newU['seatId'] = $newU['isShowcard'] = $newU['gameStart'] = 0;
                    setUser($uid, $newU);
                } else {        //彻底掉线用户
                    if ($md != 1 && $md != 3) {    //非竞技 销毁用户
                        $res = $this->model->desUserInfo($uid, $U, __LINE__);
                    } else {            //竞技场 暂不处理
                    }
                }
            } //牌桌在线用户
            else {
                $U = $this->model->getUserInfo($uid);
                if ($U && $U['fd']) {    //当前在线用户
                    //用户信息入库
                    $this->model->updUserInfo($uid, $U);
                    //获取用户缓发数据，并优先发送
                    while ($queue = $this->model->popUserQueue($uid)) {
                        $this->model->sendToUser($U, $queue['cmd'], $queue['code'], $queue['data']);
                    }
                    //重设用户信息
                    $newU['lastTableId'] = $U['tableId'];
                    $newU['tableId'] = $newU['seatId'] = $newU['isShowcard'] = $newU['gameStart'] = 0;
                    setUser($uid, $newU);
                    //通知用户: 牌桌已散
                    $fd = $U['fd'];
                    $cmd = 5;
                    $code = 1025;
                    $data = [];
                    $res = sendToFd($fd, $cmd, $code, $data);
                } else {        //彻底掉线用户
                    if ($md != 1 && $md != 3) {    //非竞技 销毁用户
                        $res = $this->model->desUserInfo($uid, $U, __LINE__);
                    } else {            //竞技场 暂不处理
                    }
                }
            }
        }
        debug("散桌执行完成[$td]");
        return true;
    }

    //[SYS]	编辑用户资料
    function EXEC_EDIT_USER($fd, $cmd, $data, $col, $ccn, $U)
    {
        //参数整理
        $str = isset($data[$col]) ? trim($data[$col]) : '';
        if (empty($str)) {
            $code = 2002;//编辑失败
            $data = ['error' => "{$ccn}不能为空"];
            sendToFd($fd, $cmd, $code, $data);
            return false;
        }
        if (in_array($col, ['nick', 'word'])) {
            $str = trim($data[$col]);
            $data_ban_words = [];
            include(ROOT . '/include/data_ban_words.php');
            $ban = $data_ban_words;
            foreach ($ban as $k => $v) {
                if (strpos($str, $v) > -1) {
                    $code = 2002;//编辑失败
                    $data = [];
                    $data = ['error' => "{$ccn}包含非法字符，请重新取名"];
                    sendToFd($fd, $cmd, $code, $data);
                    return false;
                }
            }
        }
        if ($col == 'nick') {
            $str = str_replace(['`', '~', '!', '@', '#', '$', '%', '^', '&', '*', ',', ';', '.', '"', "'", "|"], '', $str);
        }
        $str = ($col == 'nick') ? utf8substr(trim($data[$col]), 0, 21) : trim($data[$col]);//7个中文21个英文
        $mye = mysqli_real_escape_string($this->mysql->db, $str);
        if ($col == 'nick') {
            $sql = "SELECT * FROM `lord_game_user` WHERE `nick` = '$mye'";
            $res = $this->mysql->getData($sql);
            if ($res && count($res) > 1) {
                foreach ($res as $k => $v) {
                    if (!$k) continue;
                    $sql = "UPDATE `lord_game_user` SET `nick` = '{$mye}{$k}' WHERE `id` = " . $v['id'];
                    $this->mysql->runSql($sql);
                }
            } elseif ($res && isset($res[0]['uid']) && $res[0]['uid'] != $U['uid']) {
                $code = 2002;//编辑失败
                $data = [];
                $data = ['error' => "昵称重复，请重新取名。"];
                sendToFd($fd, $cmd, $code, $data);
                return false;
            }
        }
        $ud = $U['uid'];
        $md = $U['modelId'];
        $rd = $U['roomId'];
        $td = $U['tableId'];
        $sd = $U['seatId'];
        $sql = "UPDATE `lord_game_user` SET `$col` = '$mye' WHERE `uid` = $ud";
        $res = $this->mysql->runSql($sql);
        if (!$res) {
            $code = 2001;//编辑失败
            $data = ['error' => "{$ccn}编辑失败，请重新尝试。"];
            $data = [];
            sendToFd($fd, $cmd, $code, $data);
            return false;
        }
        $U[$col] = $str;
        $res = setUser($ud, $U);
        debug("用户" . $ccn . " F=$fd U=$ud R=$rd T=$td " . $col . "=" . $str);
        $code = 2000;//编辑成功
        $data = [
            'nick' => $U['nick'],
            'sex'  => $U['sex'],
            'word' => $U['word'],
        ];
        $res = sendToFd($fd, $cmd, $code, $data);

        if ($col == 'nick') {
            // TESK任务 开始
            $accode = 0;
            $action = __FUNCTION__;
            $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
            $Utesk = [];
            $teskparam = 1;
            $tesktable = [];
            if ($addU = $tesk->execute('edit_nick', $U, $Utesk, $teskparam, $tesktable)) {
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $ud, $U);
                if (($res = $this->model->incUserInfo($ud, $addU)) && $res['send']) sendToFd($fd, 4, 110, $res['send']);
            }
            // TESK任务 结束
        }
        return true;
    }

    //[SYS]	机器人报名竞技
    // gamesId 有效 竞技报名任务执行
    // gamesId 无效 竞技报名任务产生
    function MODEL_ROBOT_ENROLL($params = [])
    {
        $mrwg = $params ? $params['gamesId'] : 0;
        $tmid = time();
        $isTests = 1;//0常速报名模式1测试高速报名模式
        // 1、时间过滤	非比赛时间，直接返回
        $isRobot = intval($tmid > strtotime(date("Y-m-d 09:01:00")) && $tmid < strtotime(date("Y-m-d 23:30:00")));
        if (!$isRobot) return true;
        //加事务锁	ENROLL_CANCEL_$mrwg
        $lock = 'ENROLL_CANCEL_' . $mrwg;
        $res = setLock($lock, 1);
        if (!$res) return false;
        $isTaskSet = $isTaskRun = 0;
        if (!$mrwg) {    //报名任务产生阶段
            $isTaskSet = 1;
            $md = 1;
            $rd = 0;
            $wd = intval(date("Ymd", $tmid - (date("N") - 1) * 86400));
            $game = $this->model->getModelRoomWeekGameLast($md, $rd, $wd);
        } else {    //报名任务执行阶段
            $isTaskRun = 1;
            $mrwg = explode('_', $mrwg);
            if (count($mrwg) != 4) {
                delLock($lock);
                return gerr("机报参数错误 G=$mrwg");
            }
            $md = intval($mrwg[0]);
            $rd = intval($mrwg[1]);
            $wd = intval($mrwg[2]);
            $gd = intval($mrwg[3]);
            $game = $this->model->getModelGame($md, $rd, $wd, $gd);
            //判断赛场是否开启
            $day0 = strtotime(date('Y-m-d'));
            $wday = date("N");    //周n[1-7]
            $setting = $game['gameOpenSetting'];
            $setting = ($setting && is_array($setting)) ? $setting : [];
            $game['gameIsOpen'] = 0;
            foreach ($setting as $k => $v) {
                $v = explode("|", $v);
                if (count($v) != 3) break;
                $start = explode(" ", $v[0]);
                $dateStart = strtotime($start[0] . ' 00:00:00');
                $todayStart = strtotime(date("Y-m-d " . $start[1]));
                $end = explode(" ", $v[1]);
                $dateEnd = strtotime($end[0] . ' 23:59:59');
                $todayEnd = strtotime(date("Y-m-d " . $end[1]));
                $weeks = $v[2];
                if ($day0 > $dateStart && $day0 < $dateEnd && $tmid > $todayStart && $tmid < $todayEnd && ($weeks ? (strpos($weeks, $wday) !== false) : 1)) {
                    $game['gameIsOpen'] = 1;
                    break;
                }
            }
        }
        if (!$game) {
            delLock($lock);
            return gerr("机报赛查失败[$md|$rd|$wd] gamer-" . __LINE__);
        }
        $gd = $game['gameId'];
        $mrwg = $game['gamesId'];
        $gamePersonKeep = 1;//保留1个正常报名用户
        if ($isTests) {
            $gamePersonKeep = 0;//高速时，不保留
        }
        if ($game['gameStart'] > 0 || !($game['gameIsOpen'] && ($game['gamePerson'] + $gamePersonKeep) < $game['gamePersonAll'])) {
            debug("机报不用再报[$mrwg] IsOpen=" . $game['gameIsOpen'] . " Person=" . $game['gamePerson'] . " PersonAll=" . $game['gamePersonAll']);
            delLock($lock);
            return true;
        }
        if ($isTaskSet) {    //报名任务产生阶段
            // 2、是否可进
            // 基本概率 平均30秒可进入一个机器人，每次间隔12秒检查一次能否可以进入
            $rand = mt_rand(1, 30);
            if ($isTests) {
                $rand = 5;//高速时平均5秒
            }
            if ($rand > 15) {    //不可进入，直接返回
                // debug("机报随机不报[$mrwg] rand=$rand");
                delLock($lock);
                return true;
            }
            // 3、时间频率
            // 依据基础时段设定  越是热门时间，延迟时间越大
            if ($tmid > strtotime(date("Y-m-d 18:30:00")) && $tmid <= strtotime(date("Y-m-d 22:00:00"))) {
                $delay = 30;//40
            } elseif (($tmid > strtotime(date("Y-m-d 12:30:00")) && $tmid <= strtotime(date("Y-m-d 18:30:00")))
                || ($tmid > strtotime(date("Y-m-d 22:00:00")) && $tmid <= strtotime(date("Y-m-d 23:00:00")))
            ) {
                $delay = 20;//30
            } else {
                $delay = 15;//20
            }
            // 依据开场时长矫正  越是刚刚开场，延迟时间越小
            $game['gameCreate'] = isset($game['gameCreate']) ? $game['gameCreate'] : ($tmid - 300);
            $gameCreate = $game['gameCreate'];    //开场时间
            $gameCreated = $tmid - $gameCreate;    //开场时长
            $gameCreated = $gameCreated > 1160 ? 900 : $gameCreated;
            $delay = intval($delay + ($gameCreated - 300) / 60);
            // 随机上下浮动矫正  自身的上下1/4范围内浮动
            $delay = max(0, intval($delay - intval($delay / 4) + mt_rand(0, intval($delay * 2 / 4))));
            if ($isTests) {
                $delay = mt_rand(0, 10);//高速时，间隔10
            }
            debug("机报任务注册[$mrwg] delay=$delay");
            //事件 假人报名竞技
            $act = "MODEL_ROBOT_ENROLL";
            $params = ['gamesId' => $mrwg];
            $delay = $delay * 1000;
            if ($isTests) {
                for ($i = 0; $i < 2; $i++) {
                    setEvent($act, $params, $delay);
                }
            } else {
                setEvent($act, $params, $delay);
            }
            delLock($lock);
            return true;
        }
        //报名任务执行阶段
        $seed = mt_rand(1, 65000);
        $sql = "SELECT * FROM `lord_game_robot` WHERE `id` >= $seed AND `state` = 0 LIMIT 1";//临时使用state=0作为机器人值班状态的识别
        $v = $this->mysql->getLine($sql);
        if (!$v) {
            delLock($lock);
            return gerr("机报用户失败 G=$mrwg MYSQL->getLine($sql)");
        }
        $ud = intval($v['uid']);
        if ($this->model->getModelGamePlay($mrwg, $ud)) {
            debug("机报用户重复 G=$mrwg U=$ud");
            delLock($lock);
            return false;
        }
        $fd = 0;
        $U = ['fd'        => 0, 'modelId' => 0, 'roomId' => 0, 'tableId' => 0, 'seatId' => 0, 'gameId' => 0, 'gamesId' => 0,
              'uid'       => $v['uid'] + 0, 'cool_num' => $v['cool_num'] . "", 'nick' => $v['nick'] . "", 'word' => $v['word'] . "",
              'sex'       => $v['sex'] + 0, 'avatar' => $v['avatar'] + 0, 'exp' => $v['exp'] + 0, 'level' => $v['level'] + 0,
              'gold'      => $v['gold'] + 0, 'coins' => $v['coins'] + (mt_rand(0, 30) * 80), 'coupon' => 0,
              'play'      => mt_rand(333, 999), 'win' => intval(mt_rand(333, 999) / 3 + mt_rand(10, 77)),
              'propDress' => ['1' => 1], 'propItems' => [], 'propAcces' => [], 'buff' => [],
              'giveup'    => 0, 'score' => 0, 'isShowcard' => 0, 'channel' => "robot", 'vercode' => 10801, 'robot' => 1];
        if (ISLOCAL) $U['isShowcard'] = 1;
        //设定用户信息
        $res = setUser($ud, $U);
        //更新机器人值班状态
        $sql = "UPDATE lord_game_robot SET state = 1 WHERE uid = " . $U['uid'];
        $res = $this->mysql->runSql($sql);
        if (!$res) {
            $this->model->desUserInfo($ud, $U, __LINE__, 1);
            delLock($lock);
            return gerr("机报用户失败 G=$mrwg U=$ud MYSQL->runSql($sql)");
        }
        //机器人报名执行
        $game = $this->model->addModelGamePlay($game, $U);
        if (!$game) {
            //更新机器人值班状态
            $sql = "UPDATE lord_game_robot SET state = 0 WHERE uid = " . $U['uid'];
            $res = $this->mysql->runSql($sql);
            delLock($lock);
            return gerr("机报执行失败 G=$mrwg U=$ud");
        }
        debug("赛场机报成功 G=$mrwg U=$ud");
        //检查报名是否已满
        if ($game['gamePerson'] >= $game['gamePersonAll']) $this->ACT_MODEL_CHECK($game);
        //解事务锁
        delLock($lock);
        return true;
    }

    //[???]	竞技检查人数
    function ACT_MODEL_CHECK($game)
    {
        $mrwg = $game['gamesId'];
        $md = $game['modelId'];
        $rd = $game['roomId'];
        $wd = $game['weekId'];
        $gd = $game['gameId'];
        $gameplayall = $this->model->getModelGamePlayAll($mrwg);
        if (!$gameplayall) return gerr("赛场数据无效 G=$mrwg getModelGamePlayAll($mrwg)");
        //校验人数
        if ($game['gamePerson'] < $game['gamePersonAll'] || count($gameplayall) < $game['gamePersonAll']) return false;
        //生成下一场
        $newgame = $this->model->getModelRoomWeekGameLast($md, $rd, $wd, 1, $gd + 1);
        if (!$newgame) return gerr("赛场自增无效 G=$mrwg getModelRoomWeekGameLast($md, $rd, $wd, 1, $gd+1)");
        //踢出多余玩家
        $chunk = array_chunk($gameplayall, $game['gamePersonAll'], true);
        $gameplay = $chunk[0];
        $kickouts = isset($chunk[1]) ? $chunk[1] : [];
        foreach ($kickouts as $U) {
            $ud = $U['uid'];
            debug("赛场满员踢掉 G=$mrwg U=$ud");
            //删除参赛用户
            $res = $this->model->delModelGamePlay($game, $U);
            //通知用户: 报名人数满员
            $cmd = 5;
            $code = 104;
            $send = ['errno' => 4, 'error' => "报名人数满员，请等待下一场开放。\n报名费用已经返还到您的账户。", 'coins' => $U['coins'] + $game['gameInCoins']];
            $this->model->sendToUser($ud, $cmd, $code, $send);
        }
        //竞技赛场开始
        $game['gameStart'] = time();
        $res = $this->model->setModelGame($md, $rd, $wd, $gd, $game);
        //竞技随机组桌
        shuffle($gameplay);
        $i = 0;
        foreach ($gameplay as $k => $v) {
            $ud = $v['uid'];
            $tmp = $this->model->getUserInfo($ud);
            if ($tmp) $v['fd'] = $tmp['fd'];
            $players[$ud] = $v;
            $i++;
            if ($i % 3) continue;
            //使用真实roomId，伪造的为roomMock
            if ($T = $this->model->iniTableInfo($game['roomReal'], $players, $md, $rd, $wd, $gd)) {
                $td = $T['tableId'];
                debug("赛场新桌新局 G=$mrwg T=$td");
                $this->MODEL_GAME_START($T, 1);
                $players = [];
                continue;
            }
            gerr("赛场新桌无效 G=$mrwg players=" . json_encode($players));
            foreach ($players as $ud => $U) {
                if ($U['fd']) closeToFd($U['fd'], "赛场新桌无效 G=$mrwg");
                $this->model->desUserInfo($ud, $U);
            }
            $players = [];
        }
        return true;
    }

    //[???]	竞技再来一局
    function MODEL_GAME_GOON($game)
    {
        $mrwg = $game['gamesId'];
        $tmp = explode('_', $mrwg);
        list($md, $rd, $wd, $gd) = explode('_', $mrwg);
        $gameplay3 = $this->model->getModelGoonPlay($mrwg);
        if (!$gameplay3) return false;
        foreach ($gameplay3 as $U) {
            $players[$U['uid']] = $U;
        }
        //使用真实roomId，伪造的为roomMock
        if ($T = $this->model->iniTableInfo($game['roomReal'], $players, $md, $rd, $wd, $gd)) {
            $td = $T['tableId'];
            debug("赛场再桌新局 G=$mrwg T=$td");
            $this->MODEL_GAME_START($T, 0);
            return true;
        }
        gerr("赛场再桌无效 G=$mrwg players=" . json_encode($players));
        foreach ($players as $ud => $U) {
            if ($U['fd']) closeToFd($U['fd'], "赛场再桌无效 G=$mrwg");
            $this->model->desUserInfo($ud, $U);
        }
        return false;
    }

    //[108]	竞技准备开始
    function MODEL_GAME_START($T, $isNew = 0)
    {
        $md = $T['modelId'];
        $rd = $T['roomId'];
        $roomMock = $T['roomMock'];
        $wd = $T['weekId'];
        $gd = $T['gameId'];
        $mrwg = $T['gamesId'];
        $td = $T['tableId'];
        $rdc = $this->rooms[$rd];
        //发送通知 竞技将开
        $cmd = 5;
        $code = 108;
        $send = ['errno' => 0, 'error' => "竞技赛即将开始。", 'modelId' => $md, 'weekId' => $wd, 'gameId' => $gd];
        $this->model->sendToTable($T, $cmd, $code, $send, __LINE__);
        //加事务锁 场次变动
        $lock = 'GAMESID_' . $mrwg;
        $res = setLock($lock);
        if (!$res) return gerr("[LOCKON] lockId=$lock G=$mrwg T=$td func=" . __FUNCTION__);
        //标记竞技牌桌
        $game = $this->model->getModelGame($md, $roomMock, $wd, $gd);
        $game['tableIds'][$td] = 1;
        $res = $this->model->setModelGame($md, $roomMock, $wd, $gd, $game);
        //解事务锁 场次变动
        delLock($lock);
        //TESK任务 Start
        $accode = 0;
        $action = 'MATCH_START';
        $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
        foreach ($T['seats'] as $uid => $sid) {
            if ($T["seat{$sid}robot"]) continue;
            $U = $this->model->getUserInfo($uid);
            $Utesk = [];
            $teskparam = 1;
            $tesktable = [];
            if ($addU = $tesk->execute('match_times', $U, $Utesk, $teskparam, $tesktable)) {
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $U);
                if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
            }
        }
        //TESK任务 End
        debug("赛场拉人进房 G=$mrwg T=$td");
        //通知 进房成功
        $cmd = 5;
        $code = 1015;
        $data = [];
        $data['modelId'] = $md;
        $data['roomId'] = $rd;
        $data['enterLimit'] = $rdc['enterLimit'];
        $data['enterLimit_'] = $rdc['enterLimit_'];
        $data['isGaming'] = 0;
        $data['isContinue'] = 0;
        $data['baseCoins'] = $rdc['baseCoins'];
        $data['rate'] = $rdc['rate'];
        $data['rateMax'] = $rdc['rateMax'];
        $data['limitCoins'] = $rdc['limitCoins'];
        $data['rake'] = $rdc['rake'];
        $data['gameBombAdd'] = $rdc['gameBombAdd'];
        foreach ($T['seats'] as $uid => $sid) {
            $data['coins'] = $T["seat{$sid}coins"];
            $data['score'] = $T["seat{$sid}score"];
            $player = ['fd' => $T["seat{$sid}fd"], 'uid' => $uid, 'tableId' => $td];
            $res = $this->model->sendToPlayer($player, $cmd, $code, $data);
        }
        //事件 牌桌开始
        $sceneId = $td;
        $act = "GAME_ALL_READY";
        $params = ['tableId' => $td];
        $delay = $this->confs['time_model_game_start'] * 1000;
        $hd = $T['hostId'];
        setTimer($sceneId, $act, $params, $delay, $hd);
        // //事件 牌桌开始
        // $act = "GAME_ALL_READY";
        // $params = array('tableId'=>$td);
        // $delay = $this->confs['time_model_game_start'] * 1000;
        // $hd = $T['hostId'];
        // setEvent($act, $params, $delay, $hd);
    }

    //[118]	赛场发奖
    function MODEL_GAME_PRIZE($game)
    {
        $md = $game['modelId'];
        $rd = $game['roomId'];
        $wd = $game['weekId'];
        $gd = $game['gameId'];
        $mrwg = $game['gamesId'];
        $gameRank = $game['gameRank'];
        $gamePrize = $game['gamePrize'];
        $gameRobot = $game['gameRobot'];
        debug("赛场发奖开始 G=$mrwg");
        //TESK任务 Start
        $accode = 0;
        $action = 'MATCH_PRIZE';
        $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
        foreach ($gameRank as $uid => $rank) {
            if ($rank == 1) sendHorn("恭喜·" . $game['gameNick'] . "·获得本周竞技第{$gd}场第1名！", 1);
            if ($gameRobot[$uid]) continue;
            $U = $this->model->getUserInfo($uid);
            if ($U && $U['robot']) continue;
            if (!$U) $U = ['point' => 0];
            $v = $gamePrize[$uid];
            if (!isset($v['coins'])) $v['coins'] = 0;
            if (!isset($v['coupon'])) $v['coupon'] = 0;
            if (!isset($v['lottery'])) $v['lottery'] = 0;
            if (!isset($v['point'])) $v['point'] = 0;
            if (!isset($v['items'])) $v['items'] = [];
            //执行发奖
            $res = $this->model->userPrize($uid, $v, $U, '竞技场奖');
            $U = $this->model->getUserInfo($uid);
            if (!$U) $U = [];
            $fd = $U ? $U['fd'] : '';
            //通知用户: 竞技发奖
            $data = [
                "errno"   => 0,
                "error"   => sprintf("恭喜您获得上一场竞技赛第%s名", $rank) . ((!$v['items'] && !$v['coins'] && !$v['point']) ? "。" : ("奖励:"
                        . ($v['items'] ? ("\n" . join('、', $v['items'])) : "")
                        . ($v['coins'] ? ("\n" . "乐豆" . $v['coins'] . "个") : "")
                        . ($v['coupon'] ? (($v['coins'] ? "　　" : "\n") . "乐券" . $v['coupon'] . "个") : "")
                        . ($v['point'] ? ("\n" . "本周积分" . $v['point'] . "个") : ""))),
                "modelId" => $md,
                "weekId"  => $wd,
                "gameId"  => $gd,
                "coins"   => $v['coins'],  //奖励乐豆数
                "coupon"  => $v['coupon'],//奖励乐券数
                "point"   => $v['point'],  //奖励本周积分数
                "props"   => $v['items'],  //奖励道具名 array('3'=>'大师套装(30天)')
            ];
            if ($U) {
                //TESK任务 Start
                $Utesk = [];
                $teskparam = $rank;
                $tesktable = [];
                if ($addU = $tesk->execute('match_rank', $U, $Utesk, $teskparam, $tesktable)) {
                    foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $U);
                    if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
                }
                //TESK任务 End
            }
            if ($fd) {    //即刻通知
                debug("赛场发奖直接 G=$mrwg U=$uid F=$fd");
                $data['coins'] = $U['coins'];//用户当前乐豆
                $data['coupon'] = $U['coupon'];//用户当前奖券
                $data['props'] = $U['propDress'];//用户当前道具
                $cmd = 5;
                $code = 118;
                sendToFd($fd, $cmd, $code, $data);
                //通知 刷新数据
                $cmd = 4;
                $code = 110;
                $send = ['coins' => $U['coins'], 'coupon' => $U['coupon'], 'propDress' => $U['propDress']];
                sendToFd($fd, $cmd, $code, $send);
            } else {        //写表lord_user_message
                debug("赛场发奖间接 G=$mrwg U=$uid F=$fd");
                $cmd = 5;
                $code = 118;
                $data['act'] = 'USER_ALERT';
                $data['cmd'] = $cmd;
                $data['code'] = $code;
                $res = $this->model->insUserMsg($uid, $data);
            }
        }
        debug("赛场发奖完毕 G=$mrwg");
        return true;
    }

    //[122]	用户操作冲突 竞技场与非竞技
    function ACT_MODEL_UNIQUE($fd, $ud, $md, $rd, $type = 0)
    {
        //通知 竞技冲突
        $cmd = 5;
        $code = 122;
        $send['errno'] = 0;
        $send['error'] = $type ? "您目前正在竞技场自动配桌，请稍后。" : "您需要先结束牌局，才能参加竞技场。";
        $send['modelId'] = $md;
        $send['roomId'] = $rd;
        sendToFd($fd, $cmd, $code, $send);
        return true;
    }

    //[138]	回馈签到界面
    function ACT_LOGIN_DAY0($U, $Utask = [])
    {
        $fd = $U['fd'];
        $isPush = intval(!!$Utask);
        if (!$Utask) $Utask = $this->model->getUserTask($U['uid']);
        if (!$Utask) return false;
        $cmd = 5;
        $code = 138;
        $data['errno'] = 0;
        $data['error'] = "";
        $data['isPush'] = $isPush;
        $data['goldLevel'] = isset($Utask['gold_level']) ? $Utask['gold_level'] : 0;
        $data['goldLevelNeed'] = $this->confs['gold_level1'];
        $data['goldCostAll'] = isset($Utask['gold_all']) ? $Utask['gold_all'] : 0;
        $data['signDay'] = isset($Utask['login_day5_day']) ? $Utask['login_day5_day'] : 1;
        $data['signGot'] = isset($Utask['login_day5_got']) ? $Utask['login_day5_got'] : 0;
        $data["prizes"] = array();
        if($U["vercode"] >= 10903)
        $data["prizes"] = array(
        	"item1.png",
        	"item2.png",
        	"item3.png",
        	"item4.png",
        	"item5.png",
        	"item6.png",
        	"item7.png",
        );
        return sendToFd($fd, $cmd, $code, $data);
    }

    //[sys]
    function ACT_LIST_SHOW($fd, $cmd, $param, $code, $type, $U)
    {
        $data['errno'] = 0;
        $data['error'] = "";
        $ud = $U['uid'];
        $rd = $U['roomId'];
        $td = $U['tableId'];
        $sd = $U['seatId'];
        debug("用户查看榜单 F=$fd U=$ud R=$rd T=$td type=$type");
        $Utask = $this->model->getUserTask($ud);
        //日榜
        if ($type == 1) {
            $dd = intval(date("Ymd"));//今日
            //今日胜局排行榜		普通场每日赢场排行榜
            $data['listA'] = $this->model->zListNormalDayWin($dd);
            //今日赢取排行榜		普通场每日赢钱排行榜
            $data['listB'] = $this->model->zListNormalDayEarn($dd);
            //今日倍率排行榜		普通场每日倍率排行榜
            $data['listC'] = $this->model->zListNormalDayMaxrate($dd);
            $data['month'] = intval(substr(strval($dd), 4, 2));
            $data['day'] = intval(substr(strval($dd), 6, 2));
            $data['todayNormalPlay'] = isset($Utask['normal_day_play']) ? intval($Utask['normal_day_play']) : 0; //今日普通场次数
            $data['todayNormalWin'] = isset($Utask['normal_day_win']) ? intval($Utask['normal_day_win']) : 0; //今日普通场胜局
            $data['todayNormalWinRate'] = number_format($data['todayNormalPlay'] ? (intval($data['todayNormalWin'] * 10000 / $data['todayNormalPlay']) / 100) : 0, 2); //今日普通场胜率
            $data['todayNormalEarn'] = isset($Utask['normal_day_earn']) ? intval($Utask['normal_day_earn']) : 0; //今日普通场赢取
            $res = sendToFd($fd, $cmd, $code, $data);
            return true;
        }
        //周榜
        $dd = intval(date("Ymd", time() - (date("N") - 1) * 86400));//本周
        if ($type == 3) $dd = intval(date("Ymd", time() - (date("N") - 1) * 86400 - 7 * 86400));//上周
        //每周胜局排行榜		普通场每周赢场排行榜
        $data['listA'] = $this->model->zListNormalWeekWin($dd);
        //每周赢取排行榜		普通场每周赢钱排行榜
        $data['listB'] = $this->model->zListNormalWeekEarn($dd);
        //每周积分排行榜		竞技场每周积分排行榜
        $data['listC'] = $this->model->zListMatchWeekPoint($dd);
        $data['startMonth'] = intval(substr(strval($dd), 4, 2));
        $data['startDay'] = intval(substr(strval($dd), 6, 2));
        $wden = intval(date("Ymd", time() - (date("N") - 1) * 86400 + 6 * 86400));//本周末
        if ($type == 3) $wden = intval(date("Ymd", time() - (date("N") - 1) * 86400 - 1 * 86400));//上周末
        $data['endMonth'] = intval(substr(strval($wden), 4, 2));
        $data['endDay'] = intval(substr(strval($wden), 6, 2));
        $data['weekNormalPlay'] = isset($Utask['normal_week_play']) ? intval($Utask['normal_week_play']) : 0; //本周普通场次数
        $data['weekNormalWin'] = isset($Utask['normal_week_win']) ? intval($Utask['normal_week_win']) : 0; //本周普通场胜局
        $data['weekNormalWinRate'] = number_format($data['weekNormalPlay'] ? (intval($data['weekNormalWin'] * 10000 / $data['weekNormalPlay']) / 100) : 0, 2); //本周普通场胜率
        $data['weekNormalEarn'] = isset($Utask['normal_week_earn']) ? intval($Utask['normal_week_earn']) : 0; //本周普通场赢取
        $data['weekMatchPoint'] = isset($Utask['match_week_point']) ? intval($Utask['match_week_point']) : 0; //本周竞技场积分
        $res = sendToFd($fd, $cmd, $code, $data);
        return true;
    }

    //[FILE] 后台修改配置导致的各个服务器的文件更新操作
    function FILE_OPERATION($file, $class, $type, $data)
    {
        require_once(ROOT . "/class.$class.php");
        $obj = new $class;
        switch ($file) {
            case 'data_tesk_list':
                switch ($type) {
                    case 'update':
                        $obj->udpate($data['id'], $data);
                        break;
                    case 'delete':
                        $obj->delete($data['id']);
                        break;
                    default://add
                        $obj->create($data['id'], $data);
                        break;
                }
                break;
            case 'data_surprise_list':
                switch ($type) {
                    case 'update':
                        $obj->udpateSurprise($data['id'], $data);
                        break;
                    case 'delete':
                        $obj->deleteSurprise($data['id']);
                        break;
                    default://add
                        $obj->createSurprise($data['id'], $data);
                        break;
                }
                break;
            default:
                $obj->operate($file, $type, $data);
                break;
        }
        return true;
    }

    //[PUSH] 推送用户新邮件
    function PUSH_USR_MAILS($data)
    {
        $default = ['uid' => 0, 'id' => 0, 'subject' => "", 'content' => "", 'fileid' => 0, 'items' => 0, 'is_read' => 0, 'sort' => 1];
        $mail = array_merge($default, $data);
        if (!$mail['uid'] || !$mail['id'] || !$mail['subject'] || !$mail['content']) return gerr("推送邮件失败 F=? U=" . $mail['uid'] . " data=" . json_encode($data));
        $ud = intval($mail['uid']);
        unset($mail['uid']);
        $U = $this->model->getUserInfo($ud);
        if (!$U || !$U['fd']) return false;
        $fd = $U['fd'];
        $addU = ['mail_unread' => 1];
        $res = $this->model->incUserInfo($ud, $addU);
        if ($res) {
            $U = array_merge($U, $res['info']);
            if ($res['send']) sendToFd($fd, 4, 110, $res['send']);
        }
        //通知 全新邮件
        $cmd = 4;
        $code = 112;
        $send = ['errno' => 0, 'error' => "", 'mail_unread' => $U['mail_unread'], 'list' => [$mail]];
        sendToFd($fd, $cmd, $code, $send);
        return true;
    }

    //[TASK] 事件
    function TASK_GRAB_SURPRISE($params)
    {
        $cmd = 6;
        $code = 108;
        $id = $params['id'];
        $d = $this->model->popGRTS($id);
        if (!$d) return false;
        $ud = $this->model->popGRTS($id);
        $mailid = $d['mailId'];
        $nick = isset($d['nicks'][$ud]) ? $d['nicks'][$ud] : '没有人';
        $T = $d['table'];
        $success = $d['success'];
        $success['error'] = sprintf($success['error'], $nick);
        $failed = $d['failed'];
        $failed['error'] = sprintf($failed['error'], $nick);
        if (!$ud) $failed['error'] = "很遗憾，没有人抢走礼包。\n要加油呀，争取下次抢到哦";
        foreach ($T['seats'] as $uid => $sid) {
            $data = $uid == $ud ? $success : $failed;
            $fd = $T["seat{$sid}fd"];
            if (!$fd) continue;
            $data['log']['ud'] = $uid;
            $data['log']['td'] = $T['tableId'];
            $res = sendToFd($fd, $cmd, $code, $data);
        }
        if (!$ud) { // 没人抢时
            $res = $this->model->delGRTS($id);
            $res = $this->model->delSurpriseRecord($id);
            $res = $this->model->delUserMail($id);
        } else { // 有人抢时
            $res = $this->model->delGRTS($id);
            $res = $this->model->updSurpriseRecord($id, $ud);
            $mail = $this->model->retUserMail($mailid, $ud);
            if ($mail) return $this->PUSH_USR_MAILS($mail);
        }
        return true;
    }

    //[API]	全服代码重载
    function API_SRVRELOAD($params)
    {
        //事件 代码重载
        $act = "SRV_RELOAD";
        $params = [];
        $delay = (isset($params['delay']) ? intval($params['delay']) : 0) * 1000;
        $hd = 'ALL';
        return setEvent($act, $params, $delay, $hd);
    }

    //[EVT]	本机代码重载
    function SRV_RELOAD($params)
    {
        debug("本机代码重载 H=" . HOSTID);
        return srvReload();
    }

    //[API]	发送全服广播
    function API_BROADCAST($params)
    {
        if (!isset($params['msg']) || !$params['msg']) return false;
        $msg = $params['msg'];
        $level = isset($params['level']) ? intval($params['level']) : 1;
        return sendHorn($msg, $level);
    }

    //[API]	用户货币增扣
    function API_USERADD($params)
    {
        $type = strtolower(__FUNCTION__);
        if (!isset($params['uid']) || !isset($params['val']) || !isset($params['col'])) return gerr("[接口参数错误] act=$type params=" . json_encode($params));
        $ud = $params['uid'];
        $val = $params['val'];
        $col = $params['col'];//gold,coins,coupon,lottery
        $from = $params['from'];
        $locku = "USER_$ud";
        $res = setLock($locku);//用户行为系统锁，待续 待下一个sweety版本中，使用setUserLock($ud);
        if (!$res) gerr("[LOCKUU] lockId=$locku F=" . __FUNCTION__ . " data=" . json_encode($data));
        if ($U = $this->model->getUserInfo($ud)) {
            $addU = [$col => $val];
            if ($U['tableId']) {
                $td = $U['tableId'];
                $sd = $U['seatId'];
                $lockt = $td;
                $res = setLock($lockt);
                if (!$res) gerr("[LOCKUU] lockId=$lockt F=" . __FUNCTION__ . " data=" . json_encode($data));
                if ($T = $this->model->getTableInfo($td)) {
                    $addT["seat{$sd}coins"] = $addU['coins'];
                    $res = $this->model->incTableInfo($td, $addT);
                }
                delLock($lockt);
            }
            $res = $this->model->incUserInfo($ud, $addU);
            unset($addU);
            if ($res) {
                $U = array_merge($U, $res['info']);
                if ($res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
            }
        } else {
            $sql = "UPDATE `lord_game_user` SET `$col` = `$col` + $val WHERE `uid` = $ud";
            bobSql($sql);
        }
        if ($from === 'wechat') {
            $this->record->money('微信签到', $col, $val, $ud, $U);
        } else {
            if ($val > 0) $this->record->money('后台添加', $col, $val, $ud, $U);
            else $this->record->money('后台扣除', $col, abs($val), $ud, $U);
        }
        delLock($locku);
        return true;
    }

    //[API]	接口 金币购买乐豆
    function API_GOLD2COINS($data)
    {
        $type = strtolower('API_GOLD2COINS');
        $data = is_array($data) ? array_merge(['uid' => 0, 'gold' => 0, 'coins' => 0, 'coupon' => 0, 'propId' => 0, 'ip' => '', 'channel' => ''], $data) : false;
        if (!is_array($data) || !$data['uid'] || !$data['gold'] || !$data['coins'] || !$data['ip'] || !$data['channel']) return gerr("接口参数错误 func=API_GOLD2COINS params=" . json_encode($data));
        $ud = intval($data['uid']);
        $locku = "USER_$ud";
        $res = setLock($locku);//用户行为系统锁，待续 待下一个sweety版本中，使用setUserLock($ud);
        if (!$res) gerr("[LOCKUU] lockId=$locku func=API_GOLD2COINS data=" . json_encode($data));
        $gold = intval($data['gold']);
        $coins = intval($data['coins']);
        $coupon = intval($data['coupon']);
        $propId = intval($data['propId']);
        $ip = trim($data['ip']);
        $ch = trim($data['channel']);
        $date = date("Y-m-d H:i:s");
        $dd = intval(date("Ymd"));
        $time = time();
        $T = [];
        $U = $this->model->getUserInfo($ud);
        $Utask = $this->model->getUserTask($ud);
        $fd = $U ? $U['fd'] : 0;
        $rd = $U ? $U['roomId'] : 0;
        $td = $U ? $U['tableId'] : 0;
        $sd = $U ? $U['seatId'] : 0;
        if ($gold) {
            //首冲逻辑 修改下面配置逻辑，需要全项目搜索关键字“ 修改首冲逻辑 ”，防止漏掉。乐币到乐豆的每日首冲/每周首冲/用户首冲
            // if ( ! $Utask['gold_all'] ) {
            // 	$coins = intval($coins * 2);
            // }
            // elseif ( ! $Utask['gold_week'] ) {
            // 	$coins = intval($coins * 1.5);
            // }
            // elseif ( ! $Utask['gold_day'] ) {
            // 	$coins = (ISTESTS && $time < strtotime('2016-05-05')) || (! ISTESTS && strtotime('2016-04-28') < $time && $time < strtotime('2016-05-05')) ? intval($coins * 2) : intval($coins * 1.2);
            // }
            if (!$Utask['gold_day']) {
                if($ch != '1161-453')//微信充值
                {
                    if($gold == 30)$coins = intval($coins * 1.1);
                    elseif($gold == 50)$coins = intval($coins * 1.15);
                    elseif($gold == 100)$coins = intval($coins * 1.2);
                }
            }
            if($ch === '1161-453')$coins = intval($coins * 1.2);
            $this->model->redis->hincrby('lord_gold_day_2' . $dd, $ud, 1);
        }
        // 用户在线/离线
        if ($gold) {
            $add['gold_all'] = $add['gold_week'] = $add['gold_day'] = $gold;
            $Utask['gold_all'] += $gold;
            $Utask['gold_week'] += $gold;
            $Utask['gold_day'] += $gold;
            $res = $this->model->incUserTask($ud, $add);
            unset($add);
        }
        if ($U) {
            if ($coins) {
                $add['coins'] = $coins;
                $res = $this->model->incUserInfo($ud, $add);
                unset($add);
                if ($res) {
                    $U = array_merge($U, $res['info']);
                    if ($res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
                }
            }
            if ($td && $coins && ($T = $this->model->getTableInfo($td))) {
                $add["seat{$sd}coins"] = $coins;
                $res = $this->model->incTableInfo($td, $add);
                unset($add);
            }
            $U['mysql'] = 0;
        } else {
            $sql = "UPDATE `lord_game_user` SET `coins` = `coins` + $coins WHERE `uid` = $ud";
            $this->mysql->runSql($sql);
            $sql = "UPDATE `lord_user_task` SET `gold_all` = `gold_all` + $gold, `gold_week` = `gold_week` + $gold, `gold_day` = `gold_day` + $gold WHERE `uid` = $ud";
            bobSql($sql);
            $U = $this->model->getUserData($ud);
            if (!is_array($U) || !$U) {
                delLock($locku);
                return false;
            }
            $U['propDress'] = $this->model->getDbUserDress($ud);
            $U['propItems'] = $this->model->getDbUserItems($ud, 1);
            $U['realItems'] = $this->model->getDbUserItems($ud);
            $U['fd'] = '';
            $U['mysql'] = 1;
        }

        $sql = "UPDATE `lord_user_task` SET `gold_all` = `gold_all` + $gold, `gold_week` = `gold_week` + $gold, `gold_day` = `gold_day` + $gold WHERE `uid` = $ud";
        bobSql($sql);

        $this->record->money('SDK买豆', 'coins', $coins, $ud, $U);
        debug("用户兑换乐豆 F=$fd U=$ud R=$rd T=$td gold=$gold coins=$coins propId=$propId");
        if ($fd) {
            //购买成功
            $cmd = 5;
            $code = 4000;
            $send = ['name' => $coins . '乐豆', 'gold' => $U['gold'], 'coins' => $U['coins'], 'gold_' => $gold, 'coins_' => $coins];
            sendToFd($fd, $cmd, $code, $send);
            //刷新数据
            $cmd = 4;
            $code = 110;
            $send = ['coins' => $U['coins'], 'charge_rate' => 0];
            sendToFd($fd, $cmd, $code, $send);
        }
        // 固定任务
        if ($gold) {
            $taskid = [4, 5, 6];
            $tasker = new task($this->model, $taskid, 0, 0, $this->is_freshtask);
            $res = $tasker->run($U, $Utask);
            if ($res) {
                $uis = $uts = [];
                foreach ($taskid as $k => $id) {
                    debug("任务消耗乐币 F=$fd U=$ud R=$rd T=$td taskid=$id");
                    $uis = array_merge($uis, isset($res[$id]['userinfo']) ? $res[$id]['userinfo'] : []);
                    $uts = array_merge($uts, isset($res[$id]['usertask']) ? $res[$id]['usertask'] : []);
                }
                if ($U && $uis) $U = array_merge($U, $uis);
                $Utask = $uts ? array_merge($Utask, $uts) : [];
                !$fd && $uis && $res = $this->model->updUserInfo($ud, $uis);
                !$fd && $uts && $res = $this->model->updUserTask($ud, $uts);
            }
        }
        $sql = "INSERT INTO lord_user_cost (`dateid`,`type`,`channel`,`uid`,`gold`,`coins`,`coupon`,`propId`,`ip`,`date`,`time`) VALUES ";
        $sql .= "($dd,'$type','$ch',$ud," . ($gold * -1) . ",$coins," . ($coupon * -1) . ",$propId,'$ip','$date',$time)";
        bobSql($sql);
        if ($gold > 0) {
            if ($U['mysql']) $U = array_merge($U, ['uid' => $ud, 'fd' => '', 'channel' => '', 'roomId' => 0, 'tableId' => 0, 'lastSurprise' => 0]);
            //TESK Start
            $accode = 0;
            $action = "API_GOLD";
            $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
            $Utesk = [];
            $teskparam = $gold;
            $tesktable = $T;
            if ($addU = $tesk->execute('cost_gold', $U, $Utesk, $teskparam, $tesktable)) {
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $U);
                if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
            }
            if ($addU = $tesk->execute('buy_coins', $U, $Utesk, $teskparam, $tesktable)) {
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $U);
                if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
            }
            //TESK End
        }
        delLock($locku);
        return true;
    }


    //[API]	接口 金币购买商品
    function API_GOLD2PROP($data)
    {
        $type = strtolower(__FUNCTION__);
        $data = array_merge(['uid' => 0, 'gold' => 0, 'coins' => 0, 'coupon' => 0, 'propId' => 0, 'ip' => '', 'channel' => ''], $data);
        if (!is_array($data) || !$data['uid'] || !$data['gold'] || !$data['propId'] || !$data['ip'] || !$data['channel']) {
            return gerr("接口参数错误 act=$type params=" . json_encode($data));
        }
        $ud = intval($data['uid']);
        $locku = "USER_$ud";
        $res = setLock($locku);//用户行为系统锁，待续 待下一个sweety版本中，使用setUserLock($ud);
        if (!$res) {
            gerr("[LOCKUU][DOAPI] F=" . __FUNCTION__ . " lockId=$locku data=" . json_encode($data));
        }
        $gold = intval($data['gold']);
        $coins = intval($data['coins']);
        $coupon = intval($data['coupon']);
        $gd = intval($data['propId']);
        $ip = trim($data['ip']);
        $ch = trim($data['channel']);
        $time = time();
        $date = date("Y-m-d H:i:s", $time);
        $dd = dateid($date);
        $T = [];
        $U = $this->model->getUserInfo($ud);
        $Utask = $this->model->getUserTask($ud);
        $fd = $U ? $U['fd'] : 0;
        $rd = $U ? $U['roomId'] : 0;
        $td = $U ? $U['tableId'] : 0;
        $sd = $U ? $U['seatId'] : 0;
        if ($gold) {
            $add['gold_all'] = $add['gold_week'] = $add['gold_day'] = $gold;
            $Utask['gold_all'] += $gold;
            $Utask['gold_week'] += $gold;
            $Utask['gold_day'] += $gold;
            $res = $this->model->incUserTask($ud, $add);
            unset($add);
        }
        // 用户在线/离线
        if ($U) {
            $U['mysql'] = 0;
        } else {
            $sql = "UPDATE `lord_user_task` SET `gold_all` = `gold_all` + $gold, `gold_week` = `gold_week` + $gold, `gold_day` = `gold_day` + $gold WHERE `uid` = $ud";
            bobSql($sql);
            $U = $this->model->getUserData($ud);
            if (!is_array($U) || !$U) {
                delLock($locku);
                return false;
            }
            $U['propDress'] = $this->model->getDbUserDress($ud);
            $U['propItems'] = $this->model->getDbUserItems($ud, 1);
            $U['realItems'] = $this->model->getDbUserItems($ud);
            $U['fd'] = '';
            $U['mysql'] = 1;
        }
        $res = $this->model->buyGoods($U, $gd, 1);
        if (!is_array($res)) {
            delLock($locku);
            return gerr("接口道具失败 F=$fd U=$ud R=$rd T=$td G=$gd data=" . json_encode($data) . " res=" . json_encode($res));
        }
        debug("用户购买道具 F=$fd U=$ud R=$rd T=$td G=$gd gold=$gold coins=$coins");
        if ($fd) {
            $errno = 0;
            $error = "购买成功。";
            if (in_array($gd, [34, 35, 36, 37])) {
                $errno = 1;
                $error = "乐视礼包购买成功。";
            }
            //购买成功
            $cmd = 5;
            $code = 126;
            $send = ['errno' => $errno, 'error' => $error, 'gold' => $res['gold'], 'name' => $res['name'], 'coins' => $res['coins'], 'id' => $gd, 'propDress' => $res['propDress'], 'propItems' => $res['propItems']];
            $send['goodsSec'] = 0;
            $items = $this->model->getuserItem($ud, 1);
            foreach ($items as $k => $v) {
                if ($v['pd'] != 5) continue;
                $send['goodsSec'] = intval($v['sec'] > 0 ? $v['sec'] : max(0, $v['end'] > 0 ? ($v['end'] - time()) : 0));
                if (!$send['goodsSec']) {
                    if ($U['vercode'] < 10800) {
                        $send['goodsSec'] = 86313600;
                    } else {
                        $send['goodsSec'] = -1;
                    }
                }
                break;
            }
            sendToFd($fd, $cmd, $code, $send);
            //刷新数据
            $cmd = 4;
            $code = 110;
            $send = ['coins' => $res['coins'], 'coupon' => $res['coupon'], 'charge_rate' => 0];
            sendToFd($fd, $cmd, $code, $send);
        }
        // 执行任务

        if ($gold) {
            $taskid = [4, 5, 6];
            $tasker = new task($this->model, $taskid, 0, 0, $this->is_freshtask);
            $res = $tasker->run($U, $Utask);
            if ($res) {
                $uis = $uts = [];
                foreach ($taskid as $k => $id) {
                    debug("任务消耗乐豆 F=$fd U=$ud R=$rd T=$td taskid=$id");
                    $uis = array_merge($uis, isset($res[$id]['userinfo']) ? $res[$id]['userinfo'] : []);
                    $uts = array_merge($uts, isset($res[$id]['usertask']) ? $res[$id]['usertask'] : []);
                }
                if ($U && $uis) $U = array_merge($U, $uis);
                $Utask = $uts ? array_merge($Utask, $uts) : [];
                !$fd && $uis && $res = $this->model->updUserInfo($ud, $uis);
                !$fd && $uts && $res = $this->model->updUserTask($ud, $uts);
            }
        }

        $sql = "INSERT INTO lord_user_cost (`dateid`,`type`,`channel`,`uid`,`gold`,`coins`,`coupon`,`propId`,`ip`,`date`,`time`) VALUES ";
        $sql .= "($dd,'$type','$ch',$ud," . ($gold * -1) . "," . ($coins * -1) . "," . ($coupon * -1) . ",$gd,'$ip','$date',$time)";
        bobSql($sql);
        $goods = $this->model->getlistGoods('', 1, 1);
        if ($gold > 0) {
            if ($U['mysql']) $U = array_merge($U, ['uid' => $ud, 'fd' => '', 'channel' => '', 'roomId' => 0, 'tableId' => 0, 'lastSurprise' => 0]);
            //TESK Start
            $accode = 0;
            $action = "API_GOLD";
            $tesk = new tesk($this->mysql, $this->redis, $accode, $action);
            $Utesk = [];
            $teskparam = $gold;
            $tesktable = $T;
            if ($addU = $tesk->execute('cost_gold', $U, $Utesk, $teskparam, $tesktable)) {
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $U);
                if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
            }
            $gdbak = $gd;
            $isP = 0;
            if ($U && isset($U['channel'])) {
                if (!$isP) {
                    $conf = $this->model->getGoodsCtrl('xinshoulibao', $U['channel']);
                    if ($conf && isset($conf['ids']) && isset($conf['idm']) && in_array($gd, $conf['ids'])) {
                        $gdbak = $conf['idm'];
                        $isP = 1;
                    }
                }
                if (!$isP) {
                    $conf = $this->model->getGoodsCtrl('duochonglibao', $U['channel']);
                    if ($conf && isset($conf['ids']) && isset($conf['idm']) && in_array($gd, $conf['ids'])) {
                        $gdbak = $conf['idm'];
                        $isP = 1;
                    }
                }
            }
            $teskparam = $gdbak;
            if ($addU = $tesk->execute('buy_goods', $U, $Utesk, $teskparam, $tesktable)) {
                foreach ($addU as $k => $v) $this->record->money('动态任务', $k, $v, $uid, $U);
                if (($res = $this->model->incUserInfo($uid, $addU)) && $res['send']) sendToFd($U['fd'], 4, 110, $res['send']);
            }
            //TESK End
        }
        $noLibao = 1;
        //弹出领取包月俸禄
        if ($noLibao && ($conf = $this->model->getGoodsCtrl('baoyuelibao', $U['channel'])) && isset($goods[$conf['id']]) && $conf['id'] == $gd && $this->model->getMcard($ud) == 1) {
            $mtem = $this->model->getuserItem($ud, 1);
            $pd = 7;
            $pdSec = 0;
            foreach ($mtem as $k => $v) {
                if ($v['state'] < 2 && $v['pd'] == $pd) {
                    $pdSec += intval($v['sec'] > 0 ? $v['sec'] : max(0, $v['end'] > 0 ? ($v['end'] - $time) : 0));
                }
            }
            $cmd = 4;
            $code = 224;
            $send = ['errno' => 0, 'error' => "", 'isPush' => 1, 'state' => 1, 'id' => $conf['id'], 'price' => $goods[$conf['id']]['price'], 'fileId' => $conf['fileId'], 'sec' => $pdSec];
            $res = sendToFd($fd, $cmd, $code, $send);
            $noLibao = 0;
        }
        if ($noLibao) {

        }
        $libao = $this->model->redis->hget('lord_libao_' . $dd, $ud);
        //幸运牌局移除
        if ($noLibao && ($conf = $this->model->getGoodsCtrl('xingyunpaiju', $U['channel']))) {
            $libaoId = $conf['title'] . '_' . $goods[$gd]['price'] . '_' . $gd;
            if ($libao && isset($libao[$libaoId])) {
                unset($libao[$libaoId]);
                $this->model->redis->hset('lord_libao_' . $dd, $ud, $libao);
                $noLibao = 0;
            }
        }
        //连胜礼包移除
        if ($noLibao && ($conf = $this->model->getGoodsCtrl('lianshenglibao', $U['channel']))) {
            $libaoId = $conf['title'] . '_' . $goods[$gd]['price'] . '_' . $gd;
            if ($libao && isset($libao[$libaoId])) {
                unset($libao[$libaoId]);
                $this->model->redis->hset('lord_libao_' . $dd, $ud, $libao);
                $noLibao = 0;
            }
        }
        //免责金牌补偿
        if ($noLibao && ($conf = $this->model->getGoodsCtrl('mianzejinpai', $U['channel']))) {
            $libaoId = $conf['title'] . '_' . $goods[$gd]['price'] . '_' . $gd;
            if ($libao && isset($libao[$libaoId])) {
                $todayLibao = 1;
            } else {
                $libao = $this->model->redis->hget('lord_libao_' . dateid(date('Y-m-d H:i:s', $time - 86400)), $ud);
                $todayLibao = 0;
            }
            if ($libao && isset($libao[$libaoId])) {
                $coins = $libao[$libaoId]['coins'];
                if ($fd) {
                    $res = $this->model->incUserInfo($ud, ['coins' => $coins]);
                    sendToFd($fd, 4, 110, $res['send']);
                } else {
                    $sql = "UPDATE `lord_game_user` SET `coins` = `coins` + $coins WHERE `uid` = $ud";
                    bobSql($sql);
                }
                if ($todayLibao) {
                    unset($libao[$libaoId]);
                    $this->model->redis->hset('lord_libao_' . $dd, $ud, $libao);
                }
                $this->record->money('免责金牌', 'coins', $coins, $ud, $U);
                $noLibao = 0;
            }
        }
        delLock($locku);
        return true;
    }

}
