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

class User {

	private $user_id = '';
	private $username = '';
	private $vorname = '';
	private $nachname = '';
	private $email = '';
	private $perm = '';
	private $locked = 0;
	private $salutation = 'Herr';
	private $url = '';
	private $workplace = '';
	private $auth = 'standard';

	public function __construct() {

	}

	public function getUserId() {
		return $this->user_id;
	}

	public function getUsername() {
		return stripslashes($this->username);
	}
	
	public function getVorname() {
		return stripslashes($this->vorname);
	}

	public function getNachname() {
		return stripslashes($this->nachname);
	}

	public function getEmail() {
		return stripslashes($this->email);
	}

	public function getPerm() {
		return $this->perm;
	}

	public function getLocked() {
		return $this->locked;
	}

	public function getSalutation() {
		return $this->salutation;
	}

	public function getUrl() {
		return stripslashes($this->url);
	}

	public function getWorkplace() {
		return stripslashes($this->workplace);
	}

	public function getAuth() {
		return $this->auth;
	}

	public function setUserId($s) {
		$this->user_id = $s;
		return $this;
	}
		
	public function setUsername($s) {
		$this->username = $s;
		return $this;
	}

	public function setVorname($s) {
		$this->vorname = $s;
		return $this;
	}

	public function setNachname($s) {
		$this->nachname = $s;
		return $this;
	}

	public function setEmail($s) {
		$this->email = $s;
		return $this;
	}

	public function setPerm($s) {
		$this->perm = $s;
		return $this;
	}

	public function setLocked($s) {
		$this->locked = $s;
		return $this;
	}

	public function setSalutation($s) {
		$this->salutation = $s;
		return $this;
	}

	public function setUrl($s) {
		$this->url = $s;
		return $this;
	}

	public function setWorkplace($s) {
		$this->workplace = $s;
		return $this;
	}

	public function load($uid) {
		$db = DBManager::get();
		$r = $db->query(sprintf("SELECT user_id, username, vorname, nachname, email, perm, locked, salutation, auth FROM users WHERE user_id='%s'",$uid))->fetchAll();
		$this->user_id = $r[0]['user_id'];
		$this->username = $r[0]['username'];
		$this->vorname = $r[0]['vorname'];
		$this->nachname = $r[0]['nachname'];
		$this->email = $r[0]['email'];
		$this->perm = $r[0]['perm'];
		$this->locked = $r[0]['locked'];
		$this->salutation = $r[0]['salutation'];
		$r = $db->query(sprintf("SELECT * FROM users_info WHERE user_id='%s'",$uid))->fetchAll();
		$this->url = $r[0]['url'];
		$this->workplace = $r[0]['workplace'];
		$this->auth = $r[0]['auth'];
	}

	public function save() {
		if (!$this->user_id) {
			$this->user_id = md5(uniqid(time()));
			$stmt = DBManager::get()->prepare("INSERT INTO users (user_id, username, vorname, nachname, email, perm, locked, mkdate, salutation) VALUES (?,?,?,?,?,?,?,UNIX_TIMESTAMP(),?)");
			$stmt->execute(array($this->user_id,addslashes($this->username),addslashes($this->vorname),addslashes($this->nachname),addslashes($this->email),$this->perm,$this->locked,$this->salutation));
			$stmt = DBManager::get()->prepare("INSERT INTO users_info (user_id, url, workplace) VALUES (?,?,?)");
			$stmt->execute(array($this->user_id, addslashes($this->url), addslashes($this->workplace)));
		} else {
			$stmt = DBManager::get()->prepare("UPDATE users SET username=?, vorname=?, nachname=?, email=?, perm=?, locked=?, salutation=? WHERE user_id=?");
			$stmt->execute(array(addslashes($this->username),addslashes($this->vorname),addslashes($this->nachname),addslashes($this->email),$this->perm,$this->locked,$this->salutation, $this->user_id));
			$stmt = DBManager::get()->prepare("REPLACE INTO users_info SET user_id=?, url=?, workplace=?");
			$stmt->execute(array($this->user_id, addslashes($this->url), addslashes($this->workplace)));
		}
	}
	
}

?>
