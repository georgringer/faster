<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][$_EXTKEY] =
	'EXT:' . $_EXTKEY . '/Classes/Hooks/FrontendController.php:Tx_Faster_Hooks_FrontendController->fly';


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['insertPageIncache'][$_EXTKEY] =
	'EXT:' . $_EXTKEY . '/Classes/Hooks/InsertPageInCache.php:Tx_Faster_Hooks_InsertPageInCache';
?>