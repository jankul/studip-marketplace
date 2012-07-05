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

class MFile {

	private $file_id = '';
	private $user_id = '';
	private $mkdate = 0;
	private $file_content = '';
	private $file_name = '';
	private $file_size = 0;
	private $file_type = '';

	public function __construct() {

	}

	public function getFileId() {
		return $this->file_id;
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function getMkdate() {
		return $this->mkdate;
	}

	public function getFileName() {
		return stripslashes($this->file_name);
	}

	public function getFileSize() {
		return $this->file_size;
	}

	public function getFileType() {
		return $this->file_type;
	}

	public function setFileId($s) {
		$this->file_id = $s;
		return $this;
	}

	public function setUserId($s) {
		$this->user_id = $s;
		return $this;
	}

	public function setMkdate($s) {
		$this->mkdate = $s;
		return $this;
	}

	public function setFileType($s) {
		$this->file_type = $s;
		return $this;
	}

	public function setFileName($s) {
		$this->file_name = $s;
		return $this;
	}

	public function setFileSize($s) {
		$this->file_size = $s;
		return $this;
	}

	public function load($fid) {
		$db = DBManager::get();
		$r = $db->query(sprintf("SELECT file_id, user_id, mkdate, file_name, file_size, file_type FROM file_content WHERE file_id='%s'",$fid))->fetchAll();
		$this->file_id = $r[0]['file_id'];
		$this->user_id = $r[0]['user_id'];
		$this->mkdate = $r[0]['mkdate'];
		$this->file_name = $r[0]['file_name'];
		$this->file_size = $r[0]['file_size'];
		$this->file_type = $r[0]['file_type'];
		return TRUE;
	}

	public function save() {
		if (!$this->file_id) {
			$this->file_id = md5(uniqid(time().$this->user_id));
			$this->mkdate = time();
			$stmt = DBManager::get()->prepare("INSERT INTO file_content (file_id, user_id, mkdate, file_name, file_size, file_type) VALUES (?,?,?,?,?,?)");
			$stmt->execute(array($this->file_id,$this->user_id,$this->mkdate,addslashes($this->file_name),$this->file_size,$this->file_type));
		} else {
			$stmt = DBManager::get()->prepare("UPDATE file_content SET file_name=?, file_size=? WHERE file_id=?");
			$stmt->execute(array(addslashes($this->file_name),$this->file_size, $this->file_id));
		}
	}

	public function remove() {
		DBManager::get()->query(sprintf("DELETE FROM file_content WHERE file_id='%s'",$this->file_id));
	}

}

?>
