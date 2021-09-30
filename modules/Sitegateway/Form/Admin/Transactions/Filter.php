<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Filter.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegateway_Form_Admin_Transactions_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET')
      ;

    // Element: query
    $this->addElement('Text', 'query', array(
      'label' => 'Search',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    // Element: gateway_id
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $multiOptions = array('' => '');
    foreach( $gatewaysTable->fetchAll() as $gateway ) {
      if(!strstr($gateway->plugin, 'Sitegateway_Plugin_Gateway_')) {  
          continue;
      }
      $multiOptions[$gateway->gateway_id] = $gateway->title;
    }
    $this->addElement('Select', 'gateway_id', array(
      'label' => 'Gateway',
      'multiOptions' => $multiOptions,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    // Element: type
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitegateway');
    $multiOptions = (array) $transactionsTable->select()
      ->from($transactionsTable->info('name'), 'type')
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN)
      ;
    if (!empty($multiOptions)) {
      $multiOptions = array_combine(
        array_values($multiOptions),
        array_map('ucfirst', array_values($multiOptions))
      );
      // array_combine() will return false if the array is empty
      if (false === $multiOptions) {
        $multiOptions = array();
      }
    }
    $multiOptions = array_merge(array('' => ''), $multiOptions);
    $this->addElement('Select', 'type', array(
      'label' => 'Type',
      'multiOptions' => $multiOptions,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    // Element: state
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'sitegateway');
    $multiOptions = (array) $transactionsTable->select()
      ->from($transactionsTable->info('name'), 'state')
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN)
      ;
    if (!empty($multiOptions)) {
      $multiOptions = array_combine(
        array_values($multiOptions),
        array_map('ucfirst', array_values($multiOptions))
      );
      // array_combine() will return false if the array is empty
      if (false === $multiOptions) {
        $multiOptions = array();
      }
    }
    $multiOptions = array_merge(array('' => ''), $multiOptions);
    $this->addElement('Select', 'state', array(
      'label' => 'State',
      'multiOptions' => $multiOptions,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));
    
    // Element: resource_type
    $multiOptions = (array) $transactionsTable->select()
      ->from($transactionsTable->info('name'), 'resource_type')
      ->distinct(true)
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN)
      ;

    if (!empty($multiOptions)) {
 
      $multiOptions = array_combine(
        array_values($multiOptions),
        array_map(array(Engine_Api::_()->sitegateway(), 'getResourceName'), array_values($multiOptions))
      );
      
      // array_combine() will return false if the array is empty
      if (false === $multiOptions) {
        $multiOptions = array();
      }
    }
    $multiOptions = array_merge(array('' => ''), $multiOptions);
    $this->addElement('Select', 'resource_type', array(
      'label' => 'Resource Type',
      'multiOptions' => $multiOptions,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));    
    
    
    
    

    // Element: amount
    // @todo

    // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
        array('HtmlTag2', array('tag' => 'div')),
      ),
    ));
  }
}