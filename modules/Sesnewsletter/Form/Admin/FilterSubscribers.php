<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmember
 * @package    Sesmember
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: FilterNewsletter.php 2016-05-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_Admin_FilterSubscribers extends Engine_Form {

  public function init() {
    $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
    ))->setMethod('GET');

    $title = new Zend_Form_Element_Text('email');
    $title->setLabel('Email')
        ->clearDecorators()
        ->addDecorator('ViewHelper')
        ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
        ->addDecorator('HtmlTag', array('tag' => 'div'));


    $types = Engine_Api::_()->getDbTable('types', 'sesnewsletter')->getResult(array('fetchAll' => 1));
    $newsTypes = array('' => '');
    foreach($types as $type) {
        $newsTypes[$type->type_id]  = $type->title;
    }
    $type_id = new Zend_Form_Element_Select('type_id');
    $type_id
            ->setLabel('Newsletter Types')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions($newsTypes);

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

    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    $this->addElement('Hidden', 'order', array(
        'order' => 10001,
    ));
    $this->addElement('Hidden', 'order_direction', array(
        'order' => 10002,
    ));

    $this->addElement('Hidden', 'subscriber_id', array(
        'order' => 10003,
    ));
    $this->addElements(array($type_id,$title,$level_id, $submit));

    // Set default action without URL-specified params
    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }

}
