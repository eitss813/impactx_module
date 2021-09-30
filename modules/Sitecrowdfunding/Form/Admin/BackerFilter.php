<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BackerFilter.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_BackerFilter extends Engine_Form {

    public function init() {
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
                ->setMethod('GET');
         $this->addElement('Hidden', 'project_id', array());
        // Element: query
        $this->addElement('Text', 'title', array(
            'label' => 'Project Name',
            'placeholder' => 'Start typing the project name..',
            'autocomplete' => 'off',
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => null, 'placement' => 'PREPEND')),
                array('HtmlTag', array('tag' => 'div')),
            ),
        )); 

        // Element: gateway_id
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $multiOptions = array('' => '');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
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

        // Element: state
        $backerTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $multiOptions = (array) $backerTable->select()
                        ->from($backerTable->info('name'), 'payment_status')
                        ->distinct(true)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;
        $multiOptions = array('active' => 'Okay', 'authorised' => 'Authorized', 'pending' => 'Pending', 'failed' => 'Failed');
        $multiOptions = array_merge(array('' => ''), $multiOptions);
        $this->addElement('Select', 'status', array(
            'label' => 'Payment Status',
            'multiOptions' => $multiOptions,
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => null, 'placement' => 'PREPEND')),
                array('HtmlTag', array('tag' => 'div')),
            ),
        ));


        // Element: order
        $this->addElement('Hidden', 'order', array(
            'order' => 10004,
        ));

        // Element: direction
        $this->addElement('Hidden', 'order_direction', array(
            'order' => 10005,
        ));

        // Element: execute
        $this->addElement('Button', 'search', array(
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
