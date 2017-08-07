<?php
//$data_priv = $_SESSION['data_priv'];
include_once 'checkPriv.php';$access_priv = $_SESSION['access_priv'];$uname = $_SESSION['admin_name'];
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Menu</title>
<style tyle="text/css">
body { font-size: 13px; margin: 0px; }
a { color:#000; text-decoration:none; }
a:hover { color: #ff6600; text-decoration: underline; }
.menuHead { font-size: 15px; font-weight: bold; line-height: 30px; text-align: center; color:white; background-color: #507BAE; border-bottom:2px #aaaaaa solid; }
.menuHead a { color: #fff; }
.menuHead a:hover { text-decoration: none; }
.menuItem { font-size: 14px; padding-left:10px; line-height: 30px; text-align: left; color:#369; border-bottom:1px #507BAE solid; cursor:pointer; }
.subItem { padding-left:20px; line-height: 25px; background:#eee url(../image/arrow-right.png) no-repeat 8px 9px; cursor:pointer; }
.subItem2 { padding-left:30px; line-height: 25px; background:#fff; }
</style>
<script src="/js/jquery.js" language="javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".sty1").hide();
	$(".subItem2").hide();
	$(".menuItem").click(function(){
		var men = $("#"+this.id+"1");
		if(men.is(':visible')){
			$("#"+this.id+"1").slideUp("fast");
		}else{
			$(".subItem").css("background","#eee url(../image/arrow-right.png) no-repeat 8px 9px");
		    $(".sty1").slideUp("slow");
		    $(".subItem2").slideUp("slow");
		    $("#"+this.id+"1").slideDown("fast");
		}
	});
	$(".subItem").click(function(){
		$(".subItem2:visible").slideUp("fast");
		$(".subItem").css("background","#eee url(../image/arrow-right.png) no-repeat 8px 9px");
		var men = $("#"+this.id+"1");
		if(men.is(':visible')){
			$(this).css("background","#eee url(../image/arrow-right.png) no-repeat 8px 9px");
			$("div[name="+this.id+"1]").slideUp("fast");
		}else{
			$(this).css("background","#fff url(../image/arrow-down.png) no-repeat 8px 9px");
			$("div[name="+this.id+"1]").slideDown("fast");
		}
	});
});
</script>
</head>

<body>


<div class="menuHead">菜 单</div>


<?php if(substr_count($access_priv,"z")>0||$access_priv == 'all'){ ?>
<div class="menuItem"  id="per">系统设置</div>
<div id="per1" class="sty1">
	<div id="per11" class="subItem">权限管理</div>
		<div name="per111" id="per111" class="subItem2">·<a href="roleList.php" target="contentFrame">后台权限管理</a></div>
</div>
<?php } ?>


<?php if(substr_count($access_priv,"y")>0||$access_priv == 'all'){ ?>
<div class="menuItem" id="y">个人信息设置</div>
<div id="y1" class="sty1">
	<div id="y11" class="subItem">密码管理</div>
		<div name="y111" id="y111" class="subItem2">·<a href="upda.php" target="contentFrame">修改密码</a></div>
</div>
<?php } ?>


<?php if(substr_count($access_priv,"a")>0||$access_priv == 'all'){ ?>
<div class="menuItem"  id="a">有乐德州扑克</div>
<div id="a1" class="sty1">
	<div id="a15" class="subItem">基础工具</div>
		<div name="a151" id="a151" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/online.php" target="contentFrame">在线列表</a></div>
		<div name="a151" id="a151" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/userinfo.php" target="contentFrame">用户的详细信息</a></div>
		<div name="a151" id="a151" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/user.php" target="contentFrame">查询用户的uid</a></div>
		<div name="a151" id="a151" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/vipList.php" target="contentFrame">VIP用户列表</a></div>
	<div id="a11" class="subItem">公告系统</div>
		<div name="a111" id="a111" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/announce.php" target="contentFrame">公告系统</a></div>
		<div name="a111" id="a111" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/sendmsg.php" target="contentFrame">发送系统消息</a></div>
	<div id="a12" class="subItem">过渡页tips系统</div>
		<div name="a121" id="a121" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/enter.php?file=enter&time=${mapSign.time}&sign=${mapSign.sign}&type=1" target="contentFrame">进牌桌过渡页tips</a></div>
		<div name="a121" id="a121" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/enter.php?file=leave&time=${mapSign.time}&sign=${mapSign.sign}&type=1" target="contentFrame">出牌桌过渡页tips</a></div>
	<div id="a13" class="subItem">微信相关</div>
		<div name="a131" id="a131" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/weixin_user.php" target="contentFrame">绑定游戏ID的微信用户</a></div>
		<div name="a131" id="a131" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/weixin_nologin.php" target="contentFrame">未登录游戏的微信玩家</a></div>
	<div id="a14" class="subItem">争霸赛相关</div>
		<div name="a141" id="a141" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/truePlayer.php" target="contentFrame">争霸赛真实玩家玩牌轮数</a></div>
		<div name="a141" id="a141" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/bangdan.php" target="contentFrame">争霸赛实时排名榜单</a></div>
		<div name="a141" id="a141" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/history.php" target="contentFrame">争霸赛历史排名榜单</a></div>
		<div name="a141" id="a141" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/addData.php" target="contentFrame">加入人为数据</a></div>
	<div id="a16" class="subItem">充值相关</div>
		<div name="a161" id="a161" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/charge.php" target="contentFrame">充值赠送</a></div>
		<div name="a161" id="a161" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/pay.php" target="contentFrame">充值查询</a></div>
	<div id="a17" class="subItem">运营数据管理</div>
		<div name="a171" id="a171" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/gmsum.php" target="contentFrame">运营总表</a></div>
		<div name="a171" id="a171" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/gmsum_channel.php" target="contentFrame">渠道列表</a></div>
		<div name="a171" id="a171" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/viewAllAmount.php" target="contentFrame">用户在线数据报表</a></div>
	<div id="a18" class="subItem">系统操作日志</div>
		<div name="a181" id="a181" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/sys_log.php" target="contentFrame">操作日志</a></div>
</div>
<?php } ?>


<?php if(substr_count($access_priv,"b")>0||$access_priv == 'all'){ ?>
<div class="menuItem"  id="b">有乐德州扑克(渠道)</div>
<div id="b1" class="sty1">
	<div id="b15" class="subItem">数据查询</div>
		<div name="b151" id="b151" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/channel_userinfo.php" target="contentFrame">用户信息</a></div>
		<div name="b151" id="b151" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/channelLog.php" target="contentFrame">充值记录</a></div>
		<?php if($uname == "kevin.zhu") { ?>
		<div name="b151" id="b151" class="subItem2">·<a href="http://op.youjoy.tv/TV_mobile2/gmsum_channel.php" target="contentFrame">渠道列表</a></div>
		<?php } ?>
</div>
<?php } ?>


<?php if(substr_count($access_priv,"c")>0||$access_priv == 'all'){ ?>
	<!-- 斗地主的菜单权限标记为c，html-id为e -->
	<!-- 斗地主测试的菜单权限标记为e，html-id为f -->
<?php } ?>


<?php if(substr_count($access_priv,"d")>0||$access_priv == 'all'){ ?>
<div class="menuItem"  id="d">有乐斗地主(渠道)</div>
<div id="d1" class="sty1">
	<div id="d15" class="subItem">数据查询</div>
		<div name="d151" id="d151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/channel_userinfo.php" target="contentFrame">用户信息</a></div>
		<div name="d151" id="d151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/channelLog.php" target="contentFrame">充值记录</a></div>
		<?php if($uname == "kevin.zhu") { ?>
		<div name="d151" id="d151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/gmsum_channel.php" target="contentFrame">渠道列表</a></div>
		<?php } ?>
</div>
<?php } ?>


<?php if(substr_count($access_priv,"c")>0||$access_priv == 'all'){ ?>
<div class="menuItem"  id="e">有乐斗地主</div>
<div id="e1" class="sty1">
	<div id="e15" class="subItem">基础管理</div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userOnline.php" target="contentFrame">查找在线用户</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userInfo.php" target="contentFrame">查找所有用户</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userCoinsHistory.php" target="contentFrame">用户乐豆纪录</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userLoginout.php" target="contentFrame">用户登录纪录</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userEditNick.php" target="contentFrame">用户昵称修改</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userTransfer.php" target="contentFrame">新旧账号迁移</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/user_delete.php" target="contentFrame">用户账号删除</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userDataAdd.php" target="contentFrame">用户货币增扣</a></div>
		<div name="e151" id="e151" class="subItem2">·<a href="http://op.youjoy.tv/lord2/serverReload.php" target="contentFrame">重载服务器</a></div>
	<div id="e19" class="subItem">大厅管理</div>
		<div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/baseFileList.php" target="contentFrame">基础用图</a></div>
		<div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/insideFileList.php" target="contentFrame">特殊用图</a></div>
		<div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/lobbyFileList.php" target="contentFrame">场次图片</a></div>
		<div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/lobbyMRoomList.php" target="contentFrame">赛场列表</a></div>
		<div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/lobbyARoomList.php" target="contentFrame">广告场次</a></div>
		<!-- <div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/lobbyNaviList.php" target="contentFrame">导航列表</a></div> -->
		<!-- <div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/lobbyRoomList.php" target="contentFrame">广告场列表</a></div> -->
		<!-- <div name="e191" id="e191" class="subItem2">·<a href="http://op.youjoy.tv/lord2/lobbyFileList.php" target="contentFrame">广告场图片</a></div> -->
	<div id="e12" class="subItem">邮件管理</div>
		<div name="e121" id="e121" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userInboxList.php" target="contentFrame">用户收件箱</a></div>
		<div name="e121" id="e121" class="subItem2">·<a href="http://op.youjoy.tv/lord2/userUnboxList.php" target="contentFrame">用户废件箱</a></div>
		<div name="e121" id="e121" class="subItem2">·<a href="http://op.youjoy.tv/lord2/mailFileList.php" target="contentFrame">邮件图片</a></div>
	<div id="e13" class="subItem">活动管理</div>
		<div name="e131" id="e131" class="subItem2">·<a href="http://op.youjoy.tv/lord2/topicList.php" target="contentFrame">活动列表</a></div>
		<div name="e131" id="e131" class="subItem2">·<a href="http://op.youjoy.tv/lord2/topicFileList.php" target="contentFrame">活动图片</a></div>
		<div name="e131" id="e131" class="subItem2">·<a href="http://op.youjoy.tv/lord2/trialcoinsList.php" target="contentFrame">救济乐豆项</a></div>
		<div name="e131" id="e131" class="subItem2">·<a href="http://op.youjoy.tv/lord2/trialcdList.php" target="contentFrame">救济乐豆CD</a></div>
	<div id="e20" class="subItem">任务管理</div>
		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/teskList.php" target="contentFrame">任务列表</a></div>
		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/sourceList.php" target="contentFrame">任务源码</a></div>
		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/surpriseList.php" target="contentFrame">暴奖列表</a></div>
		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/prizeFileList.php" target="contentFrame">奖品图片</a></div>
     		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/luckyDrawFileList.php" target="contentFrame">幸运抽奖图片</a></div>
		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/tteskList.php" target="contentFrame">牌局任务</a></div>
		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/ttrateList.php" target="contentFrame">牌局任务控制</a></div>
		<div name="e201" id="e201" class="subItem2">·<a href="http://op.youjoy.tv/lord2/ttsourceList.php" target="contentFrame">牌局任务源码</a></div>
	<div id="e11" class="subItem">公告系列</div>
		<div name="e111" id="e111" class="subItem2">·<a href="http://op.youjoy.tv/lord2/noticeList.php" target="contentFrame">公告列表</a></div>
		<div name="e111" id="e111" class="subItem2">·<a href="http://op.youjoy.tv/lord2/tipsList.php" target="contentFrame">底栏提示</a></div>
		<div name="e111" id="e111" class="subItem2">·<a href="http://op.youjoy.tv/lord2/sendmsg.php" target="contentFrame">发送滚动公告</a></div>
	<div id="e16" class="subItem">商城管理</div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/mallFileList.php" target="contentFrame">素材列表</a></div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/propList.php" target="contentFrame">道具列表</a></div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/item.php" target="contentFrame">物品列表</a></div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/goods.php" target="contentFrame">商品列表</a></div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/cost.php" target="contentFrame">综合消费纪录</a></div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/costMonthCard.php" target="contentFrame">月卡用户消费纪录</a></div>

	<!-- <div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/rechargeList.php" target="contentFrame">充值列表</a></div> -->
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/pay.php" target="contentFrame">旧版充值记录</a></div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/convert.php" target="contentFrame">兑换列表</a></div>
		<div name="e161" id="e161" class="subItem2">·<a href="http://op.youjoy.tv/lord2/convertRecord.php" target="contentFrame">兑换记录</a></div>
	<div id="e22" class="subItem">比赛相关</div>
		<div name="e221" id="e221" class="subItem2">·<a href="http://op.youjoy.tv/lord2/match_award.php" target="contentFrame">淘汰赛获奖记录</a></div>
		<div name="e221" id="e221" class="subItem2">·<a href="http://op.youjoy.tv/lord2/matchHistory.php" target="contentFrame">竞技赛场次记录</a></div>
	<div id="e17" class="subItem">运营数据</div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/sum_activation.php" target="contentFrame">礼包统计</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/gmsum.php" target="contentFrame">运营总表</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/gmsum_channel.php" target="contentFrame">渠道列表</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/record_user_trend.php" target="contentFrame">用户打点</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/record_hotuser.php" target="contentFrame">用户榜单</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/record_money_day.php" target="contentFrame">货币综合分析</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/record_money_type.php" target="contentFrame">货币类型分析</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/record_money.php" target="contentFrame">货币记录</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/record_table_day.php" target="contentFrame">牌桌综合分析</a></div>
		<div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/record_table.php" target="contentFrame">牌桌记录</a></div>
		<!-- <div name="e171" id="e171" class="subItem2">·<a href="http://op.youjoy.tv/lord2/viewAllAmount.php" target="contentFrame">在线图表</a></div> -->
	<div id="e21" class="subItem">在线图表</div>
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_online_all.php" target="contentFrame">在线用户</a></div>
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_playing_all.php" target="contentFrame">活跃用户</a></div>
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_playing_normal.php" target="contentFrame">经典场</a></div>
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_playing_joker.php" target="contentFrame">赖子场</a></div>
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_playing_model.php" target="contentFrame">竞技场</a></div>
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_playing_match.php" target="contentFrame">比赛场</a></div>
		<!--<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/fruit/fruit_online_detail.php?server=s1" target="contentFrame">水果机</a></div>-->
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_fruit_online.php" target="contentFrame">水果机</a></div>
	<div id="e18" class="subItem">操作记录</div>
		<div name="e181" id="e181" class="subItem2">·<a href="http://op.youjoy.tv/lord2/sys_log.php" target="contentFrame">操作日志</a></div>
	<div id="e31" class="subItem">水果机</div>
		<div name="e311" id="e311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/enter_log.php?server=s1" target="contentFrame">进场日志</a></div>
		<div name="e311" id="e311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/exit_log.php?server=s1" target="contentFrame">出场日志</a></div>
		<div name="e311" id="e311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/play_log.php?server=s1" target="contentFrame">游戏日志</a></div>
		<div name="e311" id="e311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/player_list.php?server=s1" target="contentFrame">玩家列表</a></div>
		<div name="e311" id="e311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/operation.php?server=s1" target="contentFrame">运营日报</a></div>
		<div name="e311" id="e311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/ltv.php?server=s1" target="contentFrame">LTV</a></div>
	<div id="f32" class="subItem">牛牛</div>
                <div name="f321" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/cow_intervene.php?server=s1" target="contentFrame">干预</a></div>
</div>
<?php } ?>


<?php if(substr_count($access_priv,"e")>0||$access_priv == 'all'){ ?>
<!-- <div class="menuItem" id="f" style="display:none;">有乐斗地主测试服</div> -->
<div class="menuItem" id="f">有乐斗地主测试服</div>
<div id="f1" class="sty1">
	<div id="f15" class="subItem">基础管理</div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userOnline.php" target="contentFrame">查找在线用户</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userInfo.php" target="contentFrame">查找所有用户</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userCoinsHistory.php" target="contentFrame">用户乐豆纪录</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userLoginout.php" target="contentFrame">用户登录纪录</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userEditNick.php" target="contentFrame">用户昵称修改</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userTransfer.php" target="contentFrame">新旧账号迁移</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/user_delete.php" target="contentFrame">用户账号删除</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userDataAdd.php" target="contentFrame">用户货币增扣</a></div>
		<div name="f151" id="f151" class="subItem2">·<a href="http://op.youjoy.tv/lord3/serverReload.php" target="contentFrame">重载服务器</a></div>
	<div id="f19" class="subItem">大厅管理</div>
		<div name="f191" id="f191" class="subItem2">·<a href="http://op.youjoy.tv/lord3/baseFileList.php" target="contentFrame">基础用图</a></div>
		<div name="f191" id="f191" class="subItem2">·<a href="http://op.youjoy.tv/lord3/insideFileList.php" target="contentFrame">特殊用图</a></div>
		<div name="f191" id="f191" class="subItem2">·<a href="http://op.youjoy.tv/lord3/lobbyFileList.php" target="contentFrame">场次图片</a></div>
		<div name="f191" id="f191" class="subItem2">·<a href="http://op.youjoy.tv/lord3/lobbyMRoomList.php" target="contentFrame">赛场列表</a></div>
		<div name="f191" id="f191" class="subItem2">·<a href="http://op.youjoy.tv/lord3/lobbyARoomList.php" target="contentFrame">广告场次</a></div>
		<!-- <div name="f191" id="f191" class="subItem2">·<a href="http://op.youjoy.tv/lord3/lobbyNaviList.php" target="contentFrame">导航列表</a></div> -->
	<div id="f12" class="subItem">邮件管理</div>
		<div name="f121" id="f121" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userInboxList.php" target="contentFrame">用户收件箱</a></div>
		<div name="f121" id="f121" class="subItem2">·<a href="http://op.youjoy.tv/lord3/userUnboxList.php" target="contentFrame">用户废件箱</a></div>
		<div name="f121" id="f121" class="subItem2">·<a href="http://op.youjoy.tv/lord3/mailFileList.php" target="contentFrame">邮件图片</a></div>
	<div id="f13" class="subItem">活动管理</div>
		<div name="f131" id="f131" class="subItem2">·<a href="http://op.youjoy.tv/lord3/topicList.php" target="contentFrame">活动列表</a></div>
		<div name="f131" id="f131" class="subItem2">·<a href="http://op.youjoy.tv/lord3/topicFileList.php" target="contentFrame">活动图片</a></div>
		<div name="f131" id="f131" class="subItem2">·<a href="http://op.youjoy.tv/lord3/trialcoinsList.php" target="contentFrame">救济乐豆项</a></div>
		<div name="f131" id="f131" class="subItem2">·<a href="http://op.youjoy.tv/lord3/trialcdList.php" target="contentFrame">救济乐豆CD</a></div>
	<div id="f20" class="subItem">任务管理</div>
		<div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/teskList.php" target="contentFrame">任务列表</a></div>
		<div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/sourceList.php" target="contentFrame">任务源码</a></div>
		<div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/surpriseList.php" target="contentFrame">暴奖列表</a></div>
		<div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/prizeFileList.php" target="contentFrame">奖品图片</a></div>
	        <div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/luckyDrawFileList.php" target="contentFrame">幸运抽奖图片</a></div>
		<div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/tteskList.php" target="contentFrame">牌局任务</a></div>
		<div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/ttrateList.php" target="contentFrame">牌局任务控制</a></div>
		<div name="f201" id="f201" class="subItem2">·<a href="http://op.youjoy.tv/lord3/ttsourceList.php" target="contentFrame">牌局任务源码</a></div>
	<div id="f11" class="subItem">公告系列</div>
		<div name="f111" id="f111" class="subItem2">·<a href="http://op.youjoy.tv/lord3/noticeList.php" target="contentFrame">公告列表</a></div>
		<div name="f111" id="f111" class="subItem2">·<a href="http://op.youjoy.tv/lord3/tipsList.php" target="contentFrame">底栏提示</a></div>
		<div name="f111" id="f111" class="subItem2">·<a href="http://op.youjoy.tv/lord3/sendmsg.php" target="contentFrame">发送滚动公告</a></div>
	<div id="f16" class="subItem">商城管理</div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/mallFileList.php" target="contentFrame">素材列表</a></div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/propList.php" target="contentFrame">道具列表</a></div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/item.php" target="contentFrame">物品列表</a></div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/goods.php" target="contentFrame">商品列表</a></div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/cost.php" target="contentFrame">综合消费纪录</a></div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/costMonthCard.php" target="contentFrame">月卡用户消费纪录</a></div>
	<!-- <div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/rechargeList.php" target="contentFrame">充值列表</a></div> -->
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/pay.php" target="contentFrame">旧版充值记录</a></div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/convert.php" target="contentFrame">兑换列表</a></div>
		<div name="f161" id="f161" class="subItem2">·<a href="http://op.youjoy.tv/lord3/convertRecord.php" target="contentFrame">兑换记录</a></div>
	<div id="f22" class="subItem">比赛相关</div>
		<div name="f221" id="f221" class="subItem2">·<a href="http://op.youjoy.tv/lord3/match_award.php" target="contentFrame">淘汰赛获奖记录</a></div>
		<div name="f221" id="f221" class="subItem2">·<a href="http://op.youjoy.tv/lord3/matchHistory.php" target="contentFrame">竞技记录</a></div>
	<div id="f17" class="subItem">运营数据</div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/sum_activation.php" target="contentFrame">礼包统计</a></div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/gmsum.php" target="contentFrame">运营总表</a></div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/gmsum_channel.php" target="contentFrame">渠道列表</a></div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/record_money_day.php" target="contentFrame">货币综合分析</a></div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/record_money_type.php" target="contentFrame">货币类型分析</a></div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/record_money.php" target="contentFrame">货币记录</a></div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/record_table_day.php" target="contentFrame">牌桌综合分析</a></div>
		<div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/record_table.php" target="contentFrame">牌桌记录</a></div>
		<!-- <div name="f171" id="f171" class="subItem2">·<a href="http://op.youjoy.tv/lord3/viewAllAmount.php" target="contentFrame">在线图表</a></div> -->
	<div id="f21" class="subItem">在线图表</div>
		<div name="f211" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord3/chart_playing.php" target="contentFrame">日 活跃</a></div>
		<div name="f211" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord3/chart_playing_month.php" target="contentFrame">月 活跃</a></div>
		<div name="f211" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord3/chart_online.php" target="contentFrame">日 在线</a></div>
		<div name="f211" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord3/chart_online_month.php" target="contentFrame">月 在线</a></div>
		<div name="f211" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord3/chart_online_time.php" target="contentFrame">在线时长</a></div>
		<div name="f211" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord3/chart_online_detail.php" target="contentFrame">在线详情</a></div>
		<div name="e211" id="e211" class="subItem2">·<a href="http://op.youjoy.tv/lord2/chart_fruit_online.php?server=s1" target="contentFrame">水果机</a></div>
	<div id="f18" class="subItem">操作记录</div>
		<div name="f181" id="f181" class="subItem2">·<a href="http://op.youjoy.tv/lord3/sys_log.php" target="contentFrame">操作日志</a></div>
	<div id="f31" class="subItem">水果机</div>
		<div name="f311" id="f311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/enter_log.php?server=test" target="contentFrame">进场日志</a></div>
	    	<div name="f311" id="f311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/exit_log.php?server=test" target="contentFrame">出场日志</a></div>
		<div name="f311" id="f311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/play_log.php?server=test" target="contentFrame">游戏日志</a></div>
		<div name="f311" id="f311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/player_list.php?server=test" target="contentFrame">玩家列表</a></div>
		<div name="f311" id="f311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/operation.php?server=test" target="contentFrame">运营日报</a></div>
		<div name="f311" id="f311" class="subItem2">·<a href="http://op.youjoy.tv/fruit/ltv.php?server=test" target="contentFrame">LTV</a></div>
       <div id="f32" class="subItem">牛牛</div>
		<div name="f321" id="f211" class="subItem2">·<a href="http://op.youjoy.tv/lord3/cow_intervene.php?server=test" target="contentFrame">干预</a></div>
</div>
<?php } ?>


<div class="menuHead"><a href="javascript:top.window.location.href='logout.php'">退 出</a></div>


</body>
</html>
