<?php

class Tx_Faster_Hooks_FrontendController {
	public function fly($content, $params) {
		$TSFE = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, t3lib_div::_GP('id'), t3lib_div::_GP('type'), t3lib_div::_GP('no_cache'), t3lib_div::_GP('cHash'), t3lib_div::_GP('jumpurl'), t3lib_div::_GP('MP'), t3lib_div::_GP('RDCT'));
		$TSFE->connectToDB();

//		return;
		/** @var $db t3lib_DB */
		$db = $GLOBALS['TYPO3_DB'];
		$row = $db->exec_SELECTgetSingleRow('*', 'tx_faster_cache', 'url=' . $db->fullQuoteStr(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'), 'tx_faster_cache'));

		if (is_array($row)) {
			$pageCache = $GLOBALS['typo3CacheManager']->getCache('cache_pages');
			$cacheContent = $pageCache->get($row['identifier']);

			if ($cacheContent !== FALSE) {

				$this->render($row, $cacheContent);
			}
		}

	}

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

	protected function modifyPageContent($content, $tstamp) {
		$endTag = '</body>';
		$replace = '<!-- cached: ' . date('r', $tstamp) . ' -->' . LF . $endTag;
		$content = str_replace($endTag, $replace, $content);

		return $content;
	}
}

?>