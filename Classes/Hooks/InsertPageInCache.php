<?php

class Tx_Faster_Hooks_InsertPageInCache {

	const CACHE_TABLE = 'tx_faster_cache';

	public function insertPageIncache(tslib_fe $parentObject, $timeout) {
		try {
			if (!$parentObject->isStaticCacheble()) {
				throw new Exception('site is not fully cacheble');
			}

			$loginAllowed = $parentObject->checkIfLoginAllowedInBranch();
			if ($loginAllowed) {
				throw new Exception('login is allowed on this page, what a pitty');
			}

//			die('xxx');

			/** @var $db t3lib_DB */
			$db = $GLOBALS['TYPO3_DB'];

			// Delete any entry
			$where = 'identifier=' . $db->fullQuoteStr($parentObject->newHash, self::CACHE_TABLE);
			$where = 'url=' . $db->fullQuoteStr(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'), self::CACHE_TABLE);

			$db->exec_DELETEquery(self::CACHE_TABLE, $where);

			// Add it
			$data = array(
				'identifier' => $parentObject->newHash,
				'url' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL')
			);
			$db->exec_INSERTquery(self::CACHE_TABLE, $data);
		} catch (Exception $e) {
			// do what?

//			die('not_Cacheable');
		}
	}

}

?>