<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BestProjects.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Widget_BestProjects extends Engine_Form {

    public function init() {

        $this->setMethod('post');
        $this->setTitle('Landing Page: Best Projects Carousel');


        $this->addElement('Radio', 'categoryAtTop', array(
            'label' => 'Do you want to show category tab at the top of the Carousel ?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
                ),
            'value' => '1',
        ));

        $categories = Engine_Api::_()->getDbtable('Categories', 'sitecrowdfunding')->
        getCategories(array('category_id', 'category_name'),null,0,0,1);
        $categories_prepared = array();
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $category->category_name;
            }
            $this->addElement('MultiCheckbox', 'category_ids', array(
                'label' => 'Choose categories',
                'description' => 'Select the categories that you want to display in the slideshow.
                [Only 8 will be visible from the selected categories]',
                'multiOptions' => $categories_prepared,
                'RegisterInArrayValidator' => false,
                ));
        }

        $this->addElement( 'Select', 'popularType', array(
            'label' => 'Popularity / Sorting Criteria',
            'multiOptions' => array(
                'start_date' => 'Recently Started',
                'like' => 'Most Liked',
                'backerCount' => 'Most Backed',
                'comment' => 'Most Commented',
                'random' => 'Random',
                ),
            'value' => 'start_date',
            )
        );

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
            'multiOptions' => array_merge(array(
                'title' => 'Project Title',
                'owner' => 'Owner',
                'startDate' => 'Start Date',
                'view' => 'Views',
                'like' => 'Likes',
                'comment' => 'Comment Count',
                'endDate' => 'End Date and Time',
                'backer' => 'Backers',
                'featured' => 'Featured Label',
                'sponsored' => 'Sponsored Label',
                'location' => 'Location',
                'facebook' => 'Facebook [Social Share Link]',
                'twitter' => 'Twitter [Social Share Link]',
                'linkedin' => 'LinkedIn [Social Share Link]',
                'googleplus' => 'Google+ [Social Share Link]'
                    )
            )
                )
        );

        $this->addElement(
                'select', 'showPagination', array(
            'label' => 'Do you want to enable navigation buttons?',
            'description' => '(Note: If you select \'No\' then a limited number of projects will be displayed in a fixed block.)',
            'multiOptions' => array(1 => 'Yes', 0 => 'No'),
            'value' => '0',

                )
        );

        $this->addElement(
                'Text', 'itemCount', array(
            'label' => 'Total number of Projects to show.',
            'value' => 8,
                )
        );
        $this->addElement(
                'Text', 'itemCountPerPage', array(
            'label' => 'Number of Projects to be displayed in the fixed block.',
            'description' => '(This setting will be used if you have enabled navigation buttons)',
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

        $this->addElement('Text', 'descriptionTruncation', array(
            'label' => 'Truncation limit for description.',
            'value' => 40,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

    }

}
