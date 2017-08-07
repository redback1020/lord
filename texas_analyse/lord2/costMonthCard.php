<?php
/**
 * Created by PhpStorm.
 * User: huangxiufeng
 * Date: 16/9/1
 * Time: 下午2:52
 */


//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';
$Q = $_REQUEST;
$timenow = time();

//conf
$apiname = 'costMonthCard';
$apiword = '包月用户消费纪录';
$apitable= 'lord_user_cost_month_card';
$isDateTableName = 0;
$isDateTableColumn = 1;
$orderby = "ORDER BY `id` DESC";
$apitypes = array( 'delete'=>'隐藏', 'search'=>'搜索', 'select'=>'列表' );
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;
$id = isset($Q['id']) ? intval($Q['id']) : 0;
$inputdate = isset($Q['dateid']) && $Q['dateid'] ? trim($Q['dateid']) : ($isDateTableName ? date("Y-m-d") : '');
if ( $isDateTableName ) $apitable.= '_'.str_replace('-','',$inputdate);
$searchs = array(
    'dateid'  =>array('name'=>'日期','int'=>1,'typ'=>'date','rel'=>0,'all'=>1),
    'type'    =>array('name'=>'类型','int'=>0,'typ'=>'select','rel'=>0,'all'=>1),
    'uid'     =>array('name'=>'UID','int'=>1,'typ'=>'input','rel'=>0,'all'=>1),
    'cool_num'=>array('name'=>'编号ID','int'=>1,'typ'=>'input','rel'=>'uid','all'=>1),
);
$types = array('charge_old'=>'旧版充值','user_gold_coin'=>'旧版买豆','req_prop_buyit'=>'旧版道具','api_gold2coins'=>'SDK买豆','api_gold2prop'=>'SDK买道具');//分类归属
$is_dels = array('0'=>'显示','1'=>'隐藏');//显隐状态
$props = array();
$proplist = $db->query("SELECT `id`, `name` FROM `lord_list_goods`")->fetchAll(PDO::FETCH_ASSOC); if ( ! is_array($proplist) ) { $proplist = array(); }
foreach ( $proplist as $k => $v ) { $props[$v['id']] = $v['name']; }

//delete
if ( $apitype == 'delete' ) {
    $sql = "UPDATE `$apitable` SET `is_del` = 1, `update_time` = $timenow WHERE `id` = $id";
    $res = $pdo->getDB(1)->exec($sql);
    $res = $res ? array('errno'=>0, 'error'=>"操作成功。") : array('errno'=>8, 'error'=>"查询错误。 $sql");
    echo json_encode($res);
    exit;
}

//search
if ( $apitype == 'search' ) {
    $per_page = $Q['per_page'];
    $cur_page = $Q['cur_page'] * $per_page;
    foreach ( $searchs as $k => $v ) {
        if ( $v['rel'] && isset($Q[$k]) ) {
            if ( $v['int'] ) $Q[$k] = intval($Q[$k]);
            if ( !$Q[$k] ) continue;
            $sql = "SELECT `".$v['rel']."` FROM `lord_game_user` WHERE `$k` = ".$Q[$k];
            $res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if ( $res && is_array($res) && isset($searchs[$v['rel']]) ) {
                $Q[$v['rel']] = end($res);
                if ( $searchs[$v['rel']]['int'] ) $Q[$v['rel']] = intval($Q[$v['rel']]);
            }
        }
    }
    $where = array();
    foreach ( $searchs as $k => $v ) {
        if ( $v['rel'] ) continue;
        switch ( $v['typ'] ) {
            case 'select':
                $val = isset($Q[$k]) ? $Q[$k] : ($v['all'] ? 'all' : ($v['int'] ? null :''));
                if ( $v['all'] && $val != 'all' ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
                elseif ( $val != 'all' && !empty($val) ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
                break;
            case 'input':
                $val = isset($Q[$k]) ? $Q[$k] : ($v['int'] ? 0 : '');
                if ( $val ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
                break;
            case 'date':
                if ( ! $isDateTableColumn ) break;
                $val = isset($Q[$k]) && $Q[$k] ? str_replace('-','',$Q[$k]) : 0;
                if ( $val ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
                break;
            default:
                $val = isset($Q[$k]) ? $Q[$k] : ($v['int'] ? 0 : '');
                if ( $val ) { $where[]= "`$k` = ".($v['int'] ? intval($val) : ("'".$val."'")); }
                break;
        }
    }
    $where = $where ? ('WHERE '.join(' AND ', $where)) : '';
    $sql = "SELECT * FROM `$apitable` {$where} {$orderby} LIMIT {$cur_page}, {$per_page}";
    $res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $res = ( $res && is_array($res) ) ? $res : array();
    foreach ( $res as $k => $v )
    {
        $res[$k]['type'] = $types[$v['type']];
        $res[$k]['prop'] = $v['propId'] ? $props[$v['propId']] : '&nbsp;';
        $res[$k]['is_del_'] = $is_dels[$v['is_del']];
        $res[$k]['update_time'] = $v['update_time'] ? date("Y-m-d H:i:s", $v['update_time']) : "&nbsp;";
    }
    $json['data_res'] = $res;
    $sql = "SELECT count(*) as data_num FROM `$apitable` {$where} ";
    $res = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    $json['data_num'] = $res ? $res['data_num'] : 0;
    echo json_encode($json);
    exit;
}

//select
if ( $apitype == 'select' ) {
    ?>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
    <link type="text/css" href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <style type="text/css">
        .body{position:absolute;left:0;top:0;padding:0 0 0 10px;width:98%;}
        legend{margin-bottom: 10px;}
        table.table{ font-size: 12px;margin-bottom:8px;width: 100%!important;}
        table.table th,table.table td{ white-space: nowrap;}
        table.table th{background:#eee;}
        .row{margin-bottom:10px;}
        .row .span2{position:relative;}
        .row .span2 .bg{display:block;line-height:30px;padding-left:5px;color:#ccc;font-size:14px;}
        .row .span2 input.span2,.row .span2 select.span2{position:absolute;top:0;height:30px;text-indent:32px;background:none!important;}
    </style>
    <script>
        var per_page = 20;
        var cur_page = 0;
        $(function(){ finder(); $('.finder').change(function(){ finder(); }); });
        function linkto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } self.location.href=to; }
        function ajaxto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } $.getJSON(to, function(data){ if ( data ) { if ( data.errno == 0 ) { alert("操作成功。"); finder(); } else { alert("操作失败["+data.errno+"]："+data.error); } } }); }
        function previt() { if ( cur_page==0 ) { alert("已经是第一页"); return; } cur_page--; findit(); }
        function nextit() { cur_page++; findit(1); }
        function finder() { cur_page=0; findit(); }
        function findit( is_next ) {
            $.post("<?=$apiname?>.php?apitype=search&per_page="+per_page+"&cur_page="+cur_page, {
                <?php $postp = array(); foreach ( $searchs as $k => $v ) { $postp[]= $k." : \$('#{$k}').val()"; } echo $postp ? join(',', $postp) : ''; ?>
            }, function( data ) {
                if ( data == null || data == "" ) { alert("获取数据失败！"); return; }
                var datalist = eval("("+data+")");
                var datahtml = "";
                for ( var i=0; i<datalist.data_res.length; i++ ) {
                    var o = datalist.data_res[i];
                    datahtml += "<tr class='table-body'>";
                    datahtml += "<td>"+o.id+"</td>";
                    datahtml += "<td>"+o.dateid+"</td>";
                    datahtml += "<td><a href='userAccount.php?uid="+o.uid+"'>"+o.uid+"</a></td>";
                    datahtml += "<td>"+o.type+"</td>";
                    datahtml += "<td>"+o.gold+"</td>";
                    datahtml += "<td>"+o.coins+"</td>";
                    datahtml += "<td>"+o.prop+"</td>";
                    datahtml += "<td>"+o.date+"</td>";
                    datahtml += "<td>"+o.is_del_+"</td>";
                    datahtml += "<td>"+o.update_time+"</td>";
                    datahtml += "<td>"+(o.is_del==1?"&nbsp;":("<a href='#' onclick='ajaxto(this,\"<?=$apiname?>.php?apitype=delete&id="+o.id+"\")'>隐藏</a>"))+"</td>";
                    datahtml += "</tr>";
                }
                if ( datahtml=="" && is_next ) { alert("已到最后一页"); cur_page--; }
                else { $("#datalist").html(datahtml); $("#data_num").html(datalist.data_num); $("#page_num").html(Math.ceil(datalist.data_num/per_page)); $("#cur_page").html(cur_page+1); $("#pager").show(); }
            });
        }
    </script>

    <body>
    <div class="body">

        <fieldset>
            <legend><?=$apiword?> - <?=$apitypes[$apitype]?></legend>
            <div class="row">
                <?php foreach ( $searchs as $var => $set ) { $varn = $set['name'].': '; if ($set['typ'] == 'select') { ?>
                    <div class="span2"><span class="bg"><?=$varn?></span><select id="<?=$var?>" class="span2 finder">
                            <?php if ($set['all']) {?><option value="all">全部</option><?php } ?>
                            <?php foreach ( ${$var.'s'} as $k => $v ) { echo "<option value='$k'".($data && $data[$var]==$k ? " selected='selected'" : "").">$v</option>"; } ?>
                        </select></div>
                <?php } elseif ( $set['typ'] == 'input' ) { ?>
                    <div class="span2"><span class="bg"><?=$varn?></span><input id="<?=$var?>" value="" type="text" class="span2 finder" /></div>
                <?php } elseif ( $set['typ'] == 'date' ) { ?>
                    <div class="span2"><span class="bg"><?=$varn?></span><input id="<?=$var?>" value="<?=$inputdate?>" type="text" class="span2 finder" readonly onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" /></div>
                <?php } else { ?>
                <?php } } ?>
                <div span="span1" style="float:right;">
                    <?php if ( isset($apitypes['search']) ) { ?><input class="btn" type="button" value="查&nbsp;&nbsp;询" onclick="finder()" /><?php } ?>
                    <?php if ( isset($apitypes['create']) ) { ?><input class="btn" type="button" value="创&nbsp;&nbsp;建" onclick="linkto(this,'<?=$apiname?>.php?apitype=create')" /><?php } ?>
                </div>
            </div>
        </fieldset>

        <table class="table table-bordered table-condensed table-hover">
            <tr class="info">
                <th>序号</th>
                <th>日期</th>
                <th>UID</th>
                <th>类型</th>
                <th>乐币</th>
                <th>乐豆</th>
                <th>商品</th>
                <th>时间</th>
                <th>状态</th>
                <th>更新</th>
                <th>操作</th>
            </tr>
            <tbody id="datalist">
            </tbody>
        </table>

        <table width="98%" border="0" cellpadding="0" cellspacing="0" align="left">
            <tr>
                <td height="25" id="pager" align="center" style="display:none;">
                    共 <span id="data_num"></span>条 / <span id="page_num"></span>页&nbsp;
                    <div class="btn-group">
                        <button class="btn" onclick="previt()">前一页</button>
                        <button class="btn" id="cur_page"></button>
                        <button class="btn" onclick="nextit()">后一页</button>
                    </div>
                </td>
            </tr>
        </table>

    </div>
    </body>
    <?php
}
?>
