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

class Downloader {

	private $tmp_path;
	
	public function __construct() {
		$this->tmp_path = $GLOBALS['TMP_PATH'];
	}

	public function initiateDownload($file_id, $filename="", $export_filename="") {

		$data = "";
		$data_filename = "";

		if ($filename) {
			$data = file_get_contents($filename);
			$data_filename = ($export_filename ? prepareFilename(html_entity_decode($export_filename, ENT_QUOTES)) : "export.zip");
		} else {
			$r = new Release();
			if ($r->getReleaseFromFileId($file_id)) {
				$r->increaseDownloadCounter();
			}
			$f = new MFile();
			$f->load($file_id);
			$data = file_get_contents($GLOBALS['DYNAMIC_CONTENT_PATH'] . '/' . $f->getFileType() . '/' . $f->getFileId());
			$data_filename = prepareFilename(html_entity_decode($f->getFileName(), ENT_QUOTES));
		}
		if ($data) {
			ob_start();
			$content_type="application/octet-stream";
		        $content_disposition="attachment";
			header("Expires: Mon, 12 Dec 2001 08:00:00 GMT");
			header("Last-Modified: " . gmdate ("D, d M Y H:i:s") . " GMT");
			if ($_SERVER['HTTPS'] == "on"){
				header("Pragma: public");
				header("Cache-Control: private");
			} else {
				header("Pragma: no-cache");
				header("Cache-Control: no-store, no-cache, must-revalidate");   // HTTP/1.1
			}
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Content-Type: $content_type; name=\"".$data_filename."\"");
			header("Content-Description: File Transfer");
			header("Content-Transfer-Encoding: binary");
			header("Accept-Ranges: bytes");
			header("Content-Length: ".strlen($data));
			header("Content-Disposition: $content_disposition; filename=\"".$data_filename."\"");
			ob_end_flush();
			echo $data;
		}
	}

	public static function create_zip_from_directory($fullpath, $zip_file_name){
		if (strtolower(substr($zip_file_name, -3)) != 'zip' ) $zip_file_name = $zip_file_name . '.zip';
		if ($GLOBALS['ZIP_USE_INTERNAL']){
			$archiv = new PclZip($zip_file_name);
			$v_list = $archiv->create($fullpath, PCLZIP_OPT_REMOVE_PATH, $fullpath, PCLZIP_CB_PRE_ADD, 'pclzip_convert_filename_cb');
			return $v_list;
		} else /*if (@file_exists($GLOBALS['ZIP_PATH']) || ini_get('safe_mode'))*/ {
			//zip stuff
			$zippara = (ini_get('safe_mode')) ? ' -R ':' -r ';
			if (@chdir($fullpath)) {
				exec ($GLOBALS['ZIP_PATH'] . ' -q ' . $GLOBALS['ZIP_OPTIONS'] . ' ' . $zippara . $zip_file_name . ' *',$output, $ret);
				@chdir($GLOBALS['BASE_PATH']);
			}
			return $ret;
		}
}


}

?>
