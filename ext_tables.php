<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE')    {
    include_once(t3lib_extMgm::extPath('searchbar').'Classes/class.tx_searchbar_functions_field.php');
}


t3lib_extMgm::allowTableOnStandardPages('tx_searchbar_items');

$TCA['tx_searchbar_items'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:searchbar/locallang_db.xml:tx_searchbar_items',		
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'itemtype',	
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_searchbar_items.gif',
	),
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi1', 'FILE:EXT:searchbar/flexform_ds.xml');

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:searchbar/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_searchbar_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_searchbar_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/search_integration/', 'Universal search integration');
?>