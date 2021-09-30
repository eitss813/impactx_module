<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Report.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Report extends Engine_Form {
  
  public function init() {
    $this
        ->setAttrib('id', 'adminreport_form')
        ->setAttrib('target', '_blank')
        ->setTitle("Funding Report")
        ->setDescription("Here, you can view funding report of various Projects on your site. Project’s funding report can be generated in two ways i.e. summarised or month wise and in two different formats i.e. as a webpage or excel sheet.");
    
    // SELECT project
    $this->addElement('Select', 'report_type', array(
            'label' => 'Select Type',
            'multiOptions' => array(
                    'summarised' => 'Summarised',
                    'monthwise' => 'Month Wise'  
            ),
            'value' => 'summarised',
            'onchange' => 'return onReportTypeChange($(this))',
    ));

    // SELECT project
    $this->addElement('Select', 'select_project', array(
            'label' => 'Select Projects',
            'multiOptions' => array(
                    'all' => 'All',
                    'specific_project' => 'Particular Projects',
                    'ongoing' => 'Ongoing Projects',
                    'completedFailed' => 'Completed (Successful)',
                    'completedSuccessful' => 'Completed (UnSuccessful)'
            ),
            'value' => 'all',
            'onchange' => 'return onProjectChange($(this))',
    ));

    $this->addElement('Text', 'project_name', array(
            'label' => 'Project',
            'description' => 'Start typing the name of the project.',
            'autocomplete' => 'off'));
    Engine_Form::addDefaultDecorators($this->project_name);
    $this->project_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false)); 
   
    $start_cal = new Engine_Form_Element_CalendarDateTime('start_cal');
    $start_cal->setLabel("From");
    $start_cal->setValue(date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))));

    $this->addElement($start_cal);

    $end_cal = new Engine_Form_Element_CalendarDateTime('end_cal');
    $end_cal->setLabel("To");
    $end_cal->setValue(date('Y-m-d H:i:s'));

    $this->addElement($end_cal);

    $this->addDisplayGroup(array('start_cal', 'end_cal'), 'grp2');
    $button_group = $this->getDisplayGroup('grp2');
    $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'id' => 'time_group2', 'style' => 'width:100%;'))
    ));


    $this->addElement('Date', 'from_month', array(
            'label' => "From",
            'allowEmpty' => false,
            'required' => true,
            'value' => array('year' => date('Y'), 'month' => date('m')-1, 'day' => 1)
    ));
    $this->addElement('Date', 'to_month', array(
            'label' => "To",
            'allowEmpty' => false,
            'required' => true,
            'value' => array('year' => date('Y'), 'month' => date('m'), 'day' => date('t',strtotime(date('Y m d'))))
    ));
    $currentYear = date('Y');
    $this->from_month->setYearMax($currentYear + 5);
    $this->from_month->setYearMin($currentYear - 3);
    $this->to_month->setYearMax($currentYear + 5);
    $this->to_month->setYearMin($currentYear - 3);


    $this->addElement('Select', 'format_report', array(
            'label' => 'Format',
            'multiOptions' => array(
                    '0' => "Webpage (.html)",
                    '1' => "Excel (.xls)",
            ),
            'value' => '0',
            'onchange' => 'return onchangeFormat($(this))',
    ));

    // Init submit
    $this->addElement('Button', 'generate_report', array(
            'label' => 'Generate Report',
            'type' => 'submit',
    ));

    $this->addElement('Hidden', 'project_id', array(
            'required' => true,
            'allowEmpty' => false,
            'order' => 2,
            'validators' => array(
                    'NotEmpty'
            ),
            'filters' => array(
                    'HtmlEntities'
            ),
    ));
    Engine_Form::addDefaultDecorators($this->project_id); 
  }

}