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
class Sitepage_Form_Admin_Layoutdefault extends Engine_Form {

  public function init() {

    $this->setAttrib('id', 'form-upload');
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {

			$page_profile_layout = "Page Cover Photo Layout - 1) " . '<a href="https://lh5.googleusercontent.com/-I2lRdwC8ETw/UW-OE4g2g3I/AAAAAAAAASI/DPO1tfUoQUI/s576/tabbed-page-profile.png" target="_blank">Tabbed Layout</a>'. '     '. '2) <a href="https://lh4.googleusercontent.com/-Jc9B3cy6aio/UW_XYcXjt4I/AAAAAAAAASo/OTqnYw8KMs8/s576/w-tabbed-page-profile.png" target="_blank" >Without Tabbed Layout</a>';
			$page_member_layout = "Group Cover Photo Layout - 1) " . '<a href="https://lh3.googleusercontent.com/-2TbS683DObc/UW-OE9NENZI/AAAAAAAAASE/vMg0wbvVYwQ/s576/tabbed-group-profile.png" target="_blank">Tabbed Layout</a>'. '     '. '2) <a href="https://lh3.googleusercontent.com/-EziXRYhIn6A/UW_Xab92LWI/AAAAAAAAASw/K3gGg7EYFBs/s576/w-tabbed-group-profile.png" target="_blank" >Without Tabbed Layout</a>';

			$this->addElement( 'Radio' , 'sitepage_layout_cover_photo' , array (
				'label' => 'Cover Photo Layout',
				'description' => "Select a layout for the cover photo to be placed on the profile of directory items / pages on your site.",
				'multiOptions' => array (
					1 => $page_profile_layout,
					0 => $page_member_layout
				) ,
				//'value' => $coreSettings->getSetting('sitepage.layout.cover.photo', 1),
        'escape' => false
			));
    }

    $this->addElement('Radio', 'sitepage_layout_setting', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formRadioButtonStructure.tpl',
                    'class' => 'form element'
            )))));

    $this->addElement('Button', 'submit1', array(
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