<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Review_Global extends Engine_Form {

  public function init() {

    $this->setTitle('Review Settings')
            ->setDescription('Reviews & ratings are an extremely useful feature that enables you to gather refined ratings, reviews and feedback for the Members in your community. Below, you can highly configure the settings for reviews & ratings on your site.');

    $settings = Engine_Api::_()->getApi('settings', 'core');

     $this->addElement('Radio', 'sitemember_reviews_ratings', array(
        'label' => 'Review & Ratings',
        'description' => 'Which way to allow Reviews & Ratings for the Members?',
        'multiOptions' => array(
            2 => 'Both, Reviews & Ratings with Rating Parameters.',
            1 => 'Both, Reviews & Ratings without Rating Parameters.',
            0 => 'Only, Rating without Rating Parameters.',
            3 => 'No, Review & Ratings.'
        ),
        'value' => $settings->getSetting('sitemember.reviews.ratings', 2),
        'onclick' => 'reviewRatingInSitemember(this.value)',
    ));   
    
    $this->addElement('Radio', 'sitemember_proscons', array(
        'label' => 'Pros and Cons',
        'description' => 'Do you want Pros and Cons fields in Reviews? (If enabled, reviewers will be able to enter Pros and Cons for the Members that they review, and the same will be shown in their reviews.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitemember.proscons', 0),
        'onclick' => 'prosconsInReviews(this.value)',
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitemember_proncons', array(
        'label' => "Required Pros and Cons",
        'description' => 'Do you want to make Pros and Cons fields to be required when reviewers review members on your site?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitemember.proncons', 0),
    ));

    $this->addElement('Text', 'sitemember_limit_proscons', array(
        'label' => 'Pros and Cons Character Limit',
        'description' => 'What character limit should be applied to the Pros and Cons fields? (Enter 0 for no character limitation.)',
        'value' => $settings->getSetting('sitemember.limit.proscons', 500),
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('Radio', 'sitemember_recommend', array(
        'label' => 'Recommended in Reviews',
        'description' => 'Do you want Recommended field in Reviews? (If enabled, reviewers will be able to select if they would recommend that Member to a friend, and the same will be shown in their review.)',
        'value' => $settings->getSetting('sitemember.recommend', 0),
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitemember_summary', array(
        'label' => 'Required Summary',
        'description' => 'Do you want to make Summary field to be required when reviewers review members on your site?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitemember.summary', 0),
    ));


    $this->addElement('Radio', 'sitemember_report', array(
        'label' => 'Report',
        'description' => 'Allow logged-in users to report reviews as inappropriate.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitemember.report', 1),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitemember_share', array(
        'label' => 'Share',
        'description' => 'Allow logged-in users to share reviews in their activity feeds.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitemember.share', 1),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Radio', 'sitemember_email', array(
        'label' => 'Email',
        'description' => 'Allow logged-in users to email the review content.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sitemember.email', 1),
        'allowEmpty' => true,
        'required' => false,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}