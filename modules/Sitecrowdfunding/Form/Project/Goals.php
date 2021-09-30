<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Goals extends Engine_Form {

    public $_error = array();


    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = $user->level_id;
        $viewer_id = $user->getIdentity();
        $this->loadDefaultDecorators();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //PACKAGE BASED CHECKS

        $this->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("Add New Goals using below form for this project.")))
            //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'sitecrowdfundings_Goal_create')
            ->getDecorator('Description')->setOption('escape', false);

        $this->setAttrib('id', 'sitecrowdfundings_Goal_create_form');
        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);

        $goals = Engine_Api::_()->getDbTable('sdggoals','sitecrowdfunding')->getSDGGoals();

        $targets = Engine_Api::_()->getDbTable('sdgtargets','sitecrowdfunding')->getSDGTargets();


//        $goals = array(
//            1 => "1. End poverty in all its forms everywhere",
//            2 => "2. End hunger, achieve food security and improved nutrition and promote sustainable agriculture",
//            3 => "3. Ensure healthy lives and promote well-being for all at all ages",
//            4 => "4. Ensure inclusive and equitable quality education and promote lifelong learning opportunities for all",
//            5 => "5. Achieve gender equality and empower all women and girls",
//            6 => "6. Ensure availability and sustainable management of water and sanitation for all",
//            7 => "7. Ensure access to affordable, reliable, sustainable and modern energy for all",
//            8 => "8. Promote sustained, inclusive and sustainable economic growth, full and productive employment and decent work for all",
//            9 => "9. Build resilient infrastructure, promote inclusive and sustainable industrialization and foster innovation",
//            10 => "10. Reduce inequality within and among countries",
//            11 => "11. Make cities and human settlements inclusive, safe, resilient and sustainable",
//            12 => "12. Ensure sustainable consumption and production patterns",
//            13 => "13. Take urgent action to combat climate change and its impacts",
//            14 => "14. Conserve and sustainably use the oceans, seas and marine resources for sustainable development",
//            15 => "15. Protect, restore and promote sustainable use of terrestrial ecosystems, sustainably manage forests, combat desertification, and halt and reverse land degradation and halt biodiversity loss",
//            16 => "16. Promote peaceful and inclusive societies for sustainable development, provide access to justice for all and build effective, accountable and inclusive institutions at all levels",
//            17 => "17. Strengthen the means of implementation and revitalize the global partnership for sustainable development"
//        );

        $this->addElement('Select', 'sdg_goal_id', array(
            'label' => 'Select Goal',
            'allowEmpty' => false,
            'required' => true,
            'multiOptions' => $goals,
        ));

        $this->addElement('Select', 'sdg_target_id', array(
            'label' => 'Select Target',
            'allowEmpty' => false,
            'required' => true,
            'multiOptions' => $targets,
        ));

//        $this->addElement('textarea', 'targets', array(
//            'label' => "Targets",
//            'required' => true,
//            'allowEmpty' => false,
//            'attribs' => array('rows' => 5),
//            'filters' => array(
//                'StripTags',
//                //new Engine_Filter_HtmlSpecialChars(),
//                new Engine_Filter_EnableLinks(),
//                new Engine_Filter_Censor(),
//            ),
//        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-goals', 'controller'=>'goals', 'project_id'=> $project_id), "sitecrowdfunding_extended", true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'cancel',
        ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
