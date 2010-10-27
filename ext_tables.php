<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE')    {
    include_once(t3lib_extMgm::extPath('universal_searchbar').'Classes/class.tx_universalsearchbar_functions_field.php');
}


t3lib_extMgm::allowTableOnStandardPages('tx_universalsearchbar_items');

$TCA['tx_universalsearchbar_items'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:universal_searchbar/locallang_db.xml:tx_universalsearchbar_items',		
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
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_universalsearchbar_items.gif',
	),
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi1', 'FILE:EXT:universal_searchbar/flexform_ds.xml');

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:universal_searchbar/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_universalsearchbar_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_universalsearchbar_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/universal_search_integration/', 'Universal search integration');
?>