<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Form_ProjectPrivacy extends Engine_Form
{

    public $_error = array();
    protected $_packageId;
    protected $_owner;
    protected $_quick;
    protected $_create;
    protected $_layoutId;

    public function getOwner()
    {
        return $this->_owner;
    }

    public function setOwner($owner)
    {
        $this->_owner = $owner;
        return $this;
    }

    public function getPackageId()
    {
        return $this->_packageId;
    }

    public function setPackageId($package_id)
    {
        $this->_packageId = $package_id;
        return $this;
    }

    public function setQuick($flage)
    {
        $this->_quick = $flage;
        return $this;
    }

    public function getQuick()
    {
        return $this->_quick;
    }

    public function setCreate($value)
    {
        $this->_create = $value;
        return $this;
    }

    public function getCreate()
    {
        return $this->_create;
    }

    public function getlayoutId()
    {
        return $this->_layoutId;
    }

    public function setlayoutId($layoutId)
    {
        $this->_layoutId = $layoutId;
        return $this->_layoutId;
    }

    public function init()
    {

        $this->loadDefaultDecorators();
        parent::init();
        $i = 800000;
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $viewer = Engine_Api::_()->user()->getViewer();
        $user = $this->getOwner();
        $viewer_id = $viewer->getIdentity();
        $userlevel_id = $user->level_id;
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitepages_edit_privacy');
        $this->getDecorator('Description')->setOption('escape', false);

        // Element: page_url
        $parent_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent_id', null);

        // Privacy
        $pageadminsetting = $coreSettings->getSetting('sitepage.manageadmin', 1);
        if (!empty($pageadminsetting)) {
            $ownerTitle = "Page Admins";
        } else {
            $ownerTitle = "Just Me";
        }

        //project privacy

        // notification to users
        $this->addElement('MultiCheckbox', 'notify_project_comment', array(
            'label' => 'Send notification when someone comments',
            'description' => ' will send email and notification to specified users only',
            'multiOptions' => array(
                '0' => 'Project Owner',
                '1' => 'Project Members',
                '2' => 'Project Followers',
                '3' => 'Project Admins',
                '4' => 'Organisation Owner',
                '5' => 'Organisation Member',
                '6' => 'Organisation Followers',
                '7' => 'Organisation Admins',
            ),

        ));

        // notification to users
        $this->addElement('MultiCheckbox', 'notify_project_donate', array(
            'label' => 'Send notification when someone funds',
            'description' => ' will send email and notification to specified users only',
            'multiOptions' => array(
                '0' => 'Project Owner',
                '1' => 'Project Members',
                '2' => 'Project Followers',
                '3' => 'Project Admins',
                '4' => 'Organisation Owner',
                '5' => 'Organisation Member',
                '6' => 'Organisation Followers',
                '7' => 'Organisation Admins',
            ),

        ));
        $this->addElement('Checkbox', 'is_user_followed_after_comment_yn', array(
            'label' => "Make users as followers when someone donated.",
            'value' => 0,
            'attribs' => array('class' => 'se_quick_advanced'),
        ));
        $this->addElement('Checkbox', 'is_user_followed_after_donate_yn', array(
            'label' => "Make users as followers when someone comments.",
            'value' => 0,
            'attribs' => array('class' => 'se_quick_advanced'),
        ));
        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
