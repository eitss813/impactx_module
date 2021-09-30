<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2012-08-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagemember_Bootstrap extends Engine_Application_Bootstrap_Abstract {
  public function __construct($application) {
    parent::__construct($application);
    include_once APPLICATION_PATH . '/application/modules/Sitepagemember/controllers/license/license.php';
  }
}