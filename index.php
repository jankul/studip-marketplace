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

require_once "include/bootstrap.inc.php";

$dispatch = Request::option('dispatch');

if ($dispatch == 'download') {
	$file_id = Request::option('file_id');
	$d = new Downloader();
	$d->initiateDownload($file_id);
	die();
}
if ($dispatch == 'generate_plugin') {
	$p = new Generator();
	$p->setValues(array('pluginname'=>trim($_REQUEST['pluginname']),
			    'pluginclassname'=>Request::option('pluginclassname'),
			    'pluginauthor'=>trim($_REQUEST['pluginauthor']),
			    'studipminversion'=>$_REQUEST['studipminversion'],
			    'studipmaxversion'=>$_REQUEST['studipmaxversion'],
			    'plugintype'=>Request::option('plugintype')));
	$p->magic();
	die();
}
if ($dispatch == 'rss') {
	$feed = new FeedGenerator('plugin', 'Plugin RSS', 'Enthält Plugins', 'http://www.n-link.com');
	$feed->addAll();
	$feed->outputFeed('RSS2.0');
	die();
}
if ($dispatch == 'atom') {
	$feed = new FeedGenerator('plugin', 'Plugin ATOM', 'Enthält Plugins', 'http://www.n-link.com');
	$feed->addAll();
	$feed->outputFeed('ATOM1.0');
	die();
}
if ($dispatch == 'xml') {
	header('Content-type: text/xml', true);
	echo XmlExporter::generatePluginsXml();
	die();
}

if ($dispatch == "logout") {
	Session::get()->destroySession();
	header('HTTP/1.1 303 See Other');
        header('Location: '.$BASE_URI.'?dispatch=logoutdone');
}

if ($dispatch == 'do_login' || $dispatch == 'loginfromdev') {
	if (($dispatch == 'do_login' && !$AUTH->authenticate($_REQUEST['username'],$_REQUEST['passwort'])) || 
	    ($dispatch == 'loginfromdev' && !$AUTH->authenticateFromDev(CryptMP::decryptPrivate(base64_decode($_REQUEST['cryptloginkey'])), unserialize(base64_decode($_REQUEST['cryptinformation']))))) {
		$USER = FALSE;
		setMessage('error',"Fehler bei der Anmeldung! Benutzername oder Passwort ung&uuml;ltig!");
		$dispatch = 'login';
	} else {
		Session::saveSessionParams();
		setMessage('info',sprintf('Sie sind nun angemeldet, %s.',$UM->getFullnameByUserId($_SESSION['user_id'])));
		ob_end_clean();
		header('HTTP/1.1 303 See Other');
		header('Location: '.$BASE_URI);
	}
}

include_once 'templates/header.php';
require_once "include/includes.inc.php";

if ($dispatch == "logoutdone") {
	$GUI->showLogout();
}
if ($dispatch == 'register') {
	$GUI->showRegister();
}
if ($dispatch == 'new_password') {
	$GUI->showRequestNewPassword();
}
if ($dispatch == 'reset_password') {
	include_once 'lib/captcha/securimage.php';
	$securimage = new Securimage();
	if ($securimage->check($_REQUEST['captcha_code']) == false) {
		setMessage('error','Der Sicherheitscode war nicht korrekt!.');
	} else {
		$email = $UM->getEmailByUsername(Request::option('username'));
		if ($email) {
			$u = $UM->getUserByUsername(Request::option('username'));
			if ($u->getAuth() != 'standard') {
				setMessage('error','Sorry, dieser Account wird von einem anderen System authentifiziert, bitte ändern Sie das Passwort dort!');
			} else {
				$new_pw = $UM->generate_password();
				$UM->setPassword($u->getUser(),$new_pw);
				$GLOBALS['MAIL']->generateResetPasswordMail($u,$new_pw);
				setMessage('info',"Sie erhalten in K&uuml;rze eine E-Mail mit einem neuen Passwort.");
			}
		} else {
			setMessage('error','Sorry, ein Account mit diesem Usernamen ist unbekannt!');
		}
	}
	unset($dispatch);
}
if ($dispatch == 'do_register') {
	include_once 'lib/captcha/securimage.php';
	$securimage = new Securimage();
	if ($securimage->check($_REQUEST['captcha_code']) == false) {
		setMessage('error','Der Sicherheitscode war nicht korrekt!');
		$GUI->showRegister();
	} else {
		if (!$UM->userAlreadyExists(trim($_REQUEST['username']))) {
			$confirmation_token = md5(uniqid(time().$_REQUEST['username']));
			$UM->addNewUser(trim($_REQUEST['username']),
					trim($_REQUEST['vorname']),
					trim($_REQUEST['nachname']),
					trim($_REQUEST['email']),
					trim($_REQUEST['passwort']),
					$_REQUEST['salutation'],
					$confirmation_token);
			$GLOBALS['MAIL']->generateRegistrationMail($_REQUEST['username'],$_REQUEST['vorname'],$_REQUEST['nachname'],$_REQUEST['email'], $_REQUEST['salutation'],time(), $confirmation_token);
			setMessage('info',"Vielen Dank f&uuml;r Ihre Anmeldung. Sie erhalten in K&uuml;rze eine E-Mail mit einem Best&auml;tigungslink.");
		} else {
			setMessage('error','Sorry, ein Account mit diesem Usernamen existiert bereits!');
		}
	}
	unset($dispatch);
}
if ($dispatch == 'confirm') {
	if ($UM->confirmAccount(Request::option('token'))) {
		$GUI->showConfirmationSuccessful();
	} else {
		setMessage('error',"Confirmation failed!");
		unset($dispatch);
	}
}
if ($dispatch == 'login') {
	$GUI->showLogin();
}
// Eingeloggter Bereich
if ($USER) {
	if ($dispatch == 'assi') {
		$AUTH->checkPerm('author');
		$part = Request::option('part');
		if (empty($part)) $part = 1;
		$titel = trim($_REQUEST['titel']);
		$license = trim($_REQUEST['license']);
		$language = trim($_REQUEST['language']);
		if (empty($language)) $language = 'de';
		$in_use = trim($_REQUEST['in_use']);
		$url = trim($_REQUEST['url']);
		$categories = $_REQUEST['c_ids'];
                if (!is_array($categories)) $categories = array();
		$tags = trim($_REQUEST['tags']);
		$short_description = trim($_REQUEST['short_description']);
		$description = trim($_REQUEST['description']);
		$p = new Plugin();

		if ($part == 5) {
			$p->setName($titel)
			  ->setShortDescription($short_description)
			  ->setDescription($description)
			  ->setLicense($license)
			  ->setInUse($in_use)
			  ->setLanguage($language)
			  ->setUserId($USER['user_id'])
			  ->setCategories($categories)
			  ->setUrl($url)
			  ->save();
			setMessage('info',"Das Plugin wurde eingetragen, jetzt k&ouml;nnen Sie ein Release hochladen.");
			$MAIL->generateNewPluginMails($USER['user_id'],$p);
			$GUI->showEditRelease($p->getPluginId(), FALSE);
		} else {
			$GUI->showPluginAssi($part,$titel,$license,$in_use,$url,$language,$categories,$tags,$short_description,$description);
		}
	}
	if ($dispatch == 'update_user_profile') {
		$AUTH->checkPerm('author');
		include_once 'lib/captcha/securimage.php';
		$securimage = new Securimage();
		if ($securimage->check($_REQUEST['captcha_code']) == false) {
			setMessage('error','Der Sicherheitscode war nicht korrekt!');
		} else {
			if (is_array($_FILES['userfile'])) {
				try {
					Avatar::getAvatar($USER['user_id'])->createFromUpload('userfile');
					$msg =_("Die Bilddatei wurde erfolgreich hochgeladen. Eventuell sehen Sie das neue Bild erst, nachdem Sie diese Seite neu geladen haben (in den meisten Browsern F5 dr&uuml;cken). ");
				} catch (Exception $e) {
					$msg = $e->getMessage().' ';
				}
			}
			$u = new User();
			$u->load($USER['user_id']);
			$u->setUsername(trim($_REQUEST['username']))
			  ->setVorname(trim($_REQUEST['vorname']))
			  ->setNachname(trim($_REQUEST['nachname']))
			  ->setEmail(trim($_REQUEST['email']))
			  ->setSalutation(trim($_REQUEST['salutation']))
			  ->setUrl(trim($_REQUEST['url']))
			  ->setWorkplace(trim($_REQUEST['workplace']))
			  ->save();
			if (trim($_REQUEST['passwort']) != '' && trim($_REQUEST['passwort2']) != '' && $_REQUEST['passwort'] == $_REQUEST['passwort2'])
				$UM->setPassword($u->getUserId(),$_REQUEST['passwort']);
			$msg .= "Die Einstellungen wurden &uuml;bernommen.";
			setMessage('info',$msg);
		}
		$dispatch = 'show_profile';
	}
	if ($dispatch == 'save_screenshot') {
		$AUTH->checkPerm('author');
		$screenshot_id = Request::option('screenshot_id');
		$plugin_id = Request::option('plugin_id');
		$title_screen = (Request::option('title_screen') == 'yes' ? 1 : 0);
		$titel = trim($_REQUEST['titel']);
		$file_id = Request::option('file_id');
		if (is_array($_FILES['screenfile']) || (!is_array($_FILES['screenfile']) && $file_id)) {
			$file_id = $DBM->uploader($file_id,$USER['user_id'],$_FILES['screenfile']['tmp_name'], $_FILES['screenfile']['size'], $_FILES['screenfile']['name'],'screenshots');
			if (!$DBM->getErrorStr()) {
				$s = new Screenshot();
				if ($screenshot_id) 
					$s->load($screenshot_id);
				$s->setPluginId($plugin_id)
				  ->setTitel($titel)
				  ->setFileId($file_id)
				  ->setTitleScreen($title_screen);
				$s->save();
				setMessage('info',"Die Datei wurde hochgeladen.");
			} else {
				$f = new MFile();
				$f->load($file_id);
				$f->remove();
				unset($f);
				@unlink($GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/foto_'.$file_id.'.jpg');
		                @unlink($GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/foto_thumb_'.$file_id.'.jpg');
				setMessage('error',"Die Datei konnte nicht verarbeitet werden. ".$DBM->getErrorStr());
				$DBM->setErrorStr();
			}
		} else if (is_array($_FILES['zipfile'])) {
			$plugin_id = Request::option('plugin_id');
			if ($DBM->add_new_zip($_FILES['zipfile']['tmp_name'], $_FILES['zipfile']['size'], $_FILES['zipfile']['name'], $plugin_id, $USER['user_id'])) {
				setMessage('info',"Die Datei wurde hochgeladen und entpackt.");
			} else {
				setMessage('error',"Die Datei konnte nicht verarbeitet werden. ".$DBM->getErrorStr());
				$DBM->setErrorStr();
			}
		}
		$dispatch = 'show_edit_screenshots';
	}
	if ($dispatch == 'remove_screenshot') {
		$AUTH->checkPerm('author');
		$screenshot_id = Request::option('screenshot_id');
		$s = new Screenshot();
		$s->load($screenshot_id);
		$p = new Plugin();
		$p->load($s->getPluginId());
		if ($screenshot_id && $PERM->have_plugin_perm('author',$p->getPluginId())) {
			$s->remove();
			setMessage('info',"Der Screenshot wurde gel&ouml;scht.");
		} else
			setMessage('error',"Sie haben keine Berechtigung f&uuml;r dieses Plugin!");
		$dispatch = 'show_edit_screenshots';
	}
	if ($dispatch == 'set_plugin_participant') {
		$AUTH->checkPerm('author');
		$plugin_id = Request::option('plugin_id');
		$p = new Plugin();
		$p->load($plugin_id);
		if ($PERM->have_plugin_perm('author',$p->getPluginId())) {
			$p->setParticipant(Request::option('user_id'));
		}
		$dispatch = 'edit_plugin';
	}
	if ($dispatch == 'show_edit_screenshots') {
		$AUTH->checkPerm('author');
		$plugin_id = Request::option('plugin_id');
		$GUI->showEditScreenshots($plugin_id);
	}
	if ($dispatch == 'remove_release') {
		$AUTH->checkPerm('author');
		$release_id = Request::option('release_id');
		$r = new Release();
		$r->load($release_id);
		$p = new Plugin();
		$p->load($r->getPluginId());
		if ($release_id && $PERM->have_plugin_perm('author',$p->getPluginId())) {
			$r->remove();
			setMessage('info',"Das Release wurde gel&ouml;scht.");
		} else
			setMessage('error',"Sie haben keine Berechtigung f&uuml;r dieses Release!");
		$dispatch = 'edit_plugin';
	}
	if ($dispatch == 'remove_plugin') {
		$AUTH->checkPerm('author');
		$plugin_id = Request::option('plugin_id');
		if ($plugin_id && $PERM->have_plugin_perm('author',$plugin_id)) {
			$p = new Plugin();
			$p->load($plugin_id);
			$p->remove();
			setMessage('info',"Das Plugin wurde gel&ouml;scht.");
		} else
			setMessage('error',"Sie haben keine Berechtigung f&uuml;r dieses Plugin!");
		unset($dispatch);
	}
	if ($dispatch == 'save_plugin') {
		$AUTH->checkPerm('author');
		$plugin_id = Request::option('plugin_id');
		if (($plugin_id && $PERM->have_plugin_perm('author',$plugin_id)) || !$plugin_id) {
			$name = trim($_REQUEST['titel']);
			$short_description = trim($_REQUEST['short_description']);
			$description = trim($_REQUEST['description']);
			$license = trim($_REQUEST['license']);
			$in_use = trim($_REQUEST['in_use']);
			$categories = $_REQUEST['c_ids'];
			if (!is_array($categories)) $categories = array();
			$language = Request::option('language');
			$url = trim($_REQUEST['url']);
			$classification = trim($_REQUEST['classification']);
			if (!in_array($classification, array('firstclass', 'secondclass', 'none'))) $classification = 'none';
			$p = new Plugin();
			if ($plugin_id) $p->load($plugin_id);
			else $p->setUserId($USER['user_id']);
			$p->setName($name)
			  ->setShortDescription($short_description)
			  ->setDescription($description)
			  ->setLicense($license)
			  ->setInUse($in_use)
			  ->setLanguage($language)
			  ->setCategories($categories)
			  ->setUrl($url)
			  ->save();
			$p->setTags($_REQUEST['tags']);
			if ($PERM->have_perm('admin')) {
				$p->setClassification($classification);
			}
			if ($plugin_id)
				setMessage('info',"Das Plugin wurde gespeichert.");
			else {
				$_REQUEST['plugin_id'] = $p->getPluginId();
				setMessage('info',"Das Plugin wurde eingetragen.");
			}
			$dispatch = 'edit_plugin';
		} else {
			setMessage('error',"Sie haben keine Berechtigung f&uuml;r dieses Plugin!");
			unset($dispatch);
		}
	}
	if ($dispatch == 'remove_ptag') {
		$AUTH->checkPerm('author');
		$plugin_id = Request::option('plugin_id');
		if ($plugin_id && $PERM->have_plugin_perm('author',$plugin_id)) {
			$tag = trim($_REQUEST['tag']);
			$p = new Plugin();
			$p->load($plugin_id);
			$p->removeTag($tag);
		}
		$dispatch = 'edit_plugin';
	}
	if ($dispatch == 'remove_rtag') {
		$AUTH->checkPerm('author');
		$release_id = Request::option('release_id');
		$r = new Release();
		$r->load($release_id);
		if ($r->getPluginId() && $PERM->have_plugin_perm('author',$r->getPluginId())) {
			$tag = trim($_REQUEST['tag']);
			$r->removeTag($tag);
		}
		$dispatch = 'edit_release';
	}
	if ($dispatch == 'edit_profile') {
		$GUI->showEditProfile();
	}
	if ($dispatch == 'save_release') {
		$AUTH->checkPerm('author');
		$r = new Release();
		$file_id = Request::option('file_id');
		$release_id = Request::option('release_id');
		$plugin_id = Request::option('plugin_id');
		$dependency_ids = $_REQUEST['dep_ids'];
		if (!is_array($dependency_ids)) $dependency_ids = array();

		$err = "";
		$manifest = FALSE;
		if (is_array($_FILES['releasefile']) && $_FILES['releasefile']['size']) {
			$manifest = $DBM->checkReleaseZip($_FILES['releasefile']['tmp_name'], $_FILES['releasefile']['size'], $_FILES['releasefile']['name']);
			if (!$manifest || !count($manifest)) {
				$err .= $DBM->getErrorStr();
				$DBM->setErrorStr();
			} else {
				$file_id = $DBM->uploader($file_id,$USER['user_id'],$_FILES['releasefile']['tmp_name'], $_FILES['releasefile']['size'], $_FILES['releasefile']['name'],'releases');
				if ($DBM->getErrorStr()) {
					$err .= $DBM->getErrorStr();
					$DBM->setErrorStr();
				}
			}
		}

		// Neues Release
		if ($manifest && count($manifest) && !$err) {
			if ($release_id) $r->load($release_id);
			$r->setPluginId($plugin_id)
			  ->setVersion(trim($manifest['version']))
			  ->setStudipMinVersion(trim($manifest['studipMinVersion']))
			  ->setStudipMaxVersion(trim($manifest['studipMaxVersion']))
			  ->setOrigin(trim($manifest['origin']))
			  ->setUserId($USER['user_id'])
			  ->setFileId($file_id)
			  ->setDependencies($dependency_ids)
			  ->setReleaseType(trim($_REQUEST['release_type']))
			  ->save();
			$r->setTags($_REQUEST['tags']);
		} else if ($release_id) {
			// Update
			$r->load($release_id);
			if ($manifest && count($manifest)) {
				$r->setVersion(trim($manifest['version']))
				  ->setStudipMinVersion(trim($manifest['studipMinVersion']))
				  ->setStudipMaxVersion(trim($manifest['studipMaxVersion']))
				  ->setOrigin(trim($manifest['origin']));
			}
			$r->setUserId($USER['user_id'])
			  ->setDependencies($dependency_ids)
			  ->setReleaseType(trim($_REQUEST['release_type']))
			  ->save();
			$r->setTags($_REQUEST['tags']);

		} else {
			// Fehler
			$err = "Fehler beim Hochladen des Releases! ".$err;
		}
		if ($err)
			setMessage('error',$err);
		else
			setMessage('info',"Das Release wurde gespeichert.");
		$dispatch = 'edit_plugin';
	}
	if ($dispatch == 'edit_plugin') {
		$AUTH->checkPerm('author');
		History::add(array('uri'=>'?dispatch=edit_plugin&plugin_id='.Request::option('plugin_id'),'txt'=>'Plugin bearbeiten'),2);
		$GUI->showEditPlugin($_REQUEST['plugin_id']);
	}
	if ($dispatch == 'view_own_plugins') {
		$AUTH->checkPerm('author');
		History::add(array('uri'=>'?dispatch=view_own_plugins','txt'=>'Meine Plugins'),0);
		$GUI->showOwnPlugins($USER['user_id']);
	}
	if ($dispatch == 'edit_release') {
		$AUTH->checkPerm('author');
		History::add(array('uri'=>'?dispatch=edit_release&release_id='.Request::option('release_id').'&plugin_id='.Request::option('plugin_id'),'txt'=>'Release bearbeiten'),3);
		$GUI->showEditRelease(Request::option('plugin_id'), Request::option('release_id'));
	}
	
	// Admin Area
	if ($PERM->have_perm('admin')) {
		if ($dispatch == 'set_plugin_user') {
			$user_id = Request::option('user_id');
			$plugin_id = Request::option('plugin_id');
			if ($user_id && $plugin_id) {
				$p = new Plugin();
				$p->load($plugin_id);
				$p->setUserId($user_id)->save();
				setMessage('info',"Der Benutzer wurde zugewiesen.");
				$GUI->showEditPlugin($plugin_id);
			}
		}
		if ($dispatch == 'edit_user') {
			if (Request::option('user_id')) {
				History::add(array('uri'=>'?dispatch=edit_user&user_id='.Request::option('user_id'),'txt'=>'Benutzerverwaltung'),1);
				$GUI->showUserEditForm(Request::option('user_id'));
			}
		}
		if ($dispatch == 'show_admin_add_user') {
			$GUI->showUserEditForm(FALSE);
		}
		if ($dispatch == 'save_user') {
			$user_id = Request::option('user_id');
			$u = new User();
			if ($user_id)
				$u->load($user_id);
			$u->setUsername(trim($_REQUEST['username']))
			  ->setVorname(trim($_REQUEST['vorname']))
			  ->setNachname(trim($_REQUEST['nachname']))
			  ->setEmail(trim($_REQUEST['email']))
			  ->setPerm(trim($_REQUEST['perm']))
			  ->setLocked(trim($_REQUEST['locked']))
			  ->save();
			if (!$user_id)
				$UM->setPassword($u->getUserId(),md5(uniqid(time())));
			if (trim($_REQUEST['passwort']) && trim($_REQUEST['passwort2']) && trim($_REQUEST['passwort']) == trim($_REQUEST['passwort2'])) {
				$UM->setPassword($u->getUserId(),trim($_REQUEST['passwort']));
				$GLOBALS['MAIL']->generateResetPasswordMail($u,trim($_REQUEST['passwort']));
			}
			setMessage('info',"Die Benutzerdaten wurden ver&auml;ndert / neu eingetragen!");
			$dispatch = 'user_management';
		}
		if ($dispatch == 'user_management') {
			History::add(array('uri'=>'?dispatch=user_management','txt'=>'Benutzerverwaltung'),0);
			$GUI->showUserManagement();
		}
		if ($dispatch == 'do_clearing') {
			$plugin_id = Request::option('plugin_id');
			$p = new Plugin();
			$p->load($plugin_id);
			$p->setApproved(1)
			  ->save();
			$GLOBALS['MAIL']->generateAprovementMail($p);
			setMessage('info',"Das Plugin wurde freigeschaltet und die Autorin / der Autor benachrichtigt.");
			$dispatch = "clearing";
		}
		if ($dispatch == 'do_suspend') {
			$plugin_id = Request::option('plugin_id');
			$p = new Plugin();
			$p->load($plugin_id);
			$p->setApproved(0)
			  ->save();
			$GLOBALS['MAIL']->generateSuspendMail($p);
			setMessage('info',"Das Plugin wurde gesperrt und die Autorin / der Autor benachrichtigt.");
			$dispatch = "clearing";
		}
		if ($dispatch == "clearing") {
			$GUI->showPluginClearings();
		}

		if ($dispatch == 'save_rezension') {
			$plugin_id = Request::option('plugin_id');
			$p = new Plugin();
                        $p->load($plugin_id);
			$p->setRezension($_REQUEST['rezension_txt']);
			setMessage('info',"Die Rezension wurde gespeichert.");
			$dispatch = 'edit_rezension';
		}
		if ($dispatch == 'edit_rezension') {
			$plugin_id = Request::option('plugin_id');
			$GUI->showEditRezension($plugin_id);
		}
		if ($dispatch == 'save_content') {
			$key = Request::option('key');
			$content_txt = trim($_REQUEST['content_txt']);
			$c = new Content();
			$c->load($key);
			$c->setContentTxt($content_txt)
			  ->save();
			setMessage('info',"Inhalt gespeichtert.");
			$dispatch = 'edit_content';
		}
		if ($dispatch == 'edit_content') {
			$key = Request::option('key');
			$GUI->showEditContent($key);
		}
	}
	if ($dispatch == 'send_question') {
		include_once 'lib/captcha/securimage.php';
		$securimage = new Securimage();
		if ($securimage->check($_REQUEST['captcha_code']) == false && !($user = $GLOBALS['AUTH']->getAuthenticatedUser())) {
			setMessage('error','Der Sicherheitscode war nicht korrekt!');
			$plugin_id = Request::option('plugin_id');
			$p = new Plugin();
			$p->load($plugin_id);
			$GUI->showProfile($p->getUserId(), $plugin_id);
		} else {
			$plugin_id = Request::option('plugin_id');
			$email = trim($_REQUEST['email']);
			$users_name = trim($_REQUEST['users_name']);
			$question = trim($_REQUEST['question']);
			$question_type = trim($_REQUEST['question_type']);
			if ($plugin_id && $email && $question) {
				$GLOBALS['MAIL']->generateQuestionMail($plugin_id, $question, $question_type, $users_name, $email);
				setMessage('info',"Die Anfage wurde versendet.");
				unset($dispatch);
			}
		}
	}
}
// Eingeloggter Bereich

if ($dispatch == 'show_extended_search') {
	$GUI->showExtendedSearch();
}
if ($dispatch == 'show_plugin_generator') {
	$GUI->showPluginGenerator();
}
if ($dispatch == 'extended_search') {
	$search_txt = trim($_REQUEST['search_txt']);
	$fulltext = Request::option('fulltext');
	$category_id = Request::option('category_id');
	$language = Request::option('language');
	History::add(array('uri'=>'?dispatch=extended_search&search_txt='.urlencode($search_txt).'&fulltext='.urlencode($fulltext).'&category_id='.$category_id.'&language='.$language,'txt'=>'Erweiterte Suche'),0);
	$GUI->showExtendedSearchResults(array('search_txt'=>$search_txt, 'fulltext'=>$fulltext, 'category_id'=>$category_id, 'language'=>$language));
}
if ($dispatch == 'show_profile') {
	$plugin_id = Request::option('plugin_id');
	$username = trim($_REQUEST['username']);
	$user_id = $GLOBALS['UM']->getUserIdByUsername($username);
	if (!$user_id)
		$user_id = $USER['user_id'];
	$GUI->showProfile($user_id, ($plugin_id ? $plugin_id : FALSE));
}
if ($dispatch == 'tagsearch') {
	$tag = Request::quoted('tag');
	History::add(array('uri'=>'?dispatch=tagsearch&tag='.urlencode(stripslashes($tag)),'txt'=>'Tag-Suche ('.htmlReady(stripslashes($tag)).')'),0);
	$GUI->showTagSearch($tag);
}
if ($dispatch == 'search') {
	$txt = trim($_REQUEST['search_txt']);
	$category_id = Request::option('category_id');
	History::add(array('uri'=>'?dispatch=search&search_txt='.urlencode($txt).'&category_id='.$category_id,'txt'=>'Suche'),0);
	if ($category_id == 'all') $category_id = FALSE;
	$GUI->showSearchResults($txt,$category_id);
}
if ($dispatch == "hitlist") {
	$hitlist = trim($_REQUEST['part']);
	if (in_array($hitlist,array('recommended','latest','most_downloaded','most_rated'))) {
		$liste = "";
		switch ($hitlist) {
			case 'recommended':
				$liste = "Empfohlene Plugins";
				break;
			case 'latest': 
				$liste = "Neueste Releases";
				break;
			case 'most_downloaded':
				$liste = "Am h&auml;ufigsten heruntergeladen";
				break;
			case 'most_rated':
				$liste = "Am meisten bewertet";
				break;
			default: ;
		}
		History::add(array('uri'=>'?dispatch=hitlist&part='.urlencode($hitlist),'txt'=>'Hitliste ('.$liste.')'),0);
		$GUI->showHitlist($hitlist);
	} else 
		unset($dispatch);
}
if ($dispatch == 'show_category') {
	$category_id = Request::option('category_id');
	if ($category_id) {
		$c = $DBM->getCategory($category_id);
		History::add(array('uri'=>'?dispatch=show_category&category_id='.$category_id,'txt'=>$c['name']),0);
		$GUI->showCategory($category_id);
	} else
		unset($dispatch);
}
if ($dispatch == 'show_release_details') {
	$rid = Request::option('release_id');
	$r = new Release();
	$r->load($rid);
	History::add(array('uri'=>'?dispatch=show_release_details&release_id='.$rid,'txt'=>'Release '.htmlReady($r->getVersion())),2);
	$GUI->showReleaseDetails($rid);
}
if ($dispatch == 'show_plugin_details') {
	$pid = Request::option('plugin_id');
	$p = new Plugin();
	$p->load($pid);
	History::add(array('uri'=>'?dispatch=show_plugin_details&plugin_id='.$pid,'txt'=>htmlReady($p->getName())),1);
	$GUI->showPluginDetails($pid);
}

if (in_array($dispatch, array('welcome','marktplatz','links','team','impressum','datenschutz','nutzungsbedingungen','faq','tutorials','devfaq'))) {
	History::clear();
	$GUI->showIndex($GUI->getContent($dispatch));
}
/*if ($dispatch == 'send_question') {
	include_once 'lib/captcha/securimage.php';
        $securimage = new Securimage();
        if ($securimage->check($_REQUEST['captcha_code']) == false && !($user = $GLOBALS['AUTH']->getAuthenticatedUser())) {
                setMessage('error','Der Sicherheitscode war nicht korrekt!');
		$plugin_id = Request::option('plugin_id');
		$p = new Plugin();
		$p->load($plugin_id);
		$GUI->showProfile($p->getUserId(), $plugin_id);
        } else {
		$plugin_id = Request::option('plugin_id');
		$email = trim($_REQUEST['email']);
		$users_name = trim($_REQUEST['users_name']);
		$question = trim($_REQUEST['question']);
		$question_type = trim($_REQUEST['question_type']);
		if ($plugin_id && $email && $question) {
			$GLOBALS['MAIL']->generateQuestionMail($plugin_id, $question, $question_type, $users_name, $email);
			setMessage('info',"Die Anfage wurde versendet.");
			unset($dispatch);
		}
	}
}*/

if (!$dispatch) {
	History::clear();
	$GUI->showIndex(($PERM->have_perm('author') ? $GLOBALS['FACTORY']->open('greeting_logged_in')->render() : $GUI->getContent('welcome')));
}


include_once 'templates/footer.php';
?>
