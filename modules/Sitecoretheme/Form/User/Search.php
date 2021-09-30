<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_User_Search extends User_Form_Search {

  public function init() {
    // Add custom elements
    $this->getMemberTypeElement();
    $this->getDisplayNameElement();
    $this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $this->setMethod('get');
    $this->getDecorator('HtmlTag')->setOption('class', 'browsemembers_criteria');
  }

  public function getMemberTypeElement() {
    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
      return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

    if (count($options) <= 1) {
      if (count($options) == 1) {
        $this->_topLevelId = $profileTypeField->field_id;
        $this->_topLevelValue = $options[0]->option_id;
      }
      return;
    }

    foreach ($options as $option) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('Select', 'profile_type', array(
      'label' => 'Member Type',
      'order' => -1000001,
      'class' =>
      'field_toggle' . ' ' .
      'parent_' . 0 . ' ' .
      'option_' . 0 . ' ' .
      'field_' . $profileTypeField->field_id . ' ',
      'onchange' => 'changeFields($(this));',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'multiOptions' => $multiOptions,
    ));
    return $this->profile_type;
  }

  public function getDisplayNameElement() {
    $this->addElement('Text', 'displayname', array(
      'label' => 'Name',
      'order' => -1000000,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      //'onkeypress' => 'return submitEnter(event)',
    ));
    return $this->displayname;
  }

  public function getAdditionalOptionsElement() {

    $this->addElement('Button', 'done', array(
      'order' => 1000001,
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
    ));



    return $this;
  }

}