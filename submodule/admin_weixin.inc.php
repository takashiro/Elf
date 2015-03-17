<?php

if(!defined('IN_ADMINCP')) exit('access denied');

$type = !empty($_GET['type']) ? $_GET['type'] : '';

switch($type){
case 'menu':
	$wx = new WeixinAPI;
	if(isset($_POST['button'])){
		$button = str_replace('&quot;', '"', $_POST['button']);
		$button = stripslashes($button);
		$button = json_decode($button);
		$menu = array(
			'button' => $button,
		);

		$wx->setMenu($menu);
	}elseif(!empty($_GET['clear'])){
		$wx->setMenu(NULL);
	}else{
		$menu = $wx->getMenu();
	}
	break;

case 'autoreply':
	$db->select_table('autoreply');

	$action = &$_GET['action'];
	switch($action){
	case 'edit':
		$autoreply = array();

		if(!empty($_POST['keyword'])){
			$autoreply['keyword'] = $_POST['keyword'];
			$autoreply['keyword'] = explode("\n", $autoreply['keyword']);
			foreach ($autoreply['keyword'] as &$word) {
				$word = trim($word);
			}
			unset($word);
			$autoreply['keyword'] = implode("\n", $autoreply['keyword']);
		}

		if(!empty($_POST['reply'])){
			$autoreply['reply'] = addslashes(htmlspecialchars_decode(stripslashes(trim($_POST['reply']))));
		}

		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id > 0){
			$db->UPDATE($autoreply, 'id='.$id);
			$autoreply['id'] = $id;
		}else{
			$db->INSERT($autoreply);
			$autoreply['id'] = $db->insert_id();
		}

		Autoreply::RefreshCache();

		echo json_encode($autoreply);
		exit;

	case 'delete':
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id > 0){
			Autoreply::RefreshCache();

			$db->DELETE('id='.$id);
			echo $db->affected_rows();
		}else{
			echo 0;
		}
		exit;

	default:
		$autoreply = $db->MFETCH('*');
	}
	break;

default:
	$type = 'config';

	$wxconnect = readdata('wxconnect');
	$wxconnect_fields = array('app_id', 'app_secret', 'account', 'token', 'subscribe_text', 'entershop_keyword', 'bind_keyword', 'bind2_keyword');

	if($_POST){
		foreach($wxconnect_fields as $var){
			$wxconnect[$var] = isset($_POST['wxconnect'][$var]) ? $_POST['wxconnect'][$var] : '';
		}
		writedata('wxconnect', $wxconnect);
		showmsg('successfully_updated_wxconnect_config', 'refresh');
	}

	foreach($wxconnect_fields as $var){
		isset($wxconnect[$var]) || $wxconnect[$var] = '';
	}
}

include view('weixin_'.$type);

?>