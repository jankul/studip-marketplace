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

class Comment {

	private $comment_text = '';
	private $comment_id = '';
	private $range_id = '';
	private $user_id = '';
	private $mkdate = 0;

	public function __construct() {

	}

	public function getCommentId() {
		return $this->comment_id;
	}

	public function getRangeId() {
		return $this->range_id;
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function getCommentText() {
		return stripslashes($this->comment_text);
	}

	public function getMkdate() {
		return $this->mkdate;
	}

	public function setRangeId($s) {
		$this->range_id = $s;
		return $this;
	}

	public function setUserId($s) {
		$this->user_id = $s;
		return $this;
	}

	public function setCommentText($s) {
		$this->comment_text = $s;
		return $this;
	}

	public function load($cid) {
		$r = DBManager::get()->query(sprintf("SELECT * FROM comments WHERE comment_id='%s'",$cid))->fetchAll();
		$this->comment_id = $r[0]['comment_id'];
		$this->range_id = $r[0]['range_id'];
		$this->user_id = $r[0]['user_id'];
		$this->comment_text = $r[0]['comment_text'];
		$this->mkdate = $r[0]['mkdate'];
		return TRUE;
	}

	public function save() {
		if (!$this->comment_id) {
			$this->comment_id = md5(uniqid(time().$this->range_id));
			$this->mkdate = time();
			$stmt = DBManager::get()->prepare("INSERT INTO comments (comment_id, range_id, user_id, comment_text, mkdate) VALUES (?,?,?,?,?)");
			$stmt->execute(array($this->comment_id, $this->range_id, $this->user_id, addslashes($this->comment_text), $this->mkdate));
		} else {
			$stmt = DBManager::get()->prepare("UPDATE comments SET comment_text=? WHERE comment_id=?");
			$stmt->execute(array(addslashes($this->comment_text), $this->comment_id));
		}
	}

	public function delete() {
		DBManager::get()->query(sprintf("DELETE FROM comments WHERE comment_id='%s'",$this->comment_id));
	}

}

?>
