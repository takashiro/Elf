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

require_once './core/init.inc.php';

if(!empty($_CONFIG['site_closed'])){
	showmsg($_CONFIG['site_close_reason']);
}

if(empty($_REQUEST['mod']))
	$_REQUEST['mod'] = empty($_CONFIG['homemodule']) ? '' : $_CONFIG['homemodule'];

$module = explode(':', $_REQUEST['mod']);
if(empty($module[0]) || !preg_match('/^\w+$/', $module[0])){
	exit('illegal module id');
}

if(empty($module[1]) || !preg_match('/^\w+$/', $module[1])){
	$module[1] = 'main';
}

$module_path = 'module/'.$module[0].'/'.$module[1].'.inc.php';
file_exists($module_path) || $module_path = 'extension/'.$module_path;
if(file_exists($module_path)){
	define('MOD_NAME', $module[0]);
	define('MOD_ROOT', S_ROOT.dirname($module_path).'/');

	$_G['user'] = new User;
	$_G['user']->login();
	$_USER = $_G['user']->toReadable();

	include $module_path;
}else{
	exit('illegal module id');
}
