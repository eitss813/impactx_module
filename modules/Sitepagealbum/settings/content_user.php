<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.isActivate', 0);
if (empty($isActive)) {
  return;
}
$showContent_cover = array("mainPhoto" => $view->translate("Page Profile Photo"), "title" => $view->translate("Page Title"), "followButton" => $view->translate("Follow Button"), "likeButton" => $view->translate("Like Button"), "likeCount" => $view->translate("Total Likes"),"followCount" => $view->translate("Total Followers"));
$showContent_option = array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount");
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
	$showContent_cover['memberCount'] = $view->translate('Total Members');
	$showContent_cover['addButton'] = $view->translate('Add People Button');
	$showContent_cover['joinButton'] = $view->translate('Join Page Button');
	$showContent_option[] = 'addButton';
	$showContent_option[] = 'joinButton';
	$showContent_option[] = 'memberCount';
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Albums'),
        'description' => $view->translate('Forms the Albums tab of your Page. It displays the photos added by you and by the Page visitors. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.photos-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Photos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Random Albums'),
        'description' => $view->translate('Displays random albums and photos of the Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.albums-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Albums'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Commented Photos'),
        'description' => $view->translate('Displays the most commented photos of the Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.photocomment-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Photos'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Liked Photos'),
        'description' => $view->translate('Displays the most liked photos of the Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.photolike-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Photos'),
            'titleCount' => '',
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Photos Strip'),
        'description' => $view->translate("Displays some photos out of all the albums of a Page in a strip. Page Admin (you) can choose which photos to be shown in the strip by hiding the ones that should not be displayed. Hidden photos are replaced by new photos and so on."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.photorecent-sitepage',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Cover Photo and Information'),
        'description' => $view->translate('Displays the page cover photo with page profile photo, title and various action links that can be performed on the page from their Profile page (Like, Follow, etc.). You can choose various options from the Edit Settings of this widget. This widget should be placed on the Page Profile page.'),
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.page-cover-information-sitepage',
        'defaultParams' => array(
            'title' => $view->translate('Information'),
            'titleCount' => true,
            'showContent' => $showContent_option
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => $view->translate('Select the information options that you want to be available in this block.'),
                        'multiOptions' => $showContent_cover,
                    ),
                ), 
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Enter the cover photo height (in px). (Minimum 150 px required.)'),
                        'value' => '300',
                    )
                ),             
            ),
        ),
    ),
)
?>