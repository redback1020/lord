<?php

$Q = $_REQUEST;
$timenow = time();
$apiname = 'record_user_trend';
$apiword = '用户打点分析';
$apitypes = array(
	'select'=>'列表',
);
$apitypei = array_keys($apitypes);
$apitype = isset($Q['apitype']) ? trim($Q['apitype']) : end($apitypei);
if ( !isset($apitypes[$apitype]) ) exit;

//base
header("Content-type: text/html; charset=utf-8");
require_once '../manage/checkPriv.php';
require_once '../include/database.class.php';

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
.row .span2{position:relative;}
.row .span2 div{line-height: 30px;padding-left:5px;color:#ccc;}
.row .span2 input.span2{position:absolute;top:0;height:30px;background:none!important;text-indent:40px;}
#datalist td{text-align:right;}
</style>
<script type="text/javascript">
var per_page = 20;
var cur_page = 0;
$(function(){ finder(); $('select.finder').change(function(){ finder(); }); });
function linkto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } self.location.href=to; }
function ajaxto( o, to ) { var _a = to.split('/'); var _this = $(o); if ( _a[0] != "http" ) { var _b = self.location.href.split('/'); delete _b[_b.length-1]; to = _b.join('/') + to; } $.getJSON(to, function(data){ if ( data ) { if ( data.errno == 0 ) { alert("操作成功。"); finder(); } else { alert("操作失败["+data.errno+"]："+data.error); } } }); }
function previt() { if ( cur_page==0 ) { alert("已经是第一页"); return; } cur_page--; findit(); }
function nextit() { cur_page++; findit(1); }
function finder() { cur_page=0; findit(); }
function findit( is_next ) {
	<?php $ps_ = array(); foreach ( $searchs as $k => $v ) { $ps_[$k] = $k; } foreach ( $ps_ as $kk => $vv ) { echo "var {$vv} = \$('#{$vv}').val();\n"; $ps_[$kk] = "$vv : $vv"; } ?>
	$.ajax({
		type: "get",
		url: "http://logs.51864.com/clientddz_op.php?per_page="+per_page+"&cur_page="+cur_page+"&channel_id="+$('#channel_id').val(),
		dataType: "jsonp",
		success: function( data ) {
			if ( data == null || data == "" ) { alert("获取数据失败！"); return; }
			var datalist = data;
			var datahtml = "";
			for ( var i=0; i<datalist.data_res.length; i++ ) {
				var o = datalist.data_res[i];
				datahtml += "<tr class='table-body'>";
				datahtml += "<td>"+o.dateid+"</td>";
				datahtml += "<td>"+o.np0+"</td>";
				datahtml += "<td>"+o.p0+"</td>";
				datahtml += "<td>"+o.np1+"</td>";
				datahtml += "<td>"+o.p1+"</td>";
				datahtml += "<td>"+o.np2+"</td>";
				datahtml += "<td>"+o.p2+"</td>";
				datahtml += "<td>"+o.np3+"</td>";
				datahtml += "<td>"+o.p3+"</td>";
				datahtml += "<td>"+o.np4+"</td>";
				datahtml += "<td>"+o.p4+"</td>";
				datahtml += "<td>"+o.np5+"</td>";
				datahtml += "<td>"+o.p5+"</td>";
				datahtml += "<td>"+o.np6+"</td>";
				datahtml += "<td>"+o.p6+"</td>";
				datahtml += "<td>"+o.np7+"</td>";
				datahtml += "<td>"+o.p7+"</td>";
				datahtml += "<td>"+o.np8+"</td>";
				datahtml += "<td>"+o.p8+"</td>";
				datahtml += "<td>"+o.np9+"</td>";
				datahtml += "<td>"+o.p9+"</td>";
				datahtml += "<td>"+o.np10+"</td>";
				datahtml += "<td>"+o.p10+"</td>";
				datahtml += "<td>"+o.np11+"</td>";
				datahtml += "<td>"+o.p11+"</td>";
				datahtml += "</tr>";
			}
			if ( datahtml=="" && is_next ) { alert("已到最后一页"); cur_page--; }
			else { $("#datalist").html(datahtml); $("#data_num").html(datalist.data_num); $("#page_num").html(Math.ceil(datalist.data_num/per_page)); $("#cur_page").html(cur_page+1); $("#pager").show(); }
		},
		error: function(){alert('fail');}
	});
}
</script>

<body>
<div class="body">

<fieldset>
	<legend><?=$apiword?> - <?=$apitypes[$apitype]?></legend>
	<div class="row">
		<div class="span2">
			<label>渠道：</label>
			<select class="span2" id="channel_id">
				<option value="" selected="selected">全部</option>
				<option value="0">未知</option>
				<option value="1005">沙发</option>
				<option value="1006">360tv</option>
				<option value="1007">奇珀</option>
				<option value="1008">当贝</option>
				<option value="1009">欢网</option>
				<option value="1010">爱家</option>
				<option value="1011">乐视</option>
				<option value="1012">阿里</option>
				<option value="1013">小米</option>
				<option value="1015">酷开</option>
				<option value="1016">海信</option>
				<option value="1017">鹏博士（大麦）</option>
				<option value="1018">易视腾</option>
				<option value="1019">百视通</option>
				<option value="1020">迪优美特</option>
				<option value="1021">鸿越</option>
				<option value="1022">忆典</option>
				<option value="1027">安卓网讯</option>
				<option value="1031">开博尔</option>
				<option value="1032">我播</option>
				<option value="1033">特发</option>
				<option value="1035">长虹</option>
				<option value="1036">康佳</option>
				<option value="1037">海尔</option>
				<option value="1038">联想</option>
				<option value="1040">爱游戏</option>
				<option value="1041">英菲克</option>
				<option value="1043">视源</option>
				<option value="1045">tvapk电视家</option>
				<option value="1046">石头游戏大厅</option>
				<option value="1050">电信itv</option>
				<option value="1053">葡萄游戏厅</option>
				<option value="1054">棉花糖大厅</option>
				<option value="1056">乐升世纪</option>
				<option value="1057">中万</option>
				<option value="1064">清华同方</option>
				<option value="1065">台湾网讯</option>
				<option value="1066">海美迪</option>
				<option value="1067">晨芯</option>
				<option value="1068">金锐显</option>
				<option value="1069">TCL</option>
				<option value="1071">全家盒</option>
				<option value="1073">好视</option>
				<option value="1074">统帅</option>
				<option value="1076">youjoy</option>
				<option value="1077">PPTV</option>
				<option value="1078">上海联通</option>
				<option value="1080">飞智</option>
				<option value="1081">中兴九城</option>
				<option value="1082">金亚太</option>
				<option value="1083">ATET</option>
				<option value="1085">泰捷</option>
				<option value="1086">微游地带</option>
				<option value="1087">百度游戏</option>
				<option value="1088">畅联</option>
				<option value="1089">乐游</option>
				<option value="1090">乐升网</option>
				<option value="1092">小霸王</option>
				<option value="1093">移动咪咕</option>
				<option value="1094">快游戏</option>
				<option value="1096">智我</option>
				<option value="1099">腾讯</option>
				<option value="1101">创维盒子</option>
				<option value="1103">蟋蟀</option>
				<option value="1105">官网</option>
				<option value="1106">小鹰</option>
				<option value="1107">椰子游戏</option>
				<option value="1108">小y游戏</option>
				<option value="1110">电视之家</option>
				<option value="1111">蘑菇园</option>
				<option value="1112">视源</option>
				<option value="1113">praytech</option>
				<option value="1114">tvseeaipu</option>
				<option value="1115">优酷</option>
				<option value="1116">小优</option>
				<option value="1118">多乐播科-应用导航</option>
				<option value="1119">盒范儿</option>
				<option value="1120">海尔统帅</option>
				<option value="1121">高清范</option>
				<option value="1122">丹阳广电</option>
				<option value="1123">东方有线(十滴水)</option>
				<option value="1124">Timebox</option>
				<option value="1125">优朋普乐</option>
				<option value="1127">安徽广电</option>
				<option value="1129">视博云</option>
				<option value="1130">1905</option>
				<option value="1131">掌世界</option>
				<option value="1132">上海电信</option>
				<option value="1133">移动基地</option>
				<option value="1136">悟空</option>
				<option value="1139">国广通</option>
				<option value="1140">电视猫</option>
				<option value="1141">TBG</option>
				<option value="1143">德准</option>
				<option value="1144">未来电视</option>
				<option value="1145">劳拉</option>
				<option value="1146">品胜</option>
				<option value="1147">行悦</option>
				<option value="1148">飞利浦</option>
				<option value="1149">希恩视通</option>
				<option value="1150">微鲸</option>
				<option value="1152">东方有线</option>
				<option value="1153">爱奇艺</option>
				<option value="1155">创云方（旧）</option>
				<option value="1156">玩吧</option>
				<option value="1157">欢网秦皇岛广电</option>
				<option value="1159">新宽联</option>
				<option value="1160">格来云</option>
				<option value="1161">微信公众号</option>
				<option value="1162">风行电视</option>
				<option value="1163">小白浏览器</option>
				<option value="1164">网讯安卓</option>
				<option value="1165">贵州广电</option>
				<option value="1166">易视腾（江西）</option>
				<option value="1167">cantv</option>
				<option value="1168">桔豆</option>
				<option value="1169">集尚</option>
				<option value="1170">熊猫</option>
				<option value="1171">应用导航</option>
				<option value="1172">天津联通</option>
				<option value="1173">网宿</option>
				<option value="1174">小百合</option>
				<option value="1175">安徽广电</option>
				<option value="1176">HTCL</option>
				<option value="1177">玩吧（有SDK）</option>
				<option value="1178">创云方</option>
				<option value="1179">微信公众号(手机)</option>
				<option value="1180">QQ群(手机)</option>
				<option value="1181">沃橙</option>
				<option value="1182">易视腾（手机）</option>
				<option value="1183">爱游戏（手机）</option>
				<option value="1184">海信（手机）</option>
				<option value="1185">移动咪咕（手机）</option>
				<option value="1186">XY苹果助手（手机）</option>
				<option value="1187">创维盒子BOX</option>
				<option value="1188">Itunes</option>
				<option value="1190">联通（手机）</option>
				<option value="1191">CIBN</option>
				<option value="1192">百视通（带SDK）</option>
				<option value="1193">中信国安</option>
				<option value="1194">系统自动创建帐号</option>
				<option value="1195">多乐</option>
				<option value="1196">天威视讯</option>
				<option value="1197">海南广电</option>
				<option value="1198">小悠</option>
				<option value="1199">中信国安</option>
				<option value="1200">CIBN</option>
				<option value="1201">天威视讯</option>
				<option value="1202">深圳数字电视</option>
				<option value="1203">zimo（手机）</option>
				<option value="1204">泰捷视频</option>
				<option value="1205">蜜蜂视频</option>
				<option value="1206">迪威博众</option>
				<option value="1207">微信扫码登陆（不是渠道）</option>
				<option value="1208">安徽电信</option>
				<option value="1209">高清劲爆MV</option>
			</select>
		</div>

		<div span="span1" style="float:right;">
			<label>&nbsp;</label>
			<input type="submit" value="查&nbsp;&nbsp;询" class="btn"  onclick="cur_page=0;findit();">
		</div>
	</div>
</fieldset>

<table class="table table-bordered table-condensed table-hover">
	<tr class="info">
		<th>日期</th>
		<th colspan="2">打开游戏图标</th>
		<th colspan="2">加载JAVA成功</th>
		<th colspan="2">加载LOGO成功</th>
		<th colspan="2">用户点击登陆</th>
		<th colspan="2">进入登陆界面</th>
		<th colspan="2">登陆进行一半</th>
		<th colspan="2">用户成功登陆</th>
		<th colspan="2">用户已进大厅</th>
		<th colspan="2">用户已进房间</th>
		<th colspan="2">点击开始游戏</th>
		<th colspan="2">首局开始发牌</th>
		<th colspan="2">首次点击出牌</th>
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

