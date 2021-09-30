<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: RecentlyViewed.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Plugin_Task_RecentlyViewed extends Core_Plugin_Task_Abstract {

  public function execute() {
    
    $viewTable = Engine_Api::_()->getDbtable('views', 'sitemember');
    $numberOfDays = Engine_Api::_()->getApi('settings','core')->getSetting('sitemember.recently.views.reset.days',7);
    $resetDate = date('Y-m-d', strtotime("-$numberOfDays days"));
    $viewTable->delete(array("date <=? "=>$resetDate));
    
  }

}