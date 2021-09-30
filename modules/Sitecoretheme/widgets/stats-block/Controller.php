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
class Sitecoretheme_Widget_StatsBlockController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->bgImage = $coreSettings->getSetting('sitecoretheme.landing.stats.bgimage', '');
        $this->view->stat1 = $coreSettings->getSetting('sitecoretheme.landing.stats.title1', 'Happy Clients');
        $this->view->stat2 = $coreSettings->getSetting('sitecoretheme.landing.stats.title2', 'Launched Products');
        $this->view->stat3 = $coreSettings->getSetting('sitecoretheme.landing.stats.title3', 'Reviews & Ratings');
        $this->view->stat4 = $coreSettings->getSetting('sitecoretheme.landing.stats.title4', 'Successful Projects');
        $this->view->icon1Url = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.stats.icon1', ''), 1);
        $this->view->icon2Url = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.stats.icon2', ''), 2);
        $this->view->icon3Url = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.stats.icon3', ''), 3);
        $this->view->icon4Url = $this->getIconUrl($coreSettings->getSetting('sitecoretheme.landing.stats.icon4', ''), 4);
        $this->view->count1 = $coreSettings->getSetting('sitecoretheme.landing.stats.count1', '7000');
        $this->view->count2 = $coreSettings->getSetting('sitecoretheme.landing.stats.count2', '100');
        $this->view->count3 = $coreSettings->getSetting('sitecoretheme.landing.stats.count3', '975');
        $this->view->count4 = $coreSettings->getSetting('sitecoretheme.landing.stats.count4', '12597');

    }

    public function getIconUrl($file_id, $iconNumber) {
        $iconUrl = $defaultIcon = $this->view->layout()->staticBaseUrl . 'application/modules/Sitecoretheme/externals/images/stats/stats_'.$iconNumber.'.png';
        if($file_id) {
            $icon = Engine_Api::_()->storage()->get($file_id);
            $iconUrl = ( $icon ) ? $icon->getPhotoUrl() : $defaultIcon;
        } 
        return $iconUrl;
    }

}