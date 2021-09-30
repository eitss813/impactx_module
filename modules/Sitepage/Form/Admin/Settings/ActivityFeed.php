<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ActivityFeed.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Admin_Settings_ActivityFeed extends Engine_Form {

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'activity_feed_form',
            ))
            ->setTitle('Activity Feed Settings')
            ->setDescription('Below are the options to customize the Activity Feeds related to Pages. Once you configure all the settings below, click on "Save Changes" to save them.');
    $subCorePageApi = Engine_Api::_()->getApi('subCore', 'sitepage');
    $socialengineaddonFeedIsAdded = $subCorePageApi->isCoreActivtyFeedWidget('user_index_home', 'activity.feed');
    $advancedactivityHomeFeedsIsAdded = $subCorePageApi->isCoreActivtyFeedWidget('user_index_home', 'advancedactivity.home-feeds');
    $translate = Zend_Registry::get('Zend_Translate');
    $description = "Select the type of activity feeds that should be published for directory items / pages. By default, the photo and name of Page Admin is shown in activity feeds of directory items / pages. Using this setting, you can instead choose to show the Page's Photo and Title.";
    if ($socialengineaddonFeedIsAdded || $advancedactivityHomeFeedsIsAdded) {
      $description .= "If you choose the 2nd option to show the Page's Photo and Title, then users will also receive on their homepage the updates from Pages that they have Liked.";
    }
    $options1 = 'Page\'s Photo and Title';
    if ($socialengineaddonFeedIsAdded) {
      $options1.=" (Choosing this will send to users on their homepage, the updates from Pages that they Like. If you have the \"Advanced Activity Feeds / Wall Plugin\" (http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin) installed on your site, then Page Photo and Title will also come for comments made by Page Admins on activity feeds of their Page and comments on the content of their Page (photo, video, document, etc.))";
    } elseif ($advancedactivityHomeFeedsIsAdded) {
      $options1.=" (Choosing this will send to users on their homepage, the updates from Pages that they Like. Page Photo and Title will also come for comments made by Page Admins on activity feeds of their Page and comments on the content of their Page (photo, video, document, etc.))";
    } else {
      $options1.=" (If you have the \"Advanced Activity Feeds / Wall Plugin\" (http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin) installed on your site, then Page Photo and Title will also come for comments made by Page Admins on activity feeds of their Page and comments on the content of their Page (photo, video, document, etc.))";
    }
    $this->getView()->escape($options1);
    $this->addElement('Radio', 'sitepage_feed_type', array(
        'label' => 'Directory Items / Pages Activity Feed Type',
        'description' => $description,
        'multiOptions' => array(
            '0' => 'Page Admin\'s Photo and Name',
            '1' => $options1,
        ),
        'onclick' => 'showEditingOptions("sitepagefeed_likepage_dummy-wrapper",this.value)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.type', 0),
    ));

    if (!$socialengineaddonFeedIsAdded && !$advancedactivityHomeFeedsIsAdded) {
      $this->addElement('Dummy', 'sitepagefeed_likepage_dummy', array(
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'admin-settings/activity-feed/_feedLikepage.tpl',
                      'class' => 'form element'
              )))
      ));
    }


      $this->addElement('Radio', 'sitepage_feed_onlyliked', array(
        'label' => 'Show Directory Items / Pages Activity Feeds On Member Home Page',
        'description' => 'Select the type of Activity Feeds corresponding to Directory Items / Pages which you want to display to users on Member Home Page.',
        'multiOptions' => array(
            '0' => 'Show Feeds of all Directory Items / Pages (This will be dependent on the Content Privacy of user.)',
            '1' => 'Show Feeds only of Liked Directory Items / Pages',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.onlyliked', 1),
    ));
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>