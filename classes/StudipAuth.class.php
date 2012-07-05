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

ini_set('soap.wsdl_cache_enabled', '0');
ini_set('soap.wsdl_cache_ttl', '0');

class StudipAuth {

	private $client;
	
	public function __construct() { 
		$this->client = new SoapClient($GLOBALS['WSDL_ENDPOINT'],
						array("uri" => "urn:studip_wsd",                 //Ein Namespace
						      "style" => SOAP_RPC,                               //Art der Handhabung, hier Methodenaufruf (Remote Procedure Call)
						      "use" => SOAP_ENCODED                     //Verschlüsselte Übertragung
						     )
					      );
	}

	public function checkUserCredentials($user_name, $password) {
		$user_name = base64_encode(CryptMP::encryptPrivate($user_name));
		$password = base64_encode(CryptMP::encryptPrivate($password));
		$confirmed = FALSE;
		try {
			$confirmed = $this->client->check_user_credentials($GLOBALS['SOAP_API_KEY'],$user_name, $password);
		} catch (Exception $e) {
			echo 'Exception: ',  $e->getMessage(), "\n";
		}
		return $confirmed;
	}

	public function getUserInformation($user_name) {
		$user_name = base64_encode(CryptMP::encryptPrivate($user_name));
		$userinformation = array();
		try {
			$userinformation = $this->client->get_user_by_user_name($GLOBALS['SOAP_API_KEY'],$user_name);
		} catch (Exception $e) {
			echo 'Exception: ',  $e->getMessage(), "\n";
		}
		return $userinformation;
	}

	public function authenticate($user_name, $password) {
		if (!$this->checkUserCredentials($user_name, $password)) return FALSE;

		$cryptinformation = unserialize(base64_decode($this->getUserInformation($user_name)));

		if (!count($cryptinformation)) {
			return FALSE;
		}

		$first_name = trim(CryptMP::decryptPrivate(base64_decode($cryptinformation['first_name'])));
                $last_name  = trim(CryptMP::decryptPrivate(base64_decode($cryptinformation['last_name'])));
                $email      = trim(CryptMP::decryptPrivate(base64_decode($cryptinformation['email'])));

		$userinformation = array('first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>$email);

		if (empty($first_name) || empty($last_name) || empty($email)) {
			return FALSE;
		}
		return $userinformation;
	}

}

?>
