<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepage_Widget_PagePeoplesController extends Seaocore_Content_Widget_Abstract
{

    protected $_childCount;

    public function indexAction(){

        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        $this->view->page_id = $page_id = $sitepage->getIdentity();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $this->view->peopleNavigationLink = $params['peopleNavigationLink'] = $this->_getParam('peopleNavigationLink');
        if (empty($this->view->peopleNavigationLink) || !is_array($this->view->peopleNavigationLink)) {
            $this->view->peopleNavigationLink = $params['peopleNavigationLink'] = array();
        }

        if (count($this->view->peopleNavigationLink) <= 0) {
            return $this->setNoRender();
        }

        if (isset($params['is_ajax'])) {
            $this->view->is_ajax = $params['is_ajax'];
        } else {
            $this->view->is_ajax = $params['is_ajax'] = false;
        }

        $params['tab'] = $request->getParam('tab', null);

        if (isset($params['link']) && !empty($params['link'])) {
            $params['tab'] = '';
            $currentLink = $params['link'];
        } else if (is_array($this->view->peopleNavigationLink)) {
            $currentLink = $params['link'] = $params['peopleNavigationLink'][0];
        } else {
            $currentLink = $params['link'] = 'followed';
        }

        $this->view->widgetPath = 'widget/index/mod/sitepage/name/page-peoples';
        $this->view->controllerName = $params['controller'];
        $this->view->actionName = $params['action'];

        $this->view->paginator = $paginator = $this->getDataByLink($params);
        $this->view->params = $params;

        $this->view->pendingInvites = $pendingInvites = Engine_Api::_()->getDbtable('invites', 'invite')->getCustomORGPendingInvites($page_id);

    }

    public function getDataByLink($params) {
        $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        $paginator = array();
        $currentLink = $params['link'];
        switch ($currentLink) {
            case 'all':
                $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                $values = array();
                $values['page_id'] = $sitepage->page_id;
                $paginator = $membershipTable->getsitepageAllmembersSelect($values);
                return $paginator;
                break;
            case 'joined':
                $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                $values = array();
                $values['orderby'] = 'join_date';
                $values['page_id'] = $sitepage->page_id;
                $paginator = $membershipTable->getSitepagemembersCustomPaginator($values);
                return $paginator;
                break;
            case 'followed':
                $paginator = Engine_Api::_()->getDbTable('follows', 'seaocore')->getAllFollowsUsers($sitepage);
                return $paginator;
                break;
            case 'creator':
                $paginator[1]='create';
                return $paginator;
                break;
            case 'admin':
                $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
                $values = array();
                $values['orderby'] = 'pageadmin';
                $values['page_id'] = $sitepage->page_id;
                $paginator = $membershipTable->getSitepagemembersPaginator($values);
                return $paginator;
                break;
            default:
                break;
        }

        return $paginator;
    }

}
