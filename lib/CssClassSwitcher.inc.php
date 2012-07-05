<?php
# Lifter002: TODO
# Lifter005: TODO
# Lifter007: TODO
# Lifter003: TODO
// +---------------------------------------------------------------------------+
// This file is part of Stud.IP
// cssClassSwitcher.inc.php
// Copyright (c) 2002 Andre Noack <noack@data-quest.de>
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
/**
* cssClassSwitcher.inc.php
*
* class for handling zebra-tables
* @author		Andre Noack <noack@data-quest.de>
* @version		$Id: cssClassSwitcher.inc.php 12616 2009-06-18 10:25:12Z mlunzena $
* @access		public
* @package		studip_core
*/
class CssClassSwitcher {
	
	var $class = array("steelgraulight", "steel1");                 //Klassen
	var $headerClass = "steel";
	var $classcnt = 0;                //Counter
	var $hovercolor = array("#B7C2E2","#CED8F2");
	var $nohovercolor = array("#E2E2E2","#F2F2F2");
	var $JSenabled = TRUE;
	var $hoverenabled = FALSE;
	/**
	* cssClassSwitcher constructor
	* 
	* class for handling zebra-tables
	*
	* @access	public
	* @param	string
	* @param	string
	* @param	string
	* @param	string
	*/
	function CssClassSwitcher($class = "",$headerClass = "",$hovercolor = "",$nohovercolor = ""){
		if (is_array($class)) $this->class = $class;
		if ($headerClass) $this->headerClass = $headerClass;
		if (is_array($hovercolor)) $this->hovercolor = $hovercolor;
		if (is_array($nohovercolor)) $this->nohovercolor = $nohovercolor;
	}
	
	function enableHover($hovercolor = "",$nohovercolor = ""){
		if (is_array($hovercolor)) $this->hovercolor = $hovercolor;
		if (is_array($nohovercolor)) $this->nohovercolor = $nohovercolor;	
		if ($this->JSenabled)
			$this->hoverenabled = TRUE;
	}
	
	function disableHover(){
		$this->hoverenabled = FALSE;
	}
	
	function getHover(){
		if($this->hoverenabled && $this->JSenabled){
			$ret = $this->getFullClass();
			$ret .= " onMouseOver='doHover(this,\"".$this->nohovercolor[$this->classcnt]."\",\"".$this->hovercolor[$this->classcnt]."\")'".
				" onMouseOut='doHover(this,\"".$this->hovercolor[$this->classcnt]."\",\"".$this->nohovercolor[$this->classcnt]."\")' ";
		}
		return $ret;
	}
	
	function getFullClass(){
		$ret = ($this->hoverenabled) ?  " style=\"background-color:".$this->nohovercolor[$this->classcnt]."\" " : " class=\"" . $this->class[$this->classcnt] . "\" ";
		return $ret;
	}
	
	function getClass() {
		return ($this->hoverenabled) ? "\"  style=\"background-color:".$this->nohovercolor[$this->classcnt]." " : $this->class[$this->classcnt];
	}

	function getHeaderClass() {
		return $this->headerClass;
	}

	function resetClass() {
		return $this->classcnt = 0;
	}

	function switchClass() {
		$this->classcnt++;
		if ($this->classcnt >= sizeof($this->class))
			$this->classcnt = 0;
	}
	
	function GetHoverJSFunction(){
		static $is_called = FALSE;
		$ret = "";
		if(!$is_called) {
			$ret = "<script type=\"text/javascript\">
					function convert(x, n, m, d){
						if (x == 0) return '00';
						var r = '';
						while (x != 0){
							r = d.charAt((x & m)) + r;
							x = x >>> n
						}
						return (r.length%2) ? '0' + r : r;
					}
					
					function toHexString(x){
						return convert(x, 4, 15, '0123456789abcdef');
					}
					
					function rgbToHex(rgb_str){
						var ret = '#';
						var rgb_arr = rgb_str.substring(rgb_str.indexOf('(')+1,rgb_str.lastIndexOf(')')).split(',');
						for(var i = 0; i < rgb_arr.length; ++i){
							ret += toHexString(rgb_arr[i]);
						}
						return ret;
					}
					
					function doHover(theRow, theFromColor, theToColor){
						if (theFromColor == '' || theToColor == '') {
							return false;
						}
						if (document.getElementsByTagName) {
							var theCells = theRow.getElementsByTagName('td');
						}
						else if (theRow.cells) {
							var theCells = theRow.cells;
						} else {
							return false;
						}
						if (theRow.tagName.toLowerCase() != 'tr'){
							if ((theRow.style.backgroundColor.toLowerCase() == theFromColor.toLowerCase()) || (rgbToHex(theRow.style.backgroundColor) == theFromColor.toLowerCase())) {
								theRow.style.backgroundColor = theToColor;
							}
						} else {
							var rowCellsCnt  = theCells.length;
							for (var c = 0; c < rowCellsCnt; c++) {
								if ((theCells[c].style.backgroundColor == theFromColor.toLowerCase()) || (rgbToHex(theCells[c].style.backgroundColor) == theFromColor.toLowerCase())) {
									theCells[c].style.backgroundColor = theToColor;
								}
							}
						}
						return true;
					}
					</script>";
		}
		$is_called = TRUE;
		return $ret;
	}
}
?>
