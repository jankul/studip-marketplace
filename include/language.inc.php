<?
/**
* This function tries to find the preferred language
*
* This function tries to find the preferred language.
* It returns the first accepted language from browser settings, which is installed.
*
* @access	public
* @return		string	preferred user language, given in "en_GB"-style
*
*/
function get_accepted_languages() {
	$_language = $GLOBALS['DEFAULT_LANGUAGE'];
	$accepted_languages = explode(",", getenv("HTTP_ACCEPT_LANGUAGE"));
	if (is_array($accepted_languages) && count($accepted_languages)) {
		foreach ($accepted_languages as $temp_accepted_language) {
			foreach ($GLOBALS['INSTALLED_LANGUAGES'] as $temp_language => $temp_language_settings) {
				if (substr(trim($temp_accepted_language), 0, 2) == substr($temp_language, 0, 2)) {
					$_language = $temp_language;
					break 2;
				}
			}
		}
	}
	return $_language;
}


/**
* This function starts output via i18n system in the given language
*
* This function starts output via i18n system in the given language.
* It returns the path to the choosen language.
*
* @access	public
* @param		string	the language to use for output, given in "en_GB"-style
* @return		string	the path to the language file, given in "en"-style
*
*/
function init_i18n($_language) {
	if (isset($GLOBALS['_language_domain']) && isset($_language)) {
		$_language_path = $GLOBALS['INSTALLED_LANGUAGES'][$_language]["path"];
		setLocaleEnv($_language, $GLOBALS['_language_domain']);
	}
	return $_language_path;
}


/**
* create the img tag for graphic buttons
*
* This function creates the html text for a button.
* Decides, which button (folder)
* is used for international buttons.
*
* @access	public
* @param	string	the (german) button name
* @param	string	if mode = img, the functions return the full tag, if mode = src, it return only the src-part , if mode = input returns full input tag
* @param	string	tooltip text, if tooltip should be included in tag
* @param	string 	if mode=input this param defines the name attribut
* @return	string	html output of the button
*/
function makeButton($name, $mode = "img") {

	$url = localeButtonUrl($name . '-button.png');

	switch ($mode) {

		case 'img':
			$tag = "\n" . sprintf('<img class="button" src="%s" %s >',
			                      $url, $tooltext);
			break;
		default:
			$tag = sprintf('class="button" src="%s"', $url);

	}

	return $tag;
}


/**
* retrieves path to preferred language of user from database
*
* Can be used for sending language specific mails to other users.
*
* @access	public
* @param		string	the user_id of the recipient (function will try to get preferred language from database)
* @return		string	the path to the language files, given in "en"-style
*/
function getUserLanguagePath($uid) {
	// try to get preferred language from user
	/*$db=new DB_Seminar;
	$db->query("SELECT preferred_language FROM user_info WHERE user_id='$uid'");
	if ($db->next_record()) {
		if ($db->f("preferred_language") != NULL && $db->f("preferred_language") != "") {
			// we found a stored setting for preferred language
			$temp_language = $db->f("preferred_language");
		} else {
			// no preferred language, use system default
			$temp_language = $DEFAULT_LANGUAGE;
		}
	} else {
		// no preferred language, use system default
		$temp_language = $DEFAULT_LANGUAGE;
	}
	return $INSTALLED_LANGUAGES[$temp_language]["path"];
	*/
	return $INSTALLED_LANGUAGES[$GLOBALS['DEFAULT_LANGUAGE']]["path"];
}

/**
* switch i18n to different language
*
* This function switches i18n system to a different language.
* Should be called before writing strings to other users into database.
* Use restoreLanguage() to switch back.
*
* @access	public
* @param		string	the user_id of the recipient (function will try to get preferred language from database)
* @param		string	explicit temporary language (set $uid to FALSE to switch to this language)
*/
function setTempLanguage ($uid = FALSE, $temp_language = "") {
	/*if ($uid) {
		// try to get preferred language from user
		$db=new DB_Seminar;
		$db->query("SELECT preferred_language FROM user_info WHERE user_id='$uid'");
		if ($db->next_record()) {
			if ($db->f("preferred_language") != NULL && $db->f("preferred_language") != "") {
				// we found a stored setting for preferred language
				$temp_language = $db->f("preferred_language");
			} else {
				// no preferred language, use system default
				$temp_language = $DEFAULT_LANGUAGE;
			}
		} else {
			// should never be reached, best we can do is to set system default
			$temp_language = $DEFAULT_LANGUAGE;
		}
	}

	if ($temp_language == "") {
		// we got no arguments, best we can do is to set system default
		$temp_language = $DEFAULT_LANGUAGE;
	}

	setLocaleEnv($temp_language, $_language_domain);*/
}


/**
* switch i18n back to original language
*
* This function switches i18n system back to the original language.
* Should be called after writing strings to other users via setTempLanguage().
*
* @access	public
*/
function restoreLanguage() {
	setLocaleEnv($GLOBALS['_language'], $GLOBALS['_language_domain']);
}

/**
* set locale to a given language and select translation domain
*
* This function tries to set the appropriate environment variables and
* locale settings for the given language and also (optionally) sets the
* translation domain.
* Note: To support non-POSIX compliant systems (SuSE 9.x, OpenSolaris?),
* the environment variables LANG and LC_ALL are also set to $language.
*
* @access	public
*/
function setLocaleEnv($language, $language_domain = ''){
	putenv("LANG=$language");
	putenv("LANGUAGE=$language");
	putenv("LC_ALL=$language");
	$ret = setlocale(LC_ALL, '');
	setlocale(LC_NUMERIC, 'C');
	if($language_domain){
		bindtextdomain($language_domain, $GLOBALS['BASE_PATH'] . "/locale");
		textdomain($language_domain);
	}
	return $ret;
}

function localeButtonUrl($filename) {
  return localeUrl($filename, 'LC_BUTTONS');
}

function localePictureUrl($filename) {
  return localeUrl($filename, 'LC_PICTURES');
}

function localeUrl($filename, $category) {
  return sprintf('%s/locale/%s/%s/%s',
                 $GLOBALS['IMAGES_URL'],
                 $GLOBALS['_language_path'],
                 $category,
                 $filename);
}

?>
