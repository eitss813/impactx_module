<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndynamicform
 * @author     YouNet Company
 */
return array(
    array(
        'title' => 'YNC - Dynamic Form - Browse Menu',
        'description' => 'Displays a menu in the form browse page.',
        'category' => 'YNC - Dynamic Form',
        'type' => 'widget',
        'name' => 'yndynamicform.browse-menu',
    ),

    array(
        'title' => 'YNC - Dynamic Form - Categories',
        'description' => 'Displays a list of categories.',
        'category' => 'YNC - Dynamic Form',
        'type' => 'widget',
        'name' => 'yndynamicform.categories',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),

    array(
        'title' => 'YNC - Dynamic Form - Browse Search',
        'description' => 'Displays a search form in the form browse page.',
        'category' => 'YNC - Dynamic Form',
        'type' => 'widget',
        'name' => 'yndynamicform.browse-search',
        'defaultParams' => array(
            'title' => 'Form Search',
        ),
    ),

    array(
        'title' => 'YNC - Dynamic Form - Single Form',
        'description' => 'Displays a single form in the form browse page.',
        'category' => 'YNC - Dynamic Form',
        'type' => 'widget',
        'name' => 'yndynamicform.single-form',
        'autoEdit' => true,
        'adminForm' => 'Yndynamicform_Form_Admin_Widget_SingleForm',
        'defaultParams' => array(
            'title' => 'Single Form',
        ),
    ),
    array(
        'title' => 'YNC - Dynamic Form - Related Forms',
        'description' => 'Displays related forms in the form detail page.',
        'category' => 'YNC - Dynamic Form',
        'type' => 'widget',
        'name' => 'yndynamicform.list-related-forms',
        'requirements' => array(
            'subject' => 'yndynamicform_form',
        ),
        'defaultParams' => array(
            'title' => 'Related Forms',
            'max' => 4,
        ),
        'adminForm' => array(
            'elements' => array(
                array('Text', 'title', array('label' => 'Title')),
                array('Text', 'max', array('label' => 'Number of related forms show on page.',
                    'value' => 4)),
            )
        ),
    ),
    array(
        'title' => 'YNC - Dynamic Form - Form Details',
        'description' => 'Displays form\'s details on form detail page.',
        'category' => 'YNC - Dynamic Form',
        'type' => 'widget',
        'name' => 'yndynamicform.form-details',
        'requirements' => array(
            'subject' => 'yndynamicform_form',
        ),
        'defaultParams' => array(
            'title' => 'Form Details',
        ),
    ),

    array(
        'title' => 'YNC - Dynamic Form - Listing Form',
        'description' => 'Displays a list form in the form home page or form listing page.',
        'category' => 'YNC - Dynamic Form',
        'type' => 'widget',
        'name' => 'yndynamicform.list-forms',
        'isPaginated' => true,
        'defaultParams' => array(
            'itemCountPerPage' => '8',
        ),
    ),
)
?>