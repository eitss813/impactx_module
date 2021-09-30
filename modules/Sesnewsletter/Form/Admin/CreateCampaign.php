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

class Sesnewsletter_Form_Admin_CreateCampaign extends Engine_Form {

  public function init() {

    $this->setTitle('Create New Newsletter')->setAttrib('id', 'form-campaign');
    $this->setMethod('post');

    $this->addElement('Text', 'title', array(
        'label' => 'Newsletter Title',
        'description' => 'Enter title for this newsletter. This is only for your identification.',
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
      'label' => 'Choose Newsletter Template',
      'description' => 'Choose a Template for this newsletter which you have created from the "Manage Templates" tab of this plugin.',
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
      'description' => 'Choose the newsletter type from "Manage Newsletter Tye" section of this plugin. This newsletter will get sended to the subscribers who have subscribed to selected newsletter types.',
      'multiOptions' => $newsTypes,
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Select', 'choose_member', array(
      'label' => 'Choose Members',
      'description' => 'Choose Members',
      'multiOptions' => array(
        '0' => 'All Subscribers',
        '4' => 'Other Members',
        '1' => 'Site Member',
        '2' => 'Guest Users',
      ),
      'onchange' =>  'choosemember(this.value);',
    ));


    $levelOptions = array();
    $levelValues = array();
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      $levelOptions[$level->level_id] = $level->getTitle();
      $levelValues[] = $level->level_id;
    }
    // Select Member Levels
    $this->addElement('multiselect', 'member_levels', array(
        'label' => 'Member Levels',
        'multiOptions' => $levelOptions,
        'description' => 'Choose the Member Levels to which this Page will be displayed.',
        'value' => $levelValues,
    ));

    $networkOptions = array();
    $networkValues = array();
    foreach (Engine_Api::_()->getDbtable('networks', 'network')->fetchAll() as $network) {
      $networkOptions[$network->network_id] = $network->getTitle();
      $networkValues[] = $network->network_id;
    }

    // Select Networks
    $this->addElement('multiselect', 'networks', array(
        'label' => 'Networks',
        'multiOptions' => $networkOptions,
        'description' => 'Choose the Networks to which this Page will be displayed.',
        'value' => $networkValues,
    ));

    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      if (count($options) > 1) {
        $options = $profileTypeField->getElementParams('user');
        unset($options['options']['order']);
        unset($options['options']['multiOptions']['']);
        $optionValues = array();
        foreach ($options['options']['multiOptions'] as $key => $option) {
          $optionValues[] = $key;
        }

        $this->addElement('multiselect', 'profile_types', array(
            'label' => 'Profile Types',
            'multiOptions' => $options['options']['multiOptions'],
            'description' => 'Which Profile Types do you want to see this Slideshow?',
            'value' => $optionValues
        ));
      } else if (count($options) == 1) {
        $this->addElement('Hidden', 'profile_types', array(
            'value' => $options[0]->option_id
        ));
      }
    }

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
