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
class Sitepage_Form_Timing extends Engine_Form {

  protected $_timings;

  public function settimings($timing) {
    $this->_timings = $timing;
    return $this->_timings;
  }
  public function gettimings() {
    return $this->_timings;
  }
  public function init() {
    $this->setTitle('Add Operating Hours')
    ->setDescription('Below, you can manage the opertaing hours for your page.')
    ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'dashboard', 'action' => 'save-timing')));
    $days = array('monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday');

    $this->addElement('Radio', 'days', array(
      'label' => 'Hours',
      'multiOptions' => array(
        1 => 'Always Open',
        0 => 'Open on Selected Hours'
        ),
      'onchange' => 'showTiming(this.value)',
      'value' => '1',
      ));
    foreach($days as $key => $value) {
      $time = array();
      foreach ($this->_timings as $day => $values) {
        if($key == $day) {
          $time['start'] = $this->_timings[$day.'start'];
          $time['end'] = $this->_timings[$day.'end'];
          $time['day'] = 1;
        }
      }
      $this->addElement('Checkbox', $key, array(
        'label' => $value,
        'required' => false,
        'value' => $time['day'],
        ));
      $this->addElement('text',$key.'data',array(
        'label' => 'Start',
        'decorators' => array( array('ViewScript', array(
              'viewScript' => '_timeElement.tpl',
              'name' => $key,
              'value' => $time,
              'class' => 'form element'
          ))
        ),
        ));
      $this->addDisplayGroup(array($key, $key.'data'), $key.'_group');
      $button_group = $this->getDisplayGroup($key.'_group');
      $button_group->setDecorators(array(
        'FormElements',
        array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'time'))
        ));
    }
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Timings',
      'type' => 'submit',
      ));

  }
}

?>