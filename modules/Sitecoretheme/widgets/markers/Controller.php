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
class Sitecoretheme_Widget_MarkersController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $data = array();
    $this->view->bgImage = $coreSettings->getSetting('sitecoretheme.landing.markers.bgimage', '');
    $data[1]['subTitle'] = $coreSettings->getSetting('sitecoretheme.landing.markers.title1', 'Clients');
    $data[2]['subTitle'] = $coreSettings->getSetting('sitecoretheme.landing.markers.title2', 'Products');
    $data[3]['subTitle'] = $coreSettings->getSetting('sitecoretheme.landing.markers.title3', 'Reviews');
    $data[4]['subTitle'] = $coreSettings->getSetting('sitecoretheme.landing.markers.title4', 'Projects Done');
    $data[1]['iconUrl'] = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.markers.icon1', ''), 1);
    $data[2]['iconUrl'] = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.markers.icon2', ''), 2);
    $data[3]['iconUrl'] = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.markers.icon3', ''), 3);
    $data[4]['iconUrl'] = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.markers.icon4', ''), 4);
    $data[1]['title'] = $coreSettings->getSetting('sitecoretheme.landing.markers.count1', '7000+');
    $data[2]['title'] = $coreSettings->getSetting('sitecoretheme.landing.markers.count2', '100+');
    $data[3]['title'] = $coreSettings->getSetting('sitecoretheme.landing.markers.count3', '975+');
    $data[4]['title'] = $coreSettings->getSetting('sitecoretheme.landing.markers.count4', '12597+');
    $this->view->data = $data;
  }

  public function getIconUrl($file_id, $iconNumber) {
    $iconUrl = $defaultIcon = $this->view->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/markers/markers_' . $iconNumber . '.png';
    if ($file_id) {
      $icon = Engine_Api::_()->storage()->get($file_id);
      $iconUrl = ( $icon ) ? $icon->getPhotoUrl() : $defaultIcon;
    }
    return $iconUrl;
  }

}