<?
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

$_never_globalize_request_params = array('msg','BASE_URI','BASE_PATH','CONVERT_PATH','DYNAMIC_CONTENT_URL','DYNAMIC_CONTENT_PATH','REFRESH','TMP_PATH','SERVER_NAME');
foreach($_never_globalize_request_params as $one_param){
        if (isset($_REQUEST[$one_param])){
                unset($GLOBALS[$one_param]);
        }
}

require_once dirname(__FILE__).'/../classes/Session.class.php';
require_once dirname(__FILE__).'/language.inc.php';

$SUPPORT_ADDRESS = 'marketplace@zmml.uni-bremen.de';
$SERVER_NAME = 'plugins.studip.de';
$REFRESH = 60; // Minuten
Session::get()->startSession();
if (!$_SESSION['msg_type'])
        $_SESSION['msg_type'] = 'info';

$INSTALLED_LANGUAGES["de_DE"] = array ("path"=>"de", "picture"=>"lang_de.gif", "name"=>"Deutsch");
$INSTALLED_LANGUAGES["en_GB"] = array ("path"=>"en", "picture"=>"lang_en.gif", "name"=>"English");

$DEFAULT_LANGUAGE = "de_DE";  // which language should we use if we can gather no information from user?
$_language_path = 'de';

$_language_domain = "marketplace";

include '/home/splugin/dbpass.inc';
require_once dirname(__FILE__).'/visual.inc.php';
require_once dirname(__FILE__).'/../lib/CssClassSwitcher.inc.php';
require_once dirname(__FILE__).'/../lib/MessageBox.class.php';
require_once dirname(__FILE__).'/../lib/flexi/flexi.php';
require_once dirname(__FILE__).'/../lib/DBManager.class.php';
require_once dirname(__FILE__).'/../lib/Request.class.php';
require_once dirname(__FILE__).'/../lib/Avatar.class.php';
require_once dirname(__FILE__).'/../classes/CryptMP.class.php';
require_once dirname(__FILE__).'/../classes/User.class.php';
require_once dirname(__FILE__).'/../classes/Perm.class.php';
require_once dirname(__FILE__).'/../classes/UserManagement.class.php';
require_once dirname(__FILE__).'/../classes/GUIRenderer.class.php';
require_once dirname(__FILE__).'/../classes/MailRenderer.class.php';
require_once dirname(__FILE__).'/../classes/MPDBM.class.php';
require_once dirname(__FILE__).'/../classes/Auth.class.php';
require_once dirname(__FILE__).'/../classes/Plugin.class.php';
require_once dirname(__FILE__).'/../classes/Release.class.php';
require_once dirname(__FILE__).'/../classes/MFile.class.php';
require_once dirname(__FILE__).'/../classes/Downloader.class.php';
require_once dirname(__FILE__).'/../classes/Content.class.php';
require_once dirname(__FILE__).'/../classes/Screenshot.class.php';
require_once dirname(__FILE__).'/../classes/Comment.class.php';
require_once dirname(__FILE__).'/../classes/Generator.class.php';
require_once dirname(__FILE__).'/../classes/History.class.php';
require_once dirname(__FILE__).'/../classes/FeedGenerator.class.php';
require_once dirname(__FILE__).'/../classes/XmlExporter.class.php';
require_once dirname(__FILE__).'/../classes/AbstractPageDispatcher.class.php';
require_once dirname(__FILE__).'/../classes/HitlistDispatcher.class.php';


$BASE_URI = 'http://plugins.studip.de/';
$BASE_PATH = '/home/splugin/wwwroot/marketplace/';
$TMP_PATH = '/home/splugin/phptmp/';
$FACTORY = new Flexi_TemplateFactory(dirname(__FILE__).'/../templates');
$IMAGES_URL = $BASE_URI . 'images';
$DYNAMIC_CONTENT_URL = $BASE_URI . 'content';
$DYNAMIC_CONTENT_PATH = $BASE_PATH . 'content';
$CONVERT_PATH = "/home/splugin/wwwroot/convert";
$WSDL_ENDPOINT = "http://develop.studip.de/studip/plugins_packages/ZMML/MarketplacePlugin/soap.php?wsdl";
$SOAP_API_KEY = "";
$REMOTE_LOGIN_KEY = "";

$ZIP_USE_INTERNAL = false;
$ZIP_PATH = "/home/splugin/wwwroot/zip";
$ZIP_OPTIONS = "-r";

DBManager::getInstance()
  ->setConnection('splugin',
                  'mysql:host='.$GLOBALS['DB_HOST'].
                  ';dbname='.$GLOBALS['DB_DATABASE'],
                  $GLOBALS['DB_USER'],
                  $GLOBALS['DB_PASSWORD']);

$UM = new UserManagement();
$GUI = new GUIRenderer();
$AUTH = new Auth();
$DBM = new MPDBM();
$MAIL = new MailRenderer();

$USER = $AUTH->getAuthenticatedUser();

$PERM = new Perm();

function setMessage($type, $msg) {
        $_SESSION['msg_type'] = $type;
        $_SESSION['msg'] = $msg;
}

define("MAX_RATING_VALUE",5);

?>
