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

class CryptMP {

	public function __construct() {

	}

	public static function decryptPrivate($txt) {
		$fp = fopen(dirname(__FILE__)."/../ssl/private.key","r");
		$priv_key = fread($fp,8192);
		fclose($fp);
		$res = openssl_get_privatekey($priv_key, '1234');
		openssl_private_decrypt($txt,$newsource,$res);
		return $newsource;
	}

	public static function decryptPublic($txt) {
		$fp = fopen (dirname(__FILE__)."/../ssl/cert.crt","r"); 
		$pub_key = fread($fp,8192); 
		fclose($fp);
		openssl_get_publickey($pub_key);
		openssl_public_decrypt($txt,$newsource,$pub_key);
		return $newsource;
	}

	public static function encryptPrivate($txt) {
		$fp = fopen(dirname(__FILE__)."/../ssl/private.key","r");
		$priv_key = fread($fp,8192); 
		fclose($fp);
		$res = openssl_get_privatekey($priv_key, '1234'); 
		openssl_private_encrypt($txt,$crypttext,$res);
		return $crypttext;
	}

	public static function encryptPublic($txt) {
		$fp = fopen(dirname(__FILE__)."/../ssl/cert.crt","r");
		$pub_key = fread($fp,8192);
		fclose($fp);
		openssl_get_publickey($pub_key);
		openssl_public_encrypt($txt,$crypttext,$pub_key);
		return $crypttext;
	}

}

?>
