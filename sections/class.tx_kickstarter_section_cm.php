<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2001-2004 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * @author	Kasper Sk�rh�j <kasperYYYY@typo3.com>
 */

require_once(t3lib_extMgm::extPath("kickstarter")."class.tx_kickstarter_sectionbase.php");
 
class tx_kickstarter_section_cm extends tx_kickstarter_sectionbase {
	var $catName = "";

	/**
	 * Renders the form in the kickstarter; this was add_cat_cm()
	 */
	function render_wizard() {
		$lines=array();

		$catID = "cm";
		$action = explode(":",$this->wizard->modData["wizAction"]);
		if ($action[0]=="edit")	{
			$this->wizard->regNewEntry($catID,$action[1]);
			$lines = $this->wizard->catHeaderLines($lines,$catID,$this->wizard->options[$catID],"&nbsp;",$action[1]);
			$piConf = $this->wizard->wizArray[$catID][$action[1]];
			$ffPrefix='['.$catID.']['.$action[1].']';

				// Enter title of the module function
			$subContent="<strong>Title of the ClickMenu element:</strong><BR>".
				$this->wizard->renderStringBox_lang("title",$ffPrefix,$piConf);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';

				// Position
			$optValues = array(
				"bottom" => "Insert in bottom",
				"top" => "Insert in top",
				"before_delete" => "Insert before the 'Delete' item",
			);
			$subContent="<strong>Options</strong><BR>".
				$this->wizard->renderSelectBox($ffPrefix."[options]",$piConf["options"],$optValues);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';

				// Admin only
			$subContent =$this->wizard->resImg("cm.png");
			$subContent.= $this->wizard->renderCheckBox($ffPrefix."[second_level]",$piConf["second_level"])."Activate a second-level menu.<BR>";
			$subContent.= $this->wizard->renderCheckBox($ffPrefix."[only_page]",$piConf["only_page"])."Add only if the click menu is on a 'Page' (example)<BR>";
			$subContent.= $this->wizard->renderCheckBox($ffPrefix."[only_if_edit]",$piConf["only_if_edit"])."Only active if item is editable.<BR>";
			$subContent.= $this->wizard->renderCheckBox($ffPrefix."[remove_view]",$piConf["remove_view"])."Remove 'Show' element (example)<BR>";
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_cm'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_cm'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border=0 cellpadding=2 cellspacing=2>'.implode("",$lines).'</table>';
		return $content;
	}








	/**
	 * Renders the extension PHP codee; this was 
	 */
	function render_extPart($k,$config,$extKey) {
		$WOP="[cm][".$k."]";
		$cN = $this->wizard->returnName($extKey,"class","cm".$k);
		$filename = 'class.'.$cN.'.php';
		$pathSuffix = "cm".$k."/";

			// This will make sure our item is inserted in the clickmenu!
		$this->wizard->ext_tables[]=$this->wizard->sPS('
			'.$this->wizard->WOPcomment('WOP:'.$WOP.':').'
			if (TYPO3_MODE=="BE")	{
				$GLOBALS["TBE_MODULES_EXT"]["xMOD_alt_clickmenu"]["extendCMclasses"][]=array(
					"name" => "'.$cN.'",
					"path" => t3lib_extMgm::extPath($_EXTKEY)."'.$filename.'"
				);
			}
		');
			// Add title to the locallang file.
		$this->wizard->addLocalConf($this->wizard->ext_locallang,$config,"title","cm",$k);

			// Add icon
		$this->wizard->addFileToFileArray($pathSuffix."cm_icon.gif",t3lib_div::getUrl(t3lib_extMgm::extPath("kickstarter")."res/notfound_module.gif"));

			// 	Building class:
		$content = "";
		$content.=$this->wizard->sPS('
				// Adds the regular item:
			$LL = $this->wizard->includeLL();

				// Repeat this (below) for as many items you want to add!
				// Remember to add entries in the localconf.php file for additional titles.
			$url = t3lib_extMgm::extRelPath("'.$extKey.'")."'.$pathSuffix.'index.php?id=".$uid;
			$localItems[] = $backRef->linkItem(
				$GLOBALS["LANG"]->getLLL("cm'.$k.'_title",$LL),
				$backRef->excludeIcon(\'<img src="\'.t3lib_extMgm::extRelPath("'.$extKey.'").\''.$pathSuffix.'cm_icon.gif" width="15" height="12" border=0 align=top>\'),
				$backRef->urlRefForCM($url),
				1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
			);
		');
		if ($config["second_level"])	{
			$secondContent = $content;
			$secondContent.=chr(10).'$menuItems=array_merge($menuItems,$localItems);';

			$content = "";
			$content.=$this->wizard->sPS('
				$LL = $this->wizard->includeLL();

				$localItems[]="spacer";
				$localItems["moreoptions_'.$cN.'"]=$backRef->linkItem(
					$GLOBALS["LANG"]->getLLL("cm'.$k.'_title_activate",$LL),
					$backRef->excludeIcon(\'<img src="\'.t3lib_extMgm::extRelPath("'.$extKey.'").\''.$pathSuffix.'cm_icon_activate.gif" width="15" height="12" border=0 align=top>\'),
					"top.loadTopMenu(\'".t3lib_div::linkThisScript()."&cmLevel=1&subname=moreoptions_'.$cN.'\');return false;",
					0,
					1
				);
			');

				// Add activate title to the locallang file.
			$this->wizard->addLocalConf($this->wizard->ext_locallang,array("title_activate"=>"...Second level ->"),"title_activate","cm",$k,0,1);
				// Add activate icon
			$this->wizard->addFileToFileArray($pathSuffix."cm_icon_activate.gif",t3lib_div::getUrl(t3lib_extMgm::extPath("kickstarter")."res/notfound_module.gif"));
		}

		if ($config["only_page"])	$content=$this->wizard->sPS('
				// Returns directly, because the clicked item was not from the pages table '.$this->wizard->WOPcomment('(WOP:'.$WOP.'[only_page])').'
			if ($table!="pages")	return $menuItems;
		').$content;

		$content.=$this->wizard->sPS('
			'.$this->wizard->WOPcomment('(WOP:'.$WOP.'[options] BEGIN) Inserts the item at the chosen location').'
		');
		if ($config["options"]=="top")	{	// In top:
			$content.=$this->wizard->sPS('
				$menuItems=array_merge($localItems,$menuItems);
			');
		} elseif ($config["options"]=="before_delete")	{	// Just before "Delete" and its preceding divider line:
			$content.=$this->wizard->sPS('
					// Find position of "delete" element:
				reset($menuItems);
				$c=0;
				while(list($k)=each($menuItems))	{
					$c++;
					if (!strcmp($k,"delete"))	break;
				}
					// .. subtract two (delete item + divider line)
				$c-=2;
					// ... and insert the items just before the delete element.
				array_splice(
					$menuItems,
					$c,
					0,
					$localItems
				);
			');
		} else	{	// In bottom (default):
			$content.=$this->wizard->sPS('
				// Simply merges the two arrays together and returns ...
				$menuItems=array_merge($menuItems,$localItems);
			');
		}
		$content.=$this->wizard->sPS('
			'.$this->wizard->WOPcomment('(WOP:'.$WOP.'[options] END)').'
		');

		if ($config["only_if_edit"])	$content=$this->wizard->wrapBody('
			if ($backRef->editOK)	{
			',$content,'
			}
		');


		if ($config["remove_view"])	$content.=$this->wizard->sPS('
				// Removes the view-item from clickmenu  '.$this->wizard->WOPcomment('(WOP:'.$WOP.'[remove_view])').'
			unset($menuItems["view"]);
		');

		$content=$this->wizard->wrapBody('
			if (!$backRef->cmLevel)	{
			',$content,'
			}
		');

		if ($config["second_level"])	{
			$content.=$this->wizard->wrapBody('
				else {
				',$secondContent,'
				}
			');
		}




			// Now wrap the function body around this:
		$content=$this->wizard->wrapBody('
			function main(&$backRef,$menuItems,$table,$uid)	{
				global $BE_USER,$TCA,$LANG;

				$localItems = Array();
				',$content,'
				return $menuItems;
			}
		');
			// Add include locallanguage function:
		$content.=$this->wizard->addLLFunc($extKey);

			// Now wrap the function body around this:
		$content=$this->wizard->wrapBody('
			class '.$cN.' {
				',$content,'
			}
		');


#		$this->wizard->printPre($content);

		$this->wizard->addFileToFileArray($filename,$this->wizard->PHPclassFile($extKey,$filename,$content,"Addition of an item to the clickmenu"));


		$cN = $this->wizard->returnName($extKey,"class","cm".$k);
		$this->wizard->writeStandardBE_xMod($extKey,$config,$pathSuffix,$cN,$k,"cm");

	}

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_cm.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_cm.php']);
}


?>