<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
 ?>

<?php 
$category=array();
$subcategory=array();
$subsubcategory=array();
if(!empty($this->album->category_id)) {
	$category = array("href"=>$this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug()), 'sitealbum_general_category'),"title"=>$this->translate($this->category_name),"icon"=>"arrow-r");
}

if(!empty($this->album->subcategory_id)) {
	$subcategory = array("href"=>$this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug(), 'subcategory_id' => $this->album->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('album_category', $this->album->subcategory_id)->getCategorySlug()), "sitealbum_general_subcategory"),"title"=>$this->translate($this->subcategory_name),"icon"=>"arrow-r");
}

if(!empty($this->album->subsubcategory_id)) {
	$subsubcategory = array("href"=>$this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug(), 'subcategory_id' => $this->album->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('album_category', $this->album->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->album->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('album_category', $this->album->subsubcategory_id)->getCategorySlug()), "sitealbum_general_subsubcategory"),"title"=>$this->translate($this->subsubcategory_name),"icon"=>"arrow-r");
}

$breadcrumb = array(
	array("href"=>$this->url(array('action' => 'index'), 'sitealbum_general', true),"title"=>$this->translate("Albums Home"),"icon"=>"arrow-r"),
	$category,$subcategory,$subsubcategory,
	array("title"=>$this->album->getTitle(),"icon"=>"arrow-d"),
);

echo $this->breadcrumb($breadcrumb);
?>