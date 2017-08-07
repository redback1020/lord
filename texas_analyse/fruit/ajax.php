<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require '../include/MysqliDb.php';

class BaseController
{

    public function Pages(MysqliDb $db, $tableName, $data)
    {
        $fields = isset($data["fields"]) ? $data["fields"] : "*";
        $pageSize = isset($data["pagesize"]) ? $data["pagesize"] : 1000;;
        $page = isset($data["page"]) ? $data["page"] : 1;


        if (isset($data["where"]) && is_array($data["where"]) && count($data["where"]) > 0) {
            foreach ($data["where"] as $item) {
                $db->where($item["field"], $item["value"], $item["condition"]);
            }
        }

        $numrows = $db->get($tableName, 1, "count(*) AS num");
        $numrows = $numrows[0]["num"];
        $pages = intval($numrows / $pageSize);
        $begin = ($page - 1) * $pageSize;
        if ($begin < 0) $begin = 0;
        $end = $page * $pageSize;

        if (isset($data["where"]) && is_array($data["where"]) && count($data["where"]) > 0) {
            foreach ($data["where"] as $item) {
                $db->where($item["field"], $item["value"], $item["condition"]);
            }
        }

        if (isset($data["orderBy"]) && $data["orderBy"] != "") {
            $db->orderBy($data["orderBy"], $data["orderAsc"] ? "ASC" : "DESC");
        }

        if (isset($data["join"]) && $data["join"] != "") {
            $db->join($data['join_table'], $data['join_condition'], $data['join_type']);
        }


        $list = $db->get($tableName, [$begin, $pageSize], $fields);

        $result = [
            "list"     => $list,
            "pageSize" => $pageSize,
            "total"    => $numrows,
            "pages"    => $pages,
            "page"     => $page,
            "begin"    => $begin,
            "end"      => $end
        ];

        if (isset($data["stat"]) && $data["stat"] != "") {
            if (isset($data["where"]) && is_array($data["where"]) && count($data["where"]) > 0) {
                foreach ($data["where"] as $item) {
                    $db->where($item["field"], $item["value"], $item["condition"]);
                }
            }

            $stat = $db->get($tableName, 1, $data["stat"]);
            $result["stat"] = $stat[0];
        }

        return $result;
    }

    public function getDB()
    {
        if (SERVER_ID == 'test') {
            $db = new MysqliDb ('127.0.0.1', 'dbx5415j5nf05kqn', 'TYxYpysG8fR8PQdp', 'dbx5415j5nf05kqn');
        } elseif (SERVER_ID == 's1') {
            $db = new MysqliDb ('10.10.223.144', 'dbx5415j5nf05kqn', 'TYxYpysG8fR8PQdp', 'dbx5415j5nf05kqn');
        }

        return $db;
    }


    /**
     * @var Angular
     */
    protected $view;

    /**
     * @var \Bravo3\Orm\Services\EntityManager
     */
    protected $em;

    protected $serialiser;

    /**
     * @var MysqliDb
     */
    protected $db;

    public function __construct($view = null, $em = null, $db = null)
    {
        $this->view = $view;
        $this->em = $em;
        $this->serialiser = null;//new JsonSerialiser();
        $this->db = $db;
    }

    public function log($content)
    {
        $this->db->insert("gm_log", [
            "name"        => $_SESSION["login"]["name"],
            "content"     => $content,
            "logtime"     => time(),
            "logdatetime" => date("Y-m-d H:i:s")
        ]);
    }

    public function serialise($entity)
    {
        if (is_array($entity) || is_subclass_of($entity, "Iterator")) {
            return $this->serialises($entity);
        } else {
            if ($entity == null) {
                return null;
            }
            $metadata = $this->em->getMapper()->getEntityMetadata($entity);
            $str = $this->serialiser->serialise($metadata, $entity)->getData();
            return json_decode($str, true);
        }
    }

    public function serialises($entitys)
    {
        $result = [];
        foreach ($entitys as $entity) {
            if ($entity != null) {

                $metadata = $this->em->getMapper()->getEntityMetadata($entity);
                $str = $this->serialiser->serialise($metadata, $entity)->getData();
                $result[] = json_decode($str, true);

            }
        }
        return $result;
    }

    public function deserialise($json, &$entity)
    {
        $metadata = $this->em->getMapper()->getEntityMetadata($entity);

        $SerialisedData = new \Bravo3\Orm\Drivers\Common\SerialisedData("JSON", json_encode($json, 0));

        //var_dump($SerialisedData);exit;
        $this->serialiser->deserialiseByRaw($metadata, $json, $entity);

    }

    public function queue($key, $message)
    {

        if (is_array($message)) {
            $message = json_encode($message);
        }

        $client = $this->PredisClient();

        if ($client == null) {
            return;
        }

        $client->rpush($key, $message);

    }


    public function PredisClient()
    {
        $client = new \Predis\Client([
            'host'     => HALL_REDIS_HOST,
            'port'     => HALL_REDIS_PORT,
            'database' => HALL_REDIS_BASE,
            'password' => HALL_REDIS_PASSWORD
        ]);

        return $client;
    }
}

/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/8/17
 * Time: 下午5:55
 */
class FruitController extends BaseController
{
    public function __construct($view, $em, $db)
    {
        parent::__construct($view, $em, $db);

//        require realpath('.') . "/../HallServer/src/Logic/FruitsMachine/BaseMachine.php";
//        require realpath('.') . "/../HallServer/src/Logic/FruitsMachine/DistributionLaw.php";
//        require realpath('.') . "/../HallServer/src/Logic/FruitsMachine/FruitsMachine.php";
    }

    public function getConfigAjax()
    {
        $machine = new  CrazyChampion\Logic\FruitsMachine\FruitsMachine();
        $machine->setConfig();
        $client = new \Predis\Client([
            'host'     => HALL_REDIS_HOST,
            'port'     => HALL_REDIS_PORT,
            'database' => HALL_REDIS_BASE,
            'password' => HALL_REDIS_PASSWORD
        ]);


        $machine->setRedis(

            $client
        );
        echo json_encode([
            "status"            => 'succ',
            "config"            => $machine->getConfig(),
            'system_profit'     => $machine->getSystemProfit(),
            'system_difficulty' => $machine->getSystemDifficulty(),
            'system_lose'       => $machine->getSystemLose(),
            'system_pool'       => $machine->getSystemPool(),
            'system_pool_ext'   => $machine->getSystemPoolExt(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    }

    public function ConsoleAction()
    {
        if (ISLOCAL == false) {
            die("水果机模拟器去测试服上玩!!!");
        }


        $machine = new  CrazyChampion\Logic\FruitsMachine\FruitsMachine();
        $machine->setConfig();


        $this->view->display('fruit/console');


    }

    public function setConfigAjax($data)
    {

    }

    public function ConsoleAjax($data)
    {
        $now = time();
        $db = $this->getDB();

        $data = $data['d'];
        $coin = isset($data['coin']) ? $data['coin'] : 1000;
        $round = isset($data['round']) ? $data['round'] : 100;

        $bets = isset($data['bets']) ? explode(',', $data['bets']) : [
            1, 2, 3, 4, 5, 6, 7, 8
        ];

        //var_dump($coin,$round,$bets);
        $client = new \Predis\Client([
            'host'     => HALL_REDIS_HOST,
            'port'     => HALL_REDIS_PORT,
            'database' => HALL_REDIS_BASE,
            'password' => HALL_REDIS_PASSWORD
        ]);

        $machine = new CrazyChampion\Logic\FruitsMachine\FruitsMachine();

        $machine->setLogDb($db);
        $machine->setRedis($client);


        $machine->setPlayerId($data['player_id']);
        $machine->setPlayerName($data['player_name']);
        $machine->setConfigByString($data['config']);
        $machine->filling($coin);
        $machine->setKey($now);

        $total_wins = $credits = [];
        $total_cost = $total_win = 0;
        for ($i = 0; $i < $round; $i++) {
            $machine->collect();

            if (array_sum($bets) > $machine->getCredit()) {
                break;
            }

            $machine->setBetCell($bets);
            $machine->run();
            $total_cost += $machine->getCurrRoundCost();
            $total_win += $machine->getCurrRoundWin();
            $total_wins[] = $machine->getCurrRoundWin();
            $credits[] = $machine->getCredit();
        }
        $machine->escape();

        $counts = $db->where('`key`', $now)->groupBy('`name`')->get('log_fruit_win', 999999, 'count(*) as num ,sum(coin) as coin , `name`');

        foreach ($counts as $key => $count) {
            $counts[$key]['num_rate'] = round($count['num'] / $round * 100, 3) . "%";
        }

        echo json_encode([
            "status"            => 'succ',
            "msgs"              => $machine->getRound() > 1000 ? "" : $machine->msgs,
            'counts'            => $counts,
            'system_profit'     => $machine->getSystemProfit(),
            'system_difficulty' => $machine->getSystemDifficulty(),
            'system_lose'       => $machine->getSystemLose(),
            'system_pool'       => $machine->getSystemPool(),
            'system_pool_ext'   => $machine->getSystemPoolExt(),
            'total_win'         => $total_win,
            'total_cost'        => $total_cost,
            'real_round'        => $i,//$machine->getRound()
            'memory_get_usage'  => convert(memory_get_usage()),
            'variance'          => getVariance($total_wins),
            'bets'              => $credits


        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    }

    public function EnterLogAction()
    {
        $this->view->display('fruit/enter_log');
    }

    public function EnterLogAjax($data)
    {
        $db = $this->getDB();
        $result = $this->Pages($db, "log_fruit_enter", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }

    public function ExitLogAction()
    {
        $this->view->display('fruit/exit_log');
    }

    public function ExitLogAjax($data)
    {
        $db = $this->getDB();
        $result = $this->Pages($db, "log_fruit_exit", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }

    public function BetLogAction()
    {
        $this->view->display('fruit/bet_log');
    }

    public function BetLogAjax($data)
    {
        $db = $this->getDB();
        $result = $this->Pages($db, "log_fruit_bet", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }

    public function WinLogAction()
    {
        $this->view->display('fruit/win_log');
    }

    public function WinLogAjax($data)
    {
        $db = $this->getDB();
        $result = $this->Pages($db, "log_fruit_win", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }

    public function Analyse_basicAction()
    {
        $this->view->display('fruit/analyse_basic');
    }

    public function Analyse_ltvAction()
    {
        $this->view->display('fruit/analyse_ltv');
    }

    public function Analyse_playerAction()
    {
        $this->view->display('fruit/analyse_player');
    }

    public function AnalyseBasicAjax($data)
    {


        $db = $this->getDB();
        $result = $this->Pages($db, "stat_fruit_basic", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }

    public function AnalyseLtvAjax($data)
    {
        $db = $this->getDB();
        $result = $this->Pages($db, "stat_fruit_ltv", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }

    public function AnalysePlayerAjax($data)
    {


        if (isset($data['where'][0]['field']) && $data['where'][0]['field'] == 'id') {

            $playerId = $data['where'][0]['value'];

            /**
             * @var PlayerEntity $player
             */
            $player = $this->em->retrieveEntityOrNull(\CrazyChampion\DB\Entities\PlayerEntity::class, $playerId, false);

            if ($player != null) {
                $db = $this->getDB();
//                $exist = $db->where("id", $player->getId(), "=")->get("user_info", 1, "count(*) as count");
//
                $item = $this->serialise($player);
                // var_dump($item);
//
//                if ($exist[0]["count"] > 0) {
//                    $db->where("id", $item["id"])->update("user_info", $item);
//                } else {
//                    $db->insert("user_info", $item);
//                }

                $db->replace("user_info", $item);

//                $serialiser = new \Bravo3\Orm\Serialisers\JsonSerialiser();
//                $metadata = $this->em->getMapper()->getEntityMetadata($player);
//                $data = $serialiser->serialise($metadata, $player);
//                $arr = json_decode($data->getData(), true);
//                $arr["last_login_time"] = date("Y-m-d H:i:s", strtotime($arr["last_login_time"]));
//                $arr["reg_date"] = date("Y-m-d H:i:s", strtotime($arr["reg_date"]));
//                $result["list"] = [$arr];
//                echo json_encode(array_merge($result, [
//                    "status" => 'succ'
//                ]));
//                return;
            }


        }

        $db = $this->getDB();
        $result = $this->Pages($db, "user_info", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }


    public function PlayLogAction($data)
    {
        $this->view->display('fruit/play_log');
    }

    public function PlayLogAjax($data)
    {
        $db = $this->getDB();


        $data['fields'] = '`log_fruit_bet`.*,
`log_fruit_win`.`key`,
`log_fruit_win`.`coin`,
`log_fruit_win`.`cells`,
`log_fruit_win`.`stopName`,
`log_fruit_win`.`stopId`,
`log_fruit_win`.`name`,
`log_fruit_win`.`old` as win_old';

        $data['join'] = true;
        $data['join_table'] = 'log_fruit_win';
        $data['join_condition'] = ' `log_fruit_bet`.`round` = `log_fruit_win`.`round`
AND `log_fruit_bet`.`time` = `log_fruit_win`.`time`
AND `log_fruit_bet`.`playerId` = `log_fruit_win`.`playerId`';
        $data['join_type'] = 'LEFT';

        $result = $this->Pages($db, "log_fruit_bet", $data);
        echo json_encode(array_merge($result, [
            "status" => 'succ'
        ]));
    }

    public function OnlineAjax($data)
    {
        $logs = [];
        $db = $this->getDB();

        $today = $data["today"];
        $yesterday = date("Y-m-d", strtotime($today) - 86400);
        $lastWeek = date("Y-m-d", strtotime($today) - 86400 * 7);

        $today_logs = $db->where('logday', $today)->get("lord_stat_fruit_online", 86400, '*');
        $lastday_logs = $db->where('logday', $yesterday)->get("lord_stat_fruit_online", 86400, '*');
        $lastweek_logs = $db->where('logday', $lastWeek)->get("lord_stat_fruit_online", 86400, '*');

        $todaylogs = $yesterday_logs = $lastWeek_logs = [];

        foreach ($today_logs as $today_log) {
            $todaylogs[substr($today_log['logtime'], 0, 5)] = $today_log['num'];
        }

        for ($i = strtotime($today); $i <= strtotime($today) + 86400; $i += (60)) {
            $time = date("H:i", $i);

            $logs[] = [
                "label"     => $time,
                "today"     => isset($todaylogs[$time]) ? intval($todaylogs[$time]) : 0,
                "yesterday" => isset($yesterday_logs[$time]) ? intval($yesterday_logs[$time]) : 0,
                "lastWeek"  => isset($lastWeek_logs[$time]) ? intval($lastWeek_logs[$time]) : 0,
            ];
        }

        echo json_encode([
            "status"    => 'succ',
            "today"     => $today,
            "yesterday" => $yesterday,
            "lastWeek"  => $lastWeek,
            "logs"      => $logs,
//            "lastWeek_logs"  => $lastWeek_logs,

        ]);
    }

}

function convert($size)
{
    $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

function getVariance(array $list)
{
    $avg = array_sum($list) / count($list);

    $total_var = 0;
    foreach ($list as $lv) {
        $total_var += pow(($lv - $avg), 2);

    }

    return sqrt($total_var / count($list));
}

$post = json_decode(file_get_contents("php://input"), true);
if (isset($post['c'])) {
    $c = trim($post['c']);
    unset($post['c']);
}

if (isset($post['a'])) {
    $a = trim($post['a']);
    unset($post['a']);
}
$actionName = ucwords($a) . "Ajax";

$get_str = $post['url'];
$get_str = str_replace('?', '', $get_str);
$get_params = [];
parse_str($get_str, $get_params);

define('SERVER_ID', $get_params['server']);

$o = new FruitController(null, null, null);
$o->{$actionName}($post);