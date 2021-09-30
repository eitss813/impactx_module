<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contactinfo.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Contactinfo extends Engine_Form {

  protected $_pageowner;

  public function getPageowner() {

    return $this->_pageowner;
  }

  public function setPageowner($_pageowner) {

    $this->_pageowner = $_pageowner;
    return $this;
  }

  public function init() {


    if (empty($this->_pageowner))
      $user = Engine_Api::_()->user()->getViewer();
    else
      $user=$this->_pageowner;

    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'contact_detail');
    $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email',);
    $options_create = array_intersect_key($availableLabels, array_flip($view_options));

    if ($options_create) {
      $this->setTitle('Contact Details')
              ->setDescription('Contact information will be displayed in the Info section of your page profile.')
              ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
              ->setAttrib('name', 'contactinfo');
    }

    if (isset($options_create['phone']) && $options_create['phone'] == 'Phone') {
      $this->addElement('Text', 'phone', array(
          'label' => 'Phone:',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '63')),
              )));
    }

    if (isset($options_create['email']) && $options_create['email'] == 'Email') {
      $this->addElement('Text', 'email', array(
          'label' => 'Email:',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '127')),
              )));
    }

    if (isset($options_create['website']) && $options_create['website'] == 'Website') {
      $this->addElement('Text', 'website', array(
          'label' => 'Website:',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '127')),
              )));
    }

    if ($options_create) {
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Details',
          'type' => 'submit',
          'ignore' => true,
      ));
    } else {
      $this->addElement('Dummy', 'option', array(
          'description' => '<div class="tip"><span>Admin has not choose any option to show contact detail.</span></div>',
      ));
      $this->getElement('option')->getDecorator('Description')->setOptions(array('placement', 'PREPEND', 'escape' => false));
    }
  }

}

?>