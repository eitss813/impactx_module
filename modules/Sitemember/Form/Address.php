<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Address.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Address extends Engine_Form {

  public $_error = array();
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    // custom sitemember fields
    if (!$this->_item) {
      $sitemember_item = new Event_Model_Event(null);
      $this->setItem($sitemember_item);
    }
    parent::init();
    $this->setTitle('Edit Location')
            ->setDescription('Edit your location below, then click "Save Location" to save your location.');


    if ($this->_item->getType() == 'user') {

      $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($this->_item);
      $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
      $profilemapsTablename = $profilemapsTable->info('name');

      $select = $profilemapsTable->select()->from($profilemapsTablename, array('profile_type'));
      if (empty($aliasValues['profile_type'])) {
        $select->where($profilemapsTablename . '.option_id = ?', 1);
      } else {
        $select->where($profilemapsTablename . '.option_id = ?', $aliasValues['profile_type']);
      }

      $option_id = $select->query()->fetchColumn();
      $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
      $metaTableName = $metaTable->info('name');

      $select = $metaTable->select()
              ->from($metaTableName, array('type', 'label'))
              ->where($metaTableName . '.field_id = ?', $option_id);
      $valuesResultsLocation = $metaTable->fetchAll($select)->toarray();
      if (isset($valuesResultsLocation[0]['type']) && $valuesResultsLocation[0]['type'] == 'country') {
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
        $country[0] = "";
        foreach ($countries as $keys => $countrie) {
          $country[$keys] = $countrie;
        }
        $this->addElement('Select', 'location', array(
            'label' => $valuesResultsLocation[0]['label'],
            'description' => 'Eg: Fairview Park, Berkeley, CA',
            'multiOptions' => $country,
        ));
        $this->location->getDecorator('Description')->setOption('placement', 'append');
        include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';
      } elseif (isset($valuesResultsLocation[0]['type']) && !empty($valuesResultsLocation) && ($valuesResultsLocation[0]['type'] == 'location' || $valuesResultsLocation[0]['type'] == 'city')) {
        // LOCATION
        $this->addElement('Text', 'location', array(
            'label' => $valuesResultsLocation[0]['label'],
            'description' => 'Eg: Fairview Park, Berkeley, CA',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        )));
        $this->location->getDecorator('Description')->setOption('placement', 'append');
        $this->addElement('Hidden', 'locationParams', array('order' => 800000));
        include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';
      } else {
        // LOCATION
        $this->addElement('Text', 'location', array(
            'label' => "Location",
            'description' => 'Eg: Fairview Park, Berkeley, CA',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        )));
        $this->location->getDecorator('Description')->setOption('placement', 'append');
        $this->addElement('Hidden', 'locationParams', array('order' => 800001));
        include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';
      }
    } else {
      // LOCATION
      $this->addElement('Text', 'location', array(
          'label' => 'Location',
          'description' => 'Eg: Fairview Park, Berkeley, CA',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
      $this->location->getDecorator('Description')->setOption('placement', 'append');
      $this->addElement('Hidden', 'locationParams', array('order' => 800002));
      include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';
    }

    $this->addElement('Select', 'location_privacy', array(
        'label' => 'Privacy',
        'allowEmpty' => false,
        'required' => true,
        'multiOptions' => Fields_Api_Core::getFieldPrivacyOptions(),
    ));    

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Location',
        'order' => '998',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'order' => '999',
        'onclick' => "javascript:parent.Smoothbox.close();",
        'href' => "javascript:void(0);",
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->setOrder('999');
  }

}