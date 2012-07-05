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

class GUIRenderer {
	
	public function __construct() {

	}

	public static function showIndex($content=FALSE) {
		$tags = $GLOBALS['DBM']->getTagCounter();
		$template = $GLOBALS['FACTORY']->open('show_tag_cloud');
                $template->set_attribute('tags', $tags);
                $template->set_attribute('css_uri', $GLOBALS['BASE_URI'].'css');
		$cloud = $template->render();

		$template = $GLOBALS['FACTORY']->open('start');
		$template->set_attribute('cloud', $cloud);
		$template->set_attribute('main_content', $content);
		echo $template->render();
	}

	public function getContent($item) {
		$c = new Content();
		$c->load($item);
		if (strpos($c->getContentTxt(),"@@@___SLIDER___@@@") !== FALSE)
			return preg_replace("/@@@___SLIDER___@@@/",$this->showPageSlider(),$c->getContentTxt());
		else
			return $c->getContentTxt();
	}

	public function showPageSlider() {
		$page_dispatcher = "HitlistDispatcher";

		$page_uid = uniqid();

		if(!isset($page_dispatcher))
			die("Should contain dispatcher!");

		$instance = new $page_dispatcher();

		if(!is_subclass_of($instance, 'AbstractPageDispatcher'))
			die("Dispatcher-class should be subclass of AbstractPageDispatcher!");

		$template = $GLOBALS['FACTORY']->open('page_shift');
		$template->set_attribute('page_dispatcher', $page_dispatcher);
		$template->set_attribute('page_uid', $page_uid);
		$template->set_attribute('instance', &$instance);
		return $template->render();
	}

	public function showEditContent($item) {
		$c = new Content();
		$c->load($item);
		$template = $GLOBALS['FACTORY']->open('edit_content');
		$template->set_attribute('c', $c);
                $this->showIndex($template->render());
	}

	public function showRegister() {
		$this->showIndex($GLOBALS['FACTORY']->open('register')->render());
	}

	public function showLogout() {
		$this->showIndex($this->getContent('logout'));
	}

	public function showRequestNewPassword() {
		echo $GLOBALS['FACTORY']->open('request_password')->render();
	}

	public function showConfirmationSuccessful() {
		$this->showIndex($GLOBALS['FACTORY']->open('confirmation_successful')->render());
	}

	public function showPluginGenerator() {
		$css = new CssClassSwitcher();
		$template = $GLOBALS['FACTORY']->open('show_plugin_generator');
		$template->set_attribute('css', $css);
		$this->showIndex($template->render());
	}

	public function showEditProfile() {
		$u = new User();
		$u->load($GLOBALS['USER']['user_id']);
		$template = $GLOBALS['FACTORY']->open('edit_user_profle');
		$template->set_attribute('u', $u);
		$this->showIndex($template->render());
	}

	public function showProfile($user_id, $plugin_id = FALSE) {
		$u = new User();
		$u->load($user_id);

		$question = "";
		if ($plugin_id && $GLOBALS['PERM']->have_perm('author')) {
			$template = $GLOBALS['FACTORY']->open('question');
			$template->set_attribute('plugin_id', $plugin_id);
			$question = $template->render();
		}

		$template = $GLOBALS['FACTORY']->open('profile');
		$template->set_attribute('user_id', $user_id);
		$template->set_attribute('u', $u);
		$this->showIndex($template->render().$question);
	}

	public function showLogin() {
		echo $GLOBALS['FACTORY']->open('login')->render();
	}


	public function showEditPlugin($plugin_id = FALSE) {
		$css = new CssClassSwitcher();
                $css->enableHover();
		$plugin = new Plugin();
		if ($plugin_id) $plugin->load($plugin_id);
		$template = $GLOBALS['FACTORY']->open('edit_plugin');
		$template->set_attribute('p', $plugin);
		$template->set_attribute('css', $css);
		$this->showIndex($template->render());
	}

	public function showPluginAssi($part,$titel,$license,$in_use,$url,$language,$categories,$tags,$short_description,$description) {
		$template = $GLOBALS['FACTORY']->open('edit_plugin_assi');
		$template->set_attribute('part', $part);
		$template->set_attribute('titel', $titel);
		$template->set_attribute('license', $license);
		$template->set_attribute('language', $language);
		$template->set_attribute('in_use', $in_use);
		$template->set_attribute('url', $url);
		$template->set_attribute('categories', $categories);
		$template->set_attribute('tags', $tags);
		$template->set_attribute('short_description', $short_description);
		$template->set_attribute('description', $description);
		$this->showIndex($template->render());
	}

	public function showEditRezension($plugin_id) {
		$plugin = new Plugin();
		$plugin->load($plugin_id);
		$template = $GLOBALS['FACTORY']->open('edit_rezension');
		$template->set_attribute('p', $plugin);
		$this->showIndex($template->render());
	}

	public function showExtendedSearch() {
		$this->showIndex($GLOBALS['FACTORY']->open('extended_search')->render());
	}

	public function showEditScreenshots($plugin_id) {
		$plugin = new Plugin();
		$plugin->load($plugin_id);
		$template = $GLOBALS['FACTORY']->open('edit_screenshots');
		$template->set_attribute('p', $plugin);
		$this->showIndex($GLOBALS['FACTORY']->open('modal_window')->render().$template->render());
	}

	public function showOwnPlugins($user_id) {
		$plugins = $GLOBALS['DBM']->getPluginsByUserId($user_id);
		$plugins_rendered = $this->renderPluginItems($plugins);
		$template = $GLOBALS['FACTORY']->open('search_plugins');
		$template->set_attribute('title', count($plugins)." Plugin".(count($plugins) == 1 ? '' : 's')." gefunden.");
		$template->set_attribute('plugin_list', $plugins_rendered);
		$template->set_attribute('count_plugins', count($plugins));
	
		$this->showIndex($template->render());
	}

	public function showExtendedSearchResults($search_items) {
		$plugins = $GLOBALS['DBM']->getPluginsByExtendedSearch($search_items);
		$plugins_rendered = $this->renderPluginItems($plugins);
		$template = $GLOBALS['FACTORY']->open('search_plugins');
		$template->set_attribute('title', count($plugins)." Plugin".(count($plugins) == 1 ? '' : 's')." gefunden.");
		$template->set_attribute('plugin_list', $plugins_rendered);
		$template->set_attribute('count_plugins', count($plugins));
	
		$this->showIndex($template->render());
	}


	public function showSearchResults($txt, $catagory_id) {
		$plugins = $GLOBALS['DBM']->getPluginsByTxt($txt, $catagory_id);
		$plugins_rendered = $this->renderPluginItems($plugins);
		$template = $GLOBALS['FACTORY']->open('search_plugins');
		$template->set_attribute('title', count($plugins)." Plugin".(count($plugins) == 1 ? '' : 's')." gefunden.");
		$template->set_attribute('plugin_list', $plugins_rendered);
		$template->set_attribute('count_plugins', count($plugins));
	
		$this->showIndex($template->render());
	}

	public function showHitlist($hitlist) {
		$plugins = $GLOBALS['DBM']->getPluginsByHitlist($hitlist);
		$plugins_rendered = $this->renderPluginItems($plugins);
		$template = $GLOBALS['FACTORY']->open('search_plugins');
		$template->set_attribute('title', count($plugins)." Plugin".(count($plugins) == 1 ? '' : 's')." gefunden.");
		$template->set_attribute('plugin_list', $plugins_rendered);
		$template->set_attribute('count_plugins', count($plugins));
	
		$this->showIndex($template->render());
	}

	public function showTagSearch($tag) {
		$plugins = $GLOBALS['DBM']->getPluginsByTagName($tag);
		$plugins_rendered = $this->renderPluginItems($plugins);
		$template = $GLOBALS['FACTORY']->open('search_plugins');
		$template->set_attribute('title', count($plugins)." Plugin".(count($plugins) == 1 ? '' : 's')." gefunden.");
		$template->set_attribute('plugin_list', $plugins_rendered);
		$template->set_attribute('count_plugins', count($plugins));
	
		$this->showIndex($template->render());
	}

	public function showPluginDetails($plugin_id) {
		$plugin = new Plugin();
		$plugin->load($plugin_id);

		$comments = $GLOBALS['DBM']->getComments($plugin_id);
		$template = $GLOBALS['FACTORY']->open('comments');
		$template->set_attribute('range_id', $plugin_id);
		$template->set_attribute('comments', $comments);
		$c = $template->render();
		
		$releases = $plugin->getReleases();
		$out = "";
		$css = new CssClassSwitcher();
                $css->enableHover();
		if ($releases) {
			$release_js = $GLOBALS['FACTORY']->open('rating_js');
			$release_js->set_attribute('can_rate', ($GLOBALS['PERM']->have_perm('author') ? TRUE : FALSE));
			foreach ($releases as $r) {
				$css->switchClass();
				$template = $GLOBALS['FACTORY']->open('release_item');
				$template->set_attribute('r', $r);
				$template->set_attribute('css', $css);
				$template->set_attribute('rating', $this->getCurrentRating($r->getReleaseId(), ($GLOBALS['PERM']->have_perm('author') ? TRUE : FALSE)));
				$out .= $template->render();
			}
			$out = $release_js->render() . $out;
		}

		$template = $GLOBALS['FACTORY']->open('plugin_detail');
		$template->set_attribute('image_uri', $GLOBALS['BASE_URI'].'images');
		$template->set_attribute('css', $css);
		$template->set_attribute('rating', $this->getPluginRatings(&$plugin));
		$template->set_attribute('p', $plugin);
		if ($GLOBALS['PERM']->have_perm('author') || count($comments))
			$template->set_attribute('comments', $c);
		$template->set_attribute('releases', $out);
		$this->showIndex($GLOBALS['FACTORY']->open('modal_window_rezension')->render() . $template->render());
	}

	public function showReleaseDetails($release_id) {
		$release = new Release();
		$release->load($release_id);
		$plugin = new Plugin();
		$plugin->load($release->getPluginId());

		$comments = $GLOBALS['DBM']->getComments($release_id);
		$template = $GLOBALS['FACTORY']->open('comments');
		$template->set_attribute('range_id', $release_id);
		$template->set_attribute('comments', $comments);
		$c = $template->render();

		$release_js = $GLOBALS['FACTORY']->open('rating_js');
		$release_js->set_attribute('can_rate', ($GLOBALS['PERM']->have_perm('author') ? TRUE : FALSE));
		
		$template = $GLOBALS['FACTORY']->open('release_detail');
		$template->set_attribute('image_uri', $GLOBALS['BASE_URI'].'images');
		$template->set_attribute('rating', $this->getCurrentRating($release_id, ($GLOBALS['PERM']->have_perm('author') ? TRUE : FALSE)));
		$template->set_attribute('r', $release);
		$template->set_attribute('p', $plugin);
		if ($GLOBALS['PERM']->have_perm('author') || count($comments))
			$template->set_attribute('comments', $c);
		$this->showIndex($release_js->render().$template->render());
	}
	
	public function showCategory($category_id) {
		$plugins = $GLOBALS['DBM']->getPluginsByCategory($category_id);
		$plugins_rendered = $this->renderPluginItems($plugins);
		$template = $GLOBALS['FACTORY']->open('search_plugins');
		$template->set_attribute('title', count($plugins)." Plugin".(count($plugins) == 1 ? '' : 's')." gefunden.");
		$template->set_attribute('plugin_list', $plugins_rendered);
		$template->set_attribute('count_plugins', count($plugins));
	
		$this->showIndex($template->render());
	}

	public function showEditRelease($plugin_id, $release_id=FALSE) {
		$release = new Release();
		$release->setPluginId($plugin_id);
		$f = new MFile();
		if ($release_id) {
			$release->load($release_id);
			$f->load($release->getFileId());
		}
		$template = $GLOBALS['FACTORY']->open('edit_release');
		$template->set_attribute('r', $release);
		$template->set_attribute('f', $f);
		$this->showIndex($template->render());
	}

	public function showUserManagement() {
		$css = new CssClassSwitcher();
		$css->enableHover();
		$users = $GLOBALS['DBM']->getAllUsers();
		$template = $GLOBALS['FACTORY']->open('user_management');
		$template->set_attribute('users', $users);
		$template->set_attribute('css', $css);
		$this->showIndex($template->render());
	}

	public function showUserEditForm($uid) {
		$u = new User();
		if ($uid)
			$u->load($uid);
		$template = $GLOBALS['FACTORY']->open('user_edit_form');
		$template->set_attribute('u', $u);
		$this->showIndex($template->render());
	}

	public function showPluginClearings() {
		$css = new CssClassSwitcher();
		$css->enableHover();
		$plugins = $GLOBALS['DBM']->getUnclearPlugins();
		$template = $GLOBALS['FACTORY']->open('list_unclear_plugins');
		$template->set_attribute('css', $css);
		$template->set_attribute('plugins', $plugins);
                $this->showIndex($template->render());
	}

	public static function getRatingVisual($rating) {
                $rating_template = $GLOBALS['FACTORY']->open('rating_no_action');
                $rating_template->set_attribute('current', $rating);
                $rating_template->set_attribute('rating_width', ($rating == 0 ? 0 : $rating * 100 / MAX_RATING_VALUE));
                return $rating_template->render();
        }

	public function getCurrentRating($range_id, $action = TRUE) {
		$ratings = $GLOBALS['DBM']->getUserRatings($range_id);
                $user_rating = $GLOBALS['DBM']->getSpecificUserRating($range_id, $GLOBALS['USER']['user_id']);
		$rating_width = ($ratings['schnitt'] == 0 ? 0 : $ratings['schnitt'] * 100 / MAX_RATING_VALUE);
                $rating_template = $GLOBALS['FACTORY']->open(($action ? 'rating' : 'rating_no_action'));
                $rating_template->set_attribute('can_rate', ($action ? TRUE : FALSE));
                $rating_template->set_attribute('current', $rating_width);
                $rating_template->set_attribute('range_id', $range_id);
                $rating_template->set_attribute('rating_width', $rating_width);
                return $rating_template->render();
	}

	public function getPluginRatings($plugin) {
		$ratings = $plugin->getUserRatings();
		$rating_width = ($ratings['schnitt'] == 0 ? 0 : $ratings['schnitt'] * 100 / MAX_RATING_VALUE);
                $rating_template = $GLOBALS['FACTORY']->open('rating_no_action');
                $rating_template->set_attribute('can_rate', FALSE);
                $rating_template->set_attribute('current', $rating_width);
                $rating_template->set_attribute('range_id', $range_id);
                $rating_template->set_attribute('rating_width', $rating_width);
                return $rating_template->render();
	}

	private function renderPluginItems($plugins) {
		$plugins_rendered = "";
		foreach ($plugins as $p) {
			$template = $GLOBALS['FACTORY']->open('plugin_item');
			$template->set_attribute('image_uri', $GLOBALS['BASE_URI'].'images');
			$template->set_attribute('p', $p);
			$template->set_attribute('rating', $this->getPluginRatings(&$p));
			$plugins_rendered .= $template->render();
		}
		return $plugins_rendered;
	}

}

?>
