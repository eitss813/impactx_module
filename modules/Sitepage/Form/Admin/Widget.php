<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitepage_Form_Admin_Widget extends Engine_Form {

  public function init() {
    $this
            ->setTitle('General Settings')
            ->setDescription('Configure the general settings for various widgets available with this plugin.');

    // VALUE FOR FEATURE page IN SLIDESHOW
    $this->addElement('Text', 'sitepage_feature_widgets', array(
        'label' => 'Featured Pages Slideshow
 Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the featured pages slideshow widget? Note that out of all the featured pages, these many pages will be picked up randomly to be shown in the slideshow (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED page IN Carousel
    $this->addElement('Text', 'sitepage_sponserdsitepage_widgets', array(
        'label' => 'Sponsored Pages Carousel Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in one view of the sponsored pages carousel widget? Note that this carousel is AJAX based and users will be able to browse through all the sponsored pages (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponserdsitepage.widgets', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    // VALUE FOR Sponsored Interval
    $this->addElement('Text', 'sitepage_sponsored_interval', array(
        'label' => 'Sponsored Carousel Speed',
        'allowEmpty' => false,
        'required' => true,
        'maxlength' => '3',
        'description' => 'What maximum Carousel Speed should be applied to the sponsored widget?',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.interval', 300),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR TRUNCATION
    $this->addElement('Text', 'sitepage_title_truncationsponsored', array(
        'label' => 'Title Truncation Limit For Sponsored Items Widget',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncationsponsored', 18),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR MOSTCOMMENT
    $this->addElement('Text', 'sitepage_comment_widgets', array(
        'label' => 'Most Commented Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the most commented pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.comment.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR MOSTLIKE
    $this->addElement('Text', 'sitepage_likes_widgets', array(
        'label' => 'Most Liked Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the most liked pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.likes.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR USER page PAGE
    $this->addElement('Text', 'sitepage_usersitepage_widgets', array(
        'label' => 'Page Profile Owner Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the page profile owner pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.usersitepage.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR AJAX LAYOUT
    $this->addElement('MultiCheckbox', 'sitepage_ajax_widgets_layout', array(
        'description' => 'Choose the view types that you want to be available for pages on the pages home and browse pages.',
        'label' => 'Views on Pages Home and Browse Pages',
        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3")),
    ));

    // VALUE FOR AJAX LAYOUT ORDER
    $this->addElement('Radio', 'sitepage_ajax_layouts_oder', array(
        'description' => 'Select a default view type for Directory Items / Pages on the Pages Home Widget and Browse Pages.',
        'label' => 'Default View on Pages Home Widget and Browse Pages',
        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.ajax.layouts.oder', 1),
    ));

    // VALUE FOR LIST SHOW IN AJAX WIDGETS
    $this->addElement('MultiCheckbox', 'sitepage_ajax_widgets_list', array(
        'description' => 'Choose the ajax tabs that you want to be there in the Main Pages Home Widget.',
        'label' => 'Ajax Tabs of Main Pages Home Widget',
        // 'required' => true,
        'multiOptions' => array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.ajax.widgets.list', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5')),
    ));


    // VALUE FOR POPULAR IN SITEPAGE VIEW
    $this->addElement('Text', 'sitepage_popular_widgets', array(
        'label' => 'Popular Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the popular pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.popular.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR POPULAR IN GRID VIEW
    $this->addElement('Text', 'sitepage_popular_thumbs', array(
        'label' => 'Popular Pages Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the popular pages widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.popular.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RECENT IN SITEPAGE VIEW
    $this->addElement('Text', 'sitepage_recent_widgets', array(
        'label' => 'Recent Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the recent pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.recent.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RECENT IN GRID VIEW
    $this->addElement('Text', 'sitepage_recent_thumbs', array(
        'label' => 'Recent Pages Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the recent pages widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.recent.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN SITEPAGE VIEW
    $this->addElement('Text', 'sitepage_random_widgets', array(
        'label' => 'Random Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the random pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.random.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitepage_random_thumbs', array(
        'label' => 'Random Pages Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the random pages widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.random.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    // VALUE FOR FETURED IN SITEPAGE VIEW
    $this->addElement('Text', 'sitepage_featured_list', array(
        'label' => 'Featured Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the featured pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.list', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR FETURED IN GRID VIEW
    $this->addElement('Text', 'sitepage_featured_thumbs', array(
        'label' => 'Featured Pages  Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the featured pages widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.featured.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED IN SITEPAGE VIEW
    $this->addElement('Text', 'sitepage_sponsored_list', array(
        'label' => 'Sponsored Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the sponsored pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.list', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED IN GRID VIEW
    $this->addElement('Text', 'sitepage_sponosred_thumbs', array(
        'label' => 'Sponsored Pages Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the sponsored pages widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponosred.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    $this->addElement('Text', 'sitepage_favourite_pages', array(
        'label' => 'Favourites pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the Favourites Pages widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.favourite.pages', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitepage_suggest_sitepages', array(
        'label' => 'You May Also Like Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the you may also like widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.suggest.sitepages', 5),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitepage_recently_view', array(
        'label' => 'Recently Viewed Pages Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the recently viewed pages widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.recently.view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitepage_recentlyfriend_view', array(
        'label' => 'Recently Viewed By Friends Widget',
        'maxlength' => '3',
        'description' => 'How many directory items / pages will be shown in the recently viewed by friends widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.recentlyfriend_view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitepage_pagelike_view', array(
        'label' => 'Page Profile Likes Widget',
        'maxlength' => '3',
        'description' => 'How many users will be shown in the page profile likes widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.pagelike.view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR Discusion IN SITEPAGE VIEW
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
      $this->addElement('Text', 'sitepage_mostdiscussed_widgets', array(
          'label' => 'Most Discussed Pages Widget',
          'maxlength' => '3',
          'description' => 'How many directory items / pages will be shown in the most discussed Pages widget (value can not be empty or zero) ?',
          'required' => true,
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.mostdiscussed.widgets', 3),
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          ),
      ));
    }

    $this->addElement('Text', 'sitepage_popular_locations', array(
        'label' => 'Popular Locations Widget',
        'maxlength' => '3',
        'description' => 'How many locations will be shown in the popular locations widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.popular.locations', 10),
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