<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Forgot.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteotpverifier_Model_DbTable_Users extends Engine_Db_Table
{
	protected $_rowClass = 'Siteotpverifier_Model_User';

	public function getUser($user) {
		if (empty($user) || empty($user->getIdentity())) return false;
		$otpUser = Engine_Api::_()->getItem('siteotpverifier_user', $user->getIdentity());
		if (!empty($otpUser)) return $otpUser;
		$otpUser = $this->createRow();
		$otpUser->user_id = $user->getIdentity();
		$otpUser->save();
		return $otpUser;
	}

}