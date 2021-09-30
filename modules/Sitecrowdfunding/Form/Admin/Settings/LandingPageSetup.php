<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_LandingPageSetup extends Engine_Form {

    public function init() {
        $this->loadDefaultDecorators();
        $this->setTitle('Landing Page Settings')
                ->setName('landingpage_setup')
                ->setAttrib('class', 'landingpage_setup');
        $desc = 'Below, you can configure the landing page layout for your website.';
        $isSitehomepagevideo = false;
        if (Engine_Api::_()->hasModuleBootstrap('sitehomepagevideo')) {
            $isSitehomepagevideo = true;
        }
        if (Engine_Api::_()->hasModuleBootstrap('captivate')) {
            $isCaptivate = true;
        }
        $shouldInstall = false;
        if (empty($isSitehomepagevideo) || empty($isCaptivate)) {
            $shouldInstall = true;
            $desc .="<div class='tip'><span>You do not have installed or enabled  'Home Page Background Videos & Photos' plugin.</span></div>";
        }
        $this->setDescription($desc);
        $this->getDecorator('Description')->setOption('escape', false);
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $baseUrl = $view->baseUrl();
        $icon_url = $baseUrl . '/application/modules/Sitecrowdfunding/externals/images/Crowdfunding_Demo_Screenshot.png';
        $icon_view = '<a href="' . $icon_url . '" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->addElement('Radio', "sitecrowdfunding_landingpage_setup", array(
            'label' => 'Landing Page Layout',
            'description' => "Do you want the layout of your landing page to be changed similar to the <a href='http://demo.crowdfunding.socialengineaddons.com/' target='_blank' > Crowdfunding Demo site</a> ? If you choose 'Yes' then your current landing page layout  will be replaced and it will look similar to the landing page of Crowdfunding Demo site.$icon_view",
            'value' => $shouldInstall ? 0 : ($coreSettings->getSetting('sitecrowdfunding.landingpage.setup', 0)),
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
        ));
        $this->sitecrowdfunding_landingpage_setup->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

// Element: Button Submit
        $attrb = array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
        );
        if ($shouldInstall) {
            $attrb['attribs'] = array('disabled' => 'disabled');
        }
        $this->addElement('Button', 'submit', $attrb);
    }

}
