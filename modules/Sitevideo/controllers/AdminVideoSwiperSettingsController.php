<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminVideoSwiperSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_videoswiper');
        $this->view->form = $form = new Sitevideo_Form_Admin_VideoSwipperSettings();

        if (!$this->getRequest()->getPost())
            return;
        // If not post or form not valid, return
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        
        if((isset($values['model']) && empty($values['model'])) ){
            $values['toValues'] = '';
        }
        if($value['sitevideo_videoswipper_destination']=='video'){
            $values['toValues'] = '';
        }
        
        foreach ($values as $key => $value) {
            if ($key == 'model') {
                continue;
            }
            if ($key == 'toValues') {
                $key = 'sitevideo_videoswipper_model';
            }
            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key)) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
            }
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
    }

}
