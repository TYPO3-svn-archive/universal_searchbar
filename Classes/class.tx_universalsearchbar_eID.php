<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Sven Jürgens <t3@blue-side.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib . 'class.tslib_content.php');
require_once(PATH_t3lib . 'class.t3lib_tstemplate.php');
require_once(PATH_tslib . 'class.tslib_eidtools.php');
 
define(UNIVERSALSEARCHBAR_TYPE_NORMAL, 0);
define(UNIVERSALSEARCHBAR_TYPE_TYPOSCRIPT, 1);
define(UNIVERSALSEARCHBAR_TYPE_FUNCTIONS, 2);

class tx_universalsearchbar_eID{
	public $q;
	public $table;

	public function init() {

		tslib_eidtools::connectDB();
		tslib_eidtools::initTCA();


		$this->q = htmlspecialchars(t3lib_div::_GET('q'));
		$this->q = t3lib_div::trimExplode(' ',$this->q, 1);

		if(strtolower($this->q[0]) == 'help') {
			$this->showHelp();
			exit;
		}

		$this->table = 'tx_universalsearchbar_items';

	}


	public function main() {

		// get record
		$row = $this->getRecord($this->q[0]);

		if(empty($row)){
			$this->showHelp();
		}

		$this->getRedirect($row['0']);

	}

	public function getRedirect($row) {


		unset($this->q['0']);
		$urlPart = '';

		if($row['itemtype'] == UNIVERSALSEARCHBAR_TYPE_TYPOSCRIPT){
			$urlPart = $this->getTypoScriptCode($row['typoscript'], $this->q);
		}elseif($row['itemtype'] == UNIVERSALSEARCHBAR_TYPE_NORMAL){
			$urlPart = implode(
						$row['glue'],
						$this->q
						);
		}
		if(strpos($row['searchurl'], '###HOTKEY###') !== FALSE ){
			$url = str_replace(
					'###HOTKEY###',
					$urlPart,
					$row['searchurl']
					);
			}else{
				$url = $row['searchurl'] . $urlPart;

			}

		if($row['itemtype'] == UNIVERSALSEARCHBAR_TYPE_FUNCTIONS){
			$file = '';
			$file = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['universal_searchbar']['additionalFunctions'][$row['additionalfunctions']]['filePath'];

			if(is_file($file)){
				require_once $file;
				$userfile = t3lib_div::makeInstance($row['additionalfunctions']);
				$url = $userfile->execute($row, $this->q);
			}

		}
		 t3lib_utility_Http::redirect($url);
	}

	public function getTypoScriptCode($typoScriptCode, $q) {
		$typoScriptCode = str_replace('###INPUT###', implode($row['glue'], $this->q), $typoScriptCode);

		require_once(PATH_t3lib.'class.t3lib_tsparser.php');
		$TSparserObject = '';
		$TSparserObject = t3lib_div::makeInstance('t3lib_tsparser');
		$TSparserObject->parse($typoScriptCode);

		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start(array(), '');

		require_once(PATH_tslib . 'class.tslib_fe.php');
		$tsfeClassName = t3lib_div::makeInstanceClassName('tslib_fe');
		$GLOBALS['TSFE'] = new $tsfeClassName($GLOBALS['TYPO3_CONF_VARS'], 0, '');
		return $cObj->cObjGet($TSparserObject->setup);
	}

	public function getRecord($hotkey){
		$arrRow = array();
		

		$arrRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'title, hotkey, glue, searchurl, typoscript, itemtype, additionalfunctions',
				$this->table,
				'hotkey=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(htmlspecialchars($hotkey), $this->table) .
				t3lib_pageSelect::enableFields($this->table),
				'',
				'',
				'1'
		);

	return 	$arrRow;
	}

	public function showHelp(){
		$extConf = '';
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['universal_searchbar']);

		$arrItems = array();

		if($extConf['showHelp'] == 1){
			$arrItems = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'*',
						$this->table,
						'1=1' . t3lib_pageSelect::enableFields($this->table),
						'',
						''
					);
			if(!empty ($arrItems)){
				echo $this->buildingList($arrItems, $extConf);
			} else {
				echo 'No Entries';
			}

			exit;
		}else{
			echo 'access forbidden';
			exit;
		}
	}

	public function buildingList($arrItems, $extConf) {

		$template = '';
		$templateCode = '';

		$templateCode  = $this->getHtmlTemplate($extConf['helpTemplateFile']);

		if(empty($templateCode)) {
			return 'Template not found, please check the Extension settings in ExtensionManager';
		}

		$templateSubpart = t3lib_parsehtml::getSubpart($templateCode,'###ROW###');
		
		$alt = 0;
		$entries = array();
		
		foreach ($arrItems as $key => $item) {
			$markerArray = array(
				'###CLASS###'		=> ($alt % 2) ? 'even' : 'odd',
				'###TITLE###'		=> htmlspecialchars($item['title']),
				'###HOTKEY###'		=>	htmlspecialchars($item['hotkey']),
			);
		$entries[] = t3lib_parsehtml::substituteMarkerArray($templateSubpart, $markerArray);
		$alt++;
		}

		$template = t3lib_parsehtml::getSubpart($templateCode,'###HELPLIST###');
		return t3lib_parsehtml::substituteSubpart($template, '###ROW###', implode('', $entries));

	}
	function getHtmlTemplate($filename)	{
		
		if (substr($filename,0,4) != 'EXT:') {
			$filename = t3lib_div::resolveBackPath($this->backPath . $filename);
		} else {
			$filename = t3lib_div::getFileAbsFileName($filename, true, true);
		}
		return t3lib_div::getURL($filename);
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/universal_searchbar/Classes/class.tx_universalsearchbar_eID.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/universal_searchbar/Classes/class.tx_universalsearchbar_eID.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_universalsearchbar_eID');
$SOBE->init();
$SOBE->main();

?>
