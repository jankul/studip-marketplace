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

class Perm {

	private $permissions = array(
	        "user"       => 1,
        	"author"     => 3,
	        "admin"      => 7
		);


	public function __construct() {

	}

	private function permsum($p) {
		if (!is_array($p)) {
			return array(false, 0);
		}
		$perms = $this->permissions;

		$r = 0;
		reset($p);
		while(list($key, $val) = each($p)) {
			if (!isset($perms[$val])) {
				return array(false, 0);
			}
			$r |= $perms[$val];
		}

		return array(true, $r);
	}

	private function get_perm($user_id = false){
                if (!$user_id) $user_id = $GLOBALS['USER']['user_id'];
                if ($user_id && $user_id == $GLOBALS['USER']['user_id']){
                        return $GLOBALS['USER']['perm'];
                } else if ($user_id) {
                        $r = DBManager::get()->query(sprintf("SELECT perm FROM users WHERE user_id='%s'",$user_id))->fetch(PDO::FETCH_NUM);
                        if (!count($r)){
                                return false;
                        } else {
                                return $r[0];
                        }
                }
        }

	public function have_perm($perm, $user_id = false) {

                $pageperm = split(",", $perm);
                $userperm = split(",", $this->get_perm($user_id));

                list($ok0, $pagebits) = $this->permsum($pageperm);
                list($ok1, $userbits) = $this->permsum($userperm);

                $has_all = (($userbits & $pagebits) == $pagebits);
                if (!($has_all && $ok0 && $ok1) ) {
                        return false;
                } else {
                        return true;
                }
        }

	public function have_plugin_perm($perm, $range_id, $user_id = false) {
		if (!$user_id) {
			$user_id = $GLOBALS['USER']['user_id'];
		}

                $pageperm = split(",", $perm);
                $userperm = split(",", $this->get_plugin_perm($range_id, $user_id));

                list ($ok0, $pagebits) = $this->permsum($pageperm);
                list ($ok1, $userbits) = $this->permsum($userperm);

                $has_all = (($userbits & $pagebits) == $pagebits);

                if (!($has_all && $ok0 && $ok1) ) {
                        return false;
                } else {
                        return true;
                }
        }

	private function get_plugin_perm($range_id, $user_id) {
		$status = FALSE;
                if ($user_id && $user_id == $GLOBALS['USER']['user_id']){
                        $user_perm = $GLOBALS['USER']["perm"];
                } else {
                        $user_perm = $this->get_perm($user_id);
                        if (!$user_perm){
                                return FALSE;
                        }
                }
                if ($user_perm == "admin") {
                        return "admin";
		} else {
			$r = DBManager::get()->query(sprintf("SELECT user_id FROM plugins WHERE plugin_id='%s'",$range_id))->fetch(PDO::FETCH_NUM);
			if ($user_id == $r[0])
				return "author";
			else
				return FALSE;
		}
		return $status;
	}

}

?>
