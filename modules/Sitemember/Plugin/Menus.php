<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Plugin_Menus {

  //SHOWING THE EDIT LOCATION LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitememberGutterUsereditlocation() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1)) {
      return false;
    }

    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITETAGMEMBER SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    if ($viewer->getIdentity() == $subject->user_id) {
      
      //RETURN EDIT LOCATION LINK
      return array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Edit Location'),
          'route' => 'sitemember_userspecific',
          'params' => array(
              'controller' => 'location',
              'action' => 'edit-location',
              'seao_locationid' => $subject->seao_locationid,
              'user_id' => $subject->getIdentity(),
              'resource_type' => 'user',
          )
      );
    }
  }

  public function onMenuInitialize_SitememberGutterTopRated() {

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3) {
      return false;
    }      
      
    return array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Top Rated Members'),
        'class' => 'buttonlink icon_sitemember_rate',
        'route' => 'sitemember_review_browse',
        'params' => array(
            'action' => 'top-rated'
        )
    );
  }

  public function onMenuInitialize_SitememberGutterMostRecommended() {

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0) {
      return false;
    }

    return array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Most Recommended Members'),
        'class' => 'buttonlink icon_sitemember_recommend',
        'route' => 'sitemember_review_browse',
        'params' => array(
            'action' => 'most-recommended-members'
        )
    );
  }

  public function onMenuInitialize_SitememberGutterMostReviewed() {
  
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3) {
      return false;
    }

    return array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Most Reviewed Members'),
        'class' => 'buttonlink icon_sitemember_reviewed',
        'route' => 'sitemember_review_browse',
        'params' => array(
            'action' => 'most-reviewed-members'
        )
    );
  }

  public function onMenuInitialize_SitememberGutterTopReviewers() {

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3) {
      return false;
    }  
      
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')) {
      return array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Top Reviewers'),
          'class' => 'buttonlink icon_sitemember_reviewers',
          'route' => 'sitemember_review_browse',
          'params' => array(
              'action' => 'top-reviewers'
          )
      );
    } else {
      return array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Top Raters'),
          'class' => 'buttonlink icon_sitemember_raters',
          'route' => 'sitemember_review_browse',
          'params' => array(
              'action' => 'top-raters'
          )
      );
    }
  }

  public function canViewBrosweReview() {
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3) {
      return false;
    }

    return true;
  }

}