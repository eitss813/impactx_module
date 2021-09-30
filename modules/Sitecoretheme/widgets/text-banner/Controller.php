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
class Sitecoretheme_Widget_TextBannerController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->text = $coreSettings->getSetting('sitecoretheme.landing.tbanner.text', 'Get our plugins to let your community move one step ahead.');
    $this->view->ctatext = $coreSettings->getSetting('sitecoretheme.landing.tbanner.ctatext', 'Purchase Now');
    $this->view->url = $coreSettings->getSetting('sitecoretheme.landing.tbanner.url', '#');
    $this->view->newtab = $coreSettings->getSetting('sitecoretheme.landing.tbanner.newtab', 1);
  }

}