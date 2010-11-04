<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_searchbar_items=1
');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_searchbar_pi1.php', '_pi1', 'list_type', 1);

// eID
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['searchbar'] = 'EXT:searchbar/Classes/class.tx_searchbar_eID.php';


// Example
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['searchbar']['additionalFunctions']['tx_searchbar_ip'] = array(
	'title' => 'Show Current IP',
	'filePath' => t3lib_extMgm::extPath($_EXTKEY) . 'Classes/class.tx_searchbar_ip.php'
	);

?>