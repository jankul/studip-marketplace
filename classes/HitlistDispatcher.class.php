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

class HitlistDispatcher extends AbstractPageDispatcher
{
	private $plugins;

	public function __construct() {
		$this->plugins = $GLOBALS['DBM']->getPluginsByHitlist('latest');
	}
	/**
	 * Should return the maximum number of available pages
	 */
	public function getPageCount() {
		return count($this->plugins);
	}
	/**
	 * Should return the appropriate title of given page
	 * 
	 * @param integer $num
	 */
	public function getPageTitle($num) {
		$p = $this->plugins[$num];
		return "<A HREF=\"?dispatch=show_plugin_details&plugin_id=".$p->getPluginId()."\">".htmlReady(mila($p->getName(),38))."</A>";
	}
	/**
	 * Should return the appropriate content of given page
	 * 
	 * @param integer $num
	 */
	public function getPageContent($num) {
		$template = $GLOBALS['FACTORY']->open('plugin_page');
                $template->set_attribute('image_uri', $GLOBALS['BASE_URI'].'images');
                $template->set_attribute('p', $this->plugins[$num]);
                return $template->render();
	}
	/**
	 * Should return a unique seperator-sign for dispatching ajax-request
	 */
	public function getDispatherSign()
	{
		return "+.+";
	}
}
?>
