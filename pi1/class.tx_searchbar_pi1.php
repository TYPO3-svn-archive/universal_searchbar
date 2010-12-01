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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Add a Browser Searchbar' for the 'searchbar' extension.
 *
 * @author	Sven Jürgens <t3@blue-side.de>
 * @package	TYPO3
 * @subpackage	tx_searchbar
 */
class tx_searchbar_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_searchbar_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_searchbar_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'searchbar';	// The extension key.
	var $pi_checkCHash = true;

	public $linkForOpenSearch;
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		$this->init();
		if(t3lib_div::_GP('type') == 222){
			$content = $this->getSearch();
			header('Content-type:application/xml;');
			echo $content;
			exit;
		}else{
			$this->addHeaderPart();
			$content = $this->buildSearchProviderLink();
			$content = $this->pi_wrapInBaseClass($content);
		}
		return $content;

	}

	public function init(){

		$this->linkForOpenSearch = $this->getOpenSearchURL();

		$this->cObj->readFlexformIntoConf($this->cObj->data['pi_flexform'], $this->conf);
		$this->templateCode  = 	$this->cObj->fileResource($this->conf['templateFile']);

		if(empty($this->templateCode)) {
			$this->templateCode = $this->cObj->fileResource('EXT:' . $this->extKey . '/Resources/templates/template.html');
		}
	}


	public function addHeaderPart(){
		$link = array();
		$link[] = '<link rel="search" type="application/opensearchdescription+xml"' ;
		$link[] = 'href="' . t3lib_div::getIndpEnv(TYPO3_SITE_URL) . $this->linkForOpenSearch . '"';
		$link[] = 'title="'. $this->conf['pluginDescription'] . '" />';

		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = implode (' ',$link);
	}

	public function buildSearchProviderLink(){

		$templateSubpart = $this->cObj->getSubpart($this->templateCode, '###ADDSEARCHPROVIDER###');

		$markerArray = array(
			'###SITEURL###' => $this->cObj->typolink(array('parameter' => $GLOBALS['TSFE']->id)),
			'###SEARCHPROVIDERLINK###' => t3lib_div::getIndpEnv(TYPO3_SITE_URL) . $this->linkForOpenSearch,
			'###SEARCHPROVIDERLINKTEXT###' => $this->pi_getLL('searchproviderlinktext','add the new seachbar engine')
		);
		return $this->cObj->substituteMarkerArray($templateSubpart, $markerArray);
	}

	public function getOpenSearchURL() {
		$conf = array(
			'parameter' => $GLOBALS['TSFE']->id,
			'additionalParams' => '&type=222',
		);
		return $this->cObj->typolink_URL($conf);
	}

	public function getSearch(){

		$templateSubpart = $this->cObj->getSubpart($this->templateCode,'###CONTENT###');
		$markerArray = array(
			'###SEARCHNAME###' 			=> 	$this->conf['pluginName'],
			'###SEARCHDESCRIPTION###' 	=>	$this->conf['pluginDescription'],
			'###SEARCHURL###' 			=>	$this->getSearch_url(),
			'###SEARCHICON_PNG###'		=>	$this->getSearch_icon($this->conf['pluginIcon'],'png'),
			'###SEARCHICON_JPG###'		=>	$this->getSearch_icon($this->conf['pluginIcon'],'jpg'),
		);

		$entries[] = $this->cObj->substituteMarkerArray($templateSubpart, $markerArray);
		return implode('', $entries);
	}

	function getSearch_url(){

		return	t3lib_div::getIndpEnv(TYPO3_SITE_URL)
				. 'index.php?eID=searchbar&amp;q={searchTerms}';
	}

	public function getSearch_icon($image, $ext){

		if(empty($image)){
			$pathWithImage = t3lib_extmgm::siteRelPath($this->extKey) . 'Resources/images/TYPO3_logo.png';
		}else{
			$pathWithImage = 'uploads/tx_searchbar/' . $image;
		}
			$withAndHeight ='';
			if($ext == 'png'){
				$withAndHeight = '16';
			}else{
				$withAndHeight = '64';
			}

			$imageConf = array(
				'file' => $pathWithImage,
				'file.' => array(
					'width' => $withAndHeight,
					'height' => $withAndHeight,
					'ext' => $ext,
				)
			);
		return t3lib_div::getIndpEnv(TYPO3_SITE_URL) . $this->cObj->IMG_RESOURCE($imageConf);

	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/searchbar/pi1/class.tx_searchbar_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/searchbar/pi1/class.tx_searchbar_pi1.php']);
}

?>