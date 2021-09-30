<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Widget_MemberOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->view->memberOfDay = $memberOfDay = Engine_Api::_()->getDbtable('membership', 'sitepage')->memberOfDay();
    
    if(!empty($memberOfDay)) {
      $this->view->result = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($memberOfDay->user_id, 'memberOfDay');
		}
		
    if (empty($memberOfDay)) {
      return $this->setNoRender();
    }
  }
}