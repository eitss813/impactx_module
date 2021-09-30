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
class Sitepage_Form_Initiative extends Engine_Form
{

    public $_error = array();

    public function init()
    {
        $user = Engine_Api::_()->user()->getViewer();

        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();

        $this->loadDefaultDecorators();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        //PACKAGE BASED CHECKS

        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $initiative_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('initiative_id', null);
        $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);

        $this->setTitle('Add Initiative');

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Add Initiative")))
            ->setAttrib('name', 'sitepage_initiative')
            ->setAttrib('id', 'sitepage_initiative')
            ->getDecorator('Description')->setOption('escape', false);


        $this->addElement('Text', 'title', array(
            'label' => "Title",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('Text', 'initiative_order', array(
            'label' => "Order",
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));

        $this->addElement('File', 'logo', array(
            'label' => 'Initiative Image',
            'allowEmpty' => true,
            'required' => false,
        ));
        $this->logo->addValidator('Extension', false, 'jpg,jpeg,png');

        // Show the change/remove/reposition buttons only for edit and image is added
        if (!empty($initiative_id) && !empty($initiative['logo']) ) {

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

            $this->addDisplayGroup(array('change_logo','reposition_logo','remove_logo'), 'logo_edit_options');
            $logo_edit_options_group = $this->getDisplayGroup('logo_edit_options');

        }

        // tinymce settings
        $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions();
        $editorOptions['height'] = '350px';
        $editorOptions['width'] = '99%';

        $this->addElement('TinyMce', 'about', array(
            'label' => "About",
            'required' => false,
            'allowEmpty' => true,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));

        /*$this->addElement('Integer', 'no_of_projects', array(
            'label' => "Number of Projects"
        ));

        $this->addElement('Integer', 'no_of_families_helped', array(
            'label' => "Families Helped"
        ));

        $this->addElement('Integer', 'no_of_children_bettered', array(
            'label' => "Children Bettered"
        ));

        $this->addElement('Integer', 'no_of_funding_to_date', array(
            'label' => "Funding to Date"
        ));*/

        $this->addElement('TinyMce', 'back_story', array(
            'label' => "Backstory",
            'required' => false,
            'allowEmpty' => true,
            'editorOptions' => $editorOptions,
            'filters' => array(new Engine_Filter_Censor()),
        ));

        $this->addElement('Text', 'sections', array(
            'label' => 'Project Galleries',
            'autocomplete' => 'off',
            'description' => Zend_Registry::get('Zend_Translate')->_('Separate project galleries with commas.'),
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Button', 'add_metrics_button', array(
            'label' => 'Add Metrics',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Button', 'add_questions_button', array(
            'label' => 'Add Questions',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Text', 'payment_action_label', array(
            'label' => "Display name on the Call to Action Button. (e.g. Donate, Contribute)",
            'allowEmpty' => false,
            'required' => false
        ));

        $this->addElement('Radio', 'payment_is_tax_deductible', array(
            'label' => 'Are donations to projects in this initiative tax deductible ?',
            'multiOptions' => array("1"=>"Yes", "0"=>"No"),
            'required' => true,
            'allowEmpty' => false,
            'value' => 0,
            'onchange' => 'onChangeIsTaxDeductible(this.value);'
        ));

        $this->addElement('Text', 'payment_tax_deductible_label', array(
            'label' => "Text display to encourge funding ?",
            'allowEmpty' => false,
            'required' => false
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'list', 'controller' => 'Initiatives', 'page_id' => $page_id), "sitepage_initiatives", true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
