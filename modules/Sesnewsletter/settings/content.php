<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: content.php  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

$moduleEnable = Engine_Api::_()->sesnewsletter()->getModulesEnable();
$headScript = new Zend_View_Helper_HeadScript();
$headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
$headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');
return array(
    array(
        'title' => 'SES - Newsletter / Email Marketing Plugin - Content',
        'description' => 'Shows the page\'s primary content area. (Not all pages have primary content)',
        'category' => 'SES - Newsletter / Email Marketing Plugin',
        'type' => 'widget',
        'name' => 'sesnewsletter.content',
        'requirements' => array(
        'page-content',
        ),
    ),
    array(
        'title' => 'SES - Newsletter / Email Marketing Plugin - Header Template',
        'description' => 'Show header.',
        'category' => 'SES - Newsletter / Email Marketing Plugin',
        'type' => 'widget',
        'name' => 'sesnewsletter.header',
        'requirements' => array(
            'header-footer',
        ),
    ),
    array(
        'title' => 'SES - Newsletter / Email Marketing Plugin - Footer Template',
        'description' => 'Show footer.',
        'category' => 'SES - Newsletter / Email Marketing Plugin',
        'type' => 'widget',
        'name' => 'sesnewsletter.footer',
        'requirements' => array(
            'header-footer',
        ),
    ),
    array(
        'title' => 'SES - Newsletter / Email Marketing Plugin - Newsletter',
        'description' => 'With the help of this widget, users can subscribe on your website by entering their Email IDs. You can place this widget at any Page.',
        'category' => 'SES - Newsletter / Email Marketing Plugin',
        'type' => 'widget',
        'name' => 'sesnewsletter.newsletter',
        'autoEdit' => false,
    ),
    array(
        'title' => 'SES - Newsletter / Email Marketing Plugin - Content Highlight',
        'description' => 'This widget highlight content from chosen module in any of the 3 different designs available in this widget. Edit this widget to choose the module and design and configure various other settings.',
        'category' => 'SES - Newsletter / Email Marketing Plugin',
        'type' => 'widget',
        'name' => 'sesnewsletter.highlight',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'module',
                    array(
                        'label' => 'Choose the Module to be shown in this widget.',
                        'multiOptions' => $moduleEnable,
                    )
                ),
                array(
                    'Select',
                    'popularitycriteria',
                    array(
                        'label' => 'Choose the popularity criteria in this widget.',
                        'multiOptions' => array(
                        'creation_date' => 'Recently Created',
                        'view_count' => 'Most Viewed',
                        'like_count' => 'Most Liked',
                        'comment_count' => 'Most Commented',
                        'modified_date' => 'Recently Modified'
                        ),
                    )
                ),
                array(
                    'Text',
                    'bgcolor',
                    array(
                        'class' => 'SEScolor',
                        'label'=>'Choose widget background color.',
                        'value' => '#f2f2f2',
                    )
                ),
                array(
                    'Text',
                    'headingfontsize',
                    array(
                        'label'=>'Enter heading font size (in px).',
                        'value' => '20',
                    )
                ),
                array(
                    'Text',
                    'headingtextcolor',
                    array(
                        'class' => 'SEScolor',
                        'label'=>'Choose heading text color.',
                        'value' => '#fff',
                    )
                ),
                array(
                    'Text',
                    'headingbordercolor',
                    array(
                        'class' => 'SEScolor',
                        'label'=>'Choose heading border color.',
                        'value' => '#ff0000',
                    )
                ),
                array(
                    'Text',
                    'titlefontsize',
                    array(
                        'label'=>'Enter title font size.',
                        'value' => '13',
                    )
                ),
                array(
                    'Text',
                    'titlebgcolor',
                    array(
                        'class' => 'SEScolor',
                        'label'=>'Choose title background color.',
                        'value' => '#E91E63',
                    )
                ),
                array(
                    'Text',
                    'titletextcolor',
                    array(
                        'class' => 'SEScolor',
                        'label'=>'Choose title text color.',
                        'value' => '#fff',
                    )
                ),
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Enter content limit.',
                    ),
                    'value' => '6',
                ),
            ),
        ),
    ),
);
