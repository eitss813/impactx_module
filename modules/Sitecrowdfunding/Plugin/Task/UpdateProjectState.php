<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UpdateProjectState.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Plugin_Task_UpdateProjectState extends Core_Plugin_Task_Abstract {

  public function execute() {
        $projectTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectTable->updateProjectState();
  }

}

