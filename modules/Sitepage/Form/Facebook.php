<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Twitter.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Facebook extends Engine_Form {

    public function init() {
        $this->loadDefaultDecorators();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $fblikebox_id = $request->getParam('fblikebox_id', null);
        $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');

        if ($fblikebox_id && !empty($advfeedmodule) && !empty($advfeedmodule->enabled) && Engine_Api::_()->seaocore()->checkVersion($advfeedmodule->version, '4.2.5') == 1)
            $description = "By linking your Page to your Facebook Page, updates from your Page will also be published on your Facebook Page. Additionally, the Facebook Like Box for your Facebook Page will be displayed on your Page, thus enabling you to gain Likes for your Facebook Page from this website. This Like Box will also:<br /><br /><ul style='margin-left: 20px;'><li> Show the recent posts from your Facebook Page.</li><li>Show how many people already like your Facebook Page.</li> <li>Enable people to Like your Facebook Page.</li></ul><br /> <br /> Enter the URL of your Facebook Page below to link it with this Page.";
        else if ($fblikebox_id)
            $description = "By linking your Page to your Facebook Page, Facebook Like Box for your Facebook Page will be displayed on your Page, thus enabling you to gain Likes for your Facebook Page from this website. This Like Box will also:<br /><br /><ul style='margin-left: 20px;'><li> Show the recent posts from your Facebook Page.</li><li>Show how many people already like your Facebook Page.</li> <li>Enable people to Like your Facebook Page.</li></ul><br /> <br /> Enter the URL of your Facebook Page below to link it with this Page.";
        else if (!empty($advfeedmodule) && !empty($advfeedmodule->enabled) && Engine_Api::_()->seaocore()->checkVersion($advfeedmodule->version, '4.2.5') == 1)
            $description = "By linking your Page to your Facebook Page, updates from your Page will also be published on your Facebook Page. <br /> <br /> Enter the URL of your Facebook Page below to link it with this Page.";

        $this->setTitle("Link your Page to Facebook")
                ->setDescription($description)
                ->setMethod('POST')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $this->getDecorator('Description')->setOption('escape', false);

        $Api_facebook = Engine_Api::_()->getApi('facebook_Facebookinvite', 'seaocore');
        $facebook_userfeed = $Api_facebook->getFBInstance();
        
        
        $fb_checkconnection = $Api_facebook->checkConnection(null, $facebook_userfeed);
        $facebookPermittedPages = null;
        $isFbPageAvailable = false;
        if ($facebook_userfeed && $fb_checkconnection) {
            $facebookPermittedPages = $Api_facebook->getFBPermittedPages($facebook_userfeed);
        }

        if (empty($facebookPermittedPages)) {
            $description = 'Please login to facebook to choose your fb page';
            $this->setDescription($description);
        } elseif (!empty($facebookPermittedPages) && (count($facebookPermittedPages) < 2)) {
            $description = 'You have not created any facebook pages or you have denied manage_pages permissions for this app';
            $this->setDescription($description);
        } else {

            $isFbPageAvailable = true;
            

            $this->addElement('Select', 'fbpage_url', array(
                'label' => 'Facebook Page URL',
                'description' => "Select the facebook page you want to link to your page.",
                'style' => 'width:330px;',
                'multiOptions' => $facebookPermittedPages,
            ));


            $this->addElement('Button', 'submit', array(
                'label' => 'Save',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        }

        if (!empty($isFbPageAvailable)) {

            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => ' or ',
                'onclick' => 'javascript:parent.Smoothbox.close()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        } else {

            $this->addElement('Button', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'onclick' => 'javascript:parent.Smoothbox.close()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        }

        $this->addDisplayGroup(array(
            'submit',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}

?>