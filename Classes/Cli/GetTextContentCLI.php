<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Daniel Dorndorf <dorndorf@dreipunktnull.com>, Dreipunktnull
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

		if (TRUE === empty($task) || $task !== 'getContentCSV'){
			$this->cli_help();
			exit;
		}

		if ($task === 'getContentCSV') {
			echo 'Enter output path:';
			$outPath = $this->cli_keyboardInput();
			$this->getContentCSV($outPath);
		}
	}

	/**
	 * @param string $outPath
	 */
	protected function getContentCSV($outPath) {
		if (FALSE === $outPath || FALSE === realpath($outPath)) {
			$this->cli_help();
			exit;
		} elseif (TRUE === file_exists($outPath)) {
			echo 'File already exists';
		} else {
			$contents = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'p.uid,p.title,c.header,c.bodytext',
				'pages AS p LEFT JOIN tt_content AS c ON c.pid = p.uid',
				"c.CType = 'text'",
				'',
				'c.sorting'
			);

			if (NULL === $contents) {
				echo 'No pages with ctype \'text\' found.';
			} else {
				$csvArray = array();
				foreach ($contents as $content) {
					$csvArray[] = array(
						$content['uid'],
						$content['title'],
						$content['header'],
						$this->formatText($content['bodytext']),
					);
				}

				$file = fopen($outPath, 'w');

				if (FALSE === $file) {
					echo 'Failed to create file';
				} else {
					foreach ($csvArray as $csv) {
						fputcsv($file, $csv);
					}
					fclose($file);
				}
			}
		}
	}

	/**
	 * Removes HTML Tags and unnecessary line breaks
	 * @param string $text
	 * @return string
	 */
	protected function formatText($text) {
		$text = html_entity_decode($text);
		$text = strip_tags($text);
		$text = str_replace(
			array("\r\n", "\n"),
			array(' ', ' '),
			$text
		);

		return $text;
	}
}

$cliObject = GeneralUtility::makeInstance('getTextContentCLI');
$cliObject->cli_main($_SERVER['argv']);