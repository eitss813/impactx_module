<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Socialengineaddon
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Widget_ExtensionShowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $enableSubModules = array();
    $rss = Zend_Feed::import('http://www.socialengineaddons.com/extensions/feed');


    foreach( $rss as $item )
    {
      if($item->ptype() == 'contactpageowners') {
        $enableSubModules[] = 'sitepageadmincontact';
      } 
      elseif($item->ptype() == 'sitepageshorturl') {
        $enableSubModules[] = 'sitepageurl';
      }
      else {
        $enableSubModules[] = $item->ptype();
      }
    }
    $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $enableModules = array_intersect($enableSubModules, $enableAllModules);
    if (($key = array_search('sitepagefeature', $enableModules)) !== false) {
      unset($enableModules[$key]);
    }
    $this->view->channel = $enableModules;
  }

}
?>