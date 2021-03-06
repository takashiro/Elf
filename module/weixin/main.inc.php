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

$actions = array('login', 'bind', 'unbind');
$action = !empty($_GET['action']) && in_array($_GET['action'], $actions) ? $_GET['action'] : $actions[0];

if($action == 'login'){
	$wxconfig = readdata('wxconnect');

	if($_G['user']->isLoggedIn()){
		if(empty($wxconfig['no_prompt_on_login'])){
			showmsg('you_have_logged_in', 'index.php');
		}else{
			redirect('index.php');
		}
	}

	if(empty($_GET['user']) || empty($_GET['key'])){
		showmsg('unexpected_link_with_inadequate_parameters', 'index.php');
	}

	$authkey = new Authkey($_GET['user']);
	if($authkey->isExpired()){
		showmsg('expired_wxlogin_link');
	}

	if(!$authkey->matchOnce($_GET['key'])){
		showmsg('expired_wxlogin_link');
	}

	$user = new User;

	$open_id = $_GET['user'];
	$user->fetch('*', array('wxopenid' => $open_id));
	if($user->id <= 0){
		$wx = new WeixinAPI;
		$wxuser = $wx->getUserInfo($open_id);
		if($wxuser && isset($wxuser['unionid'])){
			$user->fetch('*', array('wxunionid' => $wxuser['unionid']));
			if($user->id > 0){
				$user->wxopenid = $open_id;
			}
		}
	}

	if($user->id <= 0){
		$user->account = null;
		$user->pwmd5 = '';
		$user->wxopenid = $open_id;
		$user->regtime = TIMESTAMP;
		$user->logintime = TIMESTAMP;

		if($wxuser && isset($wxuser['nickname'])){
			$user->nickname = $wxuser['nickname'];
		}else{
			$user->nickname = lang('message', 'wxuser');
		}
		if($wxuser && isset($wxuser['unionid'])){
			$user->wxunionid = $wxuser['unionid'];
		}

		//@to-do: remove this
		if(!empty($_COOKIE['referrerid'])){
			$referrerid = intval($_COOKIE['referrerid']);
			rsetcookie('referrerid');
		}elseif(!empty($_GET['referrerid'])){
			$referrerid = intval($_GET['referrerid']);
		}else{
			$referrerid = 0;
		}

		if($referrerid > 0 && User::Exist($referrerid)){
			$user->referrerid = $referrerid;
		}

		$user->insert('IGNORE');
		if($db->affected_rows <= 0){
			$user = new User;
			$user->fetch('*', array('wxopenid' => $open_id));
		}
	}

	$user->force_login();

	if(empty($wxconfig['no_prompt_on_login'])){
		showmsg('successfully_logged_in', 'index.php');
	}else{
		redirect('index.php');
	}

}elseif($action == 'bind'){
	if(!$_G['user']->isLoggedIn()){
		showmsg('binding_require_user_logged_in', 'back');
	}

	if(!array_key_exists('user', $_GET) || !array_key_exists('key', $_GET)){
		showmsg('unexpected_link_with_inadequate_parameters', 'index.php');
	}

	$authkey = new Authkey($_GET['user']);
	if($authkey->isExpired()){
		showmsg('expired_wxbind_link');
	}

	if(User::Exist($_GET['user'], 'wxopenid')){
		showmsg('wxopenid_binded_to_another_account');
	}

	if($authkey->matchOnce($_GET['key'])){

		$wx = new WeixinAPI;
		$wxuser = $wx->getUserInfo($_GET['user']);
		if($wxuser){
			if(isset($wxuser['nickname'])){
				$_G['user']->nickname = $wxuser['nickname'];
			}
			if(isset($wxuser['unionid'])){
				if(User::Exist($wxuser['unionid'], 'wxunionid')){
					showmsg('wxopenid_binded_to_another_account');
				}
				$_G['user']->wxunionid = $wxuser['unionid'];
			}
		}

		$_G['user']->wxopenid = $_GET['user'];

		showmsg('successfully_binded_wxopenid', 'index.php');
	}else{
		showmsg('invalid_wxbind_link');
	}

}else{
	if($_G['user']->isLoggedIn()){
		if(empty($_G['user']->account)){
			showmsg('cannot_unbind_wxopenid_with_account_empty', 'index.php?mod=user');
		}

		$_G['user']->wxopenid = NULL;
		$_G['user']->wxunionid = NULL;
		showmsg('successfully_unbinded_wxopenid', 'refresh');
	}else{
		showmsg('binding_require_user_logged_in', 'index.php?mod=user:login');
	}
}
