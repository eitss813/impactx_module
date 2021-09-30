<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Images_Add extends Engine_Form {

    public function init() {

        $this->setTitle("Add New Image")
                ->setDescription('Upload an image for Landing Page. (Note: The recommended size for the image is: 750px x 470px.)');

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Image Name (for your reference only)',
            'maxlength' => '100',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                //new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '100')),
            )
        ));

        $this->addElement('file', 'photo', array(
            'label' => 'Image',
            'accept' => 'image/*',
            'required' => true,
            'allowEmpty' => false,
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
        $this->getDisplayGroup('buttons');
    }

}