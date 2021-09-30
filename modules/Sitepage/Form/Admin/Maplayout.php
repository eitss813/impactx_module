<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Map.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Maplayout extends Engine_Form {

  public function init() {
    $this
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box')
            ->setDescription('Select layout');
    //Element: profile_type
        $pageList = Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->getLayouts();
        $options = array();
        foreach( $pageList as $pageRow ) {
            if($pageRow->status == 1) {
                $options[$pageRow->definedlayout_id] = $pageRow->title;
            }
        }
        $this->addElement('Select', 'layout_id',array(
                    'required' => true,
                    'allowEmpty' => false,
                    'multiOptions' => $options
                ));
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}

?>