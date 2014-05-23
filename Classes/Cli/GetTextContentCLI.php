<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('TYPO3_cliMode')) {
	die('You cannot run this script directly!');
}

class getTextContentCLI extends t3lib_cli {

	/**
	 * Constructor
	 */
	function getTextContentCLI() {
		$this->cli_help['name'] = 'Dreipunktnull GetTextContentCLI';
		$this->cli_help['description'] = 'Fetches all textcontents from all pages into a CSV';
		$this->cli_help['examples'] = 'cli_dispatch.phpsh dpn_get_text_contents';
		$this->cli_help['license'] = 'WTFPL';
		$this->cli_help['author'] = 'Daniel Dorndorf <dorndorf@dreipunktnull.com, (c) 2014';
	}

	/**
	 * @param array $argv	Command line arguments
	 * @return string
	 */
	function cli_main($argv) {
		$test = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'p.uid,p.title,c.header,c.bodytext',
			'pages AS p LEFT JOIN tt_content AS c ON c.pid = p.uid',
			"c.CType = 'text'",
			'',
			'c.sorting'
		);
		var_dump($test);
	}
}

$cliObject = GeneralUtility::makeInstance('getTextContentCLI');
$cliObject->cli_main($_SERVER['argv']);