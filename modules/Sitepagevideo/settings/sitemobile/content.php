<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.isActivate', 0);
if (empty($isActive)) {
  return;
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Videos'),
        'description' => $view->translate('This widget forms the Videos tab on the Page Profile and displays the videos of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagevideo.sitemobile-profile-sitepagevideos',
        'defaultParams' => array(
            'title' => $view->translate('Videos'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Videos'),
        'description' => $view->translate('Displays the list of Videos from Pages created on your community. This widget should be placed in the widgetized Page Videos page. Results from the Search Page Videos form are also shown here.'),
        'category' => $view->translate('Pages'),
        'type' => 'widget',
        'name' => 'sitepagevideo.sitepage-video',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of videos to show)'),
                        'value' => 10,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
            ),
        ),
    ),
		array(
				'title' => $view->translate('Page Video View'),
				'description' => $view->translate("This widget should be placed on the Page Video View Page."),
				'category' => $view->translate('Pages'),
				'type' => 'widget',
				'name' => 'sitepagevideo.video-content',
				'defaultParams' => array(
						'title' => '',
						'titleCount' => true,
				),
		),
)
?>