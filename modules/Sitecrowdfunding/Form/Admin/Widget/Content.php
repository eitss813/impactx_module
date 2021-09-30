<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Widget_Content extends Engine_Form {

    public function init() {
        $this->setAttrib('id', 'form-upload');

        $topNavigationLink = array(
            'createProject' => 'Create a Project',
        );

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

        $projectNavigationLink = array(
            'all' => 'My Projects',
            'backed' => 'Backed Projects',
            'liked' => 'Liked Projects',
            'favourite' => 'Favourite Projects' 
            );
        $this->addElement('Radio', 'selectProjects', array(
            'label' => "Select Projects based on status.",
            'multiOptions' => array(
                'all' => 'All',
                'ongoing' => 'Ongoing',
                'successful' => 'Successful'
            ),
            'value' => 'all',
        ));

        $this->addElement('MultiCheckbox', 'topNavigationLink', array(
            'label' => "Choose the options that you want to be displayed for the Projects in this block.",
            'multiOptions' => $topNavigationLink,
        ));

        $this->addElement('MultiCheckbox', 'projectNavigationLink', array(
            'label' => "Choose the Project navigation links that you want to display for the projects on this page.",
            'multiOptions' => $projectNavigationLink,
        ));

        $this->addElement('MultiCheckbox', 'viewType', array(
            'label' => "Choose the view type for Projects.",
            'multiOptions' => array(
                'gridView' => 'Grid view',
                'listView' => 'List view',
                'mapView' => 'Map view',
            ),
        ));

        $this->addElement('Select', 'defaultViewType', array(
            'label' => "Select a default view type for Projects",
            'multiOptions' => array(
                'gridView' => 'Grid view',
                'listView' => 'List view',
            ),
            'value' => 'gridView',
        ));

        $this->addElement('Radio', 'searchButton', array(
            'label' => "Do you want to show search button?",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => 1,
        ));

        $this->addElement('Text', 'gridWidth', array(
            'label' => "Column width for Grid View",
            'value' => 150,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'gridHeight', array(
            'label' => "Column height for Grid View",
            'value' => 150,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('MultiCheckbox', 'projectOption', array(
            'label' => "Choose the options that you want to display for the Projects in this block. (These information would be same for all the tabs available on My Projects page)",
            'multiOptions' => $projectOption,
        ));

        $this->addElement('Select', 'show_content', array(
            'label' => "What do you want for view more content?",
            'multiOptions' => array(
                '2' => 'Show View More Link at Bottom',
                '3' => 'Auto Load Content on Scrolling Down'),
            'value' => 2,
        ));

        $this->addElement('Text', 'gridItemCount', array(
            'label' => "Count (Number of items to show in Grid View)",
            'value' => 12,
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->addElement('Text', 'listItemCount', array(
            'label' => "Count (Number of items to show in List View)",
            'value' => 12,
            'validators' => array(
                array('Int', true),
            ),
        ));       
        $this->addElement('Text', 'titleTruncationGridView', array(
            'label' => "Title truncation limit of Grid View",
            'value' => 25,
            'validators' => array(
                array('Int', true),
            ),
        ));

        $this->addElement('Text','titleTruncationListView', array(
                'label' => 'Title truncation limit of List View',
                'value' => 40,
                'validators' => array(
                    array('Int', true),
                ),
        ));
        $this->addElement('Text', 'truncationLocation', array(
                'label' => 'Truncation limit of location (Depend on location)',
                'value' => 35,
            )
        );
        $this->addElement('Text', 'descriptionTruncation', array(
                'label' => 'Description truncation limit of Project',
                'value' => 100,
                'validators' => array(
                    array('Int', true),
                ),
            )
        );
    }
}

?>
