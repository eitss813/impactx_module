<?php


/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditRole.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Editmetrics extends Engine_Form
{

    protected $_field;

    public function init()
    {

        $metric_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_id', null);
        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        $this->addElement('Text', 'metric_name', array(
            'label' => "Name",
            'value' => ''
        ));
        $this->addElement('Text', 'metric_description', array(
            'label' => "Description",
            'value' => ''
        ));
        $this->addElement('Text', 'metric_unit', array(
            'label' => "Unit",
            'value' => ''
        ));

        $this->addElement('File', 'logo', array(
            'label' => 'Logo',
            'allowEmpty' => true,
            'required' => false,
        ));
        $this->logo->addValidator('Extension', false, 'jpg,jpeg,png');

        // Show the change/remove/reposition buttons only for edit and image is added
        if (!empty($metric_id) && !empty($metric['logo'])) {

            $this->addElement('Button', 'change_logo', array(
                'label' => 'Change Image',
                'onclick' => 'openChangeModal()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addElement('Button', 'reposition_logo', array(
                'label' => 'Reposition Image',
                'onclick' => 'openRepositionModal()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addElement('Button', 'remove_logo', array(
                'label' => 'Remove Image',
                'onclick' => 'openRemoveModal()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addDisplayGroup(array('change_logo', 'reposition_logo', 'remove_logo'), 'logo_edit_options');
            $logo_edit_options_group = $this->getDisplayGroup('logo_edit_options');
        }

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