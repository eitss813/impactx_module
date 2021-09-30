<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldsController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_AdminEmailsettingsController extends Fields_Controller_AdminAbstract
{
    protected $_fieldType = 'sitecrowdfunding_project';

    public function indexAction()
    {
        //        Make navigation
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_emailsettings');

        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';

        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Emailsettings();


        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        /*
        foreach ($values as $key => $value) {
            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key)) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
            }
            if (is_null($value)) {
                $value = "";
            }
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }*/

        $form->addNotice($this->view->translate('Your changes have been saved successfully.'));
    }
}