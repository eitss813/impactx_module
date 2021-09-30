<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Custom_Fields extends Fields_Form_Standard {

    public $_error = array();
    protected $_name = 'fields';
    protected $_elementsBelongTo = 'fields';

    public function init() {
        if (!$this->_item) {
            $project_item = new Sitecrowdfunding_Model_Project(array());
            $this->setItem($project_item);
        }
        parent::init();

        $this->removeElement('submit');
    }

    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this
                    ->addDecorator('FormElements');
        }
    }

}