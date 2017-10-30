<?php
if (!defined("TYPO3_MODE")){
	die("Access denied.");
}

// hook after Caching (USER_INT)
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = \PeterBenke\PbFileinfo\User\FrontendHook::class . '->modifyUncachedContent';

// hook before Caching
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = \PeterBenke\PbFileinfo\User\FrontendHook::class . '->modifyCachedContent';
