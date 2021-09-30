<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: SearchController.php 9906 2013-02-14 02:54:51Z shaun $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_SearchController extends Core_Controller_Action_Standard
{
    public function indexAction()
    {
        $searchApi = Engine_Api::_()->getApi('search', 'core');

        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
        if (!$require_check) {
            if (!$this->_helper->requireUser()->isValid()) {
                return;
            }
        }

        // todo: 5.2.1 Upgrade => Added missing custom search functions which was present earlier
        // get params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        // show only projects tab if we search from goals/category
        if(isset($params['sdg_goal_id']) && $params['sdg_goal_id'] && ($params['sdg_goal_id'] != null) && ($params['sdg_goal_id'] != 0)){
            $params['tab_link'] = 'projects_tab';
        }else if(isset($params['category_id']) && $params['category_id'] && ($params['category_id'] != null) && ($params['category_id'] != 0)){
            $params['tab_link'] = 'projects_tab';
        }
        else{
            // get tab active id
            if (empty($params['tab_link'])) {
                $params['tab_link'] = 'all_tab';
            } else if (isset($params['tab_link']) && !empty($params['tab_link'])) {
                $params['tab_link'] = $params['tab_link'];
            } else {
                $params['tab_link'] = 'all_tab';
            }
        }


        // set page_id and initiative_id
        $page_id = null;
        $initiative_id = null;
        if ($params['type'] === 'everything_in_organization') {
            if (isset($params['searched_from_page']) && !empty($params['searched_from_page'])) {
                if ($params['searched_from_page'] == 'organisation' || $params['searched_from_page'] == 'initiative' || $params['searched_from_page'] == 'project') {
                    if (isset($params['searched_from_page_id']) && !empty($params['searched_from_page_id'])) {
                        $page_id = $params['searched_from_page_id'];
                    }
                }
                if ($params['searched_from_page'] == 'initiative') {
                    if (isset($params['searched_from_initiative_id']) && !empty($params['searched_from_initiative_id'])) {
                        $initiative_id = $params['searched_from_initiative_id'];
                    }
                }
            }
        }



        // Prepare form
        $this->view->form = $form = new Core_Form_PageSearch();

        // Add types = Everything In Organization
        $options = array();
        if (isset($params['searched_from_page']) && !empty($params['searched_from_page'])) {
            if (
                $params['searched_from_page'] == 'organisation' ||
                $params['searched_from_page'] == 'initiative' ||
                $params['searched_from_page'] == 'project') {
                $options['everything_in_organization'] = 'Everything in this Organization';
            }
        }
        if (count($options) > 0) {
            $form->type->addMultiOptions($options);
        }

        $form->page_no->setValue($params['page_no']);
        $form->tab_link->setValue($params['tab_link']);
        $form->searched_from_page->setValue($params['searched_from_page']);
        $form->searched_from_page_id->setValue($params['searched_from_page_id']);
        $form->searched_from_initiative_id->setValue($params['searched_from_initiative_id']);
        $form->searched_from_project_id->setValue($params['searched_from_project_id']);
        $form->query->setValue($params['query']);
        $form->type->setValue($params['type']);
        $form->selected_goal_id->setValue($params['sdg_goal_id']);
        $this->view->sdg_goal_id = $sdg_goal_id = $params['sdg_goal_id'];

        $form->selected_category_id->setValue($params['category_id']);
        $this->view->category_id = $category_id = $params['category_id'];


        $this->view->sdg_target_id = $params['sdg_target_id'];
        if(!empty($params['sdg_target_id'])){
            $sdg_target_id = explode(",",$params['sdg_target_id']);
        }else{
            $sdg_target_id = null;
        }

        $this->view->sdg_goal_id = $sdg_goal_id;

        // Filter based on text/tab/type/page_id/initiative_id
        if ($params['tab_link'] == 'members_tab') {
            $type = 'user';
        }
        if ($params['tab_link'] == 'projects_tab') {
            $type = 'sitecrowdfunding_project';
        }
        if ($params['tab_link'] == 'organizations_tab') {
            $type = 'sitecrowdfunding_organization';
        }
        if ($params['tab_link'] == 'initiatives_tab') {
            $type = 'sitepage_initiative';
        }
        $this->view->query = $query = $params['query'];
        $this->view->tab_link = $tab_link = $params['tab_link'];
        $this->view->params = $params;

        // get count
        $allTabData = $searchApi->getCustomPaginator($query, '', $page_id, $initiative_id, $sdg_goal_id, $sdg_target_id,$category_id);
        $this->view->alltabCount = $allTabData ? $allTabData->getTotalItemCount() : 0;

        $membersData = $searchApi->getCustomPaginator($query, 'user', $page_id, $initiative_id, $sdg_goal_id, $sdg_target_id,$category_id);
        $this->view->membersCount = $membersData ? $membersData->getTotalItemCount() : 0;

        $projectsData = $searchApi->getCustomPaginator($query, 'sitecrowdfunding_project', $page_id, $initiative_id, $sdg_goal_id, $sdg_target_id,$category_id);

        $this->view->projectsCount = $projectsData ? $projectsData->getTotalItemCount() : 0;

        $organisationData = $searchApi->getCustomPaginator($query, 'sitecrowdfunding_organization', $page_id, $initiative_id, $sdg_goal_id, $sdg_target_id,$category_id);
        $this->view->organisationCount = $organisationData ? $organisationData->getTotalItemCount() : 0;

        $initiativeData = $searchApi->getCustomPaginator($query, 'sitepage_initiative', $page_id, $initiative_id, $sdg_goal_id, $sdg_target_id,$category_id);
        $this->view->initiativesCount = $initiativeData ? $initiativeData->getTotalItemCount() : 0;

        $this->view->paginator = $searchApi->getCustomPaginator($query, $type, $page_id, $initiative_id, $sdg_goal_id, $sdg_target_id,$category_id);
        $this->view->paginator->setCurrentPageNumber($params['page_no']);

        $this->view->params = $params;

    }
}
