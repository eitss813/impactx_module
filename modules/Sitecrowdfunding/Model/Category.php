<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Category.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Model_Category extends Core_Model_Item_Abstract {

    public function getTitle($inflect = false) {
        if ($inflect) {
            return ucwords($this->category_name);
        } else {
            return $this->category_name;
        }
    }


    public function getHref($params = array()) {

        if ($this->subcat_dependency) {
            $type = 'subsubcategory';
            $params['subsubcategory_id'] = $this->category_id;
            $params['subsubcategoryname'] = $this->getCategorySlug();
            $cat = $this->getTable()->getCategory($this->cat_dependency);
            $params['subcategory_id'] = $cat->category_id;
            $params['subcategoryname'] = $cat->getCategorySlug();
            $cat = $this->getTable()->getCategory($cat->cat_dependency);
            $params['category_id'] = $cat->category_id;
            $params['categoryname'] = $cat->getCategorySlug();
        } else if ($this->cat_dependency) {
            $type = 'subcategory';
            $params['subcategory_id'] = $this->category_id;
            $params['subcategoryname'] = $this->getCategorySlug();
            $cat = $this->getTable()->getCategory($this->cat_dependency);
            $params['category_id'] = $cat->category_id;
            $params['categoryname'] = $cat->getCategorySlug();
        } else {
            $type = 'category';
            $params['category_id'] = $this->category_id;
            $params['categoryname'] = $this->getCategorySlug();
        }

        $route = "sitecrowdfunding_general_$type";
        $params = array_merge(array(
            'route' => $route,
            'reset' => true,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return slug corrosponding to category name
     *
     * @return categoryname
     */
    public function getCategorySlug() {

        if (!empty($this->category_slug)) {
            $slug = $this->category_slug;
        } else {
            $slug = Engine_Api::_()->seaocore()->getSlug($this->category_name, 225);
        }

        return $slug;
    }

    /**
     * Set category icon
     *
     */
    public function setPhoto($photo) {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            return;
        }

        if (empty($file))
            return;

        //GET PHOTO DETAILS
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $mainName = $path . '/' . $name;

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $photo_params = array(
            'parent_id' => $this->category_id,
            'parent_type' => "sitecrowdfunding_category",
        );

        //RESIZE IMAGE WORK
        $image = Engine_Image::factory();
        $image->open($file);
        $image->open($file)
				->resize(1600,1600)
				->write($mainName)
                ->destroy();

        try {
            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
        } catch (Exception $e) {
            if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
                echo $e->getMessage();
                exit();
            }
        }

        return $photoFile;
    }

    public function hasChild() {
        $table = $this->getTable();
        //RETURN RESULTS
        return $table->select()
                        ->from($table, new Zend_Db_Expr('COUNT(cat_dependency)'))
                        ->where('cat_dependency = ?', $this->category_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }

    public function getIconUrl() {
        if($this->file_id)
            return Engine_Api::_()->storage()->get($this->file_id)->getPhotoUrl();
        return Zend_Registry::get('StaticBaseUrl') . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png";
    }

}
