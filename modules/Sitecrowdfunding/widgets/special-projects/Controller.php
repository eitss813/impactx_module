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
class Sitecrowdfunding_Widget_SpecialProjectsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $starttime = $this->_getParam('starttime');
        $endtime = $this->_getParam('endtime');
        $currenttime = date('Y-m-d H:i:s');

        if (!empty($starttime) && $currenttime < $starttime) {
            return $this->setNoRender();
        }

        if (!empty($endtime) && $currenttime > $endtime) {
            return $this->setNoRender();
        }
        $sitecrowdfundingSpecialProjects = Zend_Registry::isRegistered('sitecrowdfundingSpecialProjects') ? Zend_Registry::get('sitecrowdfundingSpecialProjects') : null;
        $params = array();
        $params['project_ids'] = $this->_getParam('toValues', array());
        if (empty($params['project_ids']))
            return $this->setNoRender();
        $params['project_ids'] = explode(',', $params['project_ids']);
        $params['project_ids'] = array_unique($params['project_ids']);
        if (count($params['project_ids']) <= 0)
            return $this->setNoRender();
        $params['limit'] = $this->_getParam('itemCount');
        $this->view->projectWidth = $this->_getParam('columnWidth', 150);
        $this->view->projectHeight = $this->_getParam('columnHeight', 150);
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 20);
        if (empty($sitecrowdfundingSpecialProjects))
            return $this->setNoRender();
        $this->view->descriptionTruncation = $this->_getParam('descriptionTruncation', 40);
        $this->view->projectInfo = $this->_getParam('projectOption');
        if (empty($this->view->projectInfo) || !is_array($this->view->projectInfo)) {
            $this->view->projectInfo = array();
        }
        $this->view->itemCount = $params['limit'];
        //GET PROJECTS
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding')->getProjectPaginator($params);

        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        $paginator->setItemCountPerPage($params['limit']);
    }

}
