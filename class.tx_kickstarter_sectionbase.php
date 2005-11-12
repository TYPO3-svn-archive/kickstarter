<?php

#vars that probably still need "wizard->" added: dontPrintImages printWOP

class tx_kickstarter_sectionbase {
	
	/* instance of the main Kickstarter Wizard class (class.tx_kickstarter_wizard.php) */
	var $wizard;
	
	/* instance of the Kickstarter Compilefiles class (class.tx_kickstarter_compilefiles.php) */
	var $compilefiles;
	
	/* Unique ID of this section (used in forms and data processing) */
	var $sectionID = 'uniqueID';

	/* Variable-Prefix used for the generation of input-fields */
	var $varPrefix = 'kickstarter';

	/* renders the wizard for this section */
	function render_wizard() {
	}
	
	/* renders the code for this section */
	function render_extPart() {
	}
	
	function &process_hook($hookName, &$data) {
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter'][$this->sectionID][$hookName])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter'][$this->sectionID][$hookName] as $_funcRef) {
	$data =& t3lib_div::callUserFunction($_funcRef, $data, $this);
			}
		}
		return $data;
	}

	function renderCheckBox($prefix,$value,$defVal=0)	{
		if (!isset($value))	$value=$defVal;
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="hidden" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="0"><input type="checkbox" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="1"'.($value?" CHECKED":"").' onClick="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}

	function renderTextareaBox($prefix,$value)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<textarea name="'.$this->piFieldName("wizArray_upd").$prefix.'" style="width:600px;" rows="10" wrap="OFF" onChange="'.$onCP[1].'" title="'.htmlspecialchars("WOP:".$prefix).'"'.$this->wop($prefix).'>'.t3lib_div::formatForTextarea($value).'</textarea>';
	}

	function renderStringBox($prefix,$value,$width=200)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="text" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="'.htmlspecialchars($value).'" style="width:'.$width.'px;" onChange="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}

	function renderRadioBox($prefix,$value,$thisValue)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="radio" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="'.$thisValue.'"'.(!strcmp($value,$thisValue)?" CHECKED":"").' onClick="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}

	function renderSelectBox($prefix,$value,$optValues)	{
		$onCP = $this->getOnChangeParts($prefix);
		$opt=array();
		$isSelFlag=0;
		foreach($optValues as $k=>$v)	{
			$sel = (!strcmp($k,$value)?" SELECTED":"");
			if ($sel)	$isSelFlag++;
			$opt[]='<option value="'.htmlspecialchars($k).'"'.$sel.'>'.htmlspecialchars($v).'</option>';
		}
		if (!$isSelFlag && strcmp("",$value))	$opt[]='<option value="'.$value.'" SELECTED>'.htmlspecialchars("CURRENT VALUE '".$value."' DID NOT EXIST AMONG THE OPTIONS").'</option>';
		return $this->wopText($prefix).$onCP[0].'<select name="'.$this->piFieldName("wizArray_upd").$prefix.'" onChange="'.$onCP[1].'"'.$this->wop($prefix).'>'.implode("",$opt).'</select>';
	}

	function whatIsThis($str)	{
		return ' <a href="#" title="'.htmlspecialchars($str).'" style="cursor:help" onClick="alert('.$GLOBALS['LANG']->JScharCode($str).');return false;">(What is this?)</a>';
	}

	function renderStringBox_lang($fieldName,$ffPrefix,$piConf)	{
		$content = $this->renderStringBox($ffPrefix."[".$fieldName."]",$piConf[$fieldName])." [English]";
		if (count($this->wizard->selectedLanguages))	{
			$lines=array();
			foreach($this->wizard->selectedLanguages as $k=>$v) {
				$lines[]=$this->renderStringBox($ffPrefix."[".$fieldName."_".$k."]",$piConf[$fieldName."_".$k])." [".$v."]";
			}
			$content.=$this->textSetup("",implode("<BR>",$lines));
		}
		return $content;
	}

	function textSetup($header,$content)	{
		return ($header?"<strong>".$header."</strong><BR>":"")."<blockquote>".trim($content)."</blockquote>";
	}

	function resImg($name,$p='align="center"',$pre="<BR>",$post="<BR>")	{
		if ($this->dontPrintImages)	return "<BR>";
		$imgRel = $this->path_resources().$name;
		$imgInfo = @getimagesize(PATH_site.$imgRel);
		return $pre.'<img src="'.$this->wizard->siteBackPath.$imgRel.'" '.$imgInfo[3].($p?" ".$p:"").' vspace=5 border=1 style="border:solid 1px;">'.$post;
	}

	function resIcon($name,$p="")	{
		if ($this->dontPrintImages)	return "";
		$imgRel = $this->path_resources("icons/").$name;
		if (!@is_file(PATH_site.$imgRel))	return "";
		$imgInfo = @getimagesize(PATH_site.$imgRel);
		return '<img src="'.$this->wizard->siteBackPath.$imgRel.'" '.$imgInfo[3].($p?" ".$p:"").'>';
	}

	function path_resources($subdir="res/")	{
		return substr(t3lib_extMgm::extPath("kickstarter"),strlen(PATH_site)).$subdir;
	}

	function getOnChangeParts($prefix)	{
		$md5h=t3lib_div::shortMd5($this->piFieldName("wizArray_upd").$prefix);
		return array('<a name="'.$md5h.'"></a>',"setFormAnchorPoint('".$md5h."');");
	}

	function wop($prefix)	{
		return ' title="'.htmlspecialchars("WOP: ".$prefix).'"';
	}

	function returnName($extKey,$type,$suffix='')	{
		if (substr($extKey,0,5)=='user_')	{
			$extKey = substr($extKey,5);
			switch($type)	{
				case 'class':
					return 'user_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				break;
				case 'tables':
				case 'fields':
				case 'fields':
					return 'user_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				break;
				case 'module':
					return 'u'.str_replace('_','',$extKey).$suffix;
				break;
			}
		} else {
			switch($type)	{
				case 'class':
					return 'tx_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				break;
				case 'tables':
				case 'fields':
				case 'fields':
					return 'tx_'.str_replace('_','',$extKey).($suffix?'_'.$suffix:'');
				break;
				case 'module':
					return 'tx'.str_replace('_','',$extKey).$suffix;
				break;
			}
		}
	}

	function wopText($prefix)	{
		return $this->printWOP?'<font face="verdana,arial,sans-serif" size=1 color=#999999>'.htmlspecialchars($prefix).':</font><BR>':'';
	}

	function catHeaderLines($lines,$k,$v,$altHeader="",$index="")	{
					$lines[]='<tr'.$this->bgCol(1).'><td><strong>'.$this->fw($v[0]).'</strong></td></tr>';
					$lines[]='<tr'.$this->bgCol(2).'><td>'.$this->fw($v[1]).'</td></tr>';
					$lines[]='<tr><td></td></tr>';
		return $lines;
	}

	function linkCurrentItems($cat)	{
		$items = $this->wizard->wizArray[$cat];
		$lines=array();
		$c=0;
		if (is_array($items))	{
			foreach($items as $k=>$conf)	{
				$lines[]='<strong>'.$this->linkStr($conf["title"]?$conf["title"]:"<em>Item ".$k."</em>",$cat,'edit:'.$k).'</strong>';
				$c=$k;
			}
		}
		if (!t3lib_div::inList("save,ts,TSconfig,languages",$cat) || !count($lines))	{
			$c++;
			if (count($lines))	$lines[]='';
			$lines[]=$this->linkStr('Add new item',$cat,'edit:'.$c);
		}
		return $this->fw(implode("<BR>",$lines));
	}

	function linkStr($str,$wizSubCmd,$wizAction)	{
		return '<a href="#" onClick="
			document.'.$this->varPrefix.'_wizard[\''.$this->piFieldName("wizSubCmd").'\'].value=\''.$wizSubCmd.'\';
			document.'.$this->varPrefix.'_wizard[\''.$this->piFieldName("wizAction").'\'].value=\''.$wizAction.'\';
			document.'.$this->varPrefix.'_wizard.submit();
			return false;">'.$str.'</a>';
	}

	function bgCol($n,$mod=0)	{
		$color = $this->color[$n-1];
		if ($mod)	$color = t3lib_div::modifyHTMLcolor($color,$mod,$mod,$mod);
		return ' bgColor="'.$color.'"';
	}

	function regNewEntry($k,$index)	{
		if (!is_array($this->wizard->wizArray[$k][$index]))	{
			$this->wizard->wizArray[$k][$index]=array();
		}
	}

	function bwWithFlag($str,$flag)	{
		if ($flag)	$str = '<strong>'.$str.'</strong>';
		return $str;
	}

	/**
	 * Getting link to this page + extra parameters, we have specified
	 *
	 * @param	array		Additional parameters specified.
	 * @return	string		The URL
	 */
	function linkThisCmd($uPA=array())	{
		$url = t3lib_div::linkThisScript($uPA);
		return $url;
	}

	/**
	 * Font wrap function; Wrapping input string in a <span> tag with font family and font size set
	 *
	 * @param	string		Input value
	 * @return	string		Wrapped input value.
	 */
	function fw($str)	{
		return '<span style="font-family:verdana,arial,sans-serif; font-size:10px;">'.$str.'</span>';
	}


	function piFieldName($key)	{
		return $this->varPrefix."[".$key."]";
	}

	function cmdHiddenField()	{
		return '<input type="hidden" name="'.$this->piFieldName("cmd").'" value="'.htmlspecialchars($this->currentCMD).'">';
	}

	function preWrap($str)	{
		$str = str_replace(chr(9),"&nbsp;&nbsp;&nbsp;&nbsp;",htmlspecialchars($str));
		$str = '<pre>'.$str.'</pre>';
		return $str;
	}

	function fieldIsRTE($fC)	{
		return !strcmp($fC["type"],"textarea_rte") &&
						($fC["conf_rte"]=="basic" ||
						(t3lib_div::inList("custom,moderate",$fC["conf_rte"]) && $fC["conf_mode_cssOrNot"])
						);
	}












######### Functions from compilefiles #########

	function sPS($content,$preLines=1)	{
		$lines = explode(chr(10),str_replace(chr(13),"",$content));
		$lastLineWithContent=0;
		$firstLineWithContent=-1;
		$min=array();
		reset($lines);
		while(list($k,$v)=each($lines))	{
			if (trim($v))	{
				if ($firstLineWithContent==-1)	$firstLineWithContent=$k;
				list($preSpace) = split("[^[:space:]]",$v,2);
				$min[]=count(explode(chr(9),$preSpace));
				$lastLineWithContent=$k;
			}
		}
		$number_of=count($min) ? min($min) : 0;
		$newLines=array();
		if ($firstLineWithContent>=0)	{
			for ($a=$firstLineWithContent;$a<=$lastLineWithContent;$a++)	{
				$parts = explode(chr(9),$lines[$a],$number_of);
				$newLines[]=end($parts);
			}
		}
		return str_pad("",$preLines,chr(10)).implode(chr(10),$newLines).chr(10);
	}

	function getSplitLabels_reference($config,$key,$LLkey)	{
		$this->wizard->ext_locallang_db["default"][$LLkey]=array(trim($config[$key]));
		if (count($this->wizard->languages))	{
			reset($this->wizard->languages);
			while(list($lk,$lv)=each($this->wizard->languages))	{
				if (isset($this->wizard->selectedLanguages[$lk]))	{
					$this->wizard->ext_locallang_db[$lk][$LLkey]=array(trim($config[$key."_".$lk]));
				}
			}
		}
		return "LLL:EXT:".$this->wizard->extKey."/locallang_db.php:".$LLkey;
	}

	function WOPcomment($str)	{
		return $str&&$this->wizard->outputWOP ? "## ".$str : "";
	}

	function indentLines($content,$number=1)	{
		$preTab = str_pad("",$number,chr(9));
		$lines = explode(chr(10),str_replace(chr(13),"",$content));
		while(list($k,$v)=each($lines))	{
			$lines[$k]=$preTab.$v;
		}
		return implode(chr(10),$lines);
	}

	function printPre($content)	{
		echo '<pre>'.htmlspecialchars(str_replace(chr(9),'    ',$content)).'</pre>';
	}

	function wrapBody($before,$content,$after,$indent=1)	{
		$parts=array();
		$parts[] = $this->sPS($before,0);
		$parts[] = $this->indentLines(rtrim($content),$indent);
		$parts[] = chr(10).$this->sPS($after,0);

		return implode('',$parts);
	}

	function replaceMarkers($content,$markers)	{
		reset($markers);
		while(list($k,$v)=each($markers))	{
			$content = str_replace($k,$v,$content);
		}
		return $content;
	}

	function makeFileArray($name,$content)	{
	#	echo '<HR><strong>'.$name.'</strong><HR><pre>'.htmlspecialchars($content).'</pre>';

		return array(
			'name' => $name,
			'size' => strlen($content),
			'mtime' => time(),
			'is_executable' => 0,
			'content' => $content,
			'content_md5' => md5($content)
		);
	}

	function slashValueForSingleDashes($value)	{
		return str_replace("'","\'",str_replace('\\','\\\\',$value));
	}

	function getSplitLabels($config,$key)	{
		$language=array();
		$language[]=str_replace('|','',$config[$key]);
		if (count($this->wizard->languages))	{
			reset($this->wizard->languages);
			while(list($lk,$lv)=each($this->wizard->languages))	{
				if (isset($this->wizard->selectedLanguages[$lk]))	{
					$language[]=str_replace('|','',$config[$key.'_'.$lk]);
				} else $language[]='';
			}
		}
		$out = implode('|',$language);
		$out = str_replace(chr(10),'',$out);
		$out = rtrim(str_replace('|',chr(10),$out));
		$out = str_replace(chr(10),'|',$out);
		return $out;
	}

	function addLocalLangFile($arr,$filename,$description)	{
		$lines=array();
		reset($arr);
		$lines[]='<?php';
		$lines[]=trim($this->sPS('
			/**
			 * '.$description.'
			 *
			 * This file is detected by the translation tool.
			 */
		'));
		$lines[]='';
		$lines[]='$LOCAL_LANG = Array (';
		while(list($lK,$labels)=each($arr))	{
			if (is_array($labels))	{
				$lines[]="	'".$lK."' => Array (";
				while(list($l,$v)=each($labels))	{
					if (strcmp($v[0],''))	$lines[]="		'".$l."' => '".addslashes($v[0])."',	".$this->WOPcomment($v[1]);
				}
				$lines[]='	),';
			}
		}
		$lines[]=');';
		$lines[]='?>';
		$this->addFileToFileArray($filename,implode(chr(10),$lines));
	}

	function writeStandardBE_xMod($extKey,$config,$pathSuffix,$cN,$k,$k_prefix)	{
			// Make conf.php file:
		$content = $this->sPS("
				// DO NOT REMOVE OR CHANGE THESE 3 LINES:
			define('TYPO3_MOD_PATH', 'ext/".$extKey."/".$pathSuffix."');
			\$BACK_PATH='../../../';
			\$MCONF['name']='xMOD_".$cN."';
		");
		$content=$this->wrapBody('
			<?php
			',$content,'
			?>
		',0);
		$this->addFileToFileArray($pathSuffix."conf.php",trim($content));
		$this->wizard->EM_CONF_presets["module"][]=ereg_replace("\/$","",$pathSuffix);

			// Add title to local lang file
		$ll=array();
		$this->addLocalConf($ll,$config,"title",$k_prefix,$k,1);
		$this->addLocalConf($ll,array("function1"=>"Function #1"),"function1",$k_prefix,$k,1,1);
		$this->addLocalConf($ll,array("function2"=>"Function #2"),"function2",$k_prefix,$k,1,1);
		$this->addLocalConf($ll,array("function3"=>"Function #3"),"function3",$k_prefix,$k,1,1);
		$this->addLocalLangFile($ll,$pathSuffix."locallang.php",'Language labels for '.$extKey.' module '.$k_prefix.$k);

			// Add clear.gif
		$this->addFileToFileArray($pathSuffix."clear.gif",t3lib_div::getUrl(t3lib_extMgm::extPath("kickstarter")."res/clear.gif"));

			// Make module index.php file:
		$indexContent = $this->sPS("
				// DEFAULT initialization of a module [BEGIN]
			unset(\$MCONF);
			require ('conf.php');
			require (\$BACK_PATH.'init.php');
			require (\$BACK_PATH.'template.php');
			\$LANG->includeLLFile('EXT:".$extKey."/".$pathSuffix."locallang.php');
			#include ('locallang.php');
			require_once (PATH_t3lib.'class.t3lib_scbase.php');
				// ....(But no access check here...)
				// DEFAULT initialization of a module [END]
		");

		$indexContent.= $this->sPS("
			class ".$cN." extends t3lib_SCbase {
				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 */
				function menuConfig()	{
					global \$LANG;
					\$this->MOD_MENU = Array (
						'function' => Array (
							'1' => \$LANG->getLL('function1'),
							'2' => \$LANG->getLL('function2'),
							'3' => \$LANG->getLL('function3'),
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 */
				function main()	{
					global \$BE_USER,\$LANG,\$BACK_PATH,\$TCA_DESCR,\$TCA,\$CLIENT,\$TYPO3_CONF_VARS;

						// Draw the header.
					\$this->doc = t3lib_div::makeInstance('mediumDoc');
					\$this->doc->backPath = \$BACK_PATH;
					\$this->doc->form='<form action=\"\" method=\"POST\">';

						// JavaScript
					\$this->doc->JScode = '
						<script language=\"javascript\" type=\"text/javascript\">
							script_ended = 0;
							function jumpToUrl(URL)	{
								document.location = URL;
							}
						</script>
					';

					\$this->pageinfo = t3lib_BEfunc::readPageAccess(\$this->id,\$this->perms_clause);
					\$access = is_array(\$this->pageinfo) ? 1 : 0;
					if ((\$this->id && \$access) || (\$BE_USER->user['admin'] && !\$this->id))	{
						if (\$BE_USER->user['admin'] && !\$this->id)	{
							\$this->pageinfo=array('title' => '[root-level]','uid'=>0,'pid'=>0);
						}

						\$headerSection = \$this->doc->getHeader('pages',\$this->pageinfo,\$this->pageinfo['_thePath']).'<br>'.\$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre(\$this->pageinfo['_thePath'],50);

						\$this->content.=\$this->doc->startPage(\$LANG->getLL('title'));
						\$this->content.=\$this->doc->header(\$LANG->getLL('title'));
						\$this->content.=\$this->doc->spacer(5);
						\$this->content.=\$this->doc->section('',\$this->doc->funcMenu(\$headerSection,t3lib_BEfunc::getFuncMenu(\$this->id,'SET[function]',\$this->MOD_SETTINGS['function'],\$this->MOD_MENU['function'])));
						\$this->content.=\$this->doc->divider(5);


						// Render content:
						\$this->moduleContent();


						// ShortCut
						if (\$BE_USER->mayMakeShortcut())	{
							\$this->content.=\$this->doc->spacer(20).\$this->doc->section('',\$this->doc->makeShortcutIcon('id',implode(',',array_keys(\$this->MOD_MENU)),\$this->MCONF['name']));
						}
					}
					\$this->content.=\$this->doc->spacer(10);
				}
				function printContent()	{

					\$this->content.=\$this->doc->endPage();
					echo \$this->content;
				}

				function moduleContent()	{
					switch((string)\$this->MOD_SETTINGS['function'])	{
						case 1:
							\$content='<div align=center><strong>Hello World!</strong></div><BR>
								The \"Kickstarter\" has made this module automatically, it contains a default framework for a backend module but apart from it does nothing useful until you open the script \"'.substr(t3lib_extMgm::extPath('".$extKey."'),strlen(PATH_site)).'".$pathSuffix."index.php\" and edit it!
								<HR>
								<BR>This is the GET/POST vars sent to the script:<BR>'.
								'GET:'.t3lib_div::view_array(\$_GET).'<BR>'.
								'POST:'.t3lib_div::view_array(\$_POST).'<BR>'.
								'';
							\$this->content.=\$this->doc->section('Message #1:',\$content,0,1);
						break;
						case 2:
							\$content='<div align=center><strong>Menu item #2...</strong></div>';
							\$this->content.=\$this->doc->section('Message #2:',\$content,0,1);
						break;
						case 3:
							\$content='<div align=center><strong>Menu item #3...</strong></div>';
							\$this->content.=\$this->doc->section('Message #3:',\$content,0,1);
						break;
					}
				}
			}
		");

		$this->addFileToFileArray($pathSuffix."index.php",$this->PHPclassFile($extKey,$pathSuffix."index.php",$indexContent,$extKey.' module '.$k_prefix.$k,$cN));
	}

	function addLLfunc($extKey)	{
		return $this->sPS("
			/**
			 * Includes the [extDir]/locallang.php and returns the \$LOCAL_LANG array found in that file.
			 */
			function includeLL()	{
				include(t3lib_extMgm::extPath('".$extKey."').'locallang.php');
				return \$LOCAL_LANG;
			}
		");
	}
	function addStdLocalLangConf($ll,$k,$onlyMode=0)	{
		$this->addLocalConf($ll,array(
			"list_mode_1"=>"Mode 1",
			"list_mode_1_dk"=>"Visning 1"
		),"list_mode_1","pi",$k,1,1);
		$this->addLocalConf($ll,array(
			"list_mode_2"=>"Mode 2",
			"list_mode_2_dk"=>"Visning 2"
		),"list_mode_2","pi",$k,1,1);
		$this->addLocalConf($ll,array(
			"list_mode_3"=>"Mode 3",
			"list_mode_3_dk"=>"Visning 3"
		),"list_mode_3","pi",$k,1,1);
		$this->addLocalConf($ll,array(
			"back"=>"Back",
			"back_dk"=>"Tilbage"
		),"back","pi",$k,1,1);

		if (!$onlyMode)	{
			$this->addLocalConf($ll,array(
				"pi_list_browseresults_prev"=>"< Previous",
				"pi_list_browseresults_prev_dk"=>"< Forrige"
			),"pi_list_browseresults_prev","pi",$k,1,1);
			$this->addLocalConf($ll,array(
				"pi_list_browseresults_page"=>"Page",
				"pi_list_browseresults_page_dk"=>"Side"
			),"pi_list_browseresults_page","pi",$k,1,1);
			$this->addLocalConf($ll,array(
				"pi_list_browseresults_next"=>"Next >",
				"pi_list_browseresults_next_dk"=>"N�ste >"
			),"pi_list_browseresults_next","pi",$k,1,1);
			$this->addLocalConf($ll,array(
				"pi_list_browseresults_displays"=>"Displaying results ###SPAN_BEGIN###%s to %s</span> out of ###SPAN_BEGIN###%s</span>",
				"pi_list_browseresults_displays_dk"=>"Viser resultaterne ###SPAN_BEGIN###%s til %s</span> ud af ###SPAN_BEGIN###%s</span>"
			),"pi_list_browseresults_displays","pi",$k,1,1);

			$this->addLocalConf($ll,array(
				"pi_list_searchBox_search"=>"Search",
				"pi_list_searchBox_search_dk"=>"S�g"
			),"pi_list_searchBox_search","pi",$k,1,1);
		}

		return $ll;
	}

	function addLocalConf(&$lArray,$confArray,$key,$prefix,$subPrefix,$dontPrefixKey=0,$noWOP=0,$overruleKey="")	{
		reset($this->wizard->languages);

		$overruleKey = $overruleKey ? $overruleKey : ($dontPrefixKey?"":$prefix.$subPrefix."_").$key;

		$lArray["default"][$overruleKey] = array($confArray[$key],(!$noWOP?'WOP:['.$prefix.']['.$subPrefix.']['.$key.']':''));
		while(list($k)=each($this->wizard->languages))	{
			$lArray[$k][$overruleKey] = array(trim($confArray[$key."_".$k]),(!$noWOP?'WOP:['.$prefix.']['.$subPrefix.']['.$key."_".$k.']':''));
		}
		return $lArray;
	}




	function PHPclassFile($extKey,$filename,$content,$desrc,$SOBE_class="",$SOBE_extras="")	{
		$file = trim($this->sPS('
			<?php
			/***************************************************************
			*  Copyright notice
			*
			*  (c) 2005 '.$this->userField("name").' ('.$this->userField("email").')
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
			*
			*  This script is distributed in the hope that it will be useful,
			*  but WITHOUT ANY WARRANTY; without even the implied warranty of
			*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
			*  GNU General Public License for more details.
			*
			*  This copyright notice MUST APPEAR in all copies of the script!
			***************************************************************/
			/**
			 * '.$desrc.'
			 *
			 * @author	'.$this->userField("name").' <'.$this->userField("email").'>
			 */
		'));

		$file.="\n\n\n".$content."\n\n\n";

		$file.=trim($this->sPS("

			if (defined('TYPO3_MODE') && \$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/".$extKey."/".$filename."'])	{
				include_once(\$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/".$extKey."/".$filename."']);
			}
			".($SOBE_class?"



			// Make instance:
			\$SOBE = t3lib_div::makeInstance('".$SOBE_class."');
			\$SOBE->init();
			".($SOBE_extras['include']?"
			// Include files?
			foreach(\$SOBE->include_once as \$INC_FILE)	include_once(\$INC_FILE);":"")."
			".($SOBE_extras['firstLevel']?"
			\$SOBE->checkExtObj();	// Checking for first level external objects":"")."
			\$SOBE->main();
			\$SOBE->printContent();
			":"")."
			?>
		"));

		return $file;
	}



	function addFileToFileArray($name,$content,$mode=0)	{
		switch($mode)	{
			case 1:	// Append
				$this->wizard->fileArray[$name]=$this->makeFileArray($name,$this->wizard->fileArray[$name]["content"].chr(10).$content);
			break;
			case -1:	// Prepend
				$this->wizard->fileArray[$name]=$this->makeFileArray($name,$content.chr(10).$this->wizard->fileArray[$name]["content"]);
			break;
			default:	// Substitution:
				$this->wizard->fileArray[$name]=$this->makeFileArray($name,$content);
			break;
		}
	}

	function makeEMCONFpreset($prefix="")	{
		$this->wizard->_addArray = $this->wizard->wizArray["emconf"][1];
		$EM_CONF=array();
		$presetFields = explode(",","title,description,category,shy,dependencies,conflicts,priority,module,state,internal,uploadfolder,createDirs,modify_tables,clearCacheOnLoad,lockType,author,author_email,author_company,private,download_password,version");
		while(list(,$s)=each($presetFields))	{
			$EM_CONF[$prefix.$s]="";
		}


		$EM_CONF[$prefix."uploadfolder"] = $this->wizard->EM_CONF_presets["uploadfolder"]?1:0;
		$EM_CONF[$prefix."clearCacheOnLoad"] = $this->wizard->EM_CONF_presets["clearCacheOnLoad"]?1:0;

		if (is_array($this->wizard->EM_CONF_presets["createDirs"]))	{
			$EM_CONF[$prefix."createDirs"] = implode(",",array_unique($this->wizard->EM_CONF_presets["createDirs"]));
		}

		if (is_array($this->wizard->EM_CONF_presets["dependencies"]) || $this->wizard->wizArray["emconf"][1]["dependencies"])	{
			$aa= t3lib_div::trimExplode(",",strtolower($this->wizard->wizArray["emconf"][1]["dependencies"]),1);
			$EM_CONF[$prefix."dependencies"] = implode(",",array_unique(array_merge($this->wizard->EM_CONF_presets["dependencies"],$aa)));
		}
		unset($this->wizard->_addArray["dependencies"]);
		if (is_array($this->wizard->EM_CONF_presets["module"]))	{
			$EM_CONF[$prefix."module"] = implode(",",array_unique($this->wizard->EM_CONF_presets["module"]));
		}
		if (is_array($this->wizard->EM_CONF_presets["modify_tables"]))	{
			$EM_CONF[$prefix."modify_tables"] = implode(",",array_unique($this->wizard->EM_CONF_presets["modify_tables"]));
		}

		return $EM_CONF;
	}
	function userField($k)	{
		$v = "";
		if($k == "name") {
			$v = ($this->wizard->wizArray["emconf"][1]["author"] != "") ? $this->wizard->wizArray["emconf"][1]["author"] : $GLOBALS['BE_USER']->user['realName'];
		} else if ($k == "email") {
			$v = ($this->wizard->wizArray["emconf"][1]["author_email"] != "") ? $this->wizard->wizArray["emconf"][1]["author_email"] : $GLOBALS['BE_USER']->user['email'];
		}
		return $v;
	}
}


// Include extension?
if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/kickstarter/class.tx_kickstarter_sectionbase.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/kickstarter/class.tx_kickstarter_sectionbase.php"]);
}


?>