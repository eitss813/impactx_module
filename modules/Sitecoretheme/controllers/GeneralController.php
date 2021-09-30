<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GeneralController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_GeneralController extends Core_Controller_Action_Standard
{
    public function videoAction()
    {
        $this->view->videoEmbedCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecoretheme.landing.highlights.videoEmbed', '');
    }

    public function videoUrlAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->view->url = $this->_getParam('url', null);
    }

    //ACTION FOR GET THE SEARCH RESULT BASED ON CORE SEARCH TABLE
    public function getSearchContentAction()
    {

        //GET SEARCHABLE TEXT FROM GLOBAL SEARCH BOX
        $text = $this->_getParam('text', null);
        $page_id = $this->_getParam('page_id', null);
        $initiative_id = $this->_getParam('initiative_id', null);
        $pos = strpos($text, '#');
        if (!empty($text)) {
            $values = array();
            $values['text'] = $text;
            $values['pagination'] = '';
            $values['resource_type'] = '';
            $values['limit'] = $this->_getParam('limit');

            if (!empty($page_id)) {
                $values['page_id'] = $page_id;
            } else {
                $values['page_id'] = null;
            }

            if (!empty($initiative_id)) {
                $values['initiative_id'] = $initiative_id;
            } else {
                $values['initiative_id'] = null;
            }

            $items = $this->getCustomSearchData($values);
        }
        $data = array();
        $dataSearchable = array();
        $i = 0;

        if (!empty($text)) {
            foreach ($items as $item) {

                $type = $item['type'];
                if (!Engine_Api::_()->hasItemType($type)) {
                    continue;
                }
                $item = $this->view->item($type, $item['id']);
                if (empty($item)) {
                    continue;
                }
                if ($item->getPhotoUrl() != '') {
                    $content_photo = $this->view->itemPhoto($item, 'thumb.icon');
                } else {
                    $content_photo = "<img src='" . $this->view->layout()->staticBaseUrl . "application/modules/Sitecoretheme/externals/images/nophoto_icon.png' alt='' />";
                }

                $i++;

                $resourceTitle = $item->getShortType();
                if ($type == 'user') {
                    $resourceTitle = 'member';
                }
                $iType = $this->view->translate(ucfirst($resourceTitle));
                if (is_array($iType) && isset($iType[0])) {
                    $iType = $iType[0];
                }
                $dataSearchable[] = array(
                    'label' => $item->getTitle(),
                    'type' => $iType,
                    'photo' => $content_photo,
                    'item_url' => $item->getHref(),
                    //	'total_count' => $count,
                    'count' => $i
                );
            }
            $realCount = $i;
            $data = $dataSearchable;
            if (empty($dataSearchable)) {
                $data[] = array(
                    'label' => $this->view->translate('No result found for "%s".', $text),
                    'type' => 'no_result_found',
                    'item_url' => 'no_result_found',
                    'search_text' => $text,
                );
            } else {
                $count = $realCount;
                $data[]['id'] = 'stopevent';
                $data[]['label'] = $this->_getParam('text');
                $data[$count]['item_url'] = 'seeMoreLink';
            }
            //	$data[$count]['total_count'] = $count;
        }


        return $this->_helper->json($data);
    }

    public function getCoreSearchData($params = array())
    {

        $SearchTable = Engine_Api::_()->getDbtable('search', 'core');
        $searchTableName = $SearchTable->info('name');
        $items = array();
        $text = trim($params['text']);
        if (!empty($text)) {
            $select = $SearchTable->select()
                ->setIntegrityCheck(false)
                ->from($SearchTable->info('name'), array('type', 'id', 'description', 'keywords'));
            $select->where("(`title` LIKE  ? OR `description` LIKE  ? OR `keywords` LIKE  ? OR `hidden` LIKE  ?)", "%$text%");

            $select = $select->limit($params['limit']);
            return $items = $SearchTable->fetchAll($select);
        } else {
            return $items;
        }
    }

    public function getCustomSearchData($params = array())
    {

        $projectsTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectsTableName = $projectsTable->info('name');

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pagesTableName = $pagesTable->info('name');

        $pageMembersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $pageMembersTableName = $pageMembersTable->info('name');

        $pagesAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $pagesAdminsTableName = $pagesAdminsTable->info('name');

        $initiativesTable = Engine_Api::_()->getDbtable('initiatives', 'sitepage');
        $initiativesTableName = $initiativesTable->info('name');

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');

        $items = array();
        $text = trim($params['text']);
        $page_id = trim($params['page_id']);
        $initiative_id = trim($params['initiative_id']);

        $currentDate = date('Y-m-d H:i:s');

        // if text only passed and page_id/initiative_id => null
        if (!empty($text) && empty($page_id) && empty($initiative_id)) {

            // project select
            $projectSelect = $projectsTable->select()
                ->setIntegrityCheck(false)
                ->from($projectsTableName, array('project_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                ->where("(`title` LIKE  ? OR `description` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
                ->where("state = ?", 'published')
                ->where("approved = ?", 1)
                ->where("start_date <= '$currentDate'");

            // page select
            $pagesSelect = $pagesTable->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('page_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_page'")))
                ->where("(`title` LIKE  ? OR `body` LIKE  ? OR `overview` LIKE  ? OR `location` LIKE  ?)", "%$text%")
                ->where('closed = ?', '0')
                ->where('approved = ?', '1')
                ->where('declined = ?', '0')
                ->where('draft = ?', '1');

            // initiative select
            $initiativeSelect = $initiativesTable->select()
                ->setIntegrityCheck(false)
                ->from($initiativesTableName, array('initiative_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_initiative'")))
                ->where("(`title` LIKE  ? OR `about` LIKE  ? OR `back_story` LIKE  ? OR `sections` LIKE  ?)", "%$text%");

            // user select
            $userSelect = $usersTable->select()
                ->setIntegrityCheck(false)
                ->from($usersTableName, array('user_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'user'")))
                ->where("(`username` LIKE  ? OR `displayname` LIKE  ? OR `email` LIKE  ? )", "%$text%");

            $db = Engine_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect))
                ->limit($params['limit']);

            return $items = $db->fetchAll($select);

        }

        // if text,page_id passed and initiative_id => null
        else if (!empty($text) && !empty($page_id) && empty($initiative_id)) {

            // get project belong to page
            $project_ids = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getPageProjects($page_id);

            // project select
            if(count($project_ids) > 0) {
                $projectSelect = $projectsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($projectsTableName, array('project_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                    ->where("(`title` LIKE  ? OR `description` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
                    ->where("state = ?", 'published')
                    ->where("approved = ?", 1)
                    ->where("start_date <= '$currentDate'")
                    ->where('project_id IN ( ? ) ', $project_ids);
            }else{
                // this is hack to prevent from union fix issue
                // this will not return any data, as we have used project_id=null
                $projectSelect = $projectsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($projectsTableName, array('project_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                    ->where("project_id is null");
            }

            // page select
            $pagesSelect = $pagesTable->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('page_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_page'")))
                ->where("(`title` LIKE  ? OR `body` LIKE  ? OR `overview` LIKE  ? OR `location` LIKE  ?)", "%$text%")
                ->where('closed = ?', '0')
                ->where('approved = ?', '1')
                ->where('declined = ?', '0')
                ->where('draft = ?', '1')
                ->where('page_id = ?', $page_id);

            // initiative select
            $initiativeSelect = $initiativesTable->select()
                ->setIntegrityCheck(false)
                ->from($initiativesTableName, array('initiative_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_initiative'")))
                ->where("(`title` LIKE  ? OR `about` LIKE  ? OR `back_story` LIKE  ? OR `sections` LIKE  ?)", "%$text%")
                ->where('page_id = ?', $page_id);

            // user select
            $userSelect = $usersTable->select()
                ->setIntegrityCheck(false)
                ->from($usersTableName, array('user_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'user'")))
                ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                ->joinleft($pagesAdminsTableName, "$pagesAdminsTableName.user_id = $usersTableName.user_id", array())
                ->where("$pageMembersTableName.page_id = ?", $page_id)
                ->where("$pagesAdminsTableName.page_id = ?", $page_id)
                ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");

            $db = Engine_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect))
                ->limit($params['limit']);

            return $items = $db->fetchAll($select);

        }

        // if all three passed
        else if (!empty($text) && !empty($page_id) && !empty($initiative_id)) {

            // get project belong to page
            $project_ids = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getPageProjects($page_id);

            // get initiative projects only
            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiative_id);
            $sections = preg_split('/[,]+/', $initiative['sections']);
            $sections = array_filter(array_map("trim", $sections));

            // project select
            if (count($sections) > 0) {

                // Tags
                $tagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                $tagMapTableName = $tagMapTable->info('name');

                $tagTable = Engine_Api::_()->getDbtable('Tags', 'core');
                $tagTableName = $tagTable->info('name');

                if(count($project_ids) > 0) {
                    $projectSelect = $projectsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($projectsTableName, array('project_id as id'))
                        ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                        ->joinleft($tagMapTableName, "$tagMapTableName.resource_id = $projectsTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                        ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array())
                        ->where("(`title` LIKE  ? OR `description` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
                        ->where("$projectsTableName.state = ?", 'published')
                        ->where("$projectsTableName.approved = ?", 1)
                        ->where("$projectsTableName.start_date <= '$currentDate'")
                        ->where($projectsTableName . '.project_id IN (?)', $project_ids)
                        ->where("$tagTableName.text IN (?) OR $projectsTableName.initiative_id=$initiative_id", $sections);
                }else{
                    // this is hack to prevent from union fix issue
                    // this will not return any data, as we have used project_id=null
                    $projectSelect = $projectsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($projectsTableName, array('project_id as id'))
                        ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                        ->where("project_id is null");
                }

            } else {
                if(count($project_ids) > 0) {
                    $projectSelect = $projectsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($projectsTableName, array('project_id as id'))
                        ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                        ->where("(`title` LIKE  ? OR `description` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
                        ->where("state = ?", 'published')
                        ->where("approved = ?", 1)
                        ->where("start_date <= '$currentDate'")
                        ->where('project_id IN ( ? ) ', $project_ids)
                        ->where("initiative_id = ?", $initiative_id);
                }else{
                    // this is hack to prevent from union fix issue
                    // this will not return any data, as we have used project_id=null
                    $projectSelect = $projectsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($projectsTableName, array('project_id as id'))
                        ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                        ->where("project_id is null");
                }
            }

            // page select
            $pagesSelect = $pagesTable->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('page_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_page'")))
                ->where("(`title` LIKE  ? OR `body` LIKE  ? OR `overview` LIKE  ? OR `location` LIKE  ?)", "%$text%")
                ->where('closed = ?', '0')
                ->where('approved = ?', '1')
                ->where('declined = ?', '0')
                ->where('draft = ?', '1')
                ->where('page_id = ?', $page_id);

            // initiative select
            $initiativeSelect = $initiativesTable->select()
                ->setIntegrityCheck(false)
                ->from($initiativesTableName, array('initiative_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_initiative'")))
                ->where("(`title` LIKE  ? OR `about` LIKE  ? OR `back_story` LIKE  ? OR `sections` LIKE  ?)", "%$text%")
                ->where('page_id = ?', $page_id)
                ->where('initiative_id = ?', $initiative_id);

            // user select
            $userSelect = $usersTable->select()
                ->setIntegrityCheck(false)
                ->from($usersTableName, array('user_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'user'")))
                ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                ->joinleft($pagesAdminsTableName, "$pagesAdminsTableName.user_id = $usersTableName.user_id", array())
                ->where("$pageMembersTableName.page_id = ?", $page_id)
                ->where("$pagesAdminsTableName.page_id = ?", $page_id)
                ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");

            $db = Engine_Db_Table::getDefaultAdapter();
            $select = $db->select()
                ->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect))
                ->limit($params['limit']);

            return $items = $db->fetchAll($select);
        }

        else {
            return $items;
        }
    }

    public function previewBlockAction()
    {
        $this->view->block_id = $blockId = $this->_getParam('id');
        $block = Engine_Api::_()->getDbtable('blocks', 'sitecoretheme')->getBlock($blockId);
        if (!$block) {
            throw new Core_Model_Exception('missing block');
        }
        $this->_helper->layout->setLayout('default-simple');
        $this->view->block = $block;
    }
}