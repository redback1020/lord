<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>Menu</title>
<link href="css/default.css" rel="stylesheet" type="text/css">
<script src="/js/jquery.js" language="javascript"></script>
<style tyle="text/css">
body {
	margin: 0px;
}

.menuHead{
	line-height: 30px;
	font-size: 14px;
	text-align: center;	
	background-color: #507BAE;	
	font-weight: bold;
	color:white;
	border-bottom:2px #aaaaaa solid;
}

.menuItem{
	padding-left:10px;
	line-height: 30px;
	font-size: 12px;
	text-align: left;
	border-bottom:1px #507BAE solid;
	color:#507BAE;
	cursor:pointer;
}

.subItem{
	padding-left:20px;
	line-height: 25px;
	font-size: 12px;
	text-align: left;
	background-color: #fff;	
	cursor:pointer;
	background:url(./image/arrow-right.png) no-repeat 8px 8px
}
.subItem2{
	padding-left:40px;
	line-height: 25px;
	font-size: 12px;
	text-align: left;
	background-color: #fff;	
}

.menuHead a{color: #fff;}
.menuHead a:hover{text-decoration: none;}
	
</style>
<script language="javascript">
	$(document).ready(function(){
		$(".sty1").hide();	
		$(".subItem2").hide();
		$(".menuItem").click(
			function(){
				var men = $("#"+this.id+"1");
				if(men.is(':visible')){
					$("#"+this.id+"1").slideUp("fast");
				}else{
					$(".subItem").css("background","url(./image/arrow-right.png) no-repeat 8px 8px");
				    $(".sty1").slideUp("slow");
				    $(".subItem2").slideUp("slow");
				    $("#"+this.id+"1").slideDown("fast");
				}
			}
		);
		
		$(".subItem").click(
			function(){
				var men = $("#"+this.id+"1");
				if(men.is(':visible')){
					$(this).css("background","url(./image/arrow-right.png) no-repeat 8px 8px");
					$("div[name="+this.id+"1]").slideUp("fast");
				}else{
					$(this).css("background","url(./image/arrow-down.png) no-repeat 8px 8px");
				//	$(".subItem").slideDown("slow");
					$("div[name="+this.id+"1]").slideDown("fast");
				}
			}
		);
	});
</script>
</head>
<body>
<div class="menuHead">菜单</div>
<#if param?index_of("100",0)&gt;-1>
<#assign allper = false>
<div style="background-color: #f5f5f5;">	
	<#if param?index_of("1009",0)&gt;-1 >	
	<#assign allper = true>
		<div class="menuItem"  id="per">系统设置</div>
		<div id="per1" class="sty1">		
			<div id="per11" class="subItem">权限管理</div>	
				<div name="per111" id="per111" class="subItem2">·<a href="/admin/role/roleList.action" target="contentFrame">后台权限管理</a></div>
				<div name="per111" id="per111" class="subItem2">·<a href="/admin/loadParameter.action" target="contentFrame">加载参数</a></div>
		</div>
	</#if>		
	
	<#if param?index_of("y",0)&gt;-1 || allper>
	<div class="menuItem"  id="y">财务管理模块</div>
	<div id="y1" class="sty1">	
		 	<div id="y11" class="subItem">财务数据统计</div>
		 		<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/financing.jsp" target="contentFrame">财务数据查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="http://fb.youjoy.com/manage/viewAllAmount.php" target="contentFrame">每日数据录入</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/getAmount.jsp" target="contentFrame">数据统计</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/getJsr.jsp" target="contentFrame">每日净收入查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/getYsk.jsp" target="contentFrame">每日应收款查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/getYfk.jsp" target="contentFrame">每日应付款查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/getJsrByM.jsp" target="contentFrame">净收入月查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/getYskByM.jsp" target="contentFrame">应收款月查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="/admin/financing/getYfkByM.jsp" target="contentFrame">应付款月查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="http://fb.youjoy.com/manage/getYskSE.php" target="contentFrame">应收款期初/末余额查询</a></div>
				<div name="y111" id="y111" class="subItem2">·<a href="http://fb.youjoy.com/manage/getYfkSE.php" target="contentFrame">应付款期初/末余额查询</a></div>
	</div>
	</#if>
	
	<#if param?index_of("a",0)&gt;-1 >	
		<div class="menuItem"  id="a">游戏管理(北美)</div>
		<div id="a1" class="sty1">
			<div id="a11" class="subItem">游戏设置</div>	
				<div name="a111" id="a111" class="subItem2">·<a href="/admin/game/gameList.action" target="contentFrame">服务器设置</a></div>
				<div name="a111" id="a111" class="subItem2">·<a href="/admin/game/manage.jsp" target="contentFrame">发放平台币</a></div>
			<div id="a12" class="subItem">用户登陆</div>		
				<div name="a121" id="a121" class="subItem2">·<a href="/admin/iprestrict/setip.action" target="contentFrame">IP限制</a></div>	
			<div id="a13" class="subItem">声望奖励</div>	
				<div name="a131" id="a131" class="subItem2">·<a href="/admin/game/grantGoldleaf.action" target="contentFrame">发放金叶子</a></div>
				<div name="a131" id="a131" class="subItem2">·<a href="/admin/game/prestigeSearch.action" target="contentFrame">声望使用率</a></div>
		</div>
	</#if>

	<#if param?index_of("b",0)&gt;-1 || allper>	
	<div class="menuItem"  id="b">充值管理(北美)</div>
	<div id="b1" class="sty1">	
		<div id="b11" class="subItem">上行（充入平台）</div>
			<div name="b111" id="b111" class="subItem2">·<a href="/admin/pay/searchAll.action" target="contentFrame">充值查询</a></div>
			<div name="b111" id="b111" class="subItem2">·<a href="/admin/game/gameMoney.action" target="contentFrame">充值基础数据</a></div>
			<div name="b111" id="b111" class="subItem2">·<a href="/admin/pay/gotoKKong.action" target="contentFrame">空门充值查询</a></div>
			<div name="b111" id="b111" class="subItem2">·<a href="/admin/pay/queryAgent.jsp" target="contentFrame">联运商充值数据</a></div>
		<div id="b12" class="subItem">下行（充入游戏）</div>
			<div name="b121" id="b121"  class="subItem2">·<a href="/admin/pay/dailypay.action" target="contentFrame">当日游戏数据</a></div>
			<div name="b121" id="b121" class="subItem2">·<a href="/admin/business/gameDataDailyResult.action" target="contentFrame">每日游戏数据汇总</a></div>
			<div name="b121" id="b121" class="subItem2">·<a href="/admin/game/gameCoin.action" target="contentFrame">基础运营数据</a></div>
			<div name="b121" id="b121" class="subItem2">·<a href="/admin/pay/goDateRechange.action" target="contentFrame">兑换查询</a></div>
	</div>
	</#if>	
		
	<#if param?index_of("c",0)&gt;-1 || allper>
	<div class="menuItem"  id="c">玩家信息(北美)</div>
	<div id="c1" class="sty1">	
		<div id="c11" class="subItem">玩家个人信息</div>
			<div name="c111" id="c111" class="subItem2">·<a href="/admin/user/userInfo.jsp" target="contentFrame">玩家平台信息</a></div>
			<div name="c111" id="c111" class="subItem2">·<a href="/admin/user/userGameInfo.jsp" target="contentFrame">玩家游戏信息</a></div>
			<div name="c111" id="c111" class="subItem2">·<a href="/admin/pay/payChange.jsp" target="contentFrame">充值兑换流水</a></div>
			<div name="c111" id="c111" class="subItem2">·<a href="/admin/game/rpChange.jsp" target="contentFrame">声望奖励详情</a></div>
		<div id="c12" class="subItem">玩家数据统计</div>
			<div name="c121" id="c121" class="subItem2">·<a href="/admin/pay/searchUUcoin.action" target="contentFrame">玩家剩余UCoins</a></div>
			<div name="c121" id="c121" class="subItem2">·<a href="/admin/pay/queryRankingRechangeRank.action" target="contentFrame">玩家充值排行</a></div>
			<div name="c121" id="c121" class="subItem2">·<a href="/admin/user/exportUserMail.jsp" target="contentFrame">导出用户邮箱</a></div>			
	</div>
	</#if>
	
	<#if param?index_of("d",0)&gt;-1 || allper>
	<div class="menuItem"  id="d">市场广告(北美)</div>
	<div id="d1" class="sty1">
		<div id="d11" class="subItem">广告设置</div>
			<div name="d111" id="d111" class="subItem2">·<a href="/admin/ad/fromList.action" target="contentFrame">广告渠道设置</a></div>

		<div id="d12" class="subItem">广告渠道充值</div>
			<div name="d121" id="d121" class="subItem2">·<a href="/admin/business/report/searchGameMoney.jsp" target="contentFrame">注册/充值监控</a></div>
			<div name="d121" id="d121" class="subItem2">·<a href="/admin/business/report/searchGameMoneyByTime.jsp" target="contentFrame">注册/充值统计</a></div>
			<div name="d121" id="d121" class="subItem2">·<a href="/admin/business/report/searchGameMoneyTotal.jsp" target="contentFrame">注册/充值汇总</a></div>
			<div name="d121" id="d121" class="subItem2">·<a href="/admin/business/report/searchCoin.jsp" target="contentFrame">注册/兑换统计</a></div>
			<div name="d121" id="d121" class="subItem2">·<a href="/admin/business/report/searchCoinTotal.jsp" target="contentFrame">注册/兑换汇总</a></div>

		<div id="d13" class="subItem">广告统计</div>
			<div name="d131" id="d131" class="subItem2">·<a href="/admin/business/report/searchCountry.jsp" target="contentFrame">市场国家统计</a></div>
			<div name="d131" id="d131" class="subItem2">·<a href="/admin/business/report/adReport.jsp" target="contentFrame">用户信息（广告渠道）</a></div>
	</div>
	</#if>
	
	<#if param?index_of("e",0)&gt;-1 || allper>
	<div class="menuItem"  id="e">运营工具(北美)</div>
	<div id="e1" class="sty1">	
		 	<div id="e11" class="subItem">投诉</div>
		 		<div name="e111" id="e111" class="subItem2">·<a href="/admin/complain/complainList.action?p=all" target="contentFrame">投诉列表</a></div>

			<div id="e12" class="subItem">新闻公告</div>
				<div name="e121" id="e121" class="subItem2">·<a href="/admin/news/add.action" target="contentFrame">添加新闻</a></div>
				<div name="e121" id="e121" class="subItem2">·<a href="/admin/news/search.action" target="contentFrame">新闻列表</a></div>
				<div name="e121" id="e121" class="subItem2">·<a href="/admin/news/makePage.jsp" target="contentFrame">生成页面</a></div>

			<div id="e13" class="subItem">邮件</div>
				<div name="e131" id="e131" class="subItem2">·<a href="/admin/mail/sendMail.jsp" target="contentFrame">邮件发送</a></div>
			<div id="e14" class="subItem">在线客服</div>
			     <div name="e141" id="e141" class="subItem2">·<a href="/admin/mail/onlineChat.jsp" target="contentFrame">在线客服登入</a></div>
			<div id="e15" class="subItem">OQ分享数查询</div>
			     <div name="e151" id="e151" class="subItem2">·<a href="/admin/oq/share.jsp" target="contentFrame">OQ分享数查询</a></div>
			<div id="e16" class="subItem">火影</div>
			     <div name="e161" id="e161" class="subItem2">·<a href="http://fb.youjoy.com/manage/viewEmail.php" target="contentFrame">火影</a></div>
	</div>
	</#if>
	
	<#if param?index_of("g",0)&gt;-1 || allper>
	<div class="menuItem"  id="g">运营工具(中国)</div>
	<div id="g1" class="sty1">
			<div id="g11" class="subItem">新闻公告</div>
				<div name="g111" id="g111" class="subItem2">·<a href="http://cn.youjoy.com/admin/news/add.action" target="contentFrame">添加新闻</a></div>
				<div name="g111" id="g111" class="subItem2">·<a href="http://cn.youjoy.com/admin/news/search.action" target="contentFrame">新闻列表</a></div>
				<div name="g111" id="g111" class="subItem2">·<a href="http://cn.youjoy.com/admin/news/makePage.jsp" target="contentFrame">生成页面</a></div>
	</div>
	</#if>
	
	<#if param?index_of("i",0)&gt;-1 || allper>
	<div class="menuItem"  id="i">运营工具(台湾)</div>
	<div id="i1" class="sty1">
			<div id="i11" class="subItem">新闻公告</div>
				<div name="i111" id="i111" class="subItem2">·<a href="http://tw.youjoy.com/admin/news/add.action" target="contentFrame">添加新闻</a></div>
				<div name="i111" id="i111" class="subItem2">·<a href="http://tw.youjoy.com/admin/news/search.action" target="contentFrame">新闻列表</a></div>
				<div name="i111" id="i111" class="subItem2">·<a href="http://tw.youjoy.com/admin/news/makePage.jsp" target="contentFrame">生成页面</a></div>
	</div>
	</#if>
	
	<#if param?index_of("f",0)&gt;-1 || allper>
	<div class="menuItem"  id="f">龙之逆袭（运营管理）</div>
	<div id="f1" class="sty1">	
		 	<div id="f11" class="subItem">运营数据相关（繁体版本）</div>
		 		<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/operatorsAll.jsp" target="contentFrame">运营数据查询</a></div>
		 		<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/gemSearch.jsp" target="contentFrame">宝石消耗查询</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/getRecharge.jsp" target="contentFrame">充值用户查询</a></div>
		 		<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/sumary2.jsp" target="contentFrame">运营数据相关查询</a></div>
		 		<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/sumary.jsp" target="contentFrame">游戏数据相关查询</a></div>
		 		<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/mobileSum.jsp" target="contentFrame">运营数据总览</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/consume.jsp" target="contentFrame">消费统计(按天查询)</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/consume2.jsp" target="contentFrame">消费统计(按小时查询)</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/userlevel.jsp" target="contentFrame">用户等级分布</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/7daysleft.jsp" target="contentFrame">7日留存查询</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/userspat.jsp" target="contentFrame">用户宠物分布</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="http://lznxquery.tw.youjoy.com/lznxRank/huodong/showEvents" target="contentFrame">活动链接点击数查询</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="http://lznxquery.tw.youjoy.com/lznxRank/query/showRankData" target="contentFrame">排行榜数据修改</a></div>
				<div name="f111" id="f111" class="subItem2">·<a href="/admin/mobile/operators/chargeInfo.jsp" target="contentFrame">付费用户相关明细</a></div>
				
				<div name="f111" id="f111" class="subItem2">·<a href="http://lznxquery.tw.youjoy.com/lznxRank/rewardQuery.html" target="contentFrame">礼包发放查询管理</a></div>
				

				
				
            <div id="f12" class="subItem">运营数据相关（简体版本）</div>
		 		<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/operatorsAll.jsp" target="contentFrame">运营数据查询</a></div>
		 		<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/gemSearch.jsp" target="contentFrame">宝石消耗查询</a></div>
				<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/getRecharge.jsp" target="contentFrame">充值用户查询</a></div>
		 		<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/sumary2.jsp" target="contentFrame">运营数据相关查询</a></div>
		 		<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/sumary.jsp" target="contentFrame">游戏数据相关查询</a></div>
		 		<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/mobileSum.jsp" target="contentFrame">运营数据总览</a></div>	
                <div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/consume.jsp" target="contentFrame">消费统计(按天查询)</a></div>	
				<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/consume2.jsp" target="contentFrame">消费统计(按小时查询)</a></div>
				<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/userlevel.jsp" target="contentFrame">用户等级分布</a></div>
				<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/7daysleft.jsp" target="contentFrame">7日留存查询</a></div>
				<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/userspat.jsp" target="contentFrame">用户宠物分布</a></div>
				<div name="f121" id="f121" class="subItem2">·<a href="/admin/mobile/operators/simple/chargeInfo.jsp" target="contentFrame">付费用户相关明细</a></div>
				<div name="f121" id="f121" class="subItem2">·<a href="http://lznxquery.tw.youjoy.com/lznxRank/rewardQuery_cn.html" target="contentFrame">礼包发放查询管理</a></div>
			 
				
	</div>
	</#if>

	<#if param?index_of("m",0)&gt;-1 || allper>
	<div class="menuItem"  id="m">电视游戏后台管理</div>
	<div id="m1" class="sty1">	
		 	<div id="m15" class="subItem">基础工具</div>
				<div name="m151" id="m151" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/online.php" target="contentFrame">在线列表</a></div>
				<div name="m151" id="m151" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/userinfo.php" target="contentFrame">用户的详细信息</a></div> 
				<div name="m151" id="m151" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/user.php" target="contentFrame">查询用户的uid</a></div> 
			<div id="m11" class="subItem">公告系统</div>
		 		<div name="m111" id="m111" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/announce.php" target="contentFrame">公告系统</a></div>
			<div id="m12" class="subItem">过渡页tips系统</div>
		 		<div name="m121" id="m121" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/enter.php?file=enter" target="contentFrame">进牌桌过渡页tips</a></div>
				<div name="m121" id="m121" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/enter.php?file=leave" target="contentFrame">出牌桌过渡页tips</a></div>
			<div id="m13" class="subItem">微信相关</div>
		 		<div name="m131" id="m131" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/weixin_user.php" target="contentFrame">绑定游戏ID的微信用户</a></div>
		 		<div name="m131" id="m131" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/weixin_nologin.php" target="contentFrame">未登录游戏的微信玩家</a></div>
		 	<div id="m14" class="subItem">争霸赛相关</div>
				<div name="m141" id="m141" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/truePlayer.php" target="contentFrame">争霸赛真实玩家玩牌轮数</a></div>	
                <div name="m141" id="m141" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/bangdan.php" target="contentFrame">争霸赛实时排名榜单</a></div>	
				<div name="m141" id="m141" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/history.php" target="contentFrame">争霸赛历史排名榜单</a></div>
				<div name="m141" id="m141" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/addData.php" target="contentFrame">加入人为数据</a></div>
			
			<div id="m16" class="subItem">充值相关</div>
				<div name="m161" id="m161" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/charge.php" target="contentFrame">充值赠送</a></div>
				<div name="m161" id="m161" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/chargeLog.php" target="contentFrame">充值记录</a></div>
				<div name="m161" id="m161" class="subItem2">·<a href="http://112.124.4.59/texas_analyse/TV/game_charge.php" target="contentFrame">充值卡记录查询</a></div>
				 
				
	</div>
	</#if>
	
	<#if param?index_of("h",0)&gt;-1 || allper>
	<div class="menuItem"  id="h">斗将三国（运营管理）</div>
	<div id="h1" class="sty1">
			<div id="h11" class="subItem">斗将三国充值查询</div>
				<div name="h111" id="h111" class="subItem2">·<a href="http://cn.youjoy.com/admin/djsg/chargeQuery.html" target="contentFrame">充值查询</a></div>
	</div>
	</#if>
	
	<#if param?index_of("j",0)&gt;-1 || allper>
	<div class="menuItem"  id="j">龙之逆袭（联运）</div>
	<div id="j1" class="sty1">	
		 	
			<div id="j13" class="subItem">运营数据相关（简体版本）</div>
		 		<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/operatorsAll.jsp" target="contentFrame">运营数据查询</a></div>
		 		<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/gemSearch.jsp" target="contentFrame">宝石消耗查询</a></div>
				<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/getRecharge.jsp" target="contentFrame">充值用户查询</a></div>
		 		<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/sumary2.jsp" target="contentFrame">运营数据相关查询</a></div>
		 		<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/sumary.jsp" target="contentFrame">游戏数据相关查询</a></div>
		 		<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/mobileSum.jsp" target="contentFrame">运营数据总览</a></div>	
                <div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/consume.jsp" target="contentFrame">消费统计(按天查询)</a></div>	
				<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/consume2.jsp" target="contentFrame">消费统计(按小时查询)</a></div>
				<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/userlevel.jsp" target="contentFrame">用户等级分布</a></div>
				<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/7daysleft.jsp" target="contentFrame">7日留存查询</a></div>
				<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/userspat.jsp" target="contentFrame">用户宠物分布</a></div>
				<div name="j131" id="j131" class="subItem2">·<a href="/admin/mobile/operators/yunying/chargeInfo.jsp" target="contentFrame">付费用户相关明细</a></div>
				 
				
	</div>
	</#if>
</div>
</#if>

<div class="menuHead"><a href="javascript:top.window.location.href='/admin/logout.action'">退出</a></div>
</body>
</html>