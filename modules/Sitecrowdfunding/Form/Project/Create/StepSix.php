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
class Sitecrowdfunding_Form_Project_Create_StepSix extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->loadDefaultDecorators();
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Photos And Videos")))
            ->setAttrib('id', 'sitecrowdfunding_project_new_step_five_custom')
            ->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Radio', 'profile_cover', array(
            'label' => 'Please upload a main project profile photo or video?',
            'multiOptions' => array(
                1 => 'Photo',
                0 => 'Video',
            ),
            'value' => 1,
            'required' => true,
            'onchange' => 'checkIsProfileCover(this.value);'
        ));

        $this->profile_cover->getDecorator('Description')->setOption('placement', 'append');

    }

}
