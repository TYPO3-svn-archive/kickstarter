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
 
require_once(t3lib_extMgm::extPath('kickstarter').'class.tx_kickstarter_sectionbase.php');

class tx_kickstarter_section_emconf extends tx_kickstarter_sectionbase {
	var $catName = 'General info';

	/**
	 * Renders the form in the kickstarter; this was add_cat_emconf()
	 */
	function render_wizard() {
		$lines=array();

		$catID = 'emconf';
		$action = explode(':',$this->wizard->modData['wizAction']);

		if ($action[0]=='edit')	{
			$action[1]=1;
			$this->wizard->regNewEntry($catID,$action[1]);

			$lines = $this->wizard->catHeaderLines($lines,$catID,$this->wizard->options[$catID],'&nbsp;',$action[1]);
			$piConf = $this->wizard->wizArray[$catID][$action[1]];
			$ffPrefix='['.$catID.']['.$action[1].']';

			if (!$this->wizard->EMmode && $this->wizard->saveKey)	{
				$extKeyRec = $this->wizard->pObj->getExtKeyRecord($this->wizard->saveKey);
			}

				// Title
			$subContent='<strong>Title:</strong><BR>'.
				$this->wizard->renderStringBox($ffPrefix.'[title]',$piConf['title']?$piConf['title']:$extKeyRec['title']);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';

				// Description
			$subContent='<strong>Description:</strong><BR>'.
				$this->wizard->renderStringBox($ffPrefix.'[description]',$piConf['description']?$piConf['description']:$extKeyRec['description']);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';

				// Sub-position
			$optValues = Array(
				'' => '',
				'fe' => 'Frontend',
				'plugin' => 'Frontend Plugins',
				'be' => 'Backend',
				'module' => 'Backend Modules',
				'services' => 'Services',
				'example' => 'Examples',
				'misc' => 'Miscellaneous',
				'templates' => 'Templates',
				'doc' => 'Documentation',
			);
			$subContent='<strong>Category:</strong><BR>'.
				$this->wizard->renderSelectBox($ffPrefix.'[category]',$piConf['category'],$optValues);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';




				// State
			$optValues = Array(
				'alpha' => 'Alpha (Very initial development)',
				'beta' => 'Beta (Under current development, should work partly)',
				'stable' => 'Stable (Stable and used in production)',
				'experimental' => 'Experimental (Nobody knows if this is going anywhere yet...)',
				'test' => 'Test (Test extension, demonstrates concepts etc.)',
			);
			$subContent='<strong>State</strong><BR>'.
				$this->wizard->renderSelectBox($ffPrefix.'[state]',$piConf['state'],$optValues);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';

				// Dependencies
			$subContent='<strong>Dependencies (comma list of extkeys):</strong><BR>'.
				$this->wizard->renderStringBox($ffPrefix.'[dependencies]',$piConf['dependencies']);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';




				// Author
			$subContent='<strong>Author Name:</strong><BR>'.
				$this->wizard->renderStringBox($ffPrefix.'[author]',$piConf['author']?$piConf['author']:$GLOBALS['BE_USER']->user['realName']);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';

				// Author/Email
			$subContent='<strong>Author email:</strong><BR>'.
				$this->wizard->renderStringBox($ffPrefix.'[author_email]',$piConf['author_email']?$piConf['author_email']:$GLOBALS['BE_USER']->user['email']);
			$lines[]='<tr'.$this->wizard->bgCol(3).'><td>'.$this->wizard->fw($subContent).'</td></tr>';
		}

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_emconf'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_emconf'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border=0 cellpadding=2 cellspacing=2>'.implode('',$lines).'</table>';
		return $content;
	}

	function render_extPart() {

	}
}

// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_emconf.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_emconf.php']);
}

?>