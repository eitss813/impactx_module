<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    MapSocial.php 2018-02-21 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_Form_Admin_Global extends Engine_Form {

    public function init() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //GENERAL HEADING
        $this->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');

        $coreSettings=Engine_Api::_()->getApi('settings', 'core');

        $socialsites=array('facebook'=>'Facebook','linkedin'=>'LinkedIn','twitter'=>'Twitter','instagram'=>'Instagram','google'=>'Google','yahoo'=>'Yahoo','outlook'=>'Outlook','pinterest'=>'Pinterest',
            'flickr'=>'Flickr','vk'=>'Vkontakte');

        $this->addElement('MultiCheckbox', 'linkaccount', array(
        'label' => 'Social Connect',
        'description' => 'Please select all the social sites from below with which you want your site users would be able to connect their community account (that is there account on your site). So, the users will be able to connect to the selected networks which will give them the functionality to login on your site via any of his connected social networks.',
        'multiOptions' => $socialsites,
    	));

        $socialsitessync=array('facebook'=>'Facebook','linkedin'=>'LinkedIn','twitter'=>'Twitter','instagram'=>'Instagram');

        $this->addElement('MultiCheckbox', 'syncaccount', array(
        'label' => 'Profile Synchronization',
        'description' => 'Please select all the social sites from below which you want to allow for your site users for the Profile Synchronization feature. That is, the users of your site would be able to synchronize their Profile information on your site with their existing profiles on the below selected social networks.',
        'multiOptions' => $socialsitessync,
    	));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));

    }

}