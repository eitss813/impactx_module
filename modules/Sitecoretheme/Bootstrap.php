<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
  }

  protected function _initFrontController()
  {
    $front = Zend_Controller_Front::getInstance();
  	$front->registerPlugin(new Sitecoretheme_Plugin_Core);
  }

}