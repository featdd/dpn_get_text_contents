<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('TYPO3_cliMode')) {
	die('You cannot run this script directly!');
}

class getTextContentCLI extends t3lib_cli {

	/**
	 * Constructor
	 */
	public function getTextContentCLI() {
		$this->cli_help['name'] = 'Dreipunktnull GetTextContentCLI';
		$this->cli_help['description'] = 'Fetches all textcontents from all pages into a CSV';
		$this->cli_help['synopsis'] = '###OPTIONS###';
		$this->cli_help['examples'] = './cli_dispatch.phpsh dpn_get_text_contents getContentCSV';
		$this->cli_help['license'] = 'WTFPL';
		$this->cli_help['author'] = 'Daniel Dorndorf <dorndorf@dreipunktnull.com>, (c) 2014';

		$this->cli_options = array(
			array('getContentCSV', 'Gets all Textcontents and serves it to you as a CSV')
		);
	}

	/**
	 * @param array $argv	Command line arguments
	 * @return string
	 */
	public function cli_main($argv) {
		$task = (string)$argv[1];

		if (!$task && $task !== 'getContentCSV'){
			$this->cli_help();
			exit;
		}

		if ($task == 'getContentCSV') {
			echo 'Enter output path[/home/user/content.csv]:';
			$outPath = $this->cli_keyboardInput();
			$this->getContentCSV($outPath);
		}
	}

	/**
	 * @param string $outPath
	 */
	protected function getContentCSV($outPath) {
		if (!$outPath) {
			$this->cli_help();
			exit;
		} else {
			$contents = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'p.uid,p.title,c.header,c.bodytext',
				'pages AS p LEFT JOIN tt_content AS c ON c.pid = p.uid',
				"c.CType = 'text'",
				'',
				'c.sorting'
			);

			if ($contents === NULL) {
				echo 'No pages with ctype \'text\' found.';
			} else {
				$csvArray = array();
				foreach ($contents as $content) {
					$csvArray[] = array(
						$content['uid'],
						$content['title'],
						$content['header'],
						html_entity_decode(strip_tags(str_replace(array("\r\n", "\n", chr(13)), array('', '', ''), $content['bodytext'])))
					);
				}

				$file = fopen($outPath, 'w');
				foreach ($csvArray as $csv) {
					fputcsv($file, $csv);
				}
				fclose($file);
			}
		}
	}
}

$cliObject = GeneralUtility::makeInstance('getTextContentCLI');
$cliObject->cli_main($_SERVER['argv']);