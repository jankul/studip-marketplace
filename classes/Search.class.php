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

class Search
{
	const CACHE_TIMEOUT = 600;
	
	private function __construct() {}

	public static function getCachedSearch($id)
	{
		if(!isset($_SESSION['search_cache'][$id]))
			return false;
		if(time() - $_SESSION['search_cache'][$id][0] >= self::CACHE_TIMEOUT)
		{
			unset($_SESSION['search_cache'][$id]);
			return false;
		}
		return $_SESSION['search_cache'][$id][1];
	}
	
	public static function searchSimple($text, $category_id = FALSE)
	{
		$ret = array();
		$db = DBManager::get();
		if($catagory_id == FALSE)
		{
			$stmt = DBManager::get()->prepare("SELECT * FROM plugins WHERE plugin_id IN (SELECT ta.object_id FROM tags t, tags_objects ta WHERE LOWER(t.tag) LIKE LOWER(?)) AND approved=1 ORDER BY name");
			$stmt->execute(array('%'.$txt.'%'));
			$rr = $stmt->fetchAll();
			$stmt = DBManager::get()->prepare("SELECT * FROM plugins WHERE UPPER(name) LIKE UPPER(?) AND approved=1 ORDER BY name");
			$stmt->execute(array('%'.$txt.'%'));
			$rr2 = $stmt->fetchAll();
			foreach($rr2 as $r)
				if(!in_array($r, $rr))
					array_push($rr, $r);
		}
		else
		{
			$rr = $db->query(sprintf("SELECT p.* FROM plugins p, categories_plugins cp WHERE p.plugin_id IN (SELECT ta.object_id FROM tags t, tags_objects ta WHERE LOWER(t.tag) LIKE LOWER('%s')) AND cp.plugin_id=p.plugin_id AND cp.category_id='%s' AND p.approved=1 ORDER BY p.name",'%'..addslashes($txt).'%',$catagory_id))->fetchAll();
			$rr2 = $db->query(sprintf("SELECT p.* FROM plugins p, categories_plugins cp WHERE UPPER(p.name) LIKE UPPER('%s') AND cp.plugin_id=p.plugin_id AND cp.category_id='%s' AND p.approved=1 ORDER BY p.name",'%'.$txt.'%',$catagory_id))->fetchAll();
			foreach($rr2 as $r)
				if(!in_array($r, $rr))
					array_push($rr, $r);
		}
			
		foreach($rr as $r)
		{
			$p = new Plugin();
			$p->load($r['plugin_id']);
			array_push($ret, $p);
		}
		
		$id = uniqid();
		if(!isset($_SESSION['search_cache']))
			$_SESSION['search_cache'] = array();
		$_SESSION['search_cache'][$id] = array(time(), $ret);
		
		return array($id, $ret);
	}
	
	public static function searchExtended($search_items)
	{
		$ret = array();
		if(count($search_items) == 0)
			return $ret;
		
		$db = DBManager::get();
		$sql = "SELECT p.* FROM plugins p WHERE 1=1 ";
		if($search_items['category_id'] != 'all')
			$sql .= sprintf(" AND p.plugin_id IN (SELECT cp.plugin_id FROM categories_plugins cp WHERE cp.category_id='%s' AND cp.plugin_id=p.plugin_id)",$search_items['category_id']);
		if($search_items['language'] != 'all')
			$sql .= sprintf(" AND p.language LIKE '%s'",'%'.$search_items['language'].'%');
		if($search_items['search_txt'] != '')
		{
			$tmp_sql = sprintf(" LOWER(p.name) LIKE LOWER('%s')",'%'.mysql_escape_String($search_items['search_txt']).'%');
			if($search_items['fulltext'] == 'yes')
			{
				$tmp_sql .= sprintf(" OR LOWER(p.short_description) LIKE LOWER('%s')",'%'.mysql_escape_String($search_items['search_txt']).'%');
				$tmp_sql .= sprintf(" OR LOWER(p.description) LIKE LOWER('%s')",'%'.mysql_escape_String($search_items['search_txt']).'%');
			}
			$sql = $sql . ' AND (' . $tmp_sql . ')';
		}
		$sql .= " AND p.approved=1";
		$rr = $db->query($sql)->fetchAll();
		foreach($rr as $r)
		{
			$p = new Plugin();
			$p->load($r['plugin_id']);
			array_push($ret, $p);
		}
		
		$id = uniqid();
		if(!isset($_SESSION['search_cache']))
			$_SESSION['search_cache'] = array();
		$_SESSION['search_cache'][$id] = array(time(), $ret);
		
		return array($id, $ret);
	}
}
?>
