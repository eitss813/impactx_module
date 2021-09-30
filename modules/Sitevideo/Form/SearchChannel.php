<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SearchChannel.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_SearchChannel extends Engine_Form {

    public function init() {

        $this
                ->setAttribs(array(
                    'method' => 'GET',
                    'id' => 'searchBox'
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->setAction($view->url(array('action' => 'manage'), "sitevideo_subscription_general", true))->getDecorator('HtmlTag');

        $this->addElement('Text', 'search', array(
            'label' => '',
            'placeholder' => $view->translate('Search...'),
            'autocomplete' => 'off',
            'style' => "500px;",
            'filters' => array(
              new Engine_Filter_Censor(),
              'StripTags',
            ),
        ));

        if (isset($_GET['search'])) {
            $this->search->setValue($_GET['search']);
        } elseif (isset($_GET['search'])) {
            $this->search->setValue($_GET['search']);
        }
        $this->addElement('Button', 'submitButton', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}
