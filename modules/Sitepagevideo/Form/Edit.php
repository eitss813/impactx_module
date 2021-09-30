<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Form_Edit extends Engine_Form {

  public function init() {

    //MAKE PAGE LINK
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->item('sitepage_page', $page_id)->getHref(array('tab'=>$tab_id));

    $this->setTitle('Edit Video')
            ->setAttrib('name', 'video_edit');
    $this->addElement('Text', 'title', array(
        'label' => 'Video Title',
        'required' => true,
        'notEmpty' => true,
        'validators' => array(
            'NotEmpty',
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '100'))
        )
    ));
    $this->title->getValidator('NotEmpty')->setMessage("Please specify an video title");

    $this->addElement('Text', 'tags', array(
        'label' => 'Tags (Keywords)',
        'autocomplete' => 'off',
        'description' => 'Separate tags with commas.',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        )
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'rows' => 2,
        'maxlength' => '512',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
        )
    ));

    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this video in search results.",
    ));

    $this->addElement('Button', 'execute', array(
        'label' => 'Save Video',
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
        'href' => $url,
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
?>