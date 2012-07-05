<?php
/**
* @author               Jan Kulmann <jankul@zmml.uni-bremen.de>
*/

// +---------------------------------------------------------------------------+
// Copyright (C) 2012 Jan Kulmann <jankul@zmml.uni-bremen.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+

class Content {

	private $content_id = '';
	private $content_txt = '';
	private $key = '';

	public function __construct() {

	}

	public function load($key) {
		$r = DBManager::get()->query(sprintf("SELECT * FROM mp_content WHERE ckey='%s'",$key))->fetchAll();
		$this->content_id = $r[0]['content_id'];
		$this->content_txt = $r[0]['content_txt'];
		$this->key = $r[0]['ckey'];
	}

	public function setContentTxt($s) {
		$this->content_txt = $s;
		return $this;
	}

	public function getContentTxt() {
		return stripslashes($this->content_txt);
	}

	public function getKey() {
		return $this->key;
	}

	public function save() {
		DBManager::get()->query(sprintf("UPDATE mp_content SET content_txt='%s' WHERE ckey='%s'",$this->content_txt,$this->key));
	}

}

?>
