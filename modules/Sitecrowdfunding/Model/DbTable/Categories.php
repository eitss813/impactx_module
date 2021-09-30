<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Categories.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_DbTable_Categories extends Engine_Db_Table {

    protected $_name = 'sitecrowdfunding_categories';
    protected $_rowClass = 'Sitecrowdfunding_Model_Category';
    protected $_categories = array();

    public function getCategoriesPaginator(array $params) {
        return Zend_Paginator::factory($this->getCategories(null, $params));
    }

    /**
     * Return subcaregories
     *
     * @param int category_id
     * @return all sub categories
     */
    public function getSubCategories($category_id, $fetchColumns = array()) {

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id)) {
            return;
        }

        //MAKE QUERY
        $select = $this->select();

        if (!empty($fetchColumns)) {
            $select->from($this->info('name'), $fetchColumns);
        }

        $select->where('cat_dependency = ?', $category_id)
                ->order('cat_order');
        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    public function getSubCategoriesCount($category_id, $fetchColumns = array()) {

        return count($this->getSubCategories($category_id));
    }

    /**
     * Get category object
     * @param int $category_id : category id
     * @return category object
     */
    public function getCategory($category_id) {
        if (empty($category_id))
            return;
        if (!array_key_exists($category_id, $this->_categories)) {
            $this->_categories[$category_id] = $this->find($category_id)->current();
        }
        return $this->_categories[$category_id];
    }

    /**
     * Return categories
     *
     * @param array $category_ids
     * @return all categories
     */
    public function getCategories($fetchColumns = array(), $category_ids = null, $count_only = 0, $sponsored = 0, $cat_depandency = 0, $limit = 0, $orderBy = 'cat_order', $visibility = 0, $havingProjects = 0) {
        //MAKE QUERY
        $select = $this->select();
        //GET CATEGORY TABLE NAME
        $categoryTableName = $this->info('name');
        if ($orderBy == 'category_name') {
            $select->order('category_name');
        } else {
            $select->order('cat_order');
        }

        if (!empty($cat_depandency)) {
            $select->where('cat_dependency = ?', 0);
            $select->where('subcat_dependency = ?', 0);
        }

        if (!empty($sponsored)) {
            $select->where('sponsored = ?', 1);
        }
        if (!empty($category_ids)) {
            foreach ($category_ids as $ids) {
                $categoryIdsArray[] = "category_id = $ids";
            }
            $select->where("(" . join(") or (", $categoryIdsArray) . ")");
        }

        if (!empty($count_only)) {
            return $select->from($this->info('name'), 'category_id')->query()->fetchColumn();
        } else {
            if (!empty($fetchColumns)) {
                $select->setIntegrityCheck(false)->from($categoryTableName, $fetchColumns);
            } else {
                $select->setIntegrityCheck(false)->from($categoryTableName);
            }
        }

        if ($havingProjects) {
            $tableProject = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
            $tableProjectName = $tableProject->info('name');
            $select->join($tableProjectName, "$tableProjectName.category_id=$categoryTableName.category_id", null);
            $select->where($tableProjectName . '.approved = ?', 1)->where($tableProjectName . '.state != ?', 'draft')->where($tableProjectName . '.search = ?', 1);
            //$select = $tableProject->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }
        if (!empty($limit)) {
            $select->limit($limit);
        }
        //RETURN DATA
        return $this->fetchAll($select);
    }

    /**
     * Return page categories Which has projects count
     *
     * @param array $category_ids
     * @return all categories
     */
    public function getCategoriesWithProjectsCount($limit = 0, $page_id) {

        //MAKE QUERY
        $select = $this->select()->distinct();

        //get page projects
        $projectsIds = Engine_Api::_()->getDbTable('pages','sitecrowdfunding')->getPageProjects($page_id);

        if( count($projectsIds) > 0 ){

            $currentDate = date('Y-m-d H:i:s');

            //GET CATEGORY TABLE NAME
            $categoryTableName = $this->info('name');
            $select->order('category_name');
            $select->setIntegrityCheck(false)->from($categoryTableName);

            $tableProject = Engine_Api::_()->getDbTable('projects', 'sitecrowdfunding');
            $tableProjectName = $tableProject->info('name');
            $select->join($tableProjectName, "$tableProjectName.category_id=$categoryTableName.category_id", null);
            $select
                ->where($tableProjectName . '.approved = ?', 1)
                ->where($tableProjectName . '.state = ?', 'published')
                ->where($tableProjectName . '.project_id IN (?)', $projectsIds)
                ->where($tableProjectName . '.start_date <= ?', $currentDate);

            $select->columns(array(
                '*',
                'projects_count' => new Zend_Db_Expr("COUNT($tableProjectName.project_id)")
            ));

            $select->group("$categoryTableName.category_id");

            if (!empty($limit)) {
                $select->limit($limit);
            }

            //RETURN DATA
            return $this->fetchAll($select);

        }else{
            return null;
        }

    }

    /**
     * Get Mapping array
     *
     */
    public function getMapping($profileTypeName = 'profile_type') {

        //MAKE QUERY
        $select = $this->select()->from($this->info('name'), array('category_id', "$profileTypeName"));

        //FETCH DATA
        $mapping = $this->fetchAll($select);

        //RETURN DATA
        if (!empty($mapping)) {
            return $mapping->toArray();
        }

        return null;
    }

    public function getChildMapping($category_id, $profileTypeName = 'profile_type') {

        //GET CATEGORY TABLE NAME
        $categoryTableName = $this->info('name');

        $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id);

        $select = $this->select()
                ->from($categoryTableName, 'category_id')
                ->where("$profileTypeName != ?", 0)
                ->where("cat_dependency = $category->category_id OR subcat_dependency = $category->category_id");

        return $this->fetchAll($select);
    }

    public function getChilds($category_id) {

        //GET CATEGORY TABLE NAME
        $categoryTableName = $this->info('name');

        $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $category_id);

        $select = $this->select()
                ->from($categoryTableName, array('category_id', 'category_name'))
                ->where("cat_dependency = ?", $category_id);

        //IF SUBCATEGORY THEN FETCH 3RD LEVEL CATEGORY
        if ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
            $select->where("subcat_dependency = ?", $category_id);
        }
        //IF CATEGORY THEN FETCH SUB-CATEGORY
        elseif ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
            $select->where("subcat_dependency = ?", 0);
        }
        //IF 3RD LEVEL CATEGORY
        else {
            return array();
        }

        return $this->fetchAll($select);
    }

    /**
     * Get profile_type corresponding to category_id
     *
     * @param int category_id
     */
    public function getProfileType($categoryIds = array(), $categoryId = 0, $profileTypeName = 'profile_type') {

        if (!empty($categoryIds)) {
            $profile_type = 0;
            foreach ($categoryIds as $value) {
                $profile_type = $this->select()
                        ->from($this->info('name'), array("$profileTypeName"))
                        ->where("category_id = ?", $value)
                        ->query()
                        ->fetchColumn();

                if (!empty($profile_type)) {
                    return $profile_type;
                }
            }

            return $profile_type;
        } elseif (!empty($categoryId)) {

            //FETCH DATA
            $profile_type = $this->select()
                    ->from($this->info('name'), array("$profileTypeName"))
                    ->where("category_id = ?", $categoryId)
                    ->query()
                    ->fetchColumn();

            return $profile_type;
        }

        return 0;
    }

    public function getCategoriesHavingNoChield($arrayLevels = array(), $showAllCategories = 0) {

        $categoryTableName = $this->info('name');
        $select = $this->select()
                ->from($categoryTableName, array('category_id', 'category_name', 'cat_dependency', 'subcat_dependency'))
                //->where("category_id NOT IN (SELECT cat_dependency FROM $categoryTableName)")
                ->order('cat_order');

        if (!$showAllCategories) {
            $tableProject = Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding');
            $tableProjectName = $tableProject->info('name');
            $select = $this->select()->setIntegrityCheck(false)->from($categoryTableName);
        }

        $addedProjectJoin = 0;
        if (!empty($arrayLevels) && Count($arrayLevels) < 3) {

            if (!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableProjectName, "$tableProjectName.subcategory_id=$categoryTableName.category_id OR $tableProjectName.subsubcategory_id=$categoryTableName.category_id", null);
                    $addedProjectJoin = 1;
                }

                $select->where("cat_dependency != 0 OR subcat_dependency != 0");
            } elseif (in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableProjectName, "$tableProjectName.category_id=$categoryTableName.category_id OR $tableProjectName.subsubcategory_id=$categoryTableName.category_id", null);
                    $addedProjectJoin = 1;
                }

                $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency != 0)");
            } elseif (in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableProjectName, "$tableProjectName.category_id=$categoryTableName.category_id OR $tableProjectName.subcategory_id=$categoryTableName.category_id", null);
                    $addedProjectJoin = 1;
                }

                $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency = 0)");
            } elseif (!in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableProjectName, "$tableProjectName.subsubcategory_id=$categoryTableName.category_id", null);
                    $addedProjectJoin = 1;
                }

                $select->where("cat_dependency != 0 AND subcat_dependency != 0");
            } elseif (in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableProjectName, "$tableProjectName.category_id=$categoryTableName.category_id", null);
                    $addedProjectJoin = 1;
                }

                $select->where("cat_dependency = 0 AND subcat_dependency = 0");
            } elseif (!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableProjectName, "$tableProjectName.subcategory_id=$categoryTableName.category_id", null);
                    $addedProjectJoin = 1;
                }

                $select->where("cat_dependency != 0 AND subcat_dependency = 0");
            }
        }

        if (!$addedProjectJoin && !$showAllCategories) {
            $select->join($tableProjectName, "$tableProjectName.category_id=$categoryTableName.category_id", null);
        }

        $select->order('cat_order');
        //RETURN DATA
        return $this->fetchAll($select);
    }

    public function getCategoriesDetails($arrayLevels) {

        $categories = $this->getCategoriesHavingNoChield($arrayLevels);

        $categories_prepared = array();
        foreach ($categories as $category) {
            $categoryArray = array();
            if ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
                $categoryArray['category_id'] = $category->category_id;
                $categoryArray['categoryname'] = $category->category_name;
                $categoryArray['subcategory_id'] = 0;
                $categoryArray['subcategoryname'] = '';
                $categoryArray['subsubcategory_id'] = 0;
                $categoryArray['subsubcategoryname'] = '';
            } elseif ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
                $categoryMain = Engine_Api::_()->getItem('sitecrowdfunding_category', $category->cat_dependency);
                $categoryArray['category_id'] = $categoryMain->category_id;
                $categoryArray['categoryname'] = $categoryMain->category_name;
                $categoryArray['subcategory_id'] = $category->category_id;
                $categoryArray['subcategoryname'] = $category->category_name;
                $categoryArray['subsubcategory_id'] = 0;
                $categoryArray['subsubcategoryname'] = '';
            } elseif ($category->cat_dependency != 0 && $category->subcat_dependency != 0) {
                $categorySub = Engine_Api::_()->getItem('sitecrowdfunding_category', $category->cat_dependency);
                $categoryMain = Engine_Api::_()->getItem('sitecrowdfunding_category', $categorySub->cat_dependency);
                $categoryArray['category_id'] = $categoryMain->category_id;
                $categoryArray['categoryname'] = $categoryMain->category_name;
                $categoryArray['subcategory_id'] = $categorySub->category_id;
                $categoryArray['subcategoryname'] = $categorySub->category_name;
                $categoryArray['subsubcategory_id'] = $category->category_id;
                $categoryArray['subsubcategoryname'] = $category->category_name;
            }

            $categories_prepared[$category->category_id] = $categoryArray;
        }

        //RETURN DATA
        return $categories_prepared;
    }

    /**
     * Gets all parent categories
     *
     * @return object
     */
    public function getParentCategories() {
        $select = $this->select()
                ->from($this->info('name'), array("category_id", "category_name"))
                ->where("cat_dependency = 0 AND subcat_dependency = 0");

        return $this->fetchAll($select);
    }

    public function getCatDependancyArray() {

        $cat_dependency = $this->select()->from($this->info('name'), 'cat_dependency')->where('cat_dependency <>?', 0)->group('cat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        return $cat_dependency;
    }

    public function getSubCatDependancyArray() {

        $subcat_dependency = $this->select()->from($this->info('name'), 'subcat_dependency')->where('subcat_dependency <>?', 0)->group('subcat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        return $subcat_dependency;
    }

    public function isGuestReviewAllowed($category_id) {
        $isGuestReviewAllowed = $column = $this->select()
                ->from($this->info('name'), 'allow_guestreview')
                ->where('category_id = ?', $category_id)
                ->query()
                ->fetchColumn();
        return $isGuestReviewAllowed;
    }

    /**
     * Return categories
     *
     * @param int $home_page_display
     * @return categories
     */
    public function getCategoriesByLevel($level = null) {

        $select = $this->select()->order('cat_order');
        switch ($level) {
            case 'category':
                $select->where('cat_dependency =?', 0);
                break;
            case 'subcategory':
                $select->where('cat_dependency !=?', 0);
                $select->where('subcat_dependency =?', 0);
                break;
            case 'subsubcategory':
                $select->where('cat_dependency !=?', 0);
                $select->where('subcat_dependency !=?', 0);
                break;
        }
        return $this->fetchAll($select);
    }

    /**
     * Return slug
     *
     * @param int $categoryname
     * @return categoryname
     */
    public function getCategorySlug($categoryname) {
        $slug = $categoryname;
        return Engine_Api::_()->seaocore()->getSlug($slug, 225);
    }

    public function setDefaultImages($toPath, $fromPath, $columnName) {
        @mkdir(APPLICATION_PATH . $toPath, 0777);
        $dir = APPLICATION_PATH . $fromPath;
        $public_dir = APPLICATION_PATH . $toPath;
        $fieArr = array();
        if (is_dir($dir) && is_dir($public_dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (strstr($file, '.png') || strstr($file, '.jpg') || strstr($file, '.gif')) {
                    $fieArr[] = $file;
                    @copy(APPLICATION_PATH . "$fromPath/$file", APPLICATION_PATH . "$toPath/$file");
                }
            }
            @chmod(APPLICATION_PATH . $toPath, 0777);
        }
        //MAKE QUERY
        $select = $this->select()->from($this->info('name'), array('category_id', 'category_name', $columnName));
        $categories = $this->fetchAll($select);
        //UPLOAD DEFAULT ICONS
        foreach ($categories as $category) {
            $categoryName = Engine_Api::_()->seaocore()->getSlug($category->category_name, 225);
            $iconName = false;
            foreach ($fieArr as $f) {
                if (strstr(strtolower($f), strtolower($categoryName))) {
                    $iconName = $f;
                    break;
                }
            }
            if ($iconName == false) {
                continue;
            }
           
            @chmod(APPLICATION_PATH . $toPath, 0777);
            $file = array();
            if ($columnName == 'photo_id' && !empty($category->photo_id)) {
                continue;
            } else if ($columnName == 'file_id' && !empty($category->file_id)) {
                continue;
            } else if ($columnName == 'banner_id' && !empty($category->banner_id)) {
                continue;
            }

            $file['tmp_name'] = APPLICATION_PATH . "$toPath/$iconName";
            $file['name'] = $iconName;
            if (file_exists($file['tmp_name'])) {
                $name = basename($file['tmp_name']);
                $path = dirname($file['tmp_name']);
                $mainName = $path . '/' . $file['name'];
                @chmod($mainName, 0777);
                $photo_params = array(
                    'parent_id' => $category->category_id,
                    'parent_type' => "sitecrowdfunding_category",
                );

                //RESIZE IMAGE WORK
                $image = Engine_Image::factory();
                $image->open($file['tmp_name']);
                $image->open($file['tmp_name']);
                $image->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
                        ->write($mainName)
                        ->destroy();

                $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
                //UPDATE FILE ID IN CATEGORY TABLE
                if (!empty($photoFile->file_id)) {
                    if ($columnName == 'photo_id') {
                        $category->photo_id = $photoFile->file_id;
                    } else if ($columnName == 'file_id') {
                        $category->file_id = $photoFile->file_id;
                    } else {
                        $category->banner_id = $photoFile->file_id;
                    }
                    $category->save();
                }
            }
        }

        //REMOVE THE CREATED PUBLIC DIRECTORY
        if (is_dir(APPLICATION_PATH . $toPath)) {
            $files = scandir(APPLICATION_PATH . $toPath);
            foreach ($files as $file) {
                $is_exist = file_exists(APPLICATION_PATH . "$toPath/$file");
                if ($is_exist) {
                    @unlink(APPLICATION_PATH . "$toPath/$file");
                }
            }
            @rmdir(APPLICATION_PATH . $toPath);
        }
    }

    public function uploadDefaultImages() {
        @ini_set('memory_limit', '-1');
        $mainImagesToPath = "/temporary/sitecrowdfunding_categorie_main_images";
        $mainImagesfromPath = "/application/modules/Sitecrowdfunding/externals/images/category_images/main_images";

        $bannerImagesToPath = "/temporary/sitecrowdfunding_categorie_banner_images";
        $bannerImagesfromPath = "/application/modules/Sitecrowdfunding/externals/images/category_images/banner_images";

        $this->setDefaultImages($mainImagesToPath, $mainImagesfromPath, 'photo_id');
        $this->setDefaultImages($bannerImagesToPath, $bannerImagesfromPath, 'banner_id');
    }

    public function getAllCategories() {
        $select = $this->select()
            ->from($this->info('name'), array("category_id", "category_name"))
            ->order('category_name ASC');

        return $this->fetchAll($select);
    }

}
