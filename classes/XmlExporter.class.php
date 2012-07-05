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

class XmlExporter
{
	private function __construct()
	{
		
	}
	
	public static function generatePluginsXml()
	{
		$doc = new DomDocument('1.0');
		$plugins = $doc->appendChild($doc->createElement('plugins'));
	
		$rr = DBManager::get()->query("SELECT plugin_id FROM plugins WHERE approved=1 ORDER BY mkdate DESC")->fetchAll();
		foreach($rr as $r)
		{
			$p = new Plugin();
			$p->load($r['plugin_id']);
			
			$releases = $p->getReleases();
			
			if($releases !== false)
			{
				$s = $p->getTitleScreen();
				$plugin = $plugins->appendChild($doc->createElement('plugin'));
				$plugin->setAttribute('name', rawurlencode($p->getName()));
				$plugin->setAttribute('homepage', rawurlencode($p->getUrl()));
				$plugin->setAttribute('description', $p->getDescription());
				if ($s)
					$plugin->setAttribute('image', $GLOBALS['BASE_URI'].'?dispatch=download&file_id='.$s->getFileId());
				$plugin->setAttribute('score', 'TODO');
				
				foreach($releases as $rel)
				{
					$release = $plugin->appendChild($doc->createElement('release'));
					$release->setAttribute('version', $rel->getVersion());
					$release->setAttribute('studipMinVersion', $rel->getStudipMinVersion());
					$release->setAttribute('studipMaxVersion', $rel->getStudipMaxVersion());
					$release->setAttribute('url', $GLOBALS['BASE_URI'].'?dispatch=download&file_id='.$rel->getFileId());
				}
			}
		}
		
		return $doc->saveXML();
	}
}
?>
