<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Widget_AppPromotionController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->bgImage = $coreSettings->getSetting('sitecoretheme.landing.appbanner.bgimage', '');
    $this->view->title = $coreSettings->getSetting('sitecoretheme.landing.appbanner.title', 'Download our latest app');
    $this->view->description = $coreSettings->getSetting('sitecoretheme.landing.appbanner.description', 'Enabling Business with a new perspective using Mobile apps. Get your community in the hands of your customers with our beautiful mobile solutions.');
    $this->view->appstoreUrl = $coreSettings->getSetting('sitecoretheme.landing.appbanner.appstoreUrl', '#');
    $this->view->playstoreUrl = $coreSettings->getSetting('sitecoretheme.landing.appbanner.playstoreUrl', '#');
    $this->view->actionButtonUrl = $coreSettings->getSetting('sitecoretheme.landing.appbanner.actionUrl', '#');
    $this->view->actionButtonText = $coreSettings->getSetting('sitecoretheme.landing.appbanner.actionText', 'Action Button');
    $this->view->showButtons = $coreSettings->getSetting('sitecoretheme.landing.appbanner.buttons', 0);

    $this->view->target = '';
    $newTab = $coreSettings->getSetting('sitecoretheme.landing.appbanner.newtab', 1);

    if (!empty($newTab)) {
      $this->view->target = '_blank';
    }
  }

}