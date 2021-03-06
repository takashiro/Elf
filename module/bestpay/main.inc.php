<?php

/***********************************************************************
Elf Web App
Copyright (C) 2013-2015  Kazuichi Takashiro

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.

takashiro@qq.com
************************************************************************/

if(!defined('S_ROOT')) exit('access denied');

if(!$_G['user']->isLoggedIn()){
	redirect('index.php?mod=user:login');
}

$paymentconfig = Wallet::ReadConfig();
if(empty($paymentconfig['enabled_method'][Wallet::ViaBestpay])){
	showmsg('bestpay_is_disabled');
}

$bestpay = readdata('bestpay');
if(empty($bestpay['key']) || empty($bestpay['merchantid'])){
	exit('Configuration Error');
}

if(!empty($_GET['ret'])){
	showmsg('支付成功！', 'index.php?mod=product');
}

$_G['trade'] = array(
	'out_trade_no' => '',
	'subject' => '',
	'total_fee' => 0.00,
	'attached_fee' => 0.00,
);

runhooks('trade_started', array(Wallet::ViaBestpay));

$trade = &$_G['trade'];
if(empty($trade['out_trade_no']) || empty($trade['subject']) || !is_numeric($trade['total_fee']))
	showmsg('illegal_operation');

$key = $bestpay['key'];
$merchantid = $bestpay['merchantid'];
$ordid = $trade['out_trade_no'];
$attachamount = $trade['attached_fee'];
$orderamount = $trade['total_fee'];
$productamount = $orderamount - $attachamount;

$productamount = sprintf('%.2f', $productamount);
$attachamount = sprintf('%.2f', $attachamount);
$orderamount = sprintf('%.2f', $orderamount);

$orderdate = date('YmdHis');					//订单日期

$macmd5 = "MERCHANTID=$merchantid&ORDERSEQ=$ordid&ORDERDATE=$orderdate&ORDERAMOUNT=$orderamount&KEY=$key";
$mac = md5($macmd5);							//校验值

$curtype = 'RMB';								//币种
$orderreqtranseq = '99'.date('YmdHis');			//订单交易流水号
$encodetype = '1';								//加密方式
$transdate = date('Ymd');						//交易日期
$busicode = '0001';								//Transaction type, Consume

$pagereturl = $_G['site_url'].'?mod=bestpay&ret=1';
$bgreturl = $_G['site_url'].'module/bestpay/api/notify.php';
$productdesc = $trade['subject'];

$productid = '0';
$tmnum = '0';
$customerid = $_G['user']->id;

?>

<html>
<body onload="document.getElementById('form').submit();">
<form id="form" action="https://wappaywg.bestpay.com.cn/payWap.do" method="post">
	<input type=hidden name="MERCHANTID" value="<?php echo $merchantid; ?>"/>
	<input type=hidden name="ORDERSEQ" value="<?php echo $ordid; ?>"/>
	<input type=hidden name="ORDERREQTRANSEQ" value="<?php echo $orderreqtranseq; ?>"/>
	<input type=hidden name="ORDERDATE" value="<?php echo $orderdate; ?>"/>
	<input type=hidden name="ORDERAMOUNT" value="<?php echo $orderamount; ?>"/>
	<input type=hidden name="PRODUCTAMOUNT" value="<?php echo $productamount; ?>"/>
	<input type=hidden name="ATTACHAMOUNT" value="<?php echo $attachamount; ?>"/>

	<input type=hidden name="CURTYPE" value="<?php echo $curtype; ?>"/>
	<input type=hidden name="ENCODETYPE" value="<?php echo $encodetype; ?>"/>
	<input type=hidden name="MERCHANTURL" value="<?php echo $pagereturl; ?>"/>
	<input type=hidden name="BACKMERCHANTURL" value="<?php echo $bgreturl; ?>"/>
	<input type=hidden name="BUSICODE" value="<?php echo $busicode; ?>"/>
	<input type=hidden name="PRODUCTDESC" value="<?php echo $productdesc; ?>"/>
	<input type=hidden name="PRODUCTID" value="<?php echo $productid; ?>"/>
	<input type=hidden name="TMNUM" value="<?php echo $tmnum?>"/>
	<input type=hidden name="CUSTOMERID" value="<?php echo $customerid?>"/>
	<input type=hidden name="MAC" value="<?php echo $mac; ?>"/>
</form>
Loading……
</body>
</html>
