<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$table = Engine_Api::_()->getDbTable('pages', 'sitestaticpage');
$select = $table->select()->where('page_url !=?', '');
$static_pages = $table->fetchAll($select);

$staticpages_prepared[0] = "";
if (count($static_pages) != 0) {
  foreach ($static_pages as $page) {
    $staticpages_prepared[$page->page_id] = $page->title;
  }
}

$db = Engine_Db_Table::getDefaultAdapter();
$form_ids = $db->select()
        ->from('engine4_sitestaticpage_page_fields_options', array('option_id'))
        ->where('field_id = ?', 1)
        ->query()
        ->fetchAll();

$forms = array();
$forms[0] = 'ALL';
foreach ($form_ids as $form) {
    $forms[$form['option_id']] = 'FORM_' . $form['option_id'];
}


return array(
    array(
        'title' => 'Static Page Content',
        'description' => 'This widget displays Static Page Content. Note that this widget must be placed on a widgetized static page, and then it shows that page’s static content. It gets automatically placed on a widgetized static page when you create such a page.',
        'category' => 'Static Pages & HTML Blocks',
        'type' => 'widget',
        'name' => 'sitestaticpage.page-content',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'static_pages',
                    array(
                        'label' => 'Static Pages',
                        'multiOptions' => $staticpages_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Static HTML Block',
        'description' => 'This widget displays the static HTML Block content. From the settings of this plugin, you can choose the rich static content to be shown in this widget. You can also choose a start and end date & time within which this content should be shown. Choose the height and width of the widget container appropriately based on where you want to place the widget, and the content for the widget should also be accordingly created.',
        'category' => 'Static Pages & HTML Blocks',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestaticpage.html-blocks',
        'adminForm' => 'Sitestaticpage_Form_WidgetForm',
    ),
    array(
        'title' => 'Forms Statistics',
        'description' => 'Displays a member\'s profile field data which they have submitted in the forms of Static Pages.',
        'category' => 'Static Pages & HTML Blocks',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestaticpage.form-stats',
//        'defaultParams' => array(
//            'title' => 'Overview'
//        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => "Form Stats",
                    )
                ),
                array(
                    'radio',
                    'stats_tab_setting',
                    array(
                        'label' => 'Do you want to show the Stats Tab to all the visitors who will view your profile. If yes, then all the visitors can see the information submitted by you in the custom field forms on Static Pages.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 0
                    )
                ),
                array(
                    'Multiselect',
                    'static_forms',
                    array(
                        'label' => 'Which Forms To Include',
                        'multiOptions' => $forms
                    )
                ),
            ),
        ),
    )
);
