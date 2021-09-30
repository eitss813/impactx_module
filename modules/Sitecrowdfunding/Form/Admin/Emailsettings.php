<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BackerFilter.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
class Sitecrowdfunding_Form_Admin_Emailsettings extends Engine_Form {

    public function init() {
        // create an object for view
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->loadDefaultDecorators();
        $description = sprintf(Zend_Registry::get('Zend_Translate')->_("This page contains settings for emails for projects."));

        $this->setTitle('Email Settings');
        $this->setDescription($description);
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->getDecorator('Description')->setOption('escape', false);



        $translate    = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();
        //$this->template->getDecorator("Description")->setOption("placement", "append");

        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $select = Engine_Api::_()->getDbtable('MailTemplates', 'core')
            ->select()
            ->where('module IN(?)', $enabledModuleNames)
            ->where('module = ?', 'sitecrowdfunding');

        foreach( Engine_Api::_()->getDbtable('MailTemplates', 'core')->fetchAll($select) as $mailTemplate ) {
            $title = $translate->_(strtoupper("_email_" . $mailTemplate->type . "_title"));

            $this->addElement('Radio', $mailTemplate->type, array(
                'label' => $title,
                'description' => '',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $settings->getSetting($mailTemplate->type, 0)
            ));
        }

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}