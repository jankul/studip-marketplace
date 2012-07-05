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


class MPDBM {

	private $error_str = '';
	private $tmp_path = '';
	private $authenticated = FALSE;

	function __construct() {
		$this->tmp_path = $GLOBALS['DYNAMIC_CONTENT_PATH'].'/tmp';
		$this->authenticated = $GLOBALS['AUTH']->getAuthenticatedUser();
	}

	public function getErrorStr() {
		return $this->error_str;
	}

	public function setErrorStr($s = '') {
		$this->error_str = $s;
	}

	public function disableCurrentTitleScreen($plugin_id) {
		DBManager::get()->query(sprintf("UPDATE screenshots SET title_screen=0 WHERE plugin_id='%s'",$plugin_id));
	}

	function getAllUsers() {
		$ret = array();
		$rr = DBManager::get()->query(sprintf("SELECT * FROM users ORDER BY nachname, vorname, username"))->fetchAll();
		foreach ($rr as $r) {
			$u = new User();
			$u->load($r['user_id']);
			array_push($ret, $u);
		}
		return $ret;
	}

	function getCategories() {
		$db = DBManager::get();
		$r = $db->query(sprintf("SELECT c.*, COUNT(p.plugin_id) count_cat FROM categories c LEFT JOIN categories_plugins cp USING (category_id) LEFT JOIN plugins p ON (cp.plugin_id=p.plugin_id AND (p.approved=1 OR (p.approved=0 AND 1=%d))) GROUP BY c.category_id ORDER BY c.name", ($this->authenticated ? 1 : 0)))->fetchAll();
		return $r;
	}

	function getCategory($category_id) {
		$db = DBManager::get();
		$r = $db->query(sprintf("SELECT c.* FROM categories c WHERE c.category_id='%s'",$category_id))->fetchAll();
		return $r[0];
	}

	function getAllApprovedPlugins() {
		$ret = array();
		$db = DBManager::get();
		$rr = $db->query("SELECT p.* FROM plugins p WHERE p.approved=1 ORDER BY p.name")->fetchAll();
		foreach ($rr as $r) {
			$p = new Plugin();
			$p->load($r['plugin_id']);
			array_push($ret, $p);
		}
		return $ret;
	}

	function getPluginsByUserId($user_id) {
		$ret = array();
		$db = DBManager::get();
		$rr = $db->query(sprintf("SELECT p.* FROM plugins p, users u WHERE p.user_id='%s' AND u.user_id=p.user_id ORDER BY p.name",$user_id))->fetchAll();
		foreach ($rr as $r) {
			$p = new Plugin();
			$p->load($r['plugin_id']);
			array_push($ret, $p);
		}
		return $ret;
	}

	function getPluginsByExtendedSearch($search_items) {
		$ret = array();
		if (count($search_items) == 0) return $ret;
		$db = DBManager::get();
		$params = array();
		$sql = "SELECT p.* FROM plugins p WHERE 1=1 ";
		if ($search_items['category_id'] != 'all') {
			$sql .= " AND p.plugin_id IN (SELECT cp.plugin_id FROM categories_plugins cp WHERE cp.category_id=? AND cp.plugin_id=p.plugin_id)";
			array_push($params, $search_items['category_id']);
		}
		if ($search_items['language'] != 'all') {
			$sql .= " AND p.language LIKE ?";
			array_push($params, '%'.$search_items['language'].'%');
		}
		if ($search_items['search_txt'] != '') {
			$tmp_sql = " LOWER(p.name) LIKE LOWER(?)";
			array_push($params,'%'.addslashes($search_items['search_txt']).'%');
			if ($search_items['fulltext'] == 'yes') {
				$tmp_sql .= " OR LOWER(p.short_description) LIKE LOWER(?)";
				array_push($params,'%'.addslashes($search_items['search_txt']).'%');
				$tmp_sql .= " OR LOWER(p.description) LIKE LOWER(?)";
				array_push($params,'%'.addslashes($search_items['search_txt']).'%');
			}
			$sql = $sql . ' AND (' . $tmp_sql . ')';
		}
		$sql .= " AND (p.approved=1 OR (p.approved=0 AND 1=?)) ";
		array_push($params,($this->authenticated ? 1 : 0));
		$stmt = $db->prepare($sql);
		$stmt->execute($params);
		$rr = $stmt->fetchAll();
		foreach ($rr as $r) {
			$p = new Plugin();
			$p->load($r['plugin_id']);
			array_push($ret, $p);
		}
		return $ret;
	}

	function getUnclearPlugins() {
		$ret = array();
		$db = DBManager::get();
		$rr = $db->query("SELECT * FROM plugins WHERE approved=0 ORDER BY name")->fetchAll();
		foreach ($rr as $r) {
			$p = new Plugin();
			$p->load($r['plugin_id']);
			array_push($ret, $p);
		}
		return $ret;
	}
	

	function getPluginsByCategory($category_id) {
		$ret = array();
		$db = DBManager::get();
		$rr = $db->query(sprintf("SELECT p.* FROM plugins p, categories_plugins cp WHERE cp.category_id='%s' AND p.plugin_id=cp.plugin_id AND (p.approved=1 OR (p.approved=0 AND 1=%d))",$category_id, ($this->authenticated ? 1 : 0)))->fetchAll();
		foreach ($rr as $r) {
			$p = new Plugin();
			$p->load($r['plugin_id']);
			array_push($ret, $p);
		}
		return $ret;
	}

	function getPluginsByHitlist($hitlist) {
		$ret = array();
		$db = DBManager::get();
		switch ($hitlist) {
			case 'recommended':
				$rr = $db->query(sprintf("SELECT p.* FROM plugins p WHERE (p.approved=1 OR (p.approved=0 AND 1=%d)) AND p.classification IN ('firstclass','secondclass') ORDER BY p.classification, p.mkdate DESC, p.name",($this->authenticated ? 1 : 0)))->fetchAll();
				break;
			case 'latest':
				$rr = $db->query(sprintf("SELECT p.* FROM plugins p WHERE (p.approved=1 OR (p.approved=0 AND 1=%d)) ORDER BY p.mkdate DESC, p.name",($this->authenticated ? 1 : 0)))->fetchAll();
				break;
			case 'most_downloaded':
				$rr = $db->query(sprintf("SELECT p.plugin_id, p.name, SUM(r.downloads) rel_downloads FROM plugins p, releases r WHERE (p.approved=1 OR (p.approved=0 AND 1=%d)) AND r.plugin_id=p.plugin_id GROUP BY p.plugin_id ORDER BY 3, p.name",($this->authenticated ? 1 : 0)))->fetchAll();
 ;
				break;
			case 'most_rated':
				$rr = $db->query(sprintf("SELECT p.plugin_id, p.name, COUNT(r.rating) count_plugin FROM plugins p, releases re LEFT JOIN ratings r ON (r.range_id=re.release_id) WHERE (p.approved=1 OR (p.approved=0 AND 1=%d)) AND re.plugin_id=p.plugin_id GROUP BY p.plugin_id HAVING count_plugin > 0 ORDER BY p.name",($this->authenticated ? 1 : 0)))->fetchAll();
				break;
			default:
				$rr = $db->query(sprintf("SELECT p.* FROM plugins p WHERE p.approved=1 ORDER BY p.mkdate DESC, p.name"))->fetchAll();
			
		}
		foreach ($rr as $r) {
                        $p = new Plugin();
                        $p->load($r['plugin_id']);
                        array_push($ret, $p);
                }
		return $ret;
	}

	function getPluginsByTxt($txt, $catagory_id){
		$ret = array();
		$db = DBManager::get();
		if (!$catagory_id) {
			$stmt = DBManager::get()->prepare("SELECT p.* FROM plugins p WHERE UPPER(p.name) LIKE UPPER(?) AND (p.approved=1 OR (p.approved=0 AND 1=?)) AND EXISTS (SELECT cp.plugin_id FROM categories_plugins cp WHERE cp.plugin_id=p.plugin_id) ORDER BY p.name");
			$stmt->execute(array('%'.$txt.'%',($this->authenticated ? 1 : 0)));
			$rr = $stmt->fetchAll();
		} else {
			$stmt = DBManager::get()->prepare("SELECT p.* FROM plugins p, categories_plugins cp WHERE UPPER(p.name) LIKE UPPER(?) AND cp.plugin_id=p.plugin_id AND cp.category_id=? AND (p.approved=1 OR (p.approved=0 AND 1=?)) ORDER BY p.name");
			$stmt->execute(array('%'.$txt.'%',$catagory_id,($this->authenticated ? 1 : 0)));
			$rr = $stmt->fetchAll();
		}
			
		foreach ($rr as $r) {
                        $p = new Plugin();
                        $p->load($r['plugin_id']);
                        array_push($ret, $p);
                }
		return $ret;
	}

	public function getPluginsByTagName($tag) {
		$ret = array();
		$stmt = DBManager::get()->prepare("SELECT p.plugin_id FROM plugins p, tags_objects tt, tags t WHERE t.tag=? AND tt.tag_id=t.tag_id AND p.plugin_id=tt.object_id AND (p.approved=1 OR (p.approved=0 AND 1=?))
                                                  UNION SELECT p.plugin_id FROM plugins p, tags_objects tt, tags t, releases r WHERE t.tag=? AND tt.tag_id=t.tag_id AND r.release_id=tt.object_id AND p.plugin_id=r.plugin_id AND (p.approved=1 OR (p.approved=0 AND 1=?))");
		$stmt->execute(array(addslashes($tag),($this->authenticated ? 1 : 0), addslashes($tag),($this->authenticated ? 1 : 0)));
		$rr = $stmt->fetchAll();
		foreach ($rr as $r) {
                        $p = new Plugin();
                        $p->load($r['plugin_id']);
                        array_push($ret, $p);
                }
                return $ret;
	}

	function setFileContent($file_name, $file_size, $user_id, $file_id, $file_type) {
		if (!$file_id) {
			$f = new MFile();
			$f->setUserId($user_id)
			  ->setFileName($file_name)
			  ->setFileSize($file_size)
			  ->setFileType($file_type)
			  ->save();
		} else {
			$f = new MFile();
			$f->load($file_id);
			$f->setUserId($user_id)
			  ->setFileName($file_name)
			  ->setFileSize($file_size)
			  ->save();
		}
		return $f->getFileId();
        }

	// Liest ein Verzeichnis ein
        function GetDirContents($dir){
                ini_set("max_execution_time",100);
                if (!is_dir($dir)) {
			$this->error_str = sprintf("Fehler! kein g&uuml;ltiges Verzeichnis: %s!",$dir);
			return FALSE;
		}
                if ($root=@opendir($dir)){
                        while ($file=readdir($root)){
                                if($file=="." || $file==".."){continue;}
                                if(is_dir($dir."/".$file)){
                                        $files=array_merge($files,GetDirContents($dir."/".$file));
                                }else{
                                        $files[]=$dir."/".$file;
                                }
                        }
                }
                return $files;
        }

	function add_new_zip($zipfile, $zipfile_size, $zipfile_name, $plugin_id, $user_id, $file_type='screenshots') {
                $db = DBManager::get();

                $zipdir = $this->tmp_path."/".$plugin_id."_".time();
                $zipname = basename($zipfile).".zip";

                if ($zipfile_size > 50000 * 1024) {
                        $this->error_str = "Die hochgeladene ZIP-Datei ist zu gross!";
			return FALSE;
		}

                if (!@copy($zipfile, $this->tmp_path."/$zipname")) {
			$this->error_str = "Fehler beim Kopieren des ZIP-Archivs!";
			return FALSE;
		}

                // ZIP-Testing...
                exec("unzip -t ".$this->tmp_path."/$zipname 2>&1", $out, $err);
                if ($err) {
			$this->error_str = "Fehler im ZIP-Archiv!"."unzip -t ".$this->tmp_path."/$zipname 2>&1";
			return FALSE;
		}

                unset($out);
                unset($err);

                // ZIP-Auspacking...
                exec("unzip -jno ".$this->tmp_path."/$zipname -d $zipdir 2>&1", $out, $err);
                if ($err) {
			$this->error_str = "Fehler beim Entpacken des ZIP-Archivs!";
			return FALSE;
		}

                $files = $this->GetDirContents($zipdir);

                if (!is_array($files)) return $files;

                $good = 0;
                $not_good = 0;

                if (is_array($files))
                foreach ($files as $key=>$val) {
                        //Dateiendung bestimmen
                        $ext = NULL;
                        $dot = strrpos($val,".");
                        if ($dot) {
                                $l = strlen($val) - $dot;
                                $ext = strtolower(substr($val,$dot+1,$l));
                        }

                        if (filesize($val)==0) {
                                $not_good++;
                                $this->error_str .= sprintf("Fehler beim hochladen des Fotos: %s Dateigr&ouml;sse 0 Byte!",basename($val));
                                unlink($val);
                                continue;
                        }

                        $r = $db->query(sprintf("SELECT MAX(sort) maxsort FROM screenshots WHERE plugin_id='%s'",$plugin_id))->fetch(PDO::FETCH_NUM);
                        $max_sort = ($r[0] ? $r[0] : 1);

			$file_id = $this->setFileContent(basename($val), filesize($val), $user_id, FALSE, $file_type);
                        if ($this->imaging($file_id, $val, filesize($val), basename($val))) {
				$s = new Screenshot();
                                $s->setPluginId($plugin_id)
                                  ->setFileId($file_id)
                                  ->setTitleScreen(0);
                                $s->save();

                                $good++;
                        } else {
				$f = new MFile();
                                $f->load($file_id);
                                $f->remove();
                                unset($f);
                                $not_good++;
                                $msg .= $msg2;
                        }
                        unlink($val);
                }
                $this->error_str .= sprintf("%d Bild(er) hochgeladen / %d Problem(e)",$good,$not_good);
                rmdir($zipdir);
                unlink($this->tmp_path."/$zipname");
                return TRUE;
        }



	function uploader($file_id=FALSE, $user_id, $img, $img_size, $img_name, $file_type) {
                if (!$img_name) {
                        return FALSE;
                }

                if(!file_exists($img)) {
                        @unlink($newfile);
                        return FALSE;
                } else {
                        $file_id = $this->setFileContent($img_name, $img_size, $user_id, $file_id, $file_type);
			if ($file_type == 'screenshots')
				$ok = $this->imaging($file_id, $img, $img_size, $img_name);
			else if ($file_type == 'releases') {
				$uploaddir = $GLOBALS['DYNAMIC_CONTENT_PATH'] . '/releases'; //Uploadverzeichnis
				$newfile = $uploaddir . "/".$file_id;
				if(!@copy($img,$newfile)) {
					@unlink($newfile);
					$this->error_str = "Error 4: " . sprintf(_("Es ist ein Fehler beim Kopieren der Datei %s aufgetreten. Die Datei wurde nicht hochgeladen!"),$img);
                        		return FALSE;
				}
			}
				
			return $file_id;
                }
                return FALSE;
        }

	function imaging($foto_id, $img, $img_size, $img_name) {
                $max_file_size = 8000; //max Größe der Bilddatei in KB
                $img_max_h_thumb = 150; // max picture height (thumbnail)
                $img_max_h = 800; // max picture height
                $img_max_w_thumb = 150; // max picture width (thumbnail)
                $img_max_w = 800; // max picture width
                $uploaddir = $GLOBALS['DYNAMIC_CONTENT_PATH'] . '/screenshots'; //Uploadverzeichnis für Bilder

                $msg = "";
                if ($img_size > ($max_file_size*1024)) { //Bilddatei ist zu groß
                        $this->error_str = "Error 1: " . sprintf(_("Die hochgeladene Bilddatei ist %s KB gro&szlig;.<br>Die maximale Dateigr&ouml;&szlig;e betr&auuml;gt %s KB!"), round($img_size/1024), $max_file_size);
                        return FALSE;
                }

                if (!$img_name) { //keine Datei ausgewählt!
                        $this->error_str = "Error 2: " . _("Sie haben keine Datei zum Hochladen ausgew&auml;hlt!");
                        return FALSE;
                }

                //Dateiendung bestimmen
                $dot = strrpos($img_name,".");
                if ($dot) {
                        $l = strlen($img_name) - $dot;
                        $ext = strtolower(substr($img_name,$dot+1,$l));
                }
                //passende Endung ?
                if ($ext != "jpg" && $ext != "gif" && $ext != "png") {
                        $this->error_str = sprintf(_("Der Dateityp der Bilddatei ist falsch (%s).<br>Es sind nur die Dateiendungen .gif, .png und .jpg erlaubt!§"), $ext);
                        return FALSE;
                }

                //na dann kopieren wir mal...
                $newfile = $uploaddir . "/".$foto_id;
                $newfile_thumb = $uploaddir . "/".$foto_id.'_thumb';
                if(!@copy($img,$newfile) || !@copy($img,$newfile_thumb)) {
                        @unlink($newfile);
                        @unlink($newfile_thumb);
                        $this->error_str = "Error 4: " . sprintf(_("Es ist ein Fehler beim Kopieren der Datei %s aufgetreten. Das Bild wurde nicht hochgeladen!"),$img);
                        return FALSE;
                } else {
                        list($width, $height, $img_type, ) = @getimagesize($img);
                        // Check picture size
                        $hscale = $height / $img_max_h;
                        $wscale = $width / $img_max_w;
                        $hscale_thumb = $height / $img_max_h_thumb;
                        $wscale_thumb = $width / $img_max_w_thumb;
                        // Thumbnails
                        if (($hscale_thumb > 1) || ($wscale_thumb > 1)) {
                                $scale = ($hscale_thumb > $wscale_thumb)? $hscale_thumb : $wscale_thumb;
                                $newwidth = floor($width / $scale);
                                $newheight= floor($height / $scale);
                                $ret_val = false;
                                if (@file_exists($GLOBALS['CONVERT_PATH'])){
                                        system($GLOBALS['CONVERT_PATH'] . ' -resize ' . $newwidth . 'x' . $newheight . '! ' . $newfile_thumb .' ' . $newfile_thumb, $ret_val);
                                } else if (extension_loaded('gd') && function_exists('imagecopyresampled')){
                                        // leeres Bild erzeugen
$img_res = ImageCreateTrueColor($newwidth, $newheight);
                                        switch ($img_type) {  //original Bild einlesen
                                        case 1: //GIF
                                                $img_org = ImageCreateFromGIF($img);
                                                break;
                                        case 2: //JPG
                                                $img_org = ImageCreateFromJPEG($img);
                                                break;
                                        case 3: //PNG
                                                $img_org = ImageCreateFromPNG($img);
                                                break;
                                        default:
                                                $img_org = FALSE;
                                        } // end switch
                                        if (!$img_org) {
                                                $this->error_str = "Error 5: " . _("Es ist ein Fehler beim Kopieren der Datei aufgetreten. Das Bild wurde nicht hochgeladen!");
                                                @unlink($newfile);
                                                @unlink($newfile_thumb);
                                                return FALSE;
                                        }
                                        // resampeln und als jpg speichern
                                        $ret_val = ImageCopyResampled ( $img_res, $img_org, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                                        $ret_val = ImageJPEG ( $img_res, $newfile_thumb , 70);
                                        $ret_val = $ret_val ? false : true;
                                        ImageDestroy ( $img_res);
                                        ImageDestroy ( $img_org);
                                } else {
                                        $ret_val = true; //Fehler!!!
                                }
                                if ($ret_val){
                                        @unlink($newfile);
                                        @unlink($newfile_thumb);
                                        $this->error_str= "Error 6:" . _("Es ist ein Fehler beim Kopieren der Datei aufgetreten. Das Bild wurde nicht hochgeladen!");
                                        return FALSE;
                                }
                        }
			// Big-Images
                        if (($hscale > 1) || ($wscale > 1)) {
                                $scale = ($hscale > $wscale)? $hscale : $wscale;
                                $newwidth = floor($width / $scale);
                                $newheight= floor($height / $scale);
                                $ret_val = false;
                                if (@file_exists($GLOBALS['CONVERT_PATH'])){
                                        system($GLOBALS['CONVERT_PATH'] . ' -resize ' . $newwidth . 'x' . $newheight . '! ' . $newfile .' ' . $newfile, $ret_val);
                                } else if (extension_loaded('gd') && function_exists('imagecopyresampled')){
                                        // leeres Bild erzeugen
                                        $img_res = ImageCreateTrueColor($newwidth, $newheight);
                                        switch ($img_type) {  //original Bild einlesen
                                        case 1: //GIF
                                                $img_org = ImageCreateFromGIF($img);
                                                break;
                                        case 2: //JPG
                                                $img_org = ImageCreateFromJPEG($img);
                                                break;
                                        case 3: //PNG
                                                $img_org = ImageCreateFromPNG($img);
                                                break;
                                        default:
                                                $img_org = FALSE;
                                        } // end switch
                                        if (!$img_org) {
                                                $this->error_str = "Error 7: " . _("Es ist ein Fehler beim Kopieren der Datei aufgetreten. Das Bild wurde nicht hochgeladen!");
                                                @unlink($newfile);
                                                @unlink($newfile_thumb);
                                                return FALSE;
                                        }
                                        // resampeln und als jpg speichern
                                        $ret_val = ImageCopyResampled ( $img_res, $img_org, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                                        $ret_val = ImageJPEG ( $img_res, $newfile , 70);
                                        $ret_val = $ret_val ? false : true;
                                        ImageDestroy ( $img_res);
                                        ImageDestroy ( $img_org);
                                } else {
                                        $ret_val = true; //Fehler!!!
                                }
                                if ($ret_val){
                                        @unlink($newfile);
                                        @unlink($newfile_thumb);
                                        $this->error_str = "Error 8: " . _("Es ist ein Fehler beim Kopieren der Datei aufgetreten. Das Bild wurde nicht hochgeladen!");
                                        return $msg;
                                }
                        }
                        return TRUE;
                }
                return FALSE;
        }

	function getTagCounter($limit=40) {
		$ret = array();
		$db = DBManager::get();
		$rr = $db->query(sprintf("SELECT t.tag, COUNT(t.tag_id) anz FROM tags t, tags_objects ta, plugins p, releases r WHERE ta.tag_id=t.tag_id AND ((p.plugin_id=ta.object_id AND (p.approved=1 OR (p.approved=0 AND 1=%d)))) OR (r.release_id=ta.object_id AND p.plugin_id=r.plugin_id AND (p.approved=1 OR (p.approved=0 AND 1=%d))) GROUP BY t.tag ORDER BY 2 DESC %s",($this->authenticated ? 1 : 0), ($this->authenticated ? 1 : 0), ($limit?"LIMIT 0,$limit":"")))->fetchAll();
		foreach ($rr as $r) {
			$r['tag_weight'] = $this->calcTagWeight($r['tag']);
			array_push($ret, $r);
		}
		array_multisort($ret, SORT_ASC);
		return $ret;
	}

	function getMaxTagCount($single='') {
		$db = DBManager::get();
		if (!$single)
			$r = $db->query("SELECT * FROM tags_objects")->fetchAll();
		else {
			$stmt = $db->prepare("SELECT ta.* FROM tags_objects ta, tags t WHERE ta.tag_id=t.tag_id AND t.tag=? AND (EXISTS (SELECT p.plugin_id FROM plugins p WHERE plugin_id=ta.object_id AND (p.approved=1 OR (p.approved=0 AND 1=?)))) OR EXISTS (SELECT r.release_id FROM releases r, plugins p WHERE r.release_id=ta.object_id AND p.plugin_id=r.plugin_id AND (p.approved=1 OR (p.approved=0 AND 1=?)))");
			$stmt->execute(array(addslashes($single), ($this->authenticated ? 1 : 0), ($this->authenticated ? 1 : 0)));
			$r = $stmt->fetchAll();
		}
		return count($r);
	}

	function calcTagWeight($tag) {
		$f_max = 10;
		$t_i = $this->getMaxTagCount($tag);

		$db = DBManager::get();
                $r = $db->query(sprintf("SELECT MAX(x.anz) anz_max, MIN(x.anz) anz_min FROM (SELECT t.tag, COUNT(t.tag_id) anz FROM tags t, tags_objects ta WHERE t.tag_id=ta.tag_id GROUP BY t.tag ORDER BY t.tag) x LIMIT 1"))->fetchAll();
		$anz_max = $r[0]['anz_max'];
		$anz_min = $r[0]['anz_min'];

		$delta = ($anz_max - $anz_min) / $f_max;
		$newThresholds = array();
		for ($x=1; $x<=$f_max; $x++) { 
			$newThresholds[$x] = 100 * log( ($anz_min + $x * $delta) + 2); 
		}

		$fontSet = false; 
		foreach ($newThresholds as $k=>$threshold) {
			if ( (100 * log($t_i+2) <= $newThresholds[$k]) && !$fontSet) { 
				$fontSet = true; 
				return $k;
			} 
		}
	}

	public function searchForTags($val) {
		$db = DBManager::get();
		$stmt = $db->prepare("SELECT * FROM tags WHERE LOWER(tag) LIKE LOWER(?)");
		$stmt->execute(array(addslashes($val).'%'));
		$rr = $stmt->fetchAll();
                $ret = "<UL>";
                $suche = sprintf("/^(%s)/",$val);
                $ersetze = "<SPAN STYLE=\"font-weight:bold;\">\$1</SPAN>";
                foreach ($rr as $r) {
                        $txt = preg_replace($suche,$ersetze,stripslashes($r['tag']));
                        $ret .= "<LI>".$txt."</LI>";
                }
                $ret .= "</UL>";
                return $ret;
        }

	public function getComments($range_id) {
		$ret = array();
		$rr = DBManager::get()->query(sprintf("SELECT comment_id FROM comments WHERE range_id='%s' ORDER BY mkdate DESC",$range_id))->fetchAll();
		foreach ($rr as $r) {
			$c = new Comment();
			$c->load($r['comment_id']);
			array_push($ret, $c);
		}
		return $ret;
	}
	
	public function getPluginManifest($pluginpath) {
                $manifest = @file($pluginpath . '/plugin.manifest');
                $result = array();

                if ($manifest !== false) {
                        foreach ($manifest as $line) {
                                list($key, $value) = explode('=', $line);
                                $key = trim($key);
                                $value = trim($value);

                                if ($key === '' || $key[0] === '#') {
                                        continue;
                                }

                                if ($key === 'pluginclassname' && isset($result[$key])) {
                                        $result['additionalclasses'][] = $value;
                                } else {
                                        $result[$key] = $value;
                                }
                        }
                }

                return $result;
        }


	public function rmdirr($dirname){
		// Simple delete for a file
		if (is_file($dirname)) {
			return @unlink($dirname);
		} else if (!is_dir($dirname)){
			return false;
		}

		// Loop through the folder
		$dir = dir($dirname);
		while (false !== ($entry = $dir->read())) {
		// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep delete directories
			if (is_dir("$dirname/$entry")) {
				$this->rmdirr("$dirname/$entry");
			} else {
				@unlink("$dirname/$entry");
			}
		}
		// Clean up
		$dir->close();
		return @rmdir($dirname);
	}


	public function checkReleaseZip($zipfile, $zipfile_size, $zipfile_name) {
                $zipdir = $this->tmp_path."/".time();
                $zipname = basename($zipfile).".zip";

                if ($zipfile_size > 50000 * 1024) {
                        $this->error_str = "Die hochgeladene ZIP-Datei ist zu gross!";
			return FALSE;
		}

                if (!@copy($zipfile, $this->tmp_path."/$zipname")) {
			$this->error_str = "Fehler beim Kopieren des ZIP-Archivs!";
			return FALSE;
		}

                // ZIP-Testing...
                exec("unzip -t ".$this->tmp_path."/$zipname 2>&1", $out, $err);
                if ($err) {
			$this->error_str = "Fehler im ZIP-Archiv!"."unzip -t ".$this->tmp_path."/$zipname 2>&1";
			return FALSE;
		}

                unset($out);
                unset($err);

                // ZIP-Auspacking...
                exec("unzip -jno ".$this->tmp_path."/$zipname -d $zipdir 2>&1", $out, $err);
                if ($err) {
			$this->error_str = "Fehler beim Entpacken des ZIP-Archivs!";
			return FALSE;
		}

                $files = $this->GetDirContents($zipdir);

		$manifest = array();
                if (!is_array($files)) {
			$this->error_str = "Keine Dateien im ZIP-Archiv gefunden!";
			$this->rmdirr($zipdir);
			unlink($this->tmp_path."/$zipname");
			return FALSE;
		} else {
			$manifest = $this->getPluginManifest($zipdir);
			if (count($manifest) == 0) {
				$this->error_str = "Kein Manifest gefunden!";
				$this->rmdirr($zipdir);
				unlink($this->tmp_path."/$zipname");
				return FALSE;
			} else {
				$studip_version_check = "/(\d)(\.\d+)*.*/";
				$err = array();
				if (empty($manifest['pluginname'])) $err[] = "pluginname";
				if (empty($manifest['pluginclassname'])) $err[] = "pluginclassname";
				if (empty($manifest['origin'])) $err[] = "origin";
				if (empty($manifest['version'])) $err[] = "version";
				if (empty($manifest['studipMinVersion'])) $err[] = "studipMinVersion";
				if (!empty($manifest['studipMinVersion']) && !preg_match($studip_version_check, $manifest['studipMinVersion'])) $err[] = "studipMinVersion (falsches Format)";
				if (count($err)) {
					$this->error_str = "Folgende Angaben fehlen im Manifest: ".join(', ',$err);
					$this->rmdirr($zipdir);
					unlink($this->tmp_path."/$zipname");
					return FALSE;
				}
			}
		}
                $this->rmdirr($zipdir);
                return $manifest;
        }

	public function setRating($range_id, $user_id, $rating) {
		$id = md5(uniqid(time().$user_id.$range_id));
		DBManager::get()->query(sprintf("REPLACE INTO ratings SET range_id='%s', user_id='%s', rating=%d",$range_id, $user_id, $rating));
	}

	public function getUserRatings($range_id) {
                $db = DBManager::get();
                $summe = 0;
                $rr = $db->query(sprintf("SELECT SQL_CACHE rating FROM ratings WHERE range_id='%s' AND rating IS NOT NULL AND rating!=0",$range_id))->fetchAll();
                foreach ($rr as $r) {
                        $summe += $r['rating'];
                }
                if (count($rr) > 0)
                        return array('summe'=>$summe, 'schnitt'=>($summe / count($rr)), 'anzahl'=>count($rr));
                else
                        return FALSE;
        }

	public function getSpecificUserRating($range_id, $user_id) {
                $db = DBManager::get();
                $rr = $db->query(sprintf("SELECT SQL_CACHE rating FROM ratings WHERE range_id='%s' AND user_id='%s' AND rating IS NOT NULL AND rating!=0",$range_id, $user_id))->fetchAll();
                if (count($rr) > 0)
			return $rr[0]['rating'];
                else
                        return 0;
        }
	


}

?>
