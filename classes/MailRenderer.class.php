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

class MailRenderer {

	private $header;
	
	public function __construct() {
		$this->header = 'From: '. $GLOBALS['SUPPORT_ADDRESS'] . "\r\n" . 
				'Reply-To: '. $GLOBALS['SUPPORT_ADDRESS'] . "\r\n" .
				'X-Mailer: Stud.IP Plugin Marketplace';
	}

	public function generateRegistrationMail($username, $vorname,$nachname,$email,$salutation,$mkdate,$confirmation_token) {
		$template = $GLOBALS['FACTORY']->open('mails/mail_register');
		$template->set_attribute('username', $username);
		$template->set_attribute('vorname', $vorname);
		$template->set_attribute('mkdate', $mkdate);
		$template->set_attribute('email', $email);
		$template->set_attribute('nachname', $nachname);
		$template->set_attribute('salutation', $salutation);
		$template->set_attribute('link', $GLOBALS['BASE_URI'].'?dispatch=confirm&token='.$confirmation_token);
		$mail_content = $template->render();
		mail($email,'Plugin Marktplatz: Bestätigung erforderlich',$mail_content, $this->header);
	}

	public function generateAprovementMail($p) {
		$u = new User();
		$u->load($p->getUserId());
		$template = $GLOBALS['FACTORY']->open('mails/mail_plugin_approvement');
		$template->set_attribute('p', $p);
		$template->set_attribute('u', $u);
		$mail_content = $template->render();
		mail($u->getEmail(),'Plugin Marktplatz: Plugin angenommen',$mail_content, $this->header);
	}

	public function generateSuspendMail($p) {
		$u = new User();
		$u->load($p->getUserId());
		$template = $GLOBALS['FACTORY']->open('mails/mail_plugin_suspend');
		$template->set_attribute('p', $p);
		$template->set_attribute('u', $u);
		$mail_content = $template->render();
		mail($u->getEmail(),'Plugin Marktplatz: Plugin gesperrt',$mail_content, $this->header);
	}

	public function generateResetPasswordMail($u, $new_pw) {
		$template = $GLOBALS['FACTORY']->open('mails/reset_password');
		$template->set_attribute('u', $u);
		$template->set_attribute('new_pw', $new_pw);
		$mail_content = $template->render();
		mail($u->getEmail(),'Plugin Marktplatz: Neues Passwort',$mail_content, $this->header);
	}

	public function generateNewPluginMails($user_id, $p) {
		$u = new User();
		$u->load($user_id);
		$template = $GLOBALS['FACTORY']->open('mails/mail_new_plugin');
		$template->set_attribute('u', $u);
		$template->set_attribute('p', $p);
		$mail_content = $template->render();
		$admins = UserManagement::getUsersByPerm('admin');
		foreach ($admins as $a)
			mail($a->getEmail(),'Plugin Marktplatz: Neues Plugin zur Freischaltung',$mail_content, $this->header);
	}
	

	public function generateCommentMail($range_id, $comment_user_id, $comment) {
		$r = new Release();
		$p = new Plugin();
		if ($r->load($range_id)) {
			$rel_version = $r->getVersion();
			$p->load($r->getPluginId());
		} else {
			$p->load($range_id);
		}
		$u = new User();
		$u->load($p->getUserId());
		$cu = new User();
		$cu->load($comment_user_id);
		
		$template = $GLOBALS['FACTORY']->open('mails/mail_comment');
		$template->set_attribute('u', $u);
		$template->set_attribute('cu', $cu);
		$template->set_attribute('comment', $comment);
		$template->set_attribute('p', $p);
		$template->set_attribute('rel_version', $rel_version);
		$mail_content = $template->render();
		mail($u->getEmail(),'Plugin Marktplatz: Neuer Kommentar',$mail_content, $this->header);
	}

	public function generateQuestionMail($plugin_id, $question, $question_type, $users_name, $email) {
		$p = new Plugin();
		$p->load($plugin_id);
		$recipient = $p->getAuthor();
		$subject = htmlReady($question_type)." zum Plugin ".htmlReady($p->getName());
		$footer = "\n\n-- \r\nDies ist eine Nachricht aus dem Stud.IP Plugin-Marktplatz (".$GLOBALS['BASE_URI']."). Sie können direkt darauf antworten.";
		$header = "From: ". ($users_name ? $users_name : 'Unknown') . " <" . $email . ">\r\n"; //optional headerfields
		mail($recipient['email'], $subject, $question.$footer, $header);
	}
	
}

?>
