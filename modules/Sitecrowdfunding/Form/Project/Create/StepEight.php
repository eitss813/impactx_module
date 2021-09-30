<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Create_StepEight extends Sitecrowdfunding_Form_Project_Privacy
{

    public $_error = array();
    protected $_item;
    protected $_defaultProfileId;

    public function getItem()
    {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item)
    {
        $this->_item = $item;
        return $this;
    }

    public function getDefaultProfileId()
    {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id)
    {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function init()
    {

        parent::init();

        $this->execute->setLabel('Save Changes');

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);

        $backURL = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'project-create', 'action' => 'step-seven',  'project_id'=> $project_id), "sitecrowdfunding_createspecific", true);
        $this->addElement('Button', 'previous', array(
            'label' => 'Previous',
            'onclick' => "window.location.href='".$backURL."'",
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'previous',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));

    }

}
