<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Upload.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Photo_Upload extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Add New Photos')
                ->setDescription("Choose photos on your computer to add to this project. (2MB maximum).")
                ->setAttrib('id', 'form-upload')
                ->setAttrib('class', 'global_form sitecrowdfunding_form_upload')
                ->setAttrib('name', 'albums_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null));
        $photoCount = 0;

        //PACKAGE BASED CHECKS
        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
          $photoCount = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getPackageOption($project->package_id, 'photo_count');
        }

        $uploadUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array()) . '?ul=1';
        $deleteUrl = $this->getView()->url(array('module' => 'sitecrowdfunding', 'controller' => 'photo', 'action' => 'remove'), 'default');
        $this->addElement('SeaoFancyUpload', 'file', array(
            'url' =>  $uploadUrl,
            'deleteUrl' =>  $deleteUrl,
            'accept' => 'image/*',
            'data' => array(
              'limitFiles' => $photoCount
            ),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Photos',
            'type' => 'submit',
        ));
    }

}
