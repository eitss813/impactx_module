<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Claimpage.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Claimpage extends Engine_Form {

  public function init() {

    $this->setTitle("Claim this Page")
            ->setdescription('Below, you can file a claim for this page on this community that you believe should be owned by you. Your request will be sent to the administration.')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitepages_create');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->url(array('action' => 'terms'), 'sitepage_claimpages', true);

    $this->addElement('Text', 'nickname', array(
        'label' => 'Your Name',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));

    $this->addElement('Text', 'email', array(
        'label' => 'Your Email',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));
    $this->email->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'about', array(
        'label' => 'About You and the Page',
        'required' => true,
        'allowEmpty' => false,
        'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:280px;min-height:50px;height:50px;'),
        'value' => '',
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'contactno', array(
        'label' => 'Contact Number',
        'description' => '',
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
    ));

    $this->addElement('Textarea', 'usercomments', array(
        'label' => 'Comments',
        'description' => '',
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:280px;min-height:50px;height:50px;'),
    ));

    $description = sprintf(Zend_Registry::get('Zend_Translate')->_("I have read and agree to the <a href='javascript:void(0);' onclick=window.open('%s','mywindow','width=500,height=500')>terms of service</a>."), $url);
    
    $this->addElement('Checkbox', 'terms', array(
        'label' => 'Terms of Service',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            'notEmpty',
            array('GreaterThan', false, array(0)),
        ),
        'value' => 0
    ));

    $this->terms->getValidator('GreaterThan')->setMessage('You must agree to the terms of service to continue.', 'notGreaterThan');
    $this->terms->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'terms'))
            ->addDecorator('DivDivDivWrapper');

    $this->addElement('Button', 'send', array(
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
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'send',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}

?>