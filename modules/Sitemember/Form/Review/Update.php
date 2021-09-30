<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Update.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Review_Update extends Engine_Form {

  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem($item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {

    //GET VIEWER INFO
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET DECORATORS
    $this->loadDefaultDecorators();

    $getItemUser = $this->getItem();
    $sitemember_title = "<b>" . $getItemUser->getTitle() . "</b>";

    //GET REVIEW TABLE
    $memberTable = Engine_Api::_()->getDbTable('reviews', 'sitemember');
    $params = array();
    $params['resource_id'] = $getItemUser->getIdentity();
    $params['resource_type'] = $getItemUser->getType();
    $params['viewer_id'] = $viewer_id;
    $params['type'] = 'user';
    $hasPosted = $memberTable->canPostReview($params);

    //IF NOT HAS POSTED THEN SET FORM
    $this->setTitle('Update your Review')
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("You can update your member for %s below:"), $sitemember_title))
            ->setAttrib('name', 'sitemember_update')
            ->setAttrib('id', 'sitemember_update')
            ->setAttrib('style', 'display:block')->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Textarea', 'body', array(
        'label' => 'Summary',
        'rows' => 3,
        'allowEmpty' => true,
        'required' => false,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Add your Opinion',
        'order' => 10,
        'type' => 'submit',
        'onclick' => "return submitForm('$hasPosted', $('sitemember_update'), 'update');",
        'ignore' => true
    ));
  }

}