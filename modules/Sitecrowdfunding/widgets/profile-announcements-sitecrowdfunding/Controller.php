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
class Sitecrowdfunding_Widget_ProfileAnnouncementsSitecrowdfundingController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER THIS IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.announcement', 1)) {
            return $this->setNoRender();
        }

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET SITECROWDFUNDING SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        $this->view->content_id = Engine_Api::_()->sitecrowdfunding()->existWidget('sitecrowdfunding.profile-announcements-sitecrowdfunding');

        $limit = $this->_getParam('itemCount', 3);
        $this->view->showTitle = $this->_getParam('showTitle', 1);
        $sitecrowdfundingAnnouncements = Zend_Registry::isRegistered('sitecrowdfundingAnnouncements') ? Zend_Registry::get('sitecrowdfundingAnnouncements') : null;
        $fetchColumns = array('announcement_id', 'title', 'body');
        $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'sitecrowdfunding')->announcements($project->project_id, 0, $limit, $fetchColumns);
        $this->_childCount = count($this->view->announcements);
        if (empty($sitecrowdfundingAnnouncements))
            return $this->setNoRender();
        if ($this->_childCount <= 0) {
            return $this->setNoRender();
        }
    }

    public function getChildCount() {

        return $this->_childCount;
    }

}
