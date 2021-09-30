<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Review_Create extends Engine_Form {

  public $_error = array();
  protected $_settingsReview;
  protected $_item;

  public function getSettingsReview() {
    return $this->_settingsReview;
  }

  public function setSettingsReview($settingsReview) {
    $this->_settingsReview = $settingsReview;
    return $this;
  }

  public function getItem() {
    return $this->_item;
  }

  public function setItem($item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {

    //GET REVIEW SETTINGS
    $widgetSettingsReviews = $this->getSettingsReview();

    //GET DECORATORS
    $this->loadDefaultDecorators();
    $getItemUser = $this->getItem();
    $member_title = "<b>" . $getItemUser->getTitle() . "</b>";

    //IF NOT HAS POSTED THEN THEN SET FORM
    $this->setTitle('Write a Review')
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Give your ratings and opinion for %s below:"), $member_title))
            ->setAttrib('name', 'sitemember_create')
            ->setAttrib('id', 'sitemember_create')
            ->getDecorator('Description')->setOption('escape', false);

    if ($widgetSettingsReviews['sitemember_proscons']) {
      if ($widgetSettingsReviews['sitemember_limit_proscons']) {
        $this->addElement('Textarea', 'pros', array(
            'label' => 'Pros',
            'rows' => 2,
            'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this member?"),
            'allowEmpty' => false,
            'maxlength' => $widgetSettingsReviews['sitemember_limit_proscons'],
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));
      } else {
        $this->addElement('Textarea', 'pros', array(
            'label' => 'Pros',
            'rows' => 2,
            'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this member?"),
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));
      }
      $this->pros->getDecorator('Description')->setOptions(array('placement' => 'PREPAND', 'escape' => false));

      if ($widgetSettingsReviews['sitemember_limit_proscons']) {
        $this->addElement('Textarea', 'cons', array(
            'label' => 'Cons',
            'rows' => 2,
            'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this member?"),
            'allowEmpty' => false,
            'maxlength' => $widgetSettingsReviews['sitemember_limit_proscons'],
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));
      } else {
        $this->addElement('Textarea', 'cons', array(
            'label' => 'Cons',
            'rows' => 2,
            'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this member?"),
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));
      }
      $this->cons->getDecorator('Description')->setOptions(array('placement' => 'PREPAND', 'escape' => false));
    }

    $this->addElement('Textarea', 'title', array(
        'label' => 'One-line summary',
        'rows' => 1,
        'allowEmpty' => false,
        'maxlength' => 63,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

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

    if ($widgetSettingsReviews['sitemember_recommend']) {
      $this->addElement('Radio', 'recommend', array(
          'label' => 'Recommended',
          'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Would you recommend %s to a friend?"), $member_title),
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1
      ));
      $this->recommend->getDecorator('Description')->setOption('escape', false);
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Submit',
        'order' => 10,
        'type' => 'submit',
        'onclick' => "return submitForm('0', $('sitemember_create'), 'create');",
        'ignore' => true
    ));
  }

}