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

class History {

	public function __construct() {

	}

	public static function add($content, $level) {
		$tmp = $_SESSION['history'];
		$_SESSION['history'] = array();
		foreach ($tmp as $i=>$t ) {
			if ($i >= $level) continue;
			else $_SESSION['history'][] = $tmp[$i];
		}
		$_SESSION['history'][] = $content;
	}

	public static function clear() {
		$_SESSION['history'] = array();
	}

	public static function show() {
		if (count($_SESSION['history'])) {
			echo $GLOBALS['FACTORY']->open('history')->render();
		}
	}

}

?>
