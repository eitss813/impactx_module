<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locationsearch.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_ProjectLocationFilter extends Fields_Form_Search
{


    public function init()
    {

        $this->setAttrib('id', 'filter_form')->setMethod('post');

        $this->addElement('Hidden', 'page', array('order' => '1'));

        // Search
        $this->addElement('Text', 'search_str', array(
            'label' => 'What',
            'autocomplete' => 'off',
            'description' => '(Enter keywords or Project name)',
        ));
        $this->search_str->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));



        /**** existing project location drop down ****/
        $page_id =  Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $initiative_id =  Zend_Controller_Front::getInstance()->getRequest()->getParam('initiative_id', null);
        $locationsDetail = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->getInitiativeProjectsByLocationSelect($page_id,$initiative_id);
        $project_location_prams = array();
        $project_location_prams['page_id'] = $page_id;
        $project_location_prams['initiative'] = $initiative_id;
        $projectlocationOption = array();
        $projectlocationOption['Select Location']='Select Location';
        //        $projectlocationOption['Type search']='Type search';

        foreach ($locationsDetail as $val) {
            $projectlocationOption[$val->location] = $val->location;
        }

        // Location projectlocation
        $this->addElement('Select', 'projectlocation', array(
            'label' => 'Project Locations',
            'multiOptions' => $projectlocationOption,
            'value' => '0',
            'onclick' => 'setSpecificLocationDatas(this.value);',

        ));
        $this->projectlocation->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));




        // Location
        $this->addElement('Text', 'customLocation', array(
            'label' => 'Where',
            'description' => Zend_Registry::get('Zend_Translate')->_('(Address, city, State or Country)'),
            'autocomplete' => 'off',
            'value' => '',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
        $this->customLocation->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        $this->addElement('Hidden', 'customLocationParams', array('order' => 800000));
        include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/customLocationElement.php';



        // Miles or KM
        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
        if ($flage) {
            $locationLable = "Within Kilometers";
            $locationOption = array(
                '0' => '',
                '1' => '1 Kilometer',
                '2' => '2 Kilometers',
                '5' => '5 Kilometers',
                '10' => '10 Kilometers',
                '20' => '20 Kilometers',
                '50' => '50 Kilometers',
                '100' => '100 Kilometers',
                '250' => '250 Kilometers',
                '500' => '500 Kilometers',
                '750' => '750 Kilometers',
                '1000' => '1000 Kilometers',
            );
        } else {
            $locationLable = "Within Miles";
            $locationOption = array(
                '0' => '',
                '1' => '1 Mile',
                '2' => '2 Miles',
                '5' => '5 Miles',
                '10' => '10 Miles',
                '20' => '20 Miles',
                '50' => '50 Miles',
                '100' => '100 Miles',
                '250' => '250 Miles',
                '500' => '500 Miles',
                '750' => '750 Miles',
                '1000' => '1000 Miles',
            );
        }


        // Location Miles
        $this->addElement('Select', 'customLocationMiles', array(
            'label' => $locationLable,
            'multiOptions' => $locationOption,
            'description' => Zend_Registry::get('Zend_Translate')->_('(Project Proximity)'),
            'value' => '0'
        ));
        $this->customLocationMiles->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));


//        $this->addElement('Checkbox', 'locationsearch', array(
//            'label' => 'Enter location search',
//            'value'=>'false',
//            'onclick' => 'locationSearch(this.value);'
//        ));

        //Buttons
        $this->addElement('Button', 'search_btn', array(
            'label' => 'Search',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Button', 'clear', array(
            'label' => 'Clear',
            'decorators' => array('ViewHelper'),

        ));


    }
}
