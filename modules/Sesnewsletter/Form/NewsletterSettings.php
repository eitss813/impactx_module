<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: NewsletterSettings.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesnewsletter_Form_NewsletterSettings extends Engine_Form {

  protected $_item;

  public function setItem($item)
  {
    $this->_item = $item;
  }

  public function getItem()
  {
    return $this->_item;
  }

  public function init() {

    $this->setTitle('Newsletter Types Settings')
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $types = Engine_Api::_()->getDbTable('types', 'sesnewsletter')->getResult(array('fetchAll' => 1));
    $newsTypes = array();
    foreach($types as $type) {
        $newsTypes[$type->type_id]  = $type->title;
    }

    $user = Engine_Api::_()->getItem('user', $this->getItem());

    $getResults = Engine_Api::_()->getDbTable('subscribers', 'sesnewsletter')->getResult(array('fetchAll' => '1', 'resource_type' => 'user', 'resource_id' => $this->getItem(), 'email' => $user->email));
    $alreadySubscribed = array();
    foreach($getResults as $getResult) {
        $alreadySubscribed[]  = $getResult->type_id;
    }

    $this->addElement('MultiCheckbox', 'newsletter_types', array(
      'label' => 'Newsletter Type',
      'description' => 'Choose Newsletter type from which you want Newsletters on your Email',
      'multiOptions' => $newsTypes,
      'value' => $alreadySubscribed,
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}
