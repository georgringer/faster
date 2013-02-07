<?php

class Tx_Faster_Hooks_InsertPageInCache {

	const CACHE_TABLE = 'tx_faster_cache';

	/**
	 * Save the hash of caching framework and the current url in a
	 * separate table to be able to match those later again
	 *
	 * @param tslib_fe $parentObject
	 * @param integer $timeout
	 * @throws Exception
	 */
	public function insertPageIncache(tslib_fe $parentObject, $timeout) {
		try {
			if (!$parentObject->isStaticCacheble()) {
				throw new Exception('site is not fully cached');
			}

			$loginAllowed = $parentObject->checkIfLoginAllowedInBranch();
			if ($loginAllowed) {
				throw new Exception('login is allowed on this page');
			}

			/** @var $db t3lib_DB */
			$db = $GLOBALS['TYPO3_DB'];

			// Delete any entry
			$where = 'identifier=' . $db->fullQuoteStr($parentObject->newHash, self::CACHE_TABLE)
				.' OR url=' . $db->fullQuoteStr(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'), self::CACHE_TABLE);

			$db->exec_DELETEquery(self::CACHE_TABLE, $where);

			$data = array(
				'identifier' => $parentObject->newHash,
				'url' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL')
			);
			$db->exec_INSERTquery(self::CACHE_TABLE, $data);

		} catch (Exception $e) {
//			die($e->getMessage());
		}
	}

}

?>