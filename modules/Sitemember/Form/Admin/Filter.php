<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Filter.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Filter extends Engine_Form {

  public function init() {
    $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search')) 
    ;

    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET');

    $displayname = new Zend_Form_Element_Text('displayname');
    $displayname
            ->setLabel('Display Name')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    $username = new Zend_Form_Element_Text('username');
    $username
            ->setLabel('Username')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    $email = new Zend_Form_Element_Text('email');
    $email
            ->setLabel('Email')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
    $levelMultiOptions = array(0 => ' ');
    foreach ($levels as $key => $value) {
      $levelMultiOptions[$key] = $value;
    }

    $level_id = new Zend_Form_Element_Select('level_id');
    $level_id
            ->setLabel('Level')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions($levelMultiOptions);

    $enabled = new Zend_Form_Element_Select('enabled');
    $enabled
            ->setLabel('Status')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '-1' => '',
                '0' => 'Not Approved',
                '1' => 'Approved',
            ))
            ->setValue('-1');
    $featured = new Zend_Form_Element_Select('featured');
    $featured
            ->setLabel('Featured')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '-1' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('-1');
    $sponsored = new Zend_Form_Element_Select('sponsored');
    $sponsored
            ->setLabel('Sponsored')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '-1' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('-1');
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons sm_search_button'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clr'));

    $this->addElement('Hidden', 'order', array(
        'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
        'order' => 10002,
    ));

    $this->addElement('Hidden', 'user_id', array(
        'order' => 10003,
    ));


    $this->addElements(array(
        $displayname,
        $username,
        $email,
        $level_id,
        $enabled,
        $featured,
        $sponsored,
        $submit,
    ));

    // Set default action without URL-specified params
    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }
}