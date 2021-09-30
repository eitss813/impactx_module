<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metainfo.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Metainfo extends Engine_Form {

    public function init() {

        $this->setTitle('Meta Keywords')
                ->setDescription("Meta keywords are a great way to provide search engines with information about your project so that search engines populate your project in search results. Below, you can add meta keywords for this project. (The tags entered by you for this project will also be added to the meta keywords.)")
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'metainfo');

        $this->addElement('Textarea', 'keywords', array(
            'label' => 'Meta Keywords',
            'description' => 'Separate meta tags with commas.',
        ));

        $this->keywords->getDecorator('Description')->setOption('placement', 'append');
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Details',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
