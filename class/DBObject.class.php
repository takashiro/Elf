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

abstract class DBObject{
	protected $attr = array();
	protected $oattr = array();
	protected $update = array();
	protected $table = null;

	const PRIMARY_KEY = 'id';

	static function table(){
		global $db;
		return $db->select_table(static::TABLE_NAME);
	}

	function __construct(){
		global $db;
		$this->table = self::table();
	}

	function __destruct(){
		$id = $this->attr(static::PRIMARY_KEY);
		if($id > 0){
			if($this->oattr){
				foreach($this->oattr as $key => $value){
					if($value !== $this->attr[$key]){
						$this->update[$key] = $this->attr[$key];
					}
				}
			}

			if($this->update){
				$this->table->update($this->update, static::PRIMARY_KEY.'=\''.$this->attr(static::PRIMARY_KEY).'\'');
			}
		}
	}

	function loadData($data){
		if(is_array($data)){
			$this->attr = $this->oattr = $data;
		}
	}

	function fetch($item, $condition){
		if(is_array($condition)){
			$c = array();
			foreach($condition as $attr => $value){
				if($value !== null){
					$value = raddslashes($value);
					$c[] = "`{$attr}`='{$value}'";
				}else{
					$c[] = "`{$attr}` IS NULL";
				}
			}
			$condition = implode(' AND ', $c);
		}

		$this->attr = $this->oattr = $this->table->fetch_first($item, $condition);
	}

	public function exists(){
		return !empty($this->attr[static::PRIMARY_KEY]);
	}

	public function toArray(){
		return $this->attr;
	}

	public function toReadable(){
		return $this->attr;
	}

	public function __get($attr){
		return isset($this->attr[$attr]) ? $this->attr[$attr] : null;
	}

	public function __set($attr, $value){
		return $this->attr[$attr] = $value;
	}

	public function __isset($attr){
		return isset($this->attr[$attr]);
	}

	public function attr($attr, $value = null){
		if($value === null){
			return isset($this->attr[$attr]) ? $this->attr[$attr] : null;
		}else{
			$this->attr[$attr] = $value;
		}
	}

	public function update($attr, $value){
		$this->update[$attr] = $value;
	}

	public function insert($extra = ''){
		$this->table->insert($this->attr, false, $extra);

		$id = $this->table->insert_id();
		if($id){
			$this->oattr = $this->attr;
			$this->attr(static::PRIMARY_KEY, $id);
		}
		return $this->attr(static::PRIMARY_KEY);
	}

	public function deleteFromDB(){
		$this->table->delete(array(static::PRIMARY_KEY => $this->attr(static::PRIMARY_KEY)));
		$this->attr = $this->oattr = array();
	}

	function uploadImage($var, $attr = null, $width = null, $height = null){
		$attr = $attr ?? $var;

		if($this->id && !empty($_FILES[$var]) && $_FILES[$var]['error'] == 0){
			$image = new GdImage($_FILES[$var]['tmp_name']);
			if(!$image->isValid()){
				return false;
			}

			$new_extension = $image->getExtensionId();
			if($new_extension != $this->$attr){
				$this->removeImage($attr);
				$this->$attr = $image->getExtensionId();
			}

			$dest_path = $this->getImage($attr);
			if($width){
				$height = $height ?? $width;
				$image->thumb($width, $height);
				$image->save($dest_path);
			}else{
				move_uploaded_file($_FILES[$var]['tmp_name'], $dest_path);
			}

			return true;
		}

		return false;
	}

	function removeImage($attr){
		if($this->hasImage($attr)){
			@unlink(S_ROOT.$this->getImage($attr));
			$this->$attr = null;
		}
	}

	function hasImage($attr){
		return $this->$attr !== null;
	}

	function getImage($attr){
		if(!empty($this->$attr)){
			return 'data/attachment/'.static::TABLE_NAME.'_'.$this->id.'_'.$attr.'.'.GdImage::Extension($this->$attr);
		}else{
			return '';
		}
	}

	static public function Delete($id, $extra = ''){
		$id = intval($id);

		if($extra){
			$extra = ' AND ('.$extra.')';
		}

		global $db;
		$table = $db->select_table(static::TABLE_NAME);
		$table->delete(static::PRIMARY_KEY.'='.$id.$extra);
		return $table->affected_rows();
	}

	static public function Exist($id, $field = ''){
		if(!$field){
			$field = static::PRIMARY_KEY;
		}

		global $db;
		$table = $db->select_table(static::TABLE_NAME);
		return $table->result_first($field, '`'.$field.'`=\''.$id.'\' LIMIT 1');
	}

	static public function Count(){
		global $db;
		$table = $db->select_table(static::TABLE_NAME);
		return $table->result_first('COUNT(*)');
	}
}
