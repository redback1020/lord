<?php
/**
 * Created by PhpStorm.
 * User: Tidel
 * Date: 16/10/27
 * Time: 上午11:02
 */

//百人牛牛在线数统计

require("/data/sweety/conf/cron.php");
require("/data/sweety/game/class.cow.php");

$redis = getRedis();
$mysql = getMysql();


$online_num = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["online_player"]));

$mysql->runSql(sprintf("INSERT INTO `lord_stat_cow_online` (`time`,`logday`, `logtime`,`num`) VALUES ('%s','%s','%s','%s');", time(), date("Y-m-d"), date("H:i:s"), $online_num));

if(date("H:i", time()) == "23:59")
{
    $sql = "select sum(wager1+wager2+wager3+wager4) as wager, sum(profit1+profit2+profit3+profit4) as profit from log_cow_player_settlement where create_time>".strtotime(date("Y-m-d"));
    $data = $mysql->getData($sql);
    $players_wager =  $data[0]["wager"];
    $players_profit = $data[0]["profit"];
    $open_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["open_num"]));
    $open_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["open_player"]));
    $game_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["game_player"]));
    $valid_round = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["valid_round"]));
    $total_fee = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["total_fee"]));
    $last_day_pot = $redis->redis->get(BaseCowMachine::$static_log_redis_key["last_day_pot"]);
    $pot =  intval($redis->redis->get('cow:pot'));
    $pot_change = $last_day_pot - $pot;
    $redis->redis->set(BaseCowMachine::$static_log_redis_key["last_day_pot"], $pot);
    $wager_pos1_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["wager_pos1_player"]));
    $wager_pos2_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["wager_pos2_player"]));
    $wager_pos3_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["wager_pos3_player"]));
    $wager_pos4_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["wager_pos4_player"]));
    $wager_num = $wager_pos1_player + $wager_pos2_player + $wager_pos3_player + $wager_pos4_player;
    $wager_pos1_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_pos1_num"]));
    $wager_pos2_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_pos2_num"]));
    $wager_pos3_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_pos3_num"]));
    $wager_pos4_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_pos4_num"]));
    $wager_all_pos1_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_all_pos1_num"]));
    $wager_all_pos2_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_all_pos2_num"]));
    $wager_all_pos3_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_all_pos3_num"]));
    $wager_all_pos4_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["wager_all_pos4_num"]));
    $apply_banker_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["apply_banker_player"]));
    $apply_banker_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["apply_banker_num"]));
    $banker_player = intval($redis->redis->sCard(BaseCowMachine::$static_log_redis_key["banker_player"]));
    $protect_banker_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["protect_banker_num"]));
    $flower_feedback_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["flower_feedback_num"]));
    $flower_feedback_fee = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["flower_feedback_fee"]));
    $bobm_feedback_num = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["bobm_feedback_num"]));
    $bobm_feedback_fee = intval($redis->redis->get(BaseCowMachine::$static_log_redis_key["bobm_feedback_fee"]));
    $pot_feedback = $flower_feedback_fee + $bobm_feedback_fee;
    $data=["open_num"=>$open_num,"open_player"=>$open_player, "game_player"=>$game_player, "valid_round"=>$valid_round,
        "fee"=>$total_fee,"pot"=>$pot,"pot_change"=>$pot_change, "wager_num"=>$wager_num, "wager1_point_num"=>$wager_pos1_num,
        "wager2_point_num"=>$wager_pos2_num,"wager3_point_num"=>$wager_pos3_num,"wager4_point_num"=>$wager_pos4_num,
        "wager1_all_num"=>$wager_all_pos1_num, "wager2_all_num"=>$wager_all_pos2_num, "wager3_all_num"=>$wager_all_pos3_num,
        "wager4_all_num"=>$wager_all_pos4_num, "apply_banker_player"=>$apply_banker_player,"apply_banker_num"=>$apply_banker_num,
        "banker_player"=>$banker_player, "protect_banker_num"=>$protect_banker_num,"players_wager"=>$players_wager,"players_profit"=>$players_wager,
        "pot_feedback"=> $pot_feedback, "flower_feedback_num"=>$flower_feedback_num, "flower_feedback_fee"=> $flower_feedback_fee, 
        "bobm_feedback_num" => $bobm_feedback_num, "bobm_feedback_fee"=>$bobm_feedback_fee
    ];
    $data["logday"] = date("Y-m-d");
    $data['logdate'] = date('Y-m-d H:i:s');
    
    $keys = array_keys($data);
    $values = array_values($data);
    $col = implode("`, `", $keys);
    $val = implode("', '", $values);
    $sql = "INSERT INTO `log_cow_static` (`$col`) VALUES ('$val');";
    $mysql->runSql($sql);
    foreach (BaseCowMachine::$static_log_redis_key as $key_word=>$key)
    {
        $redis->redis->del($key);
    }
}