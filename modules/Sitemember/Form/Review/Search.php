<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Review_Search extends Engine_Form {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('get')
            ->setAction($view->url(array(), "sitemember_review_browse", true));

    $order = 1;

    $this->addElement('Text', 'search', array(
        'label' => 'Search',
        'order' => $order++,
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
        ),
    ));
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if ($viewer_id) {
      $this->addElement('Select', 'show', array(
          'label' => 'Show',
          'multiOptions' => array('' => "Everyone's Reviews", 'friends_reviews' => "My Friends' Reviews", 'self_reviews' => "My Reviews", 'featured' => "Featured Reviews"),
          'order' => $order++,
      ));
    }

    $this->addElement('Select', 'order', array(
        'label' => 'Browse By',
        'order' => $order++ + 50000,
        'multiOptions' => array(
            'recent' => 'Most Recent',
            'rating_highest' => 'Highest Rating',
            'rating_lowest' => 'Lowest Rating',
            'helpfull_most' => 'Most Helpful',
            'replay_most' => 'Most Reply',
            'view_most' => 'Most Viewed'
        ),
    ));

    $this->addElement('Select', 'rating', array(
        'label' => 'Ratings',
        'order' => $order++ + 50000,
        'multiOptions' => array(
            '' => '',
            '5' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Stars'), 5),
            '4' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Stars'), 4),
            '3' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Stars'), 3),
            '2' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Stars'), 2),
            '1' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 1),
        ),
    ));

    $this->addElement('Checkbox', 'recommend', array(
        'label' => 'Only Recommended Reviews',
        'order' => $order++ + 50000,
    ));
    $this->addElement('Hidden', 'page', array(
        'value' => '1',
        'order' => $order++ + 50000,
    ));
    $this->addElement('Button', 'done', array(
        'label' => 'Search',
        'order' => $order++ + 50000,
        'type' => 'Submit',
        'ignore' => true,
    ));
  }

}