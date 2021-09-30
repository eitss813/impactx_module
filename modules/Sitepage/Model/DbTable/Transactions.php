<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Transactions.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Transactions extends Engine_Db_Table {

  protected $_rowClass = 'Sitepage_Model_Transaction';

  public function getBenefitStatus(User_Model_User $user = null) {
    // Get benefit setting
    $benefitSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.payment.benefit');
    if (!in_array($benefitSetting, array('all', 'some', 'none'))) {
      $benefitSetting = 'all';
    }

    switch ($benefitSetting) {
      default:
      case 'all':
        return true;
        break;

      case 'some':
        if (!$user) {
          return false;
        }
        return (bool) $this->select()
                ->from($this, new Zend_Db_Expr('TRUE'))
                ->where('user_id = ?', $user->getIdentity())
                ->where('type = ?', 'payment')
                ->where('status = ?', 'okay')
                ->limit(1);
        break;

      case 'none':
        return false;
        break;
    }

    return false;
  }

}

?>