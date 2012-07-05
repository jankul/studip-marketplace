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

require_once("lib/feedcreator/feedcreator.class.php");


class FeedGenerator
{
	private $rss;
	private $type;
	
	public function __construct($type, $title, $desc, $link)
	{
		$this->type = $type;
		
		$this->rss = new UniversalFeedCreator();
		$this->rss->title = $title;
		$this->rss->desc = $desc;
		$this->rss->link = $link;
	}
	
	/**
	 * Adds all items according to type to feed
	 */
	public function addAll()
	{
		if($this->type == 'plugin')
		{
			$db = DBManager::get();
			$rr = $db->query("SELECT plugin_id FROM plugins WHERE approved=1 ORDER BY mkdate DESC")->fetchAll();
			foreach($rr as $r)
			{
				$p = new Plugin();
				$p->load($r['plugin_id']);
				$this->addItem($p);
			}
		}
		elseif($this->type == 'release')
		{
			$db = DBManager::get();
			$rr = $db->query("SELECT r.release_id FROM releases r, plugins p WHERE p.plugin_id=r.plugin_id AND p.approved=1 ORDER BY mkdate DESC")->fetchAll();
			foreach($rr as $r)
			{
				$p = new Release();
				$p->load($r['release_id']);
				$this->addItem($p);
			}
		}
	}
	
	/**
	 * Adds a new item to the feed. Item is according to type parsed.
	 * 
	 * @param Plugin or Release $item
	 */
	public function addItem($item)
	{
		if($this->type == 'plugin')
		{
			if($item instanceof Plugin)
			{
				$userResult = $item->getAuthor();
				
				if($userResult !== false)
				{
					$user = new User();
					$user->load($userResult['user_id']);
				}
				
				$rssItem = new FeedItem();
				$rssItem->title = $item->getName();
				$rssItem->description = $item->getShortDescription() . $item->getDescription();
				$rssItem->link = $GLOBALS['BASE_URI'].'?dispatch=show_plugin_details&plugin_id='.$item->getPluginId();
				if(isset($user))
				{
					$rssItem->author = $user->getVorname() . ' `' . $user->getUsername() . '´ ' . $user->getNachname();
					$rssItem->authorEmail = $user->getEmail();
				}
			}
		}
		elseif($this->type == 'release')
		{
			if($item instanceof Release)
			{
				$userResult = $item->getAuthor();
				
				if($userResult !== false)
				{
					$user = new User();
					$user->load($userResult['user_id']);
				}
				
				$plugin = new Plugin();
				$plugin->load($item->getPluginId());
				
				$rssItem = new FeedItem();
				$rssItem->title = 'Release ' . $item->getVersion() . ' - ' . $plugin->getName();
				$rssItem->description = '';
				$rssItem->link = $GLOBALS['BASE_URI'].'?dispatch=show_release_detailsrelease_id='.$item->getReleaseId();
				if(isset($user))
				{
					$rssItem->author = $user->getVorname() . ' `' . $user->getUsername() . '´ ' . $user->getNachname();
					$rssItem->authorEmail = $user->getEmail();
				}
			}
		}
		if(isset($rssItem))
		{
			$this->rss->addItem($rssItem);
		}
	}
	
	/**
	 * Generates and outputs the current feed / items
	 * 
	 * @param string $format Format to be used, valid options are:
	 *   RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated), MBOX, OPML,
	 *   ATOM, ATOM1.0, ATOM0.3, HTML, JS
	 */
	public function outputFeed($format)
	{
		$this->rss->outputFeed($format);
	}
}
?>
