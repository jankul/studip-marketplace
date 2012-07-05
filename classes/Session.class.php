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

class Session {

	static private $instance;
	
	public function __construct() {

	}

	public function get() {
		if (is_null(Session::$instance)) {
			Session::$instance = new Session();
		}
		return Session::$instance;
	}

	public function startSession() {
		// Session-Cookie expires after 24 hours
		session_set_cookie_params(60 * 60 * 24,'/',$GLOBALS['SERVER_NAME']);
                session_start();
		if (!is_array($_SESSION['history'])) $_SESSION['history'] = array();
	}

	public function destroySession() {
		ob_end_clean();
		session_start();
		session_destroy();
		$GLOBALS['USER'] = FALSE;
	}

	public static function saveSessionParams() {
                DBManager::get()->query(sprintf("REPLACE INTO session_data SET sid='%s', user_id='%s', lastlogin=UNIX_TIMESTAMP(), fromhost='%s'",$_SESSION['sid'],$_SESSION['user_id'],$_SERVER["REMOTE_ADDR"]));
        }

        public static function getSessionParams($user_id, $sid=FALSE) {
                if ($sid)
                        return DBManager::get()->query(sprintf("SELECT * FROM session_data WHERE sid='%s' AND user_id='%s'",$sid,$user_id))->fetchAll();
                else
                        return DBManager::get()->query(sprintf("SELECT * FROM session_data WHERE user_id='%s' ORDER BY lastlogin DESC LIMIT 1",$user_id))->fetchAll();
        }

}

?>
