<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Form_Admin_Widget extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Widget Settings')
            ->setDescription('Configure the general settings for the various widgets available with this plugin.');

    $this->addElement('Text', 'sitepagevideo_comment_widgets', array(
        'label' => 'Page Profile Most Commented Videos',
        'maxlength' => '3',
        'description' => 'How many videos will be shown in the page profile most commented videos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.comment.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagevideo_recent_widgets', array(
        'label' => 'Page Profile Most Recent Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the page profile most recent videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.recent.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagevideo_like_widgets', array(
        'label' => 'Page Profile Most Liked Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the page profile most liked videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.like.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagevideo_view_widgets', array(
        'label' => 'Page Profile Most Viewed Videos',
        'maxlength' => '3',
        'description' => 'How many videos will be shown in the page profile most viewed videos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.view.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagevideo_rate_widgets', array(
        'label' => 'Page Profile Top Rated Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the page profile top rated videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.rate.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagevideo_featured_widgets', array(
        'label' => 'Page Profile Featured Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the page profile featured videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.featured.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagevideo_homerecentvideos_widgets', array(
        'label' => 'Recent Videos',
        'maxlength' => '3',
        'description' => 'How many videos should be shown in the recent videos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.homerecentvideos.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));  
 
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>