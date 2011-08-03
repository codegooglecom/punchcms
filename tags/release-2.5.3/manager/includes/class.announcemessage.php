<?php

/* Announce Message Class v0.1.0
 * Handles announcments.
 *
 * CHANGELOG
 * version 0.1.0, 15 Mar 2006
 *   NEW: Created class.
 */

class AnnounceMessage extends DBA_AnnounceMessage {

	public static function getMessages($blnCheck = TRUE) {
		global $objLiveUser;
		self::$__object = "AnnounceMessage";

		$strReturn = "";
		$intId = 0;

		$strSql = "SELECT *
				FROM pcms_announce_message
				WHERE id NOT IN (SELECT messageId FROM pcms_announce_user WHERE permUserId = '{$objLiveUser->getProperty('perm_user_id')}')
				ORDER BY sort";
				
		$objMessages = self::select($strSql);

		if ($blnCheck) {
			foreach ($objMessages as $objMessage) {
				$objMessage->check();
			}
		}

		return $objMessages;
	}

	public function check() {
		global $objLiveUser;

		$objAnnounceUser = new AnnounceUser();
		$objAnnounceUser->setMessageId($this->getId());
		$objAnnounceUser->setPermUserId($objLiveUser->getProperty('perm_user_id'));
		$objAnnounceUser->save();
	}

}

?>