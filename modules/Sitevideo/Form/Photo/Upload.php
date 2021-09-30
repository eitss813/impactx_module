<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Upload.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Photo_Upload extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Add New Photos')
                ->setDescription("Choose photos on your computer to add to this channel. (2MB maximum).")
                ->setAttrib('id', 'form-upload')
                ->setAttrib('class', 'global_form sitevideo_form_upload')
                ->setAttrib('name', 'albums_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $uploadUrl = $this->getView()->url(array(), 'sitevideo_photoalbumupload') . '?ul=1';
        $deleteUrl = $this->getView()->url(array('module' => 'sitevideo', 'controller' => 'photo', 'action' =>'remove'), 'default');
        $this->addElement('SeaoFancyUpload', 'file', array(
            'url' =>  $uploadUrl,
            'deleteUrl' =>  $deleteUrl,
            'accept' => 'image/*',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Photos',
            'type' => 'submit',
        ));
    }

}
