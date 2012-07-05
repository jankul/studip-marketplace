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

class Generator {

	private $values = array();
	private $manifest = '';
	private $plugin_corpus = '';

	public function __construct() {
		
	}

	public function setValues($a) {
		$this->values = $a;
	}

	public function magic() {
		$this->plugin_corpus = "";
		switch ($this->values['plugintype']) {
			case 'Administration':
				$template = $GLOBALS['FACTORY']->open('plugins/administration');
				$template->set_attribute('vals', $this->values);
				$this->plugin_corpus = $template->render();
				break;
			case 'Homepage':
				$template = $GLOBALS['FACTORY']->open('plugins/homepage');
				$template->set_attribute('vals', $this->values);
				$this->plugin_corpus = $template->render();
				break;
			case 'Portal':
				$template = $GLOBALS['FACTORY']->open('plugins/portal');
				$template->set_attribute('vals', $this->values);
				$this->plugin_corpus = $template->render();
				break;
			case 'Standard':
				$template = $GLOBALS['FACTORY']->open('plugins/standard');
				$template->set_attribute('vals', $this->values);
				$this->plugin_corpus = $template->render();
				break;
			case 'System':
				$template = $GLOBALS['FACTORY']->open('plugins/system');
				$template->set_attribute('vals', $this->values);
				$this->plugin_corpus = $template->render();
				break;
			default: ;
		}
		
		$template = $GLOBALS['FACTORY']->open('plugins/manifest');
		$template->set_attribute('vals', $this->values);
		$this->manifest = $template->render();
		$this->createZIPArchiv();
	}

	public function createZIPArchiv() {
		$f_id = md5(uniqid(time()));
		$working_directory = $GLOBALS['DYNAMIC_CONTENT_PATH']."/tmp/".md5(uniqid(time()));
		mkdir ($working_directory, 0700);
		foreach (array('images','migrations','sql','stylesheets','templates') as $t)
			mkdir ($working_directory.'/'.$t, 0700);

		$ff = fopen($working_directory.'/plugin.manifest', "w");
		fputs($ff, $this->manifest);
                fclose($ff);
	
		$ff = fopen($working_directory.'/'.prepareFilename($this->values['pluginclassname']).'.class.php', "w");
		fputs($ff, "<?".$this->plugin_corpus."\n?>");
                fclose($ff);

		Downloader::create_zip_from_directory($working_directory, $GLOBALS['DYNAMIC_CONTENT_PATH']."/tmp/".$f_id.".zip");
                @$GLOBALS['DBM']->rmdirr($working_directory);

		$d = new Downloader();
                $d->initiateDownload(null, $GLOBALS['DYNAMIC_CONTENT_PATH']."/tmp/".$f_id.".zip",$this->values['pluginclassname'].'-1.0.zip');

	}

}

?>
