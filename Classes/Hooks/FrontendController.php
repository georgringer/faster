<?php

class Tx_Faster_Hooks_FrontendController {

	/**
	 * Earliest hook in TYPO3 frontend to get the content of the page from the caching framework
	 *
	 * @param $content
	 * @param $params
	 */
	public function fly($content, $params) {
		$TSFE = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, t3lib_div::_GP('id'), t3lib_div::_GP('type'), t3lib_div::_GP('no_cache'), t3lib_div::_GP('cHash'), t3lib_div::_GP('jumpurl'), t3lib_div::_GP('MP'), t3lib_div::_GP('RDCT'));
		$TSFE->connectToDB();

//		return;
		/** @var $db t3lib_DB */
		$db = $GLOBALS['TYPO3_DB'];
		$row = $db->exec_SELECTgetSingleRow('*', 'tx_faster_cache', 'url=' . $db->fullQuoteStr(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'), 'tx_faster_cache'));

		try {
			if (!is_array($row)) {
				throw new Exception('No entry in cache table found for this url');
			}

			$pageCache = $GLOBALS['typo3CacheManager']->getCache('cache_pages');
			$cacheContent = $pageCache->get($row['identifier']);

			if ($cacheContent === FALSE) {
				throw new Exception('No entry in cache framework found for this url');
			}

			$this->render($row, $cacheContent);
		} catch (Exception $e) {

		}

	}

	/**
	 * Output the given content including some headers
	 *
	 * @param array $row
	 * @param array $cacheContent
	 */
	protected function render(array $row, array $cacheContent) {
		$pageContent = $cacheContent['content'];
		$pageContent = $this->modifyPageContent($pageContent, $cacheContent['tstamp']);

		$headers = array(
			'Last-Modified: ' . gmdate('D, d M Y H:i:s T', $cacheContent['tstamp']),
			'Expires: ' . gmdate('D, d M Y H:i:s T', $cacheContent['expires']),
			'ETag: "' . md5($pageContent) . '"',
			'Cache-Control: max-age=' . ($cacheContent['expires'] - $GLOBALS['EXEC_TIME']),
			'Pragma: public'
		);

		foreach ($headers as $header) {
			header($header);
		}

		echo $pageContent;
		exit();
	}

	/**
	 * Modify the output and add a info when the cache has been created
	 *
	 * @param string $content
	 * @param integer $tstamp
	 * @return string
	 */
	protected function modifyPageContent($content, $tstamp) {
		$endTag = '</body>';
		$replace = '<!-- cached: ' . date('r', $tstamp) . ' -->' . LF . $endTag;
		$content = str_replace($endTag, $replace, $content);

		return $content;
	}
}

?>