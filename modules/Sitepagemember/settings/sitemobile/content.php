<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$manageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1);
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
if(Engine_Api::_()->core()->hasSubject('sitepage_page')) {
	$categories_member = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getRolesAssoc(Engine_Api::_()->core()->getSubject('sitepage_page')->page_id);
} else {
  $categories_member = Engine_Api::_()->getDbtable('roles', 'sitepagemember')->getSiteAdminRoles(array(), 'adminParams');
}

$categoryOptions = array();
$categoryOptions['0'] = 'Un-categorized (Display members who have not selected their membership roles.)';
if (!empty($categories_member)) {
	asort($categories_member, SORT_LOCALE_STRING);
	foreach( $categories_member as $key => $v ) {

    if(Engine_Api::_()->core()->hasSubject('sitepage_page')) {
			$row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory(Engine_Api::_()->core()->getSubject('sitepage_page')->category_id);
      $categoryOptions[$key] = $v . '  [' .  $row->category_name . ']';
    } else  {
			if ($manageCategorySettings != 1) {
				$categoryOptions['pageadminRole'] = 'Roles created by Page Admins';
			}
			$row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($v['page_category_id']);
			$categoryOptions[$v['role_id']] = $v['role_name'] . '  [' .  $row->category_name . ']';
    }

		
	}
}

$final_array =  array(
	array(
		'title' => 'Page Profile Members',
		'description' => 'This widget form the Member tab on the Page Profile and displays the members of the Page. You can choose to display all members or members based on their Roles by using the edit settings of this widget. It should be placed in the Tabbed Blocks area of the Page Profile.',
		'category' => 'Page Profile',
		'type' => 'widget',
		'name' => 'sitepagemember.sitemobile-profile-sitepagemembers',
		'defaultParams' => array(
			'title' => 'Members',
			'titleCount' => true,
		),
	  'adminForm' => array(
			'elements' => array(
				array(
					'Radio',
					'show_option',
					array(
						'description' => 'Do you want to display members on the basis of their roles?',
						'multiOptions' => array(
						    '0' => 'Yes, display members based on their roles.',
								'1' => 'No, display all members.',	
						),'value' => 1,
					)
				),
				array(
					'MultiCheckbox',
					'roles_id',
					array(
						'description' => 'Choose the member roles which you want to display in this block.',
						'multiOptions' => $categoryOptions,
					),
				),
			),
		),
	),
	 array(
    'title' => 'Page Profile Announcements',
    'description' => 'Displays list of announcements posted by page admins for their Pages. This widget should be placed on the Page Profile.',
    'category' => 'Page Profile',
    'type' => 'widget',
    'name' => 'sitepagemember.profile-sitepagemembers-announcements',
		'defaultParams' => array(
			'title' => 'Announcements',
			'titleCount' => true,
		),
		'adminForm' => array(
			'elements' => array(
				array(
					'Text',
					'itemCount',
					array(
							'label' => 'Count',
							'description' => '(number of announcements to show)',
							'value' => 3,
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
		'title' => $view->translate('Page Members'),
		'description' => $view->translate('Displays the list of Members from Pages created on your community. This widget should be placed in the widgetized Page Members page. Results from the Search Page Members form are also shown here.'),
		'category' => 'Pages',
		'type' => 'widget',
		'name' => 'sitepagemember.sitepage-member',
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
										'label' => 'Count',
										'description' => '(number of members to show)',
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
);

return $final_array;