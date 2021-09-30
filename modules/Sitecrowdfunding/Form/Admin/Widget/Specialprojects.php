<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Specialprojects.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Widget_Specialprojects extends Engine_Form {

    public function init() {

        $this->setMethod('post');
        $this->setTitle('Special Projects');

        //SHOW PREFIELD START AND END DATETIME
        $httpReferer = $_SERVER['HTTP_REFERER'];
        $params = $toValues = $toValuesArray = array();
        $toValuesString = '';
        if (!empty($httpReferer) && strstr($httpReferer, '?page=')) {
            $httpRefererArray = explode('?page=', $httpReferer);
            $page_id = (int) $httpRefererArray['1'];
        } elseif (!empty($httpReferer) && strstr($httpReferer, 'admin/content') && !strstr($httpReferer, 'admin/content?')) {
            $page_id = 3; //FOR HOME PAGE
        }

        if (!empty($page_id) && is_numeric($page_id)) {

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');

            //GET CONTENT
            $params = $tableContent->select()
                    ->from($tableContentName, array('params'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'sitecrowdfunding.special-projects')
                    ->query()
                    ->fetchColumn();

            if (!empty($params)) {
                $params = Zend_Json_Decoder::decode($params);
            }
        }

        $projectOption = array(
            'title' => 'Project Title',
            'owner' => 'Owner',
            'startDate' => 'Start Date',
            'backer' => 'Backers',
            'favourite' => 'Favourites',
            'like' => 'Likes',
            'comment' => 'Comment Count',
            'endDate' => 'End Date and Time',
            'featured' => 'Featured Label',
            'sponsored' => 'Sponsored Label',
            'location' => 'Location',
            'facebook' => 'Facebook [Social Share Link]',
            'twitter' => 'Twitter [Social Share Link]',
            'linkedin' => 'LinkedIn [Social Share Link]',
            'googleplus' => 'Google+ [Social Share Link]'
        );

        $this->addElement('Text', 'project_ids', array(
            'autocomplete' => 'off',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '/application/modules/Sitecrowdfunding/views/scripts/admin-settings/add-special-projects.tpl',
                        'thisObject' => $this,
                        'class' => 'form element')))
        ));
        Engine_Form::addDefaultDecorators($this->project_ids);

        $this->addElement('Hidden', 'toValues', array(
            'label' => '',
            'order' => 1,
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        // Start time
        $start = new Engine_Form_Element_CalendarDateTime('starttime');
        $start->setLabel("Start Time");
        $start->setAllowEmpty(false);
        $this->addElement($start);

        // End time
        $end = new Engine_Form_Element_CalendarDateTime('endtime');
        $end->setLabel("End Time");
        $end->setAllowEmpty(false);
        $this->addElement($end);

        if (isset($params['starttime']) && !empty($params['starttime'])) {
            $start->setValue($params['starttime']);
        }

        if (isset($params['endtime']) && !empty($params['endtime'])) {
            $end->setValue($params['endtime']);
        }


        $this->addElement(
                'Text', 'columnWidth', array(
            'label' => 'Column width for Grid View.',
            'value' => '180',
                )
        );
        $this->addElement(
                'Text', 'columnHeight', array(
            'label' => 'Column height for Grid View.',
            'value' => '328',
                )
        );
        $this->addElement(
                'MultiCheckbox', 'projectOption', array(
            'label' => 'Choose the options that you want to display for the Projects in this block.',
            'multiOptions' => $projectOption,
            )
        );

        $this->addElement(
                'Text', 'itemCount', array(
            'label' => 'Number of Projects to show.',
            'value' => 3,
                )
        );

        $this->addElement(
                'Text', 'titleTruncation', array(
            'label' => 'Truncation limit for Project title.',
            'value' => 16,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement(
                'Text', 'descriptionTruncation', array(
            'label' => 'Truncation limit for description.',
            'value' => 40,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
    }

}
