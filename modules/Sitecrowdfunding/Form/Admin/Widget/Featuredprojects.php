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
class Sitecrowdfunding_Form_Admin_Widget_Featuredprojects extends Engine_Form {

    public function init() {

        $this->setMethod('post');
        $this->setTitle('Landing Page: Featured Projects');

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
                    ->where('name = ?', 'sitecrowdfunding.featured-fundraiser')
                    ->query()
                    ->fetchColumn();

            if (!empty($params)) {
                $params = Zend_Json_Decoder::decode($params);
            }
        }

        $this->addElement('Radio', 'selectProjects', array(
            'label' => 'Select Projects based on status.',
            'multiOptions' => array(
                'all' => 'All',
                'ongoing' => 'Ongoing',
                'successful' => 'Successful'
            ),
            'value' => 'all',
        ));
        $this->addElement('Select', 'showProject', array(
            'label' => 'Select Projects that you want to show in this block.',
            'multiOptions' => array(
                'featured' => 'Featured Only',
                'sponsored' => 'Sponsored Only',
                'featuredSponsored' => 'Both Featured and Sponsored',
                'special' => 'Choose Specific Projects'
            ),
            'value' => 'featuredSponsored',
            'onclick' => "hideOrShowSpecialProjectElements(this.value);"
        ));
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
            'order' => 2,
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);
        $this->addElement('MultiCheckbox', 'projectOption', array(
            'label' => 'Choose the options that you want to display for the Projects in this block.',
            'multiOptions' => array(
                'title' => 'Project Title',
                'owner' => 'Owner',
                'startDate' => 'Start Date',
                'backer' => 'Backers',
                'like' => 'Likes',
                'favourite' => 'Favourites',
                'comment' => 'Comment Count',
                'endDate' => 'End Date and Time',
                'featured' => 'Featured',
                'sponsored' => 'Sponsored',
            )
        ));
        $this->addElement('Text', 'projectHeight', array(
            'label' => 'Enter the height of each Project.',
            'value' => 300,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
        $this->addElement('Text', 'projectWidth', array(
            'label' => 'Enter the width of each Project.',
            'value' => 200,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
        $this->addElement('Radio', 'viewProjectButton', array(
            'label' => 'Do you want to show ‘View Project’ button?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => '1',
        ));
        $this->addElement('Text', 'viewProjectTitle', array(
            'label' => 'Enter title for ‘View Project’ button.',
            'value' => 'View Project',
        ));
        $this->addElement('Select', 'orderby', array(
            'label' => 'Popularity / Sorting Criteria.',
            'multiOptions' => array(
                'startDate' => 'Recently Started',
                'backerCount' => 'Most Backed',
                'likeCount' => 'Most Liked',
                'commentCount' => 'Most Commented'
            ),
            'value' => 'startDate',
        ));
        $this->addElement('Text', 'itemCount', array(
            'label' => 'Count of Projects to show',
            'value' => 5,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
                )
        );
        $this->addElement('Text', 'titleTruncation', array(
            'label' => 'Title truncation limit of Project',
            'value' => 20,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'descriptionTruncation', array(
            'label' => 'Description truncation limit of Project',
            'value' => 100,
            'validators' => array(
                array('Int', true),
            ),
        ));
    }

}
