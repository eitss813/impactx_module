<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoCategories.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_VideoCategories extends Engine_Db_Table {

    protected $_name = 'video_categories';
    protected $_rowClass = 'Sitevideo_Model_VideoCategory';
    protected $_categories = array();

    /**
     * Return categories
     *
     * @param array $category_ids
     * @return all categories
     */
    public function getCategories($params = array()) {

        //MAKE QUERY
        $select = $this->select();

        //GET CATEGORY TABLE NAME
        $categoryTableName = $this->info('name');

        if (isset($params['orderBy']) && $params['orderBy'] == 'category_name') {
            $select->order('category_name');
        }
        // todo: 5.2.1 Upgrade => Fixed Issue in Admin Layout Editor: Mysqli prepare error: Unknown column 'cat_order' in 'where clause'
        /*else {
            $select->order('cat_order');
        }*/

        // todo: 5.2.1 Upgrade => Fixed Issue in Admin Layout Editor: Mysqli prepare error: Unknown column 'cat_dependency' in 'where clause'
        /*if (isset($params['cat_depandancy']) && !empty($params['cat_depandancy'])) {
            $select->where('cat_dependency = ?', 0);
        }*/

        if (isset($params['sponsored']) && !empty($params['sponsored'])) {
            $select->where('sponsored = ?', 1);
        }

        if (isset($params['video_id']) && !empty($params['video_id'])) {
            $select->where('video_id <> ?', 0);
        }

        if (isset($params['fetchColumns']) && !empty($params['fetchColumns'])) {
            $select->setIntegrityCheck(false)->from($categoryTableName, $params['fetchColumns']);
        } else {
            $select->setIntegrityCheck(false)->from($categoryTableName);
        }

        if (isset($params['havingVideos']) && $params['havingVideos']) {
            $tableVideo = Engine_Api::_()->getDbTable('videos', 'sitevideo');
            $tableVideoName = $tableVideo->info('name');
            $select->join($tableVideoName, "$tableVideoName.category_id = $categoryTableName.category_id", null);
            $select->group('category_id');
        }

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where('category_id in (?)', $params['category_id']);
        }
        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        //RETURN DATA
        return $this->fetchAll($select);
    }

    public function getCategoriesPaginator(array $params) {
        return Zend_Paginator::factory($this->getCategories($params));
    }

    /**
     * Return subcaregories
     *
     * @param int category_id
     * @return all sub categories
     */
    public function getSubCategories($params = array()) {

        $categoryTableName = $this->info('name');

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($params['category_id'])) {
            return;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($categoryTableName, $params['fetchColumns'])
                ->where('cat_dependency = ?', $params['category_id'])
                ->order('cat_order');

        if (isset($params['havingVideos']) && $params['havingVideos']) {
            $tableVideo = Engine_Api::_()->getDbTable('videos', 'sitevideo');
            $tableVideoName = $tableVideo->info('name');
            $select->join($tableVideoName, "$tableVideoName.subcategory_id = $categoryTableName.category_id", null)
                    ->where($tableVideoName . '.category_id = ?', $params['category_id'])
                    ->group($categoryTableName . '.category_id');
        }

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    public function getCategoryName($category_id) {

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id)) {
            return;
        }

        //MAKE QUERY
        $categoryName = $this->select()
                ->from($this->info('name'), array('category_name'))
                ->where('category_id = ?', $category_id)
                ->query()
                ->fetchColumn();
        //RETURN RESULTS
        return $categoryName;
    }

    public function getCategoriesHavingNoChield($arrayLevels = array(), $showAllCategories = 0) {

        $categoryTableName = $this->info('name');
        $select = $this->select()
                ->from($categoryTableName, array('category_id', 'category_name', 'cat_dependency', 'subcat_dependency'))
                //->where("category_id NOT IN (SELECT cat_dependency FROM $categoryTableName)")
                ->order('cat_order');

        if (!$showAllCategories) {
            $tableVideo = Engine_Api::_()->getDbtable('videos', 'sitevideo');
            $tableVideoName = $tableVideo->info('name');
            $select = $this->select()->setIntegrityCheck(false)->from($categoryTableName);
        }

        $addedVideoJoin = 0;
        if (!empty($arrayLevels) && Count($arrayLevels) < 3) {

            if (!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableVideoName, "$tableVideoName.subcategory_id=$categoryTableName.category_id OR $tableVideoName.subsubcategory_id=$categoryTableName.category_id", null);
                    $addedVideoJoin = 1;
                }

                $select->where("cat_dependency != 0 OR subcat_dependency != 0");
            } elseif (in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableVideoName, "$tableVideoName.category_id=$categoryTableName.category_id OR $tableVideoName.subsubcategory_id=$categoryTableName.category_id", null);
                    $addedVideoJoin = 1;
                }

                $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency != 0)");
            } elseif (in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableVideoName, "$tableVideoName.category_id=$categoryTableName.category_id OR $tableVideoName.subcategory_id=$categoryTableName.category_id", null);
                    $addedVideoJoin = 1;
                }

                $select->where("(cat_dependency = 0 AND subcat_dependency = 0) OR (cat_dependency != 0 AND subcat_dependency = 0)");
            } elseif (!in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableVideoName, "$tableVideoName.subsubcategory_id=$categoryTableName.category_id", null);
                    $addedVideoJoin = 1;
                }

                $select->where("cat_dependency != 0 AND subcat_dependency != 0");
            } elseif (in_array('category', $arrayLevels) && !in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableVideoName, "$tableVideoName.category_id=$categoryTableName.category_id", null);
                    $addedVideoJoin = 1;
                }

                $select->where("cat_dependency = 0 AND subcat_dependency = 0");
            } elseif (!in_array('category', $arrayLevels) && in_array('subcategory', $arrayLevels) && !in_array('subusbcategory', $arrayLevels)) {

                if (!$showAllCategories) {
                    $select->join($tableVideoName, "$tableVideoName.subcategory_id=$categoryTableName.category_id", null);
                    $addedVideoJoin = 1;
                }

                $select->where("cat_dependency != 0 AND subcat_dependency = 0");
            }
        }

        if (!$addedVideoJoin && !$showAllCategories) {
            $select->join($tableVideoName, "$tableVideoName.category_id=$categoryTableName.category_id", null);
        }

        $select->order('cat_order');
        //RETURN DATA
        return $this->fetchAll($select);
    }

    public function getCategoriesDetails($arrayLevels, $showAllCategories) {
        $categories = $this->getCategoriesHavingNoChield($arrayLevels, $showAllCategories);
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
                $categoryMain = Engine_Api::_()->getItem('sitevideo_video_category', $category->cat_dependency);
                $categoryArray['category_id'] = $categoryMain->category_id;
                $categoryArray['categoryname'] = $categoryMain->category_name;
                $categoryArray['subcategory_id'] = $category->category_id;
                $categoryArray['subcategoryname'] = $category->category_name;
                $categoryArray['subsubcategory_id'] = 0;
                $categoryArray['subsubcategoryname'] = '';
            } elseif ($category->cat_dependency != 0 && $category->subcat_dependency != 0) {
                $categorySub = Engine_Api::_()->getItem('sitevideo_video_category', $category->cat_dependency);
                $categoryMain = Engine_Api::_()->getItem('sitevideo_video_category', $categorySub->cat_dependency);
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

    public function getChildMapping($category_id) {

        return $this->select()
                        ->from($this->info('name'), 'category_id')
                        ->where("profile_type != ?", 0)
                        ->where("cat_dependency = $category_id")
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
        ;
    }

    public function getChilds($params = array()) {

        if (empty($params['category_id']))
            return;

        $cat_dependency = Engine_Api::_()->getItem('sitevideo_video_category', $params['category_id'])->cat_dependency;

        //IF CATEGORY THEN FETCH SUB-CATEGORY
        if ($cat_dependency != 0) {
            return array();
        }

        $select = $this->select()
                ->from($this->info('name'), $params['fetchcolumns'])
                ->where("cat_dependency = ?", $params['category_id']);
        return $this->fetchAll($select);
    }

    /**
     * Get Mapping array
     *
     */
    public function getMapping($params = array()) {

        //MAKE QUERY
        $select = $this->select()->from($this->info('name'), $params);

        //FETCH DATA
        $mapping = $this->fetchAll($select);

        //RETURN DATA
        if (!empty($mapping)) {
            return $mapping->toArray();
        }

        return null;
    }

    /**
     * Get profile_type corresponding to category_id
     *
     * @param int category_id
     */
    public function getProfileType($params) {

        if (!empty($params['categoryIds'])) {
            $profile_type = 0;
            foreach ($params['categoryIds'] as $value) {
                $profile_type = $this->select()
                        ->from($this->info('name'), array("profile_type"))
                        ->where("category_id = ?", $value)
                        ->query()
                        ->fetchColumn();

                if (!empty($profile_type)) {
                    return $profile_type;
                }
            }

            return $profile_type;
        } elseif (!empty($params['category_id'])) {

            //FETCH DATA
            $profile_type = $this->select()
                    ->from($this->info('name'), array("profile_type"))
                    ->where("category_id = ?", $params['category_id'])
                    ->query()
                    ->fetchColumn();

            return $profile_type;
        }

        return 0;
    }

    public function getCategoriesByLevel($level = null) {

        $select = $this->select()->order('cat_order');
        switch ($level) {
            case 'category':
                $select->where('cat_dependency =?', 0);
                break;
            case 'subcategory':
                $select->where('cat_dependency !=?', 0);
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

    public function getCatDependancyArray() {

        return $this->select()
                        ->from($this->info('name'), 'cat_dependency')
                        ->where('cat_dependency <>?', 0)
                        ->group('cat_dependency')
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    public function getSubCatDependancyArray() {

        return $this->select()->from($this->info('name'), 'subcat_dependency')->where('subcat_dependency <>?', 0)->group('subcat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);
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
                if (strstr($f, $categoryName)) {
                    $iconName = $f;
                    break;
                }
            }
            if ($iconName == false) {
                continue;
            }
            @chmod(APPLICATION_PATH . $toPath, 0777);
            $file = array();
            if ($columnName == 'video_id' && !empty($category->video_id)) {
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
                    'parent_type' => "sitevideo_video_category",
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
                    if ($columnName == 'video_id') {
                        $category->video_id = $photoFile->file_id;
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
        $iconImagesToPath = "/temporary/video_categorie_icons";
        $iconImagesfromPath = "/application/modules/Sitevideo/externals/images/category_images/video/icons";

        $mainImagesToPath = "/temporary/video_categorie_main_images";
        $mainImagesfromPath = "/application/modules/Sitevideo/externals/images/category_images/video/main_images";

        $bannerImagesToPath = "/temporary/video_categorie_banner_images";
        $bannerImagesfromPath = "/application/modules/Sitevideo/externals/images/category_images/video/banner_images";

        $this->setDefaultImages($iconImagesToPath, $iconImagesfromPath, 'file_id');
        $this->setDefaultImages($mainImagesToPath, $mainImagesfromPath, 'video_id');
        $this->setDefaultImages($bannerImagesToPath, $bannerImagesfromPath, 'banner_id');
        $this->setSposoredCategories();
    }

    public function setSposoredCategories() {
        //MAKE QUERY
        $select = $this->select()->from($this->info('name'), array('category_id'))->where('category_id in(?)', array(2, 3, 5, 7, 11, 15));
        $categories = $this->fetchAll($select);
        //UPLOAD DEFAULT ICONS
        foreach ($categories as $category) {
            $categoryMain = Engine_Api::_()->getItem('sitevideo_video_category', $category['category_id']);
            $categoryMain->sponsored = 1;
            $categoryMain->save();
        }
    }

}
