<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagediscussion
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
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
        'description' => $view->translate('Forms the Discussions tab of your Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepage.discussion-sitepage',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
    ),
)
?>