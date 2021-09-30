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
class Sitepage_Form_Admin_Map extends Engine_Form {

  public function init() {
    $this
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box')
            ->setDescription('After selecting a profile type, if you click on "Yes", then the already created pages of this category with a different profile type will be changed to this new profile type. If you click on "No", then this mapping will only apply on the pages that will be created after saving this mapping.');
    //Element: profile_type
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitepage_page');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      if (count($options) > 0) {
        $options = $profileTypeField->getElementParams('sitepage_page');
        unset($options['options']['order']);
        unset($options['options']['multiOptions']['0']);
        $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
                    'required' => true,
                    'allowEmpty' => false,
                )));
      } else if (count($options) == 1) {
        $this->addElement('Hidden', 'profile_type', array(
             'order' => 900,
            'value' => $options[0]->option_id
        ));
      }
    }

    $this->addElement('Button', 'yes_button', array(
        'label' => 'Yes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Button', 'no_button', array(
        'label' => 'No',
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
    $this->addDisplayGroup(array('yes_button', 'no_button', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}

?>