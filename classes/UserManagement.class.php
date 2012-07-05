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

class UserManagement {

	private $userdata;
	static $db;

	public function __construct($uid = FALSE) {
		$this->db = DBManager::get();
		$this->userdata = array();
		if ($uid) {
			$r = $this->db->query(sprintf("SELECT * FROM users WHERE user_id='%s'",$uid))->fetchAll();
			if (count($r) == 1) {
				$this->userdata = $r[0];
			}
		}
	}

	public function addNewUser($username, $vorname, $nachname, $email, $passwort, $salutation, $confirmation_token, $authplugin='standard') {
                $id = md5(uniqid(time().$username));
		$username = strtolower($username);
		$stmt = DBManager::get()->prepare("INSERT INTO users (user_id, username, vorname, nachname, email, passwort, salt, confirmation_token, remember_token, email_confirmed, mkdate, salutation, perm, auth) VALUES (?,?,?,?,?,?,?,?,'',0,UNIX_TIMESTAMP(),?,'author',?)");
		$stmt->execute(array($id,addslashes($username),addslashes($vorname),addslashes($nachname),addslashes($email),md5($passwort),'',$confirmation_token,$salutation,$authplugin));
        }

	public static function userAlreadyExists($username) {
		$username = strtolower($username);
                $r = DBManager::get()->query(sprintf("SELECT * FROM users WHERE username='%s'",mysql_escape_string($username)))->fetchAll();
                return (count($r) ? TRUE : FALSE);
        }

	public static function updateUserInformation($username, $userinformation) {
		$username = strtolower($username);
		$stmt = DBManager::get()->prepare("UPDATE users SET vorname=?, nachname=?, email=? WHERE username=?");
		$stmt->execute(array($userinformation['first_name'], $userinformation['last_name'], $userinformation['email'], $username));
	}

	public function confirmAccount($confirmation_token) {
                $r = $this->db->query(sprintf("SELECT * FROM users WHERE confirmation_token='%s'",$confirmation_token))->fetchAll();
                if (count($r)) {
                        $this->db->query(sprintf("UPDATE users SET email_confirmed=1 WHERE confirmation_token='%s'", $confirmation_token));
                        return TRUE;
                } else {
                        return FALSE;
                }
        }

	public static function getEmailByUserId($uid) {
		$r = DBManager::get()->query(sprintf("SELECT email FROM users WHERE user_id='%s'",$uid))->fetch(PDO::FETCH_NUM);
		return $r[0];
	}

	public static function getFullnameByUserId($uid) {
		$r = DBManager::get()->query(sprintf("SELECT vorname, nachname FROM users WHERE user_id='%s'",$uid))->fetch(PDO::FETCH_NUM);
		return $r[0].' '.$r[1];
	}

	public static function getUsernameByUserId($uid) {
		$r = DBManager::get()->query(sprintf("SELECT username FROM users WHERE user_id='%s'",$uid))->fetch(PDO::FETCH_NUM);
		return $r[0];
	}

	public static function getUserIdByUsername($uname) {
		$uname = strtolower($uname);
		$r = DBManager::get()->query(sprintf("SELECT user_id FROM users WHERE username='%s'",mysql_escape_string($uname)))->fetch(PDO::FETCH_NUM);
		return $r[0];
	}

	public static function getEmailByUsername($username) {
		$username = strtolower($username);
		$r = DBManager::get()->query(sprintf("SELECT email FROM users WHERE username='%s'",mysql_escape_string($username)))->fetch(PDO::FETCH_NUM);
		return $r[0];
	}

	public static function getUserByUsername($username) {
		$username = strtolower($username);
		$r = DBManager::get()->query(sprintf("SELECT user_id FROM users WHERE username='%s'",mysql_escape_string($username)))->fetchAll();
		$u = new User();
		$u->load($r[0]['user_id']);
		return $u;
	}

	/**
        * generate a secure password of $length characters [a-z0-9]
        *
        * @access       private
        * @param        integer $length number of characters
        * @return       string password
        */
        public static function generate_password($length = 8) {
                mt_srand((double)microtime()*1000000);
                for ($i=1;$i<=$length;$i++) {
                        $temp = mt_rand() % 36;
                        if ($temp < 10)
                                $temp += 48;     // 0 = chr(48), 9 = chr(57)
                        else
                                $temp += 87;     // a = chr(97), z = chr(122)
                        $pass .= chr($temp);
                }
                return $pass;
        }

	public function setPassword($user_id, $new_password) {
		DBManager::get()->query(sprintf("UPDATE users SET passwort='%s' WHERE user_id='%s'",md5($new_password),$user_id));
	}

	public static function getUsersByPerm($perm) {
		$ret = array();
		$rr = DBManager::get()->query(sprintf("SELECT user_id FROM users WHERE perm='%s'",$perm))->fetchAll();
		foreach ($rr as $r) {
			$u = new User();
			$u->load($r['user_id']);
			$ret[] = $u;
		}
		return $ret;
	}
}

?>
