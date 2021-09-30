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
class Siteotpverifier_Model_DbTable_Forgot extends Engine_Db_Table
{
  public function gc()
  {
    $resettime = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteotpverifier', 'resettime') ?: (3600 * 24);
    // Delete everything older than <del>6</del> 24 hours
    $this->delete(array(
      'creation_date < ?' => date('Y-m-d H:i:s', (time() - $resettime)),
    ));
  }

  public function createCode($type, $user)
  {
    $forgotSelect = $this->select()
        ->where('user_id = ?', $user->getIdentity())->where('type = ?', $type);
    $forgotRow = $this->fetchRow($forgotSelect);
    $resentcount = 0;
    $creationdate = date('Y-m-d H:i:s');
    $response = array(
      'status' => false,
      'error' => '',
      'code' => null,
    );
    $resendCountAllowed = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteotpverifier', 'max_resend');
    if( $forgotRow && !empty($resendCountAllowed) ) {
      $resentcount = $forgotRow->resent;
      $creationdate = $forgotRow->creation_date;
      $lastUpdated = $forgotRow->modified_date;

      if( $resentcount >= $resendCountAllowed ) {
        $time = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteotpverifier', 'time') ?: (3600 * 24);
        $waitingtime = time() - strtotime($lastUpdated);
        if( $time > $waitingtime ) {
          $blocktime = $time - $waitingtime;
          $timestring = Engine_Api::_()->getApi('core', 'siteotpverifier')->convertTime($blocktime);
          $translate = Zend_Registry::get('Zend_Translate');
          $response['error'] = sprintf($translate->translate('You have reached limit of attempts via OTP. Please wait for %s and try again.'), $timestring);
          return $response;
        }
      }

      $resettime = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteotpverifier', 'resettime') ?: (3600 * 24);
      $waitingtimefromcreation = time() - strtotime($creationdate);
      if( $resettime < $waitingtimefromcreation ) {
        $resentcount = 0;
        $creationdate = date('Y-m-d H:i:s');
      }
    }

    $code = Engine_Api::_()->getApi('core', 'siteotpverifier')->generateCode();
    $this->delete(array(
      'user_id = ?' => $user->getIdentity(),
      'type = ?' => $type
    ));
    $this->insert(array(
      'user_id' => $user->getIdentity(),
      'code' => $code,
      'creation_date' => $creationdate,
      'modified_date' => date('Y-m-d H:i:s'),
      'resent' => $resentcount + 1,
      'type' => $type,
    ));
    $response['status'] = true;
    $response['code'] = $code;
    return $response;
  }

}
