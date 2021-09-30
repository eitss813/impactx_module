<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardTransactionFilter.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_ProjectsFilter extends Engine_Form
{

    public function init()
    {

        $this->setAttrib('id', 'filter_form');

        $this->addElement('Hidden', 'page',array('order' => '1'));

        $this->addElement('Text', 'project_name', array(
            'label' => 'Project',
            'placeholder' => 'Start typing project name....',
            'description' => '',
            'autocomplete' => 'off'));

        $this->addElement('Text', 'project_order', array(
            'label' => 'Project Order',
            'placeholder' => 'Project order....',
            'description' => '',
            'autocomplete' => 'off'));
        
        $this->addElement('Hidden', 'project_id', array('order' => '2'));

        $this->addElement('Text', 'user_name', array(
            'label' => 'Owner',
            'placeholder' => 'Start typing owner name....',
            'description' => '',
            'autocomplete' => 'off'));

        $this->addElement('Hidden', 'user_id', array('order' => '3'));

        $this->addElement('select', 'project_status', array(
            'label' => 'Project Status',
            'multiOptions' => array(
                0 => '',
                1 => 'Draft',
                2 => 'Published',
                3 => 'Successful',
                4 => 'Failed',
                5 => 'Submit for approval',
                6 => 'Rejected'
            ),
            'value' => '0',
        ));

        $this->addElement('select', 'funding_status', array(
            'label' => 'Funding Status',
            'multiOptions' => array(
                0 => '',
                1 => 'Draft',
                2 => 'Published',
                3 => 'Successful',
                4 => 'Failed',
                5 => 'Submit for approval',
                6 => 'Rejected'
            ),
            'value' => '0',
        ));

        $this->addElement('select', 'is_published_yn', array(
            'label' => 'Published/ Unpublished',
            'multiOptions' => array(
                '' => '',
                '2' => 'Published',
                '1' => 'Unpublished'
            ),
        ));

        $this->addElement('select', 'is_funding_enabled_yn', array(
            'label' => 'Funding Enabled / Disabled',
            'multiOptions' => array(
                '' => '',
                '2' => 'Enabled',
                '1' => 'Disabled'
            ),
        ));

        $this->addElement('select', 'is_payment_edit', array(
            'label' => 'Payment Edit',
            'multiOptions' => array(
                '' => '',
                '2' => 'Enabled',
                '1' => 'Disabled'
            ),
        ));

        $this->addElement('Text', 'goal_amount_min', array(
            'label' => 'Goal Amount',
            'placeholder' => 'min',
            'autocomplete' => 'off'));

        $this->addElement('Text', 'goal_amount_max', array(
            'placeholder' => 'max',
            'autocomplete' => 'off'));

        $this->addElement('select', 'sort_field', array(
            'label' => 'Sort Field',
            'multiOptions' => array(
                '' => '',
                'project_name' => 'Project Name',
                'owner' => 'Owner',
                'project_order' => 'project_order',
                'project_status' => 'Project Status',
                'funding_status' => 'Funding Status',
                'goal_amount' => 'Goal Amount',
                'date' => '	Creation Date'
            ),
        ));

        $this->addElement('select', 'sort_direction', array(
            'label' => 'Sort Direction',
            'multiOptions' => array(
                '' => '',
                'asc' => 'Ascending',
                'desc' => 'Descending'
            ),
        ));

        // Buttons
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'ignore' => true,
            'type' => 'button',
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Button', 'clear', array(
            'label' => 'Clear',
            'decorators' => array('ViewHelper')
        ));
        $this->addDisplayGroup(array('search', 'clear'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');

    }

}