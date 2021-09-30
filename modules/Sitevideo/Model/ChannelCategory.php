<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChannelCategory.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_ChannelCategory extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;

    public function getShortType($inflect = false) {
        return 'category';
    }

    public function getTitle() {
        return $this->category_name;
    }

    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        }
        return $this->_table;
    }

    public function getUsedCount() {
        $table = Engine_Api::_()->getDbTable('channels', 'sitevideo');
        $rName = $table->info('name');
        $select = $table->select()
                ->from($rName)
                ->where($rName . '.category_id = ?', $this->category_id);
        $row = $table->fetchAll($select);
        $total = count($row);
        return $total;
    }

    // Ownership

    public function isOwner($owner) {
        if ($owner instanceof Core_Model_Item_Abstract) {
            return ( $this->getIdentity() == $owner->getIdentity() && $this->getType() == $owner->getType() );
        } else if (is_array($owner) && count($owner) === 2) {
            return ( $this->getIdentity() == $owner[1] && $this->getType() == $owner[0] );
        } else if (is_numeric($owner)) {
            return ( $owner == $this->getIdentity() );
        }

        return false;
    }

    public function getOwner($recurseType = null) {
        return $this;
    }

    public function setVideo($video) {

        if ($video instanceof Zend_Form_Element_File) {
            $file = $video->getFileName();
        } else if (is_array($video) && !empty($video['tmp_name'])) {
            $file = $video['tmp_name'];
        } else if (is_string($video) && file_exists($video)) {
            $file = $video;
        } else {
            return;
        }

        if (empty($file))
            return;

        //GET VIDEO DETAILS
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $mainName = $path . '/' . $name;

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $video_params = array(
            'parent_id' => $this->category_id,
            'parent_type' => "sitevideo_channel_category",
        );

        //RESIZE IMAGE WORK
        $image = Engine_Image::factory();
        $image->open($file);
        $image->open($file)
                ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
                ->write($mainName)
                ->destroy();

        try {
            $videoFile = Engine_Api::_()->storage()->create($mainName, $video_params);
        } catch (Exception $e) {
            if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
                echo $e->getMessage();
                exit();
            }
        }

        return $videoFile;
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

        $route = "sitevideo_general_" . $type;
        if ($type == 'category' && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $route = Engine_Api::_()->sitevideo()->getCategoryHomeRoute();
        }

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

        $params = array_merge(array(
            'route' => "sitevideo_general_" . $type,
            'reset' => true,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

}

?>