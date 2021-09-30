<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layoutdefault.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_CoverPhotoLayout extends Engine_Form {

  public function init() {

    $this
            ->setAttrib('id', 'cover-form-upload')
						->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save-cover-page-layout')));

    $this->addElement('Radio', 'sitepage_layout_coverphoto', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formRadioButtonStructureCover.tpl',
                    'class' => 'form element'
            )))));

    $this->addElement('Button', 'submit2', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
  }

}

?>