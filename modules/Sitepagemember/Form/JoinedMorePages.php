<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: JoinedMorePages.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Form_JoinedMorePages extends Engine_Form {

  protected $_field;

  public function init() {
  
    $this->setMethod('post');
    $this->setTitle('Join More Pages');
        //->setDescription('Enter the name of the page below.');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Enter the name of the page which you want to join.')
					->addValidator('NotEmpty')
					->setRequired(true)
					->setAttrib('class', 'text')
					->setAttrib('style', 'width:300px;');

    // init to
    $this->addElement('Hidden', 'page_id', array( 'order' => 900,));

    $this->addElements(array(
        $label,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Join Page',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
			$URL = $view->url(array('action'=>'index'), 'sitepage_packages');
		} else {
			$URL = $view->url(array('action'=>'create'), 'sitepage_general');
		}

		
		$click = Zend_Registry::get('Zend_Translate')->_("<a href='" . $URL ."' class='buttonlink sitepage_quick_create' target='_parent'>Click here</a>");
		
    $customBlocks = sprintf(Zend_Registry::get('Zend_Translate')->_("%s to create a new page."), $click);

		
    $this->addElement('Dummy', 'new_page', array(
      'description' => $customBlocks,
    ));
    $this->getElement('new_page')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

  }
}