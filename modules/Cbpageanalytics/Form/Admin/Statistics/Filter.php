<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */

/**
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 */
class Cbpageanalytics_Form_Admin_Statistics_Filter extends Engine_Form {

    public function init() {
        $this->setAttrib('class', 'global_form_box')
                ->addDecorator('FormElements')
                ->addDecorator('Form')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $this->addElement('Select', 'page', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
            ),
        ));

        $this->addElement('Text', 'user', array(
            'placeholder' => 'Enter member name',
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
            ),
        ));

        // Init period
        $this->addElement('Select', 'period', array(
            'multiOptions' => array(
                Zend_Date::DAY => 'Today',
                Zend_Date::WEEK => 'This week',
                Zend_Date::MONTH => 'This month',
                Zend_Date::YEAR => 'This year',
            ),
            'value' => 'month',
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
            ),
        ));

        // Init chunk
        $this->addElement('Select', 'chunk', array(
            'multiOptions' => array(
                Zend_Date::DAY => 'By Day',
                Zend_Date::WEEK => 'By Week',
                Zend_Date::MONTH => 'By Month',
                Zend_Date::YEAR => 'By Year',
            ),
            'value' => 'day',
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
            ),
            'style' => 'display: none'
        ));

        // Init submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div')),
            ),
        ));
    }

}
