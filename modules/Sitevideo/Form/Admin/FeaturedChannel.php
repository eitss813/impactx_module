<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FeaturedChannel.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_FeaturedChannel extends Engine_Form {

    protected $_field;

    public function init() {
        $this->setMethod('post');
        $this->setTitle('Add a Channel as Featured')
                ->setDescription('Using the auto-suggest field below, choose the channel to be made featured');
        // init to
        $label = new Zend_Form_Element_Text('title');
        $label->setLabel('Channel Title (Use this auto-suggest channel title box to select the channel that you want to make featured.)')
                ->addValidator('NotEmpty')
                ->setRequired(true)
                ->setAttrib('class', 'text')
                ->setAttrib('style', 'width:300px;');

        $tagline1 = new Zend_Form_Element_Text('tagline1', array('label' => 'Tagline1', 'style' => 'width:300px;'));
        $tagline2 = new Zend_Form_Element_Text('tagline2', array('label' => 'Tagline2', 'style' => 'width:300px;'));
        $taglineDesc = new Zend_Form_Element_Textarea('tagline_description', array('label' => 'Tagline Description'));
        $url = new Zend_Form_Element_Text('url', array('label' => 'URL [Enter the URL where you want to redirect the users upon clicking on this featured channel.]', 'style' => 'width:300px;'));
        $this->addElements(array(
            $label,
            $tagline1,
            $tagline2,
            $taglineDesc,
            $url
        ));
        $this->addElement('Hidden', 'resource_id', array(
            'allowEmpty' => false,
            'required' => true,
            'order' => 10001,
            'validators' => array(
                array('NotEmpty', true),
            ),
        ));
        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Make Featured',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}
