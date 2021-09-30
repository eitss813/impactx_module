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
        'title' => 'Page Profile Discussions',
        'description' => 'This widget forms the Discussions tab on the Page Profile and displays the discussions of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepage.discussion-sitepage',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
    ),
    
    array(
        'title' => 'Page Discussion Topic View',
        'description' => "This widget should be placed on the Page Discussion Topic View Page.",
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
        'title' => 'Page Content Profile Discussions',
        'description' => 'This widget forms the Discussions tab on the Page Content Profile and displays the discussions of the Page Content. It should be placed in the Tabbed Blocks area of the Page Content Profile.',
        'category' => 'Page Content Profile',
        'type' => 'widget',
        'name' => 'sitepage.profile-discussions',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Most Discussed Pages',
        'description' => 'Displays list of Pages having maximum number of discussions.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepage.mostdiscussion-sitepage',
        'defaultParams' => array(
            'title' => 'Most Discussed Pages',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of pages to show)',
                        'value' => 4,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
            ),
        ),
    ),
)
?>