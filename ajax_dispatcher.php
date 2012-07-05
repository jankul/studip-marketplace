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

$cmd = Request::option('ajaxcmd');

switch ($cmd) {
	case 'get_current_categories':
		$plugin_id = Request::option('plugin_id'); 
		$p = new Plugin();
		$p->load($plugin_id);
		$out = "";
		foreach ($p->getCategoriesFull() as $c) {
			$template = $GLOBALS['FACTORY']->open('category_item');
			$template->set_attribute('c', $c);
			$out .= $template->render();
		}
		echo $out;
		break;
	case 'get_available_categories':
		$plugin_id = Request::option('plugin_id');
		$p = new Plugin();
                $p->load($plugin_id);
		$out = "";
		$cats = $GLOBALS['DBM']->getCategories();
		$current_cats = $p->getCategoriesFull();
		$current_cats_ids = array();
		foreach ($current_cats as $c)
			array_push($current_cats_ids,$c['category_id']);
		if (!empty($_REQUEST['hidden_cats'])) {
			$hidden_cats = json_decode(stripslashes($_REQUEST['hidden_cats']));
			if (is_array($hidden_cats))
				foreach ($hidden_cats as $h)
					array_push($current_cats_ids,$h);
		}
		foreach ($cats as $c) {
			$template = $GLOBALS['FACTORY']->open('category_item_available');
			$template->set_attribute('c', $c);
			$template->set_attribute('visibility', (in_array($c['category_id'], $current_cats_ids) ? 'none' : 'block'));
			$out .= $template->render();
		}
		echo $out;
		break;
	case 'get_category_item':
		$cat_id = Request::option('category_id'); 
		$c = $GLOBALS['DBM']->getCategory($cat_id);
		$template = $GLOBALS['FACTORY']->open('category_item');
		$template->set_attribute('c', $c);
		echo $template->render();
		break;
	case 'tag_completer':
                $val = (basename(trim($_REQUEST['value'])));
                if ($val == "") return;
                echo $GLOBALS['DBM']->searchForTags($val);
                break;
	case 'set_title_screen':
		$plugin_id = Request::option('plugin_id');
		if ($GLOBALS['PERM']->have_plugin_perm('author',$plugin_id)) {
			$screenshot_id = Request::option('screenshot_id');
			$GLOBALS['DBM']->disableCurrentTitleScreen($plugin_id);
			$s = new Screenshot();
			$s->load($screenshot_id);
			$s->setTitleScreen(1)
			  ->save();
		}
		break;
	case 'check_username':
		$username = trim($_REQUEST['username']);
		$ret = UserManagement::userAlreadyExists($username);
		echo ($ret ? 'ERROR' : 'OK');
		break;
	case 'insert_comment':
		if (!$GLOBALS['USER']) die;
		$rid = Request::option('rid');
		$comment = trim(utf8_decode($_REQUEST['comment_value']));
		if ($comment && $rid) {
			$c = new Comment();
			$c->setRangeId($rid)
			  ->setUserId($GLOBALS['USER']['user_id'])
			  ->setCommentText($comment)
			  ->save();
			$GLOBALS['MAIL']->generateCommentMail($rid,$GLOBALS['USER']['user_id'],$comment);
		}
		echo "OK";
		break;
	case 'get_comments':
		$rid = Request::option('rid');
		$comments = $GLOBALS['DBM']->getComments($rid);
		$out = "";
		if (count($comments)) {
			foreach ($comments as $c) {
				$template = $GLOBALS['FACTORY']->open('comment_item');
				$template->set_attribute('c', $c);
				$template->set_attribute('rechte', ($c->getUserId() == $GLOBALS['USER']['user_id'] || $GLOBALS['PERM']->have_perm("admin")));
				$out .= $template->render();
			}
		}
		echo $out;
		break;
	case 'remove_comment_item':
		$range_id = Request::option('rid');
		$comment_id = Request::option('item');
		$c = new Comment();
		$c->load($comment_id);
		if ($c->getUserId() == $GLOBALS['USER']['user_id'] || $GLOBALS['PERM']->have_perm("admin")) {
			$c->delete();
		}
		break;
	case 'remove_screenshot':
                $GLOBALS['AUTH']->checkPerm('author');
                $screenshot_id = Request::option('screenshot_id');
                $s = new Screenshot();
                $s->load($screenshot_id);
                $p = new Plugin();
                $p->load($s->getPluginId());
                if ($screenshot_id && $PERM->have_plugin_perm('author',$p->getPluginId())) {
                        $s->remove();
			echo "OK";
                } else
			echo "ERROR";
        	break;
	case 'get_screenshot_details':
                $GLOBALS['AUTH']->checkPerm('author');
                $screenshot_id = Request::option('screenshot_id');
                $s = new Screenshot();
                $s->load($screenshot_id);
                $p = new Plugin();
                $p->load($s->getPluginId());
		$f = new MFile();
		$f->load($s->getFileId());
                if ($screenshot_id && $PERM->have_plugin_perm('author',$p->getPluginId())) {
			$template = $GLOBALS['FACTORY']->open('edit_screenshot_details');
			$template->set_attribute('s', $s);
			$template->set_attribute('f', $f);
			$template->set_attribute('p', $p);
			echo $template->render();
                }
        	break;
	case 'set_rating':
		$GLOBALS['AUTH']->checkPerm('author');
		$rating = Request::option('val');
		$range_id = Request::option('range_id');
		$GLOBALS['DBM']->setRating($range_id, $GLOBALS['USER']['user_id'], $rating);
		$ratings = $GLOBALS['DBM']->getUserRatings($range_id);
		$user_rating = $GLOBALS['DBM']->getSpecificUserRating($range_id, $GLOBALS['USER']['user_id']);
		$rating_template = $GLOBALS['FACTORY']->open('rating');
		$rating_template->set_attribute('can_rate', true);
		$rating_template->set_attribute('current', $user_rating);
		$rating_template->set_attribute('range_id', $range_id);
		$rating_template->set_attribute('rating_width', ($user_rating == 0 ? 0 : $user_rating * 100 / MAX_RATING_VALUE));
		echo $rating_template->render();
		break;
	case 'get_rezension':
		$plugin_id = Request::option('plugin_id');
		$p = new Plugin();
		$p->load($plugin_id);
		if ($p->getApproved())
			echo $p->getRezension();
		else
			echo "";
		break;
	case 'get_available_dependency_plugins':
		$plugins = $GLOBALS['DBM']->getAllApprovedPlugins();
		$out = "";
		foreach ($plugins as $p) {
			$template = $GLOBALS['FACTORY']->open('dep_plugin_item');
                        $template->set_attribute('p', $p);
                        $out .= $template->render();
		}
		echo $out;
		break;
	case 'get_available_dependency_releases':
		$plugin_id = Request::option('plugin_id');
		$release_id = Request::option('release_id');
		$p = new Plugin();
		$p->load($plugin_id);
		if ($release_id) {
			$release = new Release();
			$release->load($release_id);
			$current_release_ids = array();
			foreach ($release->getDependencies() as $d) {
				array_push($current_release_ids, $d->getReleaseId());
			}
			array_push($current_release_ids, $release_id);
		}
		$out = "";
		if ($releases = $p->getReleases()) {
			foreach ($releases as $r) {
				$template = $GLOBALS['FACTORY']->open('dep_release_item_available');
                	        $template->set_attribute('r', $r);
				$template->set_attribute('visibility', (in_array($r->getReleaseId(), $current_release_ids) ? 'none' : 'block'));
                        	$out .= $template->render();
			}
		}
		echo $out;
		break;
	case 'get_release_item':
		$release_id = Request::option('release_id');
		$r = new Release();
		$r->load($release_id);
		$p = new Plugin();
		$p->load($r->getPluginId());
		$template = $GLOBALS['FACTORY']->open('dep_release_item_current');
                $template->set_attribute('r', $r);
		$template->set_attribute('p', $p);
             	echo $template->render();
		break;
	case 'get_current_dependencies':
		$plugin_id = Request::option('plugin_id');
		$release_id = Request::option('release_id');
		$p = new Plugin();
		$p->load($plugin_id);
		$release = new Release();
		$release->load($release_id);
		$out = "";
		foreach ($release->getDependencies() as $d) {
			$template = $GLOBALS['FACTORY']->open('dep_release_item_current');
               	        $template->set_attribute('p', $p);
               	        $template->set_attribute('r', $d);
                       	$out .= $template->render();
		}
		echo $out;
		break;
	case 'page_load':
		$page_dispatcher = Request::option('page_dispatcher');
		$page_number = Request::option('page_number');
		if(!isset($page_dispatcher))
			break;
		$instance = new $page_dispatcher();
		if(!is_subclass_of($instance, 'AbstractPageDispatcher'))
			break;
		if($page_number < 0 || $page_number >= $instance->getPageCount())
			break;	
		echo $instance->getPageTitle($page_number) . $instance->getDispatherSign() . $instance->getPageContent($page_number);
		break;
	case 'remove_participant':
		$plugin_id = Request::option('plugin_id');
		$user_id = Request::option('user_id');
		$p = new Plugin();
                $p->load($plugin_id);
                if ($GLOBALS['PERM']->have_plugin_perm('author',$p->getPluginId())) {
	                 $p->removeParticipant($user_id);
                }
		break;
	default: ;
}
die();

?>
