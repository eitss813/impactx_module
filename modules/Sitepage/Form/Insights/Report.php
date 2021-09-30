<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Report.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Insights_Report extends Engine_Form {

  public function init() {
    $this
            ->setAttrib('id', 'report_form')
            ->setTitle("Page Reports")
            ->setDescription("You can view performance reports of your page over multiple durations and time intervals. The generated reports include statistics like views, likes, comments and active users. You can also export and save the reports.")
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    // Init mode

    $this->addElement('hidden', 'page_id', array(
         'order' => 900,
    ));

    // Init chunk
    $this->addElement('Select', 'time_summary', array(
        'label' => 'Time Summary',
        'multiOptions' => array(
            'Monthly' => 'Monthly',
            //'Weekly' => 'Weekly',
            'Daily' => 'Daily',
        ),
        'onchange' => 'return onChangeTime($(this))',
        'value' => 'Daily',
    ));

    $this->addElement('Select', 'month_start', array(
        'label' => '',
        'multiOptions' => array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ),
        'value' => '01',
        'decorators' => array(
            'ViewHelper'),
    ));


    $this->addElement('Select', 'year_start', array(
        'multiOptions' => array(
        ),
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('month_start', 'year_start'), 'start_group');
    $button_group = $this->getDisplayGroup('start_group');
    $button_group->setDescription('From');
    $button_group->setDecorators(array(
        'FormElements',
        array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper', 'id' => 'start_group', 'style' => 'display:none;'))
    ));



    $this->addElement('Select', 'month_end', array(
        'multiOptions' => array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ),
        'value' => '12',
        'decorators' => array(
            'ViewHelper'),
    ));

    $this->addElement('Select', 'year_end', array(
        'multiOptions' => array(
        ),
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('month_end', 'year_end'), 'end_group');
    $button_group = $this->getDisplayGroup('end_group');

    $button_group->setDescription('To');
    $button_group->setDecorators(array(
        'FormElements',
        array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper', 'id' => 'end_group', 'style' => 'display:none;'))
    ));

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
        array('HtmlTag', array('tag' => 'div', 'id' => 'time_group2', 'style' => "width:100%"))
    ));


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
    $this->addElement('Button', 'submit', array(
        'label' => 'Generate Report',
        'type' => 'submit',
    ));
  }

}

?>