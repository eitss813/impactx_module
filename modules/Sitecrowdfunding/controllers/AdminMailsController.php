<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMailsReminderController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Sitecrowdfunding_AdminMailsController extends Core_Controller_Action_Admin {

    //ACTION FOR MAIL SETTINGS
    public function indexAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_reminder_mails');
        $site_title = $_SERVER['HTTP_HOST'];
        $this->view->form = $form = new Sitecrowdfunding_Form_Admin_Email();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();

        if ($values['sitecrowdfunding_reminder_demo'] != 1)
            $values['sitecrowdfunding_admin_mail'] = '';

        include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/controllers/license/license2.php';
        if (empty($tempMailsend)) {
            return;
        }
        foreach ($values as $key => $value) {
            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key)) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
            }
            if (is_null($value)) {
                $value = "";
            }
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice($this->view->translate('Your changes have been saved successfully.'));

        if ($values['sitecrowdfunding_reminder_demo'] == 1 && !empty($values['sitecrowdfunding_admin_mail'])) {
            $mailId = $values['sitecrowdfunding_admin_mail'];
            $user = Engine_Api::_()->getItemTable('user')->fetchRow(array(
                'email = ?' => $mailId
            ));
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, "SITECROWDFUNDING_REMINDER_TEST", array(
                'member_name' => $user->getTitle()
            ));
            $form->addNotice('Test email has been sent to the email id provided by you.');
        }
    }

}
