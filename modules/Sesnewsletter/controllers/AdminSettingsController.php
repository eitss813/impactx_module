<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminSettingsController.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {

        $db = Engine_Db_Table::getDefaultAdapter();

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_settings');

        $this->view->form = $form = new Sesnewsletter_Form_Admin_Settings_Global();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            if(@$values['sesnewsletter_enabletestmode'] == '1' && empty($values['sesnewsletter_testemail'])) {
                $error = Zend_Registry::get('Zend_Translate')->_("Test Email: Please complete this field - it is requried.");
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
            unset($values['defaulttext']);
            include_once APPLICATION_PATH . "/application/modules/Sesnewsletter/controllers/License.php";
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesnewsletter.pluginactivated')) {
                foreach ($values as $key => $value) {
                if($value != '')
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }
                $form->addNotice('Your changes have been saved.');
                if($error)
                $this->_helper->redirector->gotoRoute(array());
            }
        }
    }


    public function headerfootersettingsAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_hdfotrsettings');

        $this->view->form = $form = new Sesnewsletter_Form_Admin_HeaderFooterSettings();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            foreach ($values as $key => $value) {
                if (Engine_Api::_()->getApi('settings', 'core')->hasSetting($key, $value))
                    Engine_Api::_()->getApi('settings', 'core')->removeSetting($key);
                if (!$value && strlen($value) == 0)
                    continue;
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }
            $form->addNotice('Your changes have been saved.');
        }
    }

    public function widgetCheck($params = array()) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        return $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('type = ?', 'widget')
                        ->where('page_id = ?', $params['page_id'])
                        ->where('name = ?', $params['widget_name'])
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

    public function supportAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesnewsletter_admin_main', array(), 'sesnewsletter_admin_main_support');
    }
}
