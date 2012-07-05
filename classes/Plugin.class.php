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

class Plugin {

	private $name = "";
	private $short_description = "";
	private $description = "";
	private $license = "";
	private $in_use = "";
	private $plugin_id = "";
	private $mkdate = 0;
	private $user_id = "";
	private $categories = array();
	private $approved = 0;
	private $classification = 'none';
	private $url = '';
	private $language = 'de';
	private $chdate = 0;

	public function __construct() {

	}

	public function getPluginId() {
		return $this->plugin_id;
	}

	public function getName() {
		return stripslashes($this->name);
	}

	public function getShortDescription() {
		return stripslashes($this->short_description);
	}

	public function getDescription() {
		return stripslashes($this->description);
	}

	public function getLicense() {
		return stripslashes($this->license);
	}

	public function getInUse() {
		return stripslashes($this->in_use);
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function getCategories() {
		return $this->categories;
	}

	public function getApproved() {
		return $this->approved;
	}

	public function getUrl() {
		return stripslashes($this->url);
	}

	public function getClassification() {
		return $this->classification;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function getChdate() {
		return $this->chdate;
	}

	public function getMkdate() {
		return $this->mkdate;
	}

	public function getRezension() {
		$r = DBManager::get()->query(sprintf("SELECT rezension_txt FROM rezension WHERE plugin_id='%s'",$this->plugin_id))->fetch(PDO::FETCH_NUM);
		return stripslashes($r[0]);
	}

	public function getParticipants() {
		$ret = array();
		$rr = DBManager::get()->query(sprintf("SELECT user_id FROM user_plugins WHERE plugin_id='%s'",$this->plugin_id))->fetchAll();
		foreach ($rr as $r) {
			$u = new User();
			$u->load($r['user_id']);
			array_push($ret, $u);
		}
		return $ret;
	}

	public function removeParticipant($user_id) {
		DBManager::get()->query(sprintf("DELETE FROM user_plugins WHERE plugin_id='%s' AND user_id='%s'",$this->plugin_id,$user_id));
	}

	public function setParticipant($user_id) {
		$r = DBManager::get()->query(sprintf("SELECT user_id FROM user_plugins WHERE plugin_id='%s' AND user_id='%s'",$this->plugin_id,$user_id))->fetchAll();
		if (count($r) == 0) {
			DBManager::get()->query(sprintf("INSERT INTO user_plugins (user_id, plugin_id) VALUES ('%s','%s')",$user_id,$this->plugin_id));
		}
	}

	public function getAuthor() {
		if ($this->plugin_id) {
			$db = DBManager::get();
			$r = $db->query(sprintf("SELECT * FROM users WHERE user_id='%s'",$this->user_id))->fetchAll();
			if (count($r)) return $r[0];
			else return FALSE;
		}
		return FALSE;
	}

	public function setClassification($s) {
		DBManager::get()->query(sprintf("UPDATE plugins SET classification='%s' WHERE plugin_id='%s'",$s,$this->plugin_id));
	}

	public function setRezension($s) {
		$r = DBManager::get()->query(sprintf("SELECT rezension_txt FROM rezension WHERE plugin_id='%s'",$this->plugin_id))->fetchAll();
		if (count($r)) {
			DBManager::get()->query(sprintf("UPDATE rezension SET rezension_txt='%s' WHERE plugin_id='%s'",mysql_escape_string($s),$this->plugin_id));
		} else {
			$id = md5(uniqid(time()));
			DBManager::get()->query(sprintf("INSERT INTO rezension (rezension_id, user_id, plugin_id, rezension_txt, mkdate) VALUE ('%s','%s','%s','%s',UNIX_TIMESTAMP())",$id,$GLOBALS['USER']['user_id'],$this->plugin_id,mysql_escape_string($s)));
		}
	}

	public function setPluginId($s) {
		$this->plugin_id = $s;
		return $this;
	}

	public function setName($s) {
		$this->name = $s;
		return $this;
	}

	public function setShortDescription($s) {
		$this->short_description = $s;
		return $this;
	}

	public function setDescription($s) {
		$this->description = $s;
		return $this;
	}

	public function setLicense($s) {
		$this->license = $s;
		return $this;
	}

	public function setInUse($s) {
		$this->in_use = $s;
		return $this;
	}

	public function setUserId($s) {
		$this->user_id = $s;
		return $this;
	}

	public function setCategories($s) {
		$this->categories = $s;
		return $this;
	}

	public function setApproved($s) {
		$this->approved = $s;
		return $this;
	}

	public function setUrl($s) {
		$this->url = $s;
		return $this;
	}

	public function setLanguage($s) {
		$this->language = $s;
		return $this;
	}

	public function save() {
		$db = DBManager::get();
		if (!$this->plugin_id) {
			$this->plugin_id = md5(uniqid(time().$this->name.$this->user_id));
			$this->mkdate = time();
			$stmt = $db->prepare("INSERT INTO plugins (plugin_id, name, short_description, description, mkdate, license, user_id, in_use, approved, url, language) VALUES (?,?,?,?,?,?,?,?,0,?,?)");
			$stmt->execute(array($this->plugin_id,addslashes($this->name),addslashes($this->short_description),addslashes($this->description),$this->mkdate,addslashes($this->license),$this->user_id,addslashes($this->in_use),addslashes($this->url),$this->language));
			// assign standard tags
        	        $rr = $db->query("SELECT * FROM tags WHERE owner='root'")->fetchAll();
                	foreach ($rr as $r) {
                        	$qq = $db->query(sprintf("SELECT p.* FROM plugins p WHERE p.plugin_id='%s' AND LOWER(p.name) LIKE '%s' AND NOT EXISTS (SELECT ta.tag_id FROM tags_objects ta WHERE ta.tag_id='%s' AND ta.object_id=p.plugin_id)",$this->plugin_id,"%".$r['tag']."%", $r['tag_id']))->fetchAll();
				foreach ($qq as $q)
                                	$db->query(sprintf("INSERT INTO tags_objects (tag_id, object_id) VALUES ('%s','%s')",$r['tag_id'],$q['plugin_id']));
                        }
		} else {
			$stmt = $db->prepare("UPDATE plugins SET name=?, short_description=?, description=?, license=?, in_use=?, approved=?, url=?, language=?, chdate=UNIX_TIMESTAMP(), user_id=? WHERE plugin_id=?");
			$stmt->execute(array(addslashes($this->name),addslashes($this->short_description),addslashes($this->description),addslashes($this->license),addslashes($this->in_use),$this->approved,addslashes($this->url),$this->language,$this->user_id,$this->plugin_id));
		}
		$db->query(sprintf("DELETE FROM categories_plugins WHERE plugin_id='%s'",$this->plugin_id));	
		foreach ($this->categories as $c) {
			$db->query(sprintf("INSERT INTO categories_plugins (category_id, plugin_id) VALUES ('%s','%s')",$c, $this->plugin_id));
		}


        }

	public function load($id) {
		$db = DBManager::get();
		$r = $db->query(sprintf("SELECT * FROM plugins WHERE plugin_id='%s'",$id))->fetchAll();
		if (count($r)) {
			$this->name = $r[0]['name'];
			$this->short_description = $r[0]['short_description'];
			$this->description = $r[0]['description'];
			$this->license = $r[0]['license'];
			$this->in_use = $r[0]['in_use'];
			$this->plugin_id = $r[0]['plugin_id'];
			$this->mkdate = $r[0]['mkdate'];
			$this->chdate = $r[0]['chdate'];
			$this->user_id = $r[0]['user_id'];
			$this->approved = $r[0]['approved'];
			$this->classification = $r[0]['classification'];
			$this->url = $r[0]['url'];
			$this->language = $r[0]['language'];
			$rr = $db->query(sprintf("SELECT category_id FROM categories_plugins WHERE plugin_id='%s'",$id))->fetchAll();
			foreach ($rr as $c)
				array_push($this->categories, $c['category_id']);
			return TRUE;
		} else return FALSE;
	}

	public function getReleases() {
		$ret = array();
		$db = DBManager::get();
		$rr = $db->query(sprintf("SELECT release_id FROM releases WHERE plugin_id='%s'",$this->plugin_id))->fetchAll();
		foreach ($rr as $r) {
			$rel = new Release();
			$rel->load($r['release_id']);
			array_push($ret, $rel);
		}
		return (count($ret) ? $ret : FALSE);
	}

	public function getCategoriesFull() {
		return DBManager::get()->query(sprintf("SELECT c.* FROM categories_plugins cp, categories c WHERE cp.plugin_id='%s' AND c.category_id=cp.category_id",$this->plugin_id))->fetchAll();
	}

	public function setTags($t) {
                if (trim($t) == "") return $this;
                $tags = explode(',',$t);
                $current_tags = $this->getTags();
                foreach ($tags as $t) {
                        $t = trim(strtolower($t));
                        if ($t == "") continue;
                        if (in_array($t, $current_tags)) continue;
                        $r = DBManager::get()->query(sprintf("SELECT * FROM tags WHERE tag='%s'",addslashes($t)))->fetchAll();
                        if (count($r)) {
                                DBManager::get()->query(sprintf("INSERT INTO tags_objects (object_id, tag_id) VALUES ('%s','%s')", $this->plugin_id, $r[0]['tag_id']));
                        } else {
                                $id = md5(uniqid(time().$this->plugin_id));
				$stmt = DBManager::get()->prepare("INSERT INTO tags (tag_id, tag, owner) VALUES (?,?,?)");
				$stmt->execute(array($id, addslashes($t), $GLOBALS['USER']['user_id']));
				DBManager::get()->query(sprintf("INSERT INTO tags_objects (object_id, tag_id) VALUES ('%s','%s')", $this->plugin_id, $id));
                        }
                        array_push($current_tags, $t);
                }
		return $this;
        }

	public function getTags() {
                $ret = array();
                $rr = DBManager::get()->query(sprintf("SELECT tag FROM tags t, tags_objects ta WHERE ta.object_id='%s' AND t.tag_id=ta.tag_id", $this->plugin_id))->fetchAll();
                foreach ($rr as $r)
                        array_push($ret, stripslashes($r['tag']));
                return $ret;
        }

	public function removeTag($t) {
		if (trim($t) != '') {
			$r = DBManager::get()->query(sprintf("SELECT t.tag_id FROM tags_objects tob, tags t WHERE t.tag_id = tob.tag_id AND LOWER(t.tag) = LOWER('%s')",addslashes($t)))->fetchAll();
			DBManager::get()->query(sprintf("DELETE FROM tags_objects WHERE tag_id='%s' AND object_id='%s'",$r[0]['tag_id'], $this->plugin_id));
			if (count($r) == 1)
				DBManager::get()->query(sprintf("DELETE FROM tags WHERE tag_id='%s' AND owner!='root'",$r[0]['tag_id']));
		}
	}

	public function getAllScreenshots() {
		$ret = array();
		$rr = DBManager::get()->query(sprintf("SELECT screenshot_id FROM screenshots WHERE plugin_id='%s'",$this->plugin_id))->fetchAll();
		foreach ($rr as $r) {
			$s = new Screenshot();
			$s->load($r['screenshot_id']);
			array_push($ret, $s);
		}
		return $ret;
	}
	
	public function getTitleScreen() {
		$r = DBManager::get()->query(sprintf("SELECT screenshot_id FROM screenshots WHERE plugin_id='%s' AND title_screen=1",$this->plugin_id))->fetchAll();
		if (count($r) == 1) {
			$s = new Screenshot();
			$s->load($r[0]['screenshot_id']);
			return $s;
		} else return FALSE;
	}

	public function remove() {
		if ($rel = $this->getReleases()) {
			foreach ($rel as $r)
				$r->remove();
		}
		foreach ($this->getAllScreenshots() as $s) 
			$s->remove();
                DBManager::get()->query(sprintf("DELETE FROM tags_objects WHERE object_id='%s'", $this->plugin_id));
                DBManager::get()->query(sprintf("DELETE FROM ratings WHERE range_id='%s'", $this->plugin_id));
                DBManager::get()->query(sprintf("DELETE FROM comments WHERE range_id='%s'", $this->plugin_id));
                DBManager::get()->query(sprintf("DELETE FROM categories_plugins WHERE plugin_id='%s'", $this->plugin_id));
		DBManager::get()->query(sprintf("DELETE FROM user_plugins WHERE plugin_id='%s'",$this->plugin_id));
                DBManager::get()->query(sprintf("DELETE FROM plugins WHERE plugin_id='%s'", $this->plugin_id));
        }

	public function getUserRatings() {
                $db = DBManager::get();
                $summe = 0;
                $rr = $db->query(sprintf("SELECT SQL_CACHE ra.rating FROM ratings ra, plugins p, releases r WHERE p.plugin_id='%s' AND r.plugin_id=p.plugin_id AND ra.range_id=r.release_id AND ra.rating IS NOT NULL AND ra.rating!=0",$this->plugin_id))->fetchAll();
                foreach ($rr as $r) {
                        $summe += $r['rating'];
                }
                if (count($rr) > 0)
                        return array('summe'=>$summe, 'schnitt'=>($summe / count($rr)), 'anzahl'=>count($rr));
                else
			return FALSE;
	}

	public function getLatestRelease() {
		$rr = DBManager::get()->query(sprintf("SELECT release_id FROM releases WHERE plugin_id='%s' ORDER BY mkdate ASC LIMIT 1",$this->plugin_id))->fetchAll();
		if (count($rr) == 1) {
			$r = new Release();
			$r->load($rr[0]['release_id']);
			return $r;
		} else
			return FALSE;
	}
}

?>
