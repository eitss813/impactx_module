<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Search.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Api_Search extends Core_Api_Abstract
{
    protected $_types;

    public function index(Core_Model_Item_Abstract $item)
    {
        // Check if not search allowed
        if( isset($item->search) && !$item->search )
        {
            return false;
        }

        // Get info
        $type = $item->getType();
        $id = $item->getIdentity();
        $title = substr(trim($item->getTitle()), 0, 255);
        $description = substr(trim($item->getDescription()), 0, 255);
        $keywords = substr(trim($item->getKeywords()), 0, 255);
        $hiddenText = substr(trim($item->getHiddenSearchData()), 0, 255);

        // Ignore if no title and no description
        if( !$title && !$description )
        {
            return false;
        }

        // Check if already indexed
        $table = Engine_Api::_()->getDbtable('search', 'core');
        $select = $table->select()
            ->where('type = ?', $type)
            ->where('id = ?', $id)
            ->limit(1);

        $row = $table->fetchRow($select);

        if( null === $row )
        {
            $row = $table->createRow();
            $row->type = $type;
            $row->id = $id;
        }

        $row->title = $title;
        $row->description = $description;
        $row->keywords = $keywords;
        $row->hidden = $hiddenText;
        $row->save();
    }

    public function unindex(Core_Model_Item_Abstract $item)
    {
        $table = Engine_Api::_()->getDbtable('search', 'core');

        $table->delete(array(
            'type = ?' => $item->getType(),
            'id = ?' => $item->getIdentity(),
        ));

        return $this;
    }

    public function getPaginator($text, $type = null)
    {
        return Zend_Paginator::factory($this->getSelect($text, $type));
    }

    // todo: 5.2.1 Upgrade => Added missing functions which was present earlier
    public function getCustomPaginator($text,$type,$page_id,$initiative_id,$sdg_goal_id,$sdg_target_id,$category_id)
    {
        $params = array();
        $params['text'] = $text;
        $params['type'] = $type;
        $params['page_id'] = $page_id;
        $params['initiative_id'] = $initiative_id;
        $params['sdg_goal_id'] = $sdg_goal_id;
        $params['sdg_target_id'] = $sdg_target_id;
        $params['category_id'] = $category_id;
        return Zend_Paginator::factory($this->getCustomSearchData($params));
    }

    public function getSelect($text, $type = null)
    {
        // Build base query
        $table = Engine_Api::_()->getDbtable('search', 'core');
        $db = $table->getAdapter();
        $select = $table->select()
            ->where(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (? IN BOOLEAN MODE)', $text)))
            ->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)));

        // Filter by item types
        $availableTypes = Engine_Api::_()->getItemTypes();

        // todo: 5.2.1 Upgrade => Added missing dropdown which was present earlier
        $availableTypesFinal=array();
        $i=0;
        $c=0;
        foreach ($availableTypes as  $value) {
            if($value=='user' ||
                $value=='sitecrowdfunding_project' ||
                $value=='sitecrowdfunding_organization'  ||
                $value=='sitepage_initiative'  ) {
                $availableTypesFinal[$c]=$value;$c++;
            }
            $i++;
        }

        if( $type && in_array($type, $availableTypesFinal) ) {
            $select->where('type = ?', $type);
        } else {
            $select->where('type IN(?)', $availableTypesFinal);
        }

        return $select;
    }

    public function getAvailableTypes()
    {
        if( null === $this->_types ) {
            $this->_types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
                ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
                ->fetchAll(Zend_Db::FETCH_COLUMN);
            $this->_types = array_intersect($this->_types, Engine_Api::_()->getItemTypes());
        }

        // todo: 5.2.1 Upgrade => Added missing dropdown which was present earlier
        $typesTemp = $this->_types;
        //  //print_r($availableTypes);
        $availableTypesFinal=array();$i=0;$c=0;
        foreach ($typesTemp as  $value) {
            //  //print_r($value);
            if($value=='user' || $value=='sitecrowdfunding_project' || $value=='sitecrowdfunding_organization'
                || $value=='sitepage_initiative'  ) {

                $availableTypesFinal[$c]=$value;$c++;
            }

            $i++;
        }

        return $availableTypesFinal;
    }

    // todo: 5.2.1 Upgrade => Added missing functions which was present earlier
    public function getCustomSearchData($params = array())
    {

        $projectsTable = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
        $projectsTableName = $projectsTable->info('name');

        $goalsTable = Engine_Api::_()->getDbtable('goals', 'sitecrowdfunding');
        $goalsTableName = $goalsTable->info('name');

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pagesTableName = $pagesTable->info('name');

        $initiativesTable = Engine_Api::_()->getDbtable('initiatives', 'sitepage');
        $initiativesTableName = $initiativesTable->info('name');

        $usersTable = Engine_Api::_()->getDbtable('users', 'user');
        $usersTableName = $usersTable->info('name');


        $pageMembersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
        $pageMembersTableName = $pageMembersTable->info('name');



        $items = array();
        $text = $params['text'];
        $type = $params['type'];
        $page_id = $params['page_id'];
        $initiative_id = $params['initiative_id'];
        $sdg_goal_id = $params['sdg_goal_id'];
        $sdg_target_id = $params['sdg_target_id'];
        $category_id = $params['category_id'];

        $currentDate = date('Y-m-d H:i:s');

        $db = Engine_Db_Table::getDefaultAdapter();

        /*****
        if text != null
         ******/
        // if page_id = null & initiative_id = null
        if (!empty($text) && empty($page_id) && empty($initiative_id)) {

            //print_r('case1');

            // project select
            $projectSelect = $projectsTable->select()
                ->setIntegrityCheck(false)
                ->from($projectsTableName, array('project_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                ->where("(`title` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
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


            // if sdg_goal_id != null or sdg_target_id != null
            // Hack to prevent initiatives/users/pages tab to list its data when goal is passed
            if(
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0 ))
                ||
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0))
            ){
                $pagesSelect->where("page_id is null");
                $initiativeSelect->where("initiative_id is null");
                $userSelect->where("user_id is null");
            }

            // projects filter based on sdg_goal_id or sdg_target_id
            // if sdg_goal_id != null or sdg_target_id = null
            if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id);
            }
            // if sdg_goal_id != null or sdg_target_id != null
            else if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id)
                    ->where("$goalsTableName.sdg_target_id in (?)", $sdg_target_id);
            }

            // if category_id is passed
            if($category_id != 'null' && $category_id != ""){
                $projectSelect->where("$projectsTableName.category_id = ?", $category_id);
            }

            if($type=='user') {
                $select = $db->select()->union(array($userSelect));
            }
            else if($type=='sitecrowdfunding_project') {
                $select = $db->select()->union(array($projectSelect));
            }
            else if($type=='sitecrowdfunding_organization' ) {
                $select = $db->select()->union(array($pagesSelect));
            }
            else if($type=='sitepage_initiative' ) {
                $select = $db->select()->union(array($initiativeSelect));
            }
            else {
                $select = $db->select()->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect));
            }
            return $select;
        }

        // if page_id != null and initiative_id = null
        else if (!empty($text) && !empty($page_id) && empty($initiative_id)) {

            //print_r('case2');

            // get project belong to page
            $project_ids = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getPageProjects($page_id);

            // project select
            if(count($project_ids) > 0){
                $projectSelect = $projectsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($projectsTableName, array('project_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                    ->where("(`title` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
                    ->where("state = ?", 'published')
                    ->where("approved = ?", 1)
                    ->where("start_date <= '$currentDate'")
                    ->where("$projectsTableName.project_id IN ( ? ) ", $project_ids);
            }else{
                // this is hack to prevent from union fix issue
                // this will not return any data, as we have used project_id=null
                $projectSelect = $projectsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($projectsTableName, array('project_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                    ->where("$projectsTableName.project_id is null");
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
//            $userSelect = $usersTable->select()
//                ->setIntegrityCheck(false)
//                ->from($usersTableName, array('user_id as id'))
//                ->columns(array("type" => new Zend_Db_Expr("'user'")))
//                ->where("(`username` LIKE  ? OR `displayname` LIKE  ? OR `email` LIKE  ? )", "%$text%");

            if($page_id  && !$text && !$sdg_goal_id) {

                $userSelect = $usersTable->select()
                    ->setIntegrityCheck(false)
                    ->from($usersTableName, array('user_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
                    ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                    ->where("$pageMembersTableName.page_id = ?", $page_id)
                    ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");

            }
            else if( $text && !$sdg_goal_id) {

                $userSelect = $usersTable->select()
                    ->setIntegrityCheck(false)
                    ->from($usersTableName, array('user_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
                    ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                    ->where("$pageMembersTableName.page_id = ?", $page_id)
                    ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");

            }
            else {

//                $userSelect = $usersTable->select()
//                    ->setIntegrityCheck(false)
//                    ->from($usersTableName, array('user_id as id'))
//                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
//                    ->where("(`username` LIKE  ? OR `displayname` LIKE  ? OR `email` LIKE  ? )", "%$text%");
                $userSelect = $usersTable->select()
                    ->setIntegrityCheck(false)
                    ->from($usersTableName, array('user_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
                    ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                    ->where("$pageMembersTableName.page_id = ?", $page_id)
                    ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");


            }

            // if sdg_goal_id != null or sdg_target_id != null
            // Hack to prevent initiatives/users/pages tab to list its data when goal is passed
            if(
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0 ))
                ||
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0))
            ){
                $pagesSelect->where("page_id is null");
                $initiativeSelect->where("initiative_id is null");
                $userSelect->where("user_id is null");
            }

            // projects filter based on sdg_goal_id or sdg_target_id
            // if sdg_goal_id != null or sdg_target_id = null
            if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id);
            }
            // if sdg_goal_id != null or sdg_target_id != null
            else if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id)
                    ->where("$goalsTableName.sdg_target_id in (?)", $sdg_target_id);
            }

            // if category_id is passed
            if($category_id != 'null' && $category_id != ""){
                $projectSelect->where("$projectsTableName.category_id = ?", $category_id);
            }

            if($type=='user') {
                $select = $db->select()->union(array($userSelect));
            }
            else if($type=='sitecrowdfunding_project') {
                $select = $db->select()->union(array($projectSelect));
            }
            else if($type=='sitecrowdfunding_organization' ) {
                $select = $db->select()->union(array($pagesSelect));
            }
            else if($type=='sitepage_initiative' ) {
                $select = $db->select()->union(array($initiativeSelect));
            }
            else {
                $select = $db->select()->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect));
            }
            return $select;

        }

        // if page_id != null and initiative_id != null
        else if (!empty($text) && !empty($page_id) && !empty($initiative_id)) {

            //print_r('case3');

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

                // project select
                if(count($project_ids) > 0) {
                    $projectSelect = $projectsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($projectsTableName, array('project_id as id'))
                        ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                        ->joinleft($tagMapTableName, "$tagMapTableName.resource_id = $projectsTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                        ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array())
                        ->where("(`title` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
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
                        ->where("(`title` LIKE  ? OR `desire_desc` LIKE  ? OR `help_desc` LIKE  ?)", "%$text%")
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
//            $userSelect = $usersTable->select()
//                ->setIntegrityCheck(false)
//                ->from($usersTableName, array('user_id as id'))
//                ->columns(array("type" => new Zend_Db_Expr("'user'")))
//                ->where("(`username` LIKE  ? OR `displayname` LIKE  ? OR `email` LIKE  ? )", "%$text%");
            if($page_id  && !$text && !$sdg_goal_id) {

                $userSelect = $usersTable->select()
                    ->setIntegrityCheck(false)
                    ->from($usersTableName, array('user_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
                    ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                    ->where("$pageMembersTableName.page_id = ?", $page_id)
                    ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");

            }
            else if( $text && !$sdg_goal_id) {

                $userSelect = $usersTable->select()
                    ->setIntegrityCheck(false)
                    ->from($usersTableName, array('user_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
                    ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                    ->where("$pageMembersTableName.page_id = ?", $page_id)
                    ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");

            }
            else {

//                $userSelect = $usersTable->select()
//                    ->setIntegrityCheck(false)
//                    ->from($usersTableName, array('user_id as id'))
//                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
//                    ->where("(`username` LIKE  ? OR `displayname` LIKE  ? OR `email` LIKE  ? )", "%$text%");
                $userSelect = $usersTable->select()
                    ->setIntegrityCheck(false)
                    ->from($usersTableName, array('user_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'user'")))
                    ->joinleft($pageMembersTableName, "$pageMembersTableName.user_id = $usersTableName.user_id", array())
                    ->where("$pageMembersTableName.page_id = ?", $page_id)
                    ->where("($usersTableName.username LIKE  ? OR $usersTableName.displayname LIKE  ? OR $usersTableName.email LIKE  ? )", "%$text%");


            }


            // if sdg_goal_id != null or sdg_target_id != null
            // Hack to prevent initiatives/users/pages tab to list its data when goal is passed
            if(
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0 ))
                ||
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0))
            ){
                $pagesSelect->where("page_id is null");
                $initiativeSelect->where("initiative_id is null");
                $userSelect->where("user_id is null");
            }

            // projects filter based on sdg_goal_id or sdg_target_id
            // if sdg_goal_id != null or sdg_target_id = null
            if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id);
            }
            // if sdg_goal_id != null or sdg_target_id != null
            else if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id)
                    ->where("$goalsTableName.sdg_target_id in (?)", $sdg_target_id);
            }

            // if category_id is passed
            if($category_id != 'null' && $category_id != ""){
                $projectSelect->where("$projectsTableName.category_id = ?", $category_id);
            }

            if($type=='user') {
                $select = $db->select()->union(array($userSelect));
            }
            else if($type=='sitecrowdfunding_project') {
                $select = $db->select()->union(array($projectSelect));
            }
            else if($type=='sitecrowdfunding_organization' ) {
                $select = $db->select()->union(array($pagesSelect));
            }
            else if($type=='sitepage_initiative' ) {
                $select = $db->select()->union(array($initiativeSelect));
            }
            else {
                $select = $db->select()->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect));
            }
            return $select;
        }


        /******
        if text = null
         ******/
        // if page_id != null and initiative_id != null
        else if (empty($text) && !empty($page_id) && !empty($initiative_id)) {

            //print_r('case4');

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

                // project select
                if(count($project_ids) > 0) {
                    $projectSelect = $projectsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($projectsTableName, array('project_id as id'))
                        ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                        ->joinleft($tagMapTableName, "$tagMapTableName.resource_id = $projectsTableName.project_id and " . $tagMapTableName . ".resource_type = 'sitecrowdfunding_project'", array())
                        ->joinleft($tagTableName, "$tagTableName.tag_id = $tagMapTableName.tag_id", array())
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
                        ->where("$projectsTableName .project_id is null");
                }

            } else {
                if(count($project_ids) > 0) {
                    $projectSelect = $projectsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($projectsTableName, array('project_id as id'))
                        ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
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
                        ->where("$projectsTableName .project_id is null");
                }
            }

            // page select
            $pagesSelect = $pagesTable->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('page_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_page'")))
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
                ->where('page_id = ?', $page_id)
                ->where('initiative_id = ?', $initiative_id);

            // user select
            $userSelect = $usersTable->select()
                ->setIntegrityCheck(false)
                ->from($usersTableName, array('user_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'user'")));


            // if sdg_goal_id != null or sdg_target_id != null
            // Hack to prevent initiatives/users/pages tab to list its data when goal is passed
            if(
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0 ))
                ||
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0))
            ){
                $pagesSelect->where("page_id is null");
                $initiativeSelect->where("initiative_id is null");
                $userSelect->where("user_id is null");
            }

            // projects filter based on sdg_goal_id or sdg_target_id
            // if sdg_goal_id != null or sdg_target_id = null
            if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0) ){
//            $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
//                ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id);
                $goalSelect = $goalsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($goalsTableName, array('project_id'))
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id);


                $results = $goalsTable->fetchAll($goalSelect);
                foreach( $results as $row ) {
                    $items[] = $row['project_id'];
                }
                $proIds = array_unique($items);

                if(count($results)) {
                    $projectSelect->where('project_id IN ( ? ) ', $proIds);
                }

            }
            // if sdg_goal_id != null or sdg_target_id != null
            else if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0) ){
                $goalSelect = $goalsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($goalsTableName, array('project_id'))
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id)
                    ->where("$goalsTableName.sdg_target_id in (?)", $sdg_target_id);

                $results = $goalsTable->fetchAll($goalSelect);
                foreach( $results as $row ) {
                    $items[] = $row['project_id'];
                }
                $proIds = array_unique($items);

                if(count($results)) {
                    $projectSelect->where('project_id IN ( ? ) ', $proIds);
                }


            }

            // if category_id is passed
            if($category_id != 'null' && $category_id != ""){
                $projectSelect->where("$projectsTableName.category_id = ?", $category_id);
            }

            // if goal passed then get only projects
            if($type=='user') {
                $select = $db->select()->union(array($userSelect));
            }
            else if($type=='sitecrowdfunding_project') {
                $select = $db->select()->union(array($projectSelect));
            }
            else if($type=='sitecrowdfunding_organization' ) {
                $select = $db->select()->union(array($pagesSelect));
            }
            else if($type=='sitepage_initiative' ) {
                $select = $db->select()->union(array($initiativeSelect));
            }
            else {
                $select = $db->select()->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect));
            }

            return $select;
        }

        // if page_id != null and initiative_id = null
        else if (empty($text) && !empty($page_id) && empty($initiative_id)) {

            //print_r('case5');

            // get project belong to page
            $project_ids = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getPageProjects($page_id);

            if(count($project_ids) > 0) {
                $projectSelect = $projectsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($projectsTableName, array('project_id as id'))
                    ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                    ->where("state = ?", 'published')
                    ->where("approved = ?", 1)
                    ->where("start_date <= '$currentDate'")
                    ->where("$projectsTableName.project_id IN ( ? ) ", $project_ids)
                    ->group("$projectsTableName.project_id");
            }

            // page select
            $pagesSelect = $pagesTable->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('page_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_page'")))
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
                ->where('page_id = ?', $page_id);

            // user select
            $userSelect = $usersTable->select()
                ->setIntegrityCheck(false)
                ->from($usersTableName, array('user_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'user'")));


            // if sdg_goal_id != null or sdg_target_id != null
            // Hack to prevent initiatives/users/pages tab to list its data when goal is passed
            if(
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0 ))
                ||
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0))
            ){
                $pagesSelect->where("page_id is null");
                $initiativeSelect->where("initiative_id is null");
                $userSelect->where("user_id is null");
            }

            // projects filter based on sdg_goal_id or sdg_target_id
            // if sdg_goal_id != null or sdg_target_id = null
            if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id);
            }
            // if sdg_goal_id != null or sdg_target_id != null
            else if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id)
                    ->where("$goalsTableName.sdg_target_id in (?)", $sdg_target_id);
            }

            // if category_id is passed
            if($category_id != 'null' && $category_id != ""){
                $projectSelect->where("$projectsTableName.category_id = ?", $category_id);
            }

            // if goal passed then get only projects
            if($type=='user') {
                $select = $db->select()->union(array($userSelect));
            }
            else if($type=='sitecrowdfunding_project') {
                $select = $db->select()->union(array($projectSelect));
            }
            else if($type=='sitecrowdfunding_organization' ) {
                $select = $db->select()->union(array($pagesSelect));
            }
            else if($type=='sitepage_initiative' ) {
                $select = $db->select()->union(array($initiativeSelect));
            }
            else {
                $select = $db->select()->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect));
            }

            return $select;
        }

        // if page_id = null & initiative_id = null
        else if (empty($text) && empty($page_id) && empty($initiative_id)){

            //print_r('case6');

            // projects select
            $projectSelect = $projectsTable->select()
                ->setIntegrityCheck(false)
                ->from($projectsTableName, array('project_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitecrowdfunding_project'")))
                ->where("state = ?", 'published')
                ->where("approved = ?", 1)
                ->where("start_date <= '$currentDate'");

            // page select
            $pagesSelect = $pagesTable->select()
                ->setIntegrityCheck(false)
                ->from($pagesTableName, array('page_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_page'")))
                ->where('closed = ?', '0')
                ->where('approved = ?', '1')
                ->where('declined = ?', '0')
                ->where('draft = ?', '1');

            // initiative select
            $initiativeSelect = $initiativesTable->select()
                ->setIntegrityCheck(false)
                ->from($initiativesTableName, array('initiative_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'sitepage_initiative'")));

            // user select
            $userSelect = $usersTable->select()
                ->setIntegrityCheck(false)
                ->from($usersTableName, array('user_id as id'))
                ->columns(array("type" => new Zend_Db_Expr("'user'")));


            // if sdg_goal_id != null or sdg_target_id != null
            // Hack to prevent initiatives/users/pages tab to list its data when goal is passed
            if(
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0 ))
                ||
                ( (!empty($sdg_goal_id) && $sdg_goal_id != 'null') && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0))
            ){
                $pagesSelect->where("page_id is null");
                $initiativeSelect->where("initiative_id is null");
                $userSelect->where("user_id is null");
            }

            // projects filter based on sdg_goal_id or sdg_target_id
            // if sdg_goal_id != null or sdg_target_id = null
            if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (empty($sdg_target_id) || $sdg_target_id == 'null' || count($sdg_target_id) <= 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id);
            }
            // if sdg_goal_id != null or sdg_target_id != null
            else if( (!empty($sdg_goal_id) && $sdg_goal_id != 'null')  && (!empty($sdg_target_id) && $sdg_target_id !== 'null' && count($sdg_target_id) > 0) ){
                $projectSelect->joinInner($goalsTableName, "$goalsTableName.project_id = $projectsTableName.project_id", null)
                    ->where("$goalsTableName.sdg_goal_id = ?", $sdg_goal_id)
                    ->where("$goalsTableName.sdg_target_id in (?)", $sdg_target_id);
            }

            if($category_id != 'null' && $category_id != ""){
                $projectSelect->where("$projectsTableName.category_id = ?", $category_id);
            }

            // if goal passed then get only projects
            if($type=='user') {
                $select = $db->select()->union(array($userSelect));
            }
            else if($type=='sitecrowdfunding_project') {
                $select = $db->select()->union(array($projectSelect));
            }
            else if($type=='sitecrowdfunding_organization' ) {
                $select = $db->select()->union(array($pagesSelect));
            }
            else if($type=='sitepage_initiative' ) {
                $select = $db->select()->union(array($initiativeSelect));
            }
            else {
                $select = $db->select()->union(array($projectSelect, $pagesSelect, $initiativeSelect, $userSelect));
            }

            return $select;
        }

        else {
            return $items;
        }

    }
}
