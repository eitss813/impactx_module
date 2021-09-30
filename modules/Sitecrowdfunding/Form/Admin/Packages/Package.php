<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Package.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Packages_Package extends Engine_Form {

    public function init() {

        $this->setTitle('Packages Settings')
                ->setName('sitecrowdfunding_package_settings');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $packageInfoArray = array('price' => 'Price','billing_cycle'=> 'Billing Cycle','duration'=>'Duration','featured'=>'Featured','sponsored'=>'Sponsored','rich_overview'=>'Rich Overview','videos'=>'Videos','photos'=>'Photos','description'=>'Description', 'commission' => 'Commission');
          
              //VALUE FOR ENABLE/DISABLE PACKAGE
      $this->addElement('Radio', 'sitecrowdfunding_package_setting', array(
          'label' => 'Packages',
          'description' => 'Do you want Packages to be activated? Packages can vary, based upon their features available to the Projects created under them. If enabled, users will have to select a package in the first step of project creation, which can be changed again later. Project owners can manage their package from ‘Packages’ section available on the \'Project Dashboard\'. [Note: If you have enabled packages on your site, then feature settings for Projects will depend on packages and member levels based feature settings will be off. If packages are disabled, then feature settings for Projects could be configured from member level settings.]',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'javascript:showUiOption(this.value)',
          'value' => $settings->getSetting('sitecrowdfunding.package.setting', 0),
      ));

      $this->addElement('Radio', 'sitecrowdfunding_package_view', array(
          'label' => 'Package View',
          'description' => 'Select the view type of packages that will be shown in the first step of Project creation.',
          'multiOptions' => array(
              1 => 'Vertical',
              0 => 'Horizontal'
          ),
          'value' => $settings->getSetting('sitecrowdfunding.package.view',1),
      ));

      $this->addElement('MultiCheckbox', 'sitecrowdfunding_package_information', array(
          'label' => 'Package Information',
          'description' => 'Select the information options that you want to be available in package details.',
          'multiOptions' =>  $packageInfoArray,       
          'value' => $settings->getSetting('sitecrowdfunding.package.information', array_keys($packageInfoArray)),
      ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}