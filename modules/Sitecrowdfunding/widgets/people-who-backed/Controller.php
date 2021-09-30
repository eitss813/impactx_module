<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_PeopleWhoBackedController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF VEWER IS EMPTY
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            return $this->setNoRender();
        }
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        $sitecrowdfundingPeopleWhoBacked = Zend_Registry::isRegistered('sitecrowdfundingPeopleWhoBacked') ? Zend_Registry::get('sitecrowdfundingPeopleWhoBacked') : null;
        //GET LIST SUBJECT
        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->project_id = $subject->project_id;
        $this->view->resource_type = $resource_type = $subject->getType();
        $this->view->resource_id = $resource_id = $subject->getIdentity();
        $this->view->options = $options = $this->_getParam('options', array('name', 'amount', 'totalCount'));
        if (empty($this->view->options) || !is_array($this->view->options))
            $this->view->options = $options = array();
        $params = array();
        $params['project_id'] = $resource_id;
        if (empty($sitecrowdfundingPeopleWhoBacked))
            return $this->setNoRender();
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $backers = $backersTable->getAllBackers($params);
        $this->view->backerCount = $backerCount = count($backers);
        if ($backerCount == 0)
            return $this->setNoRender();
        $this->view->backers = $backers;
    }

}
