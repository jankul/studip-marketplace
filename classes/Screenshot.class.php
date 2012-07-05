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

class Screenshot {

	private $screenshot_id = '';
	private $plugin_id = '';
	private $mkdate = 0;
	private $title_screen = 0;
	private $file_id = '';
	private $titel = '';
	
	public function __construct() {

	}

	public function getScreenshotId() {
		return $this->screenshot_id;
	}

	public function getPluginId() {
		return $this->plugin_id;
	}

	public function getMkdate() {
		return $this->mkdate;
	}

	public function getTitleScreen() {
		return $this->title_screen;
	}

	public function getFileId() {
		return $this->file_id;
	}

	public function getTitel() {
		return stripslashes($this->titel);
	}
	
	public function setPluginId($s) {
		$this->plugin_id = $s;
		return $this;
	}

	public function setTitleScreen($s) {
		$this->title_screen = $s;
		return $this;
	}

	public function setFileId($s) {
		$this->file_id = $s;
		return $this;
	}

	public function setTitel($s) {
		$this->titel = $s;
		return $this;
	}

	public function load($id) {
		$r = DBManager::get()->query(sprintf("SELECT * FROM screenshots WHERE screenshot_id='%s'",$id))->fetchAll();
		$this->screenshot_id = $r[0]['screenshot_id'];
		$this->plugin_id = $r[0]['plugin_id'];
		$this->mkdate = $r[0]['mkdate'];
		$this->title_screen = $r[0]['title_screen'];
		$this->file_id = $r[0]['file_id'];
		$this->titel = $r[0]['titel'];
		return TRUE;
	}

	public function save() {
		if (!$this->screenshot_id) {
			$this->screenshot_id = md5(uniqid(time()));
			$this->mkdate = time();
			$stmt =  DBManager::get()->prepare("INSERT INTO screenshots (screenshot_id, plugin_id, mkdate, title_screen, file_id, titel) VALUES (?,?,?,?,?,?)");
			$stmt->execute(array($this->screenshot_id, $this->plugin_id, $this->mkdate, $this->title_screen, $this->file_id, addslashes($this->titel)));
		} else {
			$stmt =  DBManager::get()->prepare("UPDATE screenshots SET title_screen=?, titel=? WHERE screenshot_id=?");
			$stmt->execute(array($this->title_screen, addslashes($this->titel), $this->screenshot_id));
		}
	}

	public function remove() {
		DBManager::get()->query(sprintf("DELETE FROM screenshots WHERE screenshot_id='%s'",$this->screenshot_id));
		DBManager::get()->query(sprintf("DELETE FROM file_content WHERE file_id='%s'",$this->file_id));
		@unlink($GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/foto_'.$this->file_id.'.jpg');
		@unlink($GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/foto_thumb_'.$this->file_id.'.jpg');
	}
}

?>
