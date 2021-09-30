<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Backproject.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Widget_Backproject extends Engine_Form {

    public function init() {
        $this->setMethod('post');
        $this->setTitle('Back Project');


        $this->addElement('Text', 'project_ids', array(
            'autocomplete' => 'off',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '/application/modules/Sitecrowdfunding/views/scripts/_chooseProjectBackWidgetSetting.tpl',
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
        $this->addElement('Text', 'backTitle', array(
            'label' => 'Enter the text to be displayed as link.', 
            'value' => 'Back This Project',
        )); 
    }
} 
  
