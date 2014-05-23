<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['cliKeys']['dpn_get_text_contents'] = array('EXT:dpn_get_text_contents/Classes/Cli/GetTextContentCLI.php', '_CLI_lowlevel');