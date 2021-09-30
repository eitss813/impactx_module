<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metainfo.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Metainfo extends Engine_Form {

    public function init() {

        $this->setTitle('Meta Keywords')
                ->setDescription("Meta keywords are a great way to provide search engines with information about your channel so that search engines populate your channel in search results. Below, you can add meta keywords for this channel. (The tags entered by you for this channel will also be added to the meta keywords.)")
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'metainfo');

        $this->addElement('Textarea', 'keywords', array(
            'label' => 'Meta Keywords',
            'description' => 'Separate meta tags with commas.',
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));

        $this->keywords->getDecorator('Description')->setOption('placement', 'append');
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Details',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
