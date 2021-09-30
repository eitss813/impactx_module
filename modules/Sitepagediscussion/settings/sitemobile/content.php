<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagediscussion
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagediscussion.isActivate', 0);
if (empty($isActive)) {
  return;
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Discussions'),
        'description' => $view->translate('This widget forms the Discussions tab on the Page Profile and displays the discussions of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.sitemobile-discussion-sitepage',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Discussion Topic View'),
        'description' => $view->translate("This widget should be placed on the Page Discussion Topic View Page."),
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.discussion-content',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
				'adminForm' => array(
						'elements' => array(
								array(
										'Radio',
										'postorder',
										array(
												'label' => 'Select the order below to display the posts on your site.',
												'multiOptions' => array(
														1 => 'Newer to older',
														0 => 'Older to newer'
												),
												'value' => 0,
										)
								),
						),
				),
    ),  
    array(
        'title' => $view->translate('Page Event Discussion'),
        'description' => $view->translate("This widget should be placed on the Page Event Profile Page."),
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.sitemobile-profile-discussions',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ), 
)
?>