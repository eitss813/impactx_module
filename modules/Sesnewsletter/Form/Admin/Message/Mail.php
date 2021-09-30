<?php

class Sesnewsletter_Form_Admin_Message_Mail extends Engine_Form
{

  public function init()
  {

	// Decorators
    $this->loadDefaultDecorators();
	$this->getDecorator('Description')->setOption('escape', false);

    $this
      ->setTitle('Email All Members')
      ->setDescription("Using this form, you will be able to send an email out to all the members of your choice. Emails are sent out using a queue system, so they will be sent out over time.");

    $settings = Engine_Api::_()->getApi('settings', 'core')->core_mail;

    $this->addElement('Text', 'from_address', array(
      'label' => 'From:',
      'value' => (!empty($settings['from']) ? $settings['from'] : 'noreply@' . $_SERVER['HTTP_HOST']),
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress',
      )
    ));
    $this->from_address->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

    $this->addElement('Text', 'from_name', array(
      'label' => 'From (name):',
      'required' => true,
      'allowEmpty' => false,
      'value' => (!empty($settings['name']) ? $settings['name'] : 'Site Administrator'),
    ));

    $this->addElement('Text', 'subject', array(
      'label' => 'Subject:',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('TinyMce', 'body', array(
      'label' => 'Body',
      'required' => true,
      'editorOptions' => array(
        'html' => true,
      ),
      'allowEmpty' => false,
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


    $this->addElement('Select', 'choose_member', array(
      'label' => 'Choose Members',
      'description' => 'Choose Members',
      'multiOptions' => array(
        '1' => 'Specific Member',
        '2' => 'Members Without Photo',
        '3' => 'Members Having Birthday Today',
        '5' => 'External Emails',
        '4' => 'Other Members',
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
        'description' => 'Choose the Member Levels to which this email will be sent.',
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
        'description' => 'Choose the Networks to which this email will be sent.',
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
            'description' => 'Choose the Profile Types to which this email will be sent.',
            'value' => $optionValues
        ));
      } else if (count($options) == 1) {
        $this->addElement('Hidden', 'profile_types', array(
            'value' => $options[0]->option_id,
			'order' => 10000,
        ));
      }
    }

    $this->addElement('Text', "member_name", array(
        'label' => 'Member Name',
        'description' => "Enter the name of member on your website who you want to send email in the auto-suggest below.",
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Hidden', 'user_id', array('order' => 10100,));

    $this->addElement('Textarea', 'external_emails', array(
      'label' => 'Enter correct email by seprated by commas.',
      'required' => false,
      'allowEmpty' => true,
    ));


    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Emails',
      'type' => 'submit',
      'ignore' => true,
    ));
  }

}
