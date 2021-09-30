<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: CreateCampaign.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_EditCampaign extends Engine_Form {

  public function init() {

    $this->setTitle('Edit Newsletter')->setAttrib('id', 'form-campaign');
    $this->setMethod('post');

    $this->addElement('Text', 'title', array(
        'label' => 'Newsletter Title',
        'description' => 'Enter title for this newsletter. This is only for identification.',
        'allowEmpty' => false,
        'required' => true,
    ));

    $templates = Engine_Api::_()->getDbTable('templates', 'sesnewsletter')->getResult();

    $templateIds = array();
    foreach($templates as $template) {
        if(in_array($template->template_id, array('1', '2')))
            continue;
        $templateIds[$template->template_id]  = $template->displayname;
    }

    $this->addElement('Select', 'template_id', array(
      'label' => 'Choose Template',
      'description' => 'Choose Template',
      'multiOptions' => $templateIds,
    ));


    $this->addElement('TinyMce', 'body', array(
      'label' => 'Body',
      'required' => true,
      'editorOptions' => array(
        'html' => true,
      ),
      'allowEmpty' => false,
    ));


    $types = Engine_Api::_()->getDbTable('types', 'sesnewsletter')->getResult(array('fetchAll' => 1));
    $newsTypes = array();
    foreach($types as $type) {
        $newsTypes[$type->type_id]  = $type->title;
    }
    $this->addElement('MultiCheckbox', 'newsletter_types', array(
      'label' => 'Choose Newsletter Type',
      'description' => 'Choose Newsletter Type',
      'multiOptions' => $newsTypes,
      'required' => true,
      'allowEmpty' => false,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
