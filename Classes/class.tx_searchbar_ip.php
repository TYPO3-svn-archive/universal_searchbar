<?php

class tx_searchbar_ip{

	function execute(&$row, &$searchEngineInput){

		echo 'My IP: ' . t3lib_div::getIndpEnv('REMOTE_ADDR');
		exit;
	}

}

?>
