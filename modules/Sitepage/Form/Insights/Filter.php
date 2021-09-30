<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Filter.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Insights_Filter extends Engine_Form {

  public function init() {
    $this
            ->setAttrib('class', 'global_form_box')
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    // Init mode
    $this->addElement('Select', 'mode', array(
        'label' => 'Type',
        'multiOptions' => array(
            'normal' => 'All',
            'cumulative' => 'Cumulative',
            'delta' => 'Change in',
        ),
        'value' => 'normal',
    ));
    $this->mode->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    $this->addElement('Select', 'type', array(
        'label' => 'Metric',
        'multiOptions' => array(
            'all' => 'All',
            'active_users' => 'Active Users',
            'view' => 'Views',
            'like' => 'Likes',
// 			'comment' => 'Comments',
        ),
        'value' => 'all',
    ));

    // check if comments should be displayed or not
    $show_comments = Engine_Api::_()->sitepage()->displayCommentInsights();
    if (!empty($show_comments)) {
      $this->type->addMultiOption('comment', 'Comments');
    }
    $this->type->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    // Init period
    $this->addElement('Select', 'period', array(
        'label' => 'Duration',
        'multiOptions' => array(
            //'day' => 'Today',
            Zend_Date::WEEK => 'This week',
            Zend_Date::MONTH => 'This month',
            Zend_Date::YEAR => 'This year',
        ),
        'value' => 'month',
        'onchange' => 'return filterDropdown($(this))',
    ));
    $this->period->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    // Init chunk
    $this->addElement('Select', 'chunk', array(
        'label' => 'Time Summary',
        'multiOptions' => array(
            Zend_Date::DAY => 'By Day',
        ),
        'value' => 'day',
    ));
    $this->chunk->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    // Init submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Filter',
        'type' => 'submit',
        'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
    ));
  }

}

?>