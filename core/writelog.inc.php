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

$logfile = S_ROOT.'./data/log/'.rdate(TIMESTAMP, 'Ymd').'_'.$logfile.'.log.php';
$need_prefix = !file_exists($logfile);
$fp = fopen($logfile, 'a');
flock($fp, LOCK_EX);
if($need_prefix){
	fwrite($fp, '<?php exit;?>');
}

$prefix = "\r\n".User::ip()."\t".rdate(TIMESTAMP)."\t";
if(is_array($data)){
	foreach($data as $v){
		fwrite($fp, $prefix.$v);
	}
}else{
	fwrite($fp, $prefix.$data);
}

fclose($fp);
