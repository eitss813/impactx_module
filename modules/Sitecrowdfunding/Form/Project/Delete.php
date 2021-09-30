<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Delete extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Delete Project')
                ->setDescription('Are you sure you want to delete this Project?')
                ->setMethod('POST')
                ->setAction($_SERVER['REQUEST_URI'])
                ->setAttrib('class', 'global_form_popup')
        ;

        $this->addElement('Button', 'execute', array(
            'label' => 'Delete Project',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        /*$this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));*/

        $this->addDisplayGroup(array(
            'execute'
           // 'cancel'
                ), 'buttons');
    }

}
