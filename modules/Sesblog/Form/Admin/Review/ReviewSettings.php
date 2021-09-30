<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: ReviewSettings.php 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblog_Form_Admin_Review_ReviewSettings extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->setTitle('Manage Reviews & Ratings Settings')
            ->setDescription('Here, you can configure settings for  reviews for blogs on your website.');

    $this->addElement('Radio', 'sesblog_allow_review', array(
        'label' => 'Allow Reviews',
        'description' => 'Do you want to allow users to give reviews on blogs on your website? (Users will also be able to rate events, if you choose "Yes" for this setting.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onchange' => 'allowReview(this.value)',
        'value' => $settings->getSetting('sesblog.allow.review', 1),
    ));

    $this->addElement('Radio', 'sesblog_allow_owner', array(
        'label' => 'Allow Reviews on Own Blogs',
        'description' => 'Do you want to allow users to give reviews on own blogs on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.allow.owner', 1),
    ));

    $this->addElement('Radio', 'sesblog_show_pros', array(
        'label' => 'Allow Pros in Reviews',
        'description' => 'Do you want to allow users to enter Pros in their reviews?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.show.pros', 1),
    ));

    $this->addElement('Radio', 'sesblog_show_cons', array(
        'label' => 'Allow Cons in Reviews',
        'description' => 'Do you want to allow users to enter Cons in their reviews?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.show.cons', 1),
    ));

    $this->addElement('Radio', 'sesblog_review_summary', array(
        'label' => 'Allow Description in Reviews',
        'description' => 'Do you want to allow users to enter description in their reviews?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onchange' => "showEditor(this.value)",
        'value' => $settings->getSetting('sesblog.review.summary', 1),
    ));

    $this->addElement('Radio', 'sesblog_show_tinymce', array(
        'label' => 'Enable WYSIWYG Editor for Description',
        'description' => 'Do you want to enable WYSIWYG Editor for description for reviews?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.show.tinymce', 1),
    ));

    $this->addElement('Radio', 'sesblog_show_recommended', array(
        'label' => 'Allow Recommended Option',
        'description' => 'Do you want to allow users to choose to recommend the blogs in their reviews?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.show.recommended', 1),
    ));

    $this->addElement('Radio', 'sesblog_allow_share', array(
        'label' => 'Allow Share Option',
        'description' => 'Do you want to allow users to share reviews on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.allow.share', 1),
    ));

    $this->addElement('Radio', 'sesblog_show_report', array(
        'label' => 'Allow Report Option',
        'description' => 'Do you want to allow users to report reviews on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesblog.show.report', 1),
    ));
    
    /* text for rating starts */
    $this->addElement('Text', "sesblog_rating_stars_one", array(
        'label' => 'Mouseover Text for First Star',
        'description' => "Enter the text that you want to display when users mouse over on first rating star.",
        'value' => $settings->getSetting('sesblog.rating.stars.one', 'terrible'),
    ));
    $this->addElement('Text', "sesblog_rating_stars_two", array(
        'label' => 'Mouseover Text for Second Star',
        'description' => "Enter the text that you want to display when users mouse over on second rating star.",
        'value' => $settings->getSetting('sesblog.rating.stars.second', 'poor'),
    ));
    $this->addElement('Text', "sesblog_rating_stars_three", array(
        'label' => 'Mouseover Text for Third Star',
        'description' => "Enter the text that you want to display when users mouse over on third rating star.",
        'value' => $settings->getSetting('sesblog.rating.stars.three', 'average'),
    ));
    $this->addElement('Text', "sesblog_rating_stars_four", array(
        'label' => 'Mouse over rating text on fourth star',
        'description' => "Enter the text that you want to display when users mouse over on fourth rating star.",
        'value' => $settings->getSetting('sesblog.rating.stars.four', 'very good'),
    ));
    $this->addElement('Text', "sesblog_rating_stars_five", array(
        'label' => 'Mouseover Text for Fifth Star',
        'description' => "Enter the text that you want to display when users mouse over on fifth rating star.",
        'value' => $settings->getSetting('sesblog.rating.stars.five', 'excellent'),
    ));

    $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
    ));
  }
}
