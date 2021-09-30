<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminProfilemapsController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminProfilemapsController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGING THE PROFILE-CATEGORY MAPPING
    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_profilemaps');

        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_admin_main_profilemaps', array(), 'sitevideo_admin_main_profilemaps_channel');
        //GET FIELD OPTION TABLE NAME
        $tableFieldOptions = Engine_Api::_()->getDbtable('options', 'sitevideo');

        //GET TOTAL PROFILES
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitevideo_channel');
        $this->view->totalProfileTypes = 1;
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            $this->view->totalProfileTypes = Count($options);
        }

        // GET CATEGOTY TABLE 
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $categories = array();
        $category_info = $tableCategory->getCategories(array('fetchColumns' => array('category_id', 'category_name', 'cat_order', 'profile_type'), 'sponsored' => 0, 'cat_depandancy' => 1));

        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $category_info2 = $tableCategory->getSubCategories(array('category_id' => $value->category_id, 'fetchColumns' => array('category_id', 'category_name', 'profile_type', 'cat_order')));
            foreach ($category_info2 as $subresults) {

                $treesubarray = array();

                $subcategory_info2 = $tableCategory->getSubCategories(array('category_id' => $subresults->category_id, 'fetchColumns' => array('category_id', 'category_name', 'profile_type', 'cat_order')));

                $treesubarrays[$subresults->category_id] = array();
                foreach ($subcategory_info2 as $subvalues) {

                    $tree_profile_type_label = '---';
                    if (!empty($subvalues->profile_type)) {
                        $tree_profile_type_label = $tableFieldOptions->getProfileTypeLabel($subvalues->profile_type);
                    }

                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subvalues->category_id,
                        'tree_sub_cat_name' => $subvalues->category_name,
                        'order' => $subvalues->cat_order,
                        'tree_profile_type_id' => $subvalues->profile_type,
                        'tree_profile_type_label' => $tree_profile_type_label
                    );
                }
                $subcat_profile_type_label = '---';
                if (!empty($subresults->profile_type)) {
                    $subcat_profile_type_label = $tableFieldOptions->getProfileTypeLabel($subresults->profile_type);
                }

                $tmp_array = array('sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'order' => $subresults->cat_order,
                    'subcat_profile_type_id' => $subresults->profile_type,
                    'subcat_profile_type_label' => $subcat_profile_type_label
                );
                $sub_cat_array[] = $tmp_array;
            }

            $cat_profile_type_label = '---';
            if (!empty($value->profile_type)) {
                $cat_profile_type_label = $tableFieldOptions->getProfileTypeLabel($value->profile_type);
            }

            $categories[] = $category_array = array(
                'category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'sub_categories' => $sub_cat_array,
                'cat_profile_type_id' => $value->profile_type,
                'cat_profile_type_label' => $cat_profile_type_label,
            );
        }
        include APPLICATION_PATH . '/application/modules/Sitevideo/controllers/license/license2.php';
    }

    //ACTION FOR MAP THE PROFILE WITH CATEGORY
    public function mapAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID
        $this->view->category_id = $category_id = $this->_getParam('category_id');

        //GET CHIELD MAPPING
        $chieldMappingCategoriesIds = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getChildMapping($category_id);
        $countChieldMapping = Count($chieldMappingCategoriesIds);

        //GENERATE THE FORM
        $this->view->form = $form = new Sitevideo_Form_Admin_Profilemaps_Map(array('countChieldMapping' => $countChieldMapping));

        //GET MAPPING ITEM
        $category = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id);

        //POST DATA
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $tempCatFlag = false;
            //GET DATA
            $values = $form->getValues();

            //GET CHANNEL TABLE
            $channelTable = Engine_Api::_()->getDbTable('channels', 'sitevideo');

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $category->profile_type = $values['profile_type'];
                $category->save();
                $tempCatFlag = true;

                if (empty($tempCatFlag)) {
                    return;
                }
                //IF YES BUTTON IS CLICKED THEN CHANGE MAPPING OF ALL CHANNELS
                if (isset($_POST['yes_button'])) {

                    if (!empty($countChieldMapping)) {

                        $chieldMappingArray = array();
                        foreach ($chieldMapping as $chieldMappingItem) {
                            $chieldMappingArray[] = $chieldMappingItem->category_id;
                        }

                        //HAVE TO UPDATE PREVIOUS MAPPED CHANNELS
                        $select = $channelTable->select()
                                ->from($channelTable->info('name'), array('channel_id', 'profile_type', 'category_id', 'subcategory_id', 'subsubcategory_id'))
                                ->where("category_id = $category_id OR category_id IN (?) OR subcategory_id IN (?) OR subsubcategory_id IN (?)", $chieldMappingArray)
                                ->where("profile_type != ?", $values['profile_type']);
                        $channelsIds = $channelTable->fetchAll($select);

                        $chieldMappingArrayStr = "(" . join(",", $chieldMappingCategoriesIds) . ")";
                        Zend_Db_Table_Abstract::getDefaultAdapter()->query("UPDATE `engine4_sitevideo_channel_categories` SET `profile_type` = 0 WHERE category_id IN $chieldMappingArrayStr");
                    } else {
                        //SELECT CHANNELS WHICH HAVE THIS CATEGORY AND THIS PROFILE TYPE
                        $channelsIds = $channelTable->getMappedSitevideo($category_id);
                    }

                    if (!empty($channelsIds)) {
                        foreach ($channelsIds as $channel_id) {

                            //GET FIELD VALUE TABLE
                            $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitevideo_channel', 'values');

                            //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                            Engine_Api::_()->fields()->getTable('sitevideo_channel', 'values')->delete(array('item_id = ?' => $channel_id));
                            Engine_Api::_()->fields()->getTable('sitevideo_channel', 'search')->delete(array('item_id = ?' => $channel_id));

                            //PUT NEW PROFILE TYPE
                            $fieldvalueTable->insert(array(
                                'item_id' => $channel_id,
                                'field_id' => Engine_Api::_()->getDbTable('metas', 'sitevideo')->defaultProfileId(),
                                'index' => 0,
                                'value' => $category->profile_type,
                            ));

                            $channelTable->update(array('profile_type' => $category->profile_type), array('channel_id = ?' => $channel_id));
                        }
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been done successfully.'))
            ));
        }

        $this->renderScript('admin-profilemaps/map.tpl');
    }

    public function editAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID
        $this->view->category_id = $category_id = $this->_getParam('category_id');

        //GET PROFILE TYPE
        $old_profile_type_id = $this->_getParam('profile_type');

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitevideo_channel');
        $this->view->totalProfileTypes = 1;
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();
            $this->view->totalProfileTypes = Count($options);
        }

        //GENERATE THE FORM
        $this->view->form = $form = new Sitevideo_Form_Admin_Profilemaps_Edit();

        //POST DATA
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET DATA
            $values = $form->getValues();
            $new_profile_type_id = $values['profile_type'];

            if ($old_profile_type_id != $new_profile_type_id) {

                //BEGIN TRANSCATION
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {

                    //GET MAPPING ITEM
                    $category = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id);

                    //GET CHANNEL TABLE
                    $sitevideoTable = Engine_Api::_()->getDbTable('channels', 'sitevideo');

                    //FOR CATEGORY
                    if ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
                        $select = $sitevideoTable->select()
                                ->from($sitevideoTable->info('name'), array('channel_id'))
                                ->where('category_id = ?', $category->category_id)
                                ->where('subcategory_id = ?', 0)
                        ;
                    }
                    //FOR SUBCATEGORY
                    elseif ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
                        $select = $sitevideoTable->select()
                                ->from($sitevideoTable->info('name'), array('channel_id'))
                                ->where('subcategory_id = ?', $category->category_id)
                                ->where('subsubcategory_id = ?', 0)
                        ;
                    } elseif ($category->cat_dependency != 0 && $category->subcat_dependency != 0) {
                        $select = $sitevideoTable->select()
                                ->from($sitevideoTable->info('name'), array('channel_id'))
                                ->where('subsubcategory_id = ?', $category->category_id)
                        ;
                    }

                    $channelIds = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

                    if (!empty($channelIds)) {

                        $fieldMapsTable = Engine_Api::_()->getDbTable('maps', 'sitevideo');
                        $fieldValuesTable = Engine_Api::_()->fields()->getTable('sitevideo_channel', 'values');

                        $old_meta_ids = $fieldMapsTable->getMappingIds($old_profile_type_id);
                        $new_meta_ids = $fieldMapsTable->getMappingIds($new_profile_type_id);
                        $array_diff = array_diff($old_meta_ids, $new_meta_ids);

                        $fieldValuesTable->update(array('value' => $new_profile_type_id), array('item_id IN (?)' => "'" . join("',", $channelIds) . "'", 'field_id = ?' => 1, 'value = ?' => $old_profile_type_id));

                        //DELETE UN-COMMON VALUES FROM CUSTOM TABLES
                        if (!empty($array_diff)) {
                            $fieldValuesTable->delete(array('item_id IN (?)' => "'" . join("',", $channelIds) . "'", 'field_id IN (?)' => "'" . join("',", $array_diff) . "'"));
                        }

                        //UPDATE PROFILE-TYPE VALUE IN CHANNEL TABLE
                        $sitevideoTable->update(array('profile_type' => $new_profile_type_id), array('channel_id IN (?)' => (array) $channelIds));
                    }

                    $category->profile_type = $new_profile_type_id;
                    $category->save();

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been edited successfully.'))
            ));
        }
    }

    //ACTION FOR DELETE MAPPING 
    public function removeAction() {

        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET MAPPING ID
        $this->view->category_id = $category_id = $this->_getParam('category_id');

        //GET CHILD CATEGORIES
        $categoryTable = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $getChilds = $categoryTable->getChilds(array('category_id' => $category_id, 'fetchcolumns' => array('category_id', 'category_name')));
        $this->view->countChilds = Count($getChilds);

        //GET MAPPING ITEM
        $this->view->category = $category = Engine_Api::_()->getItem('sitevideo_channel_category', $category_id);

        //POST DATA
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //GET CHANNEL TABLE
                $sitevideoTable = Engine_Api::_()->getDbTable('channels', 'sitevideo');

                if (!isset($_POST['import_profile'])) {

                    //SELECT CHANNELS WHICH HAVE THIS CATEGORY
                    $channelsIds = $sitevideoTable->getMappedSitevideo($category_id);
                } else {

                    foreach ($getChilds as $getChild) {
                        $child = Engine_Api::_()->getItem('sitevideo_channel_category', $getChild->category_id);
                        $child->profile_type = $category->profile_type;
                        $child->save();
                    }

                    //FOR CATEGORY
                    if ($category->cat_dependency == 0 && $category->subcat_dependency == 0) {
                        $select = $sitevideoTable->select()
                                ->from($sitevideoTable->info('name'), array('channel_id'))
                                ->where('category_id = ?', $category->category_id)
                                ->where('subcategory_id = ?', 0)
                        ;
                    }
                    //FOR SUBCATEGORY
                    elseif ($category->cat_dependency != 0 && $category->subcat_dependency == 0) {
                        $select = $sitevideoTable->select()
                                ->from($sitevideoTable->info('name'), array('channel_id'))
                                ->where('subcategory_id = ?', $category->category_id)
                                ->where('subsubcategory_id = ?', 0)
                        ;
                    }

                    $channelsIds = $sitevideoTable->fetchAll($select);

                    if (!empty($channelsIds)) {
                        $channelsIds = $channelsIds->toArray();
                    }
                }

                foreach ($channelsIds as $channel_id) {

                    //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                    Engine_Api::_()->fields()->getTable('sitevideo_channel', 'values')->delete(array('item_id = ?' => $channel_id));
                    Engine_Api::_()->fields()->getTable('sitevideo_channel', 'search')->delete(array('item_id = ?' => $channel_id));

                    //UPDATE THE PROFILE TYPE OF ALREADY CREATED CHANNELS
                    $sitevideoTable->update(array('profile_type' => 0), array('channel_id = ?' => $channel_id));
                }

                //DELETE MAPPING
                $category->profile_type = 0;
                $category->save();


                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping has been deleted successfully!'))
            ));
        }
        $this->renderScript('admin-profilemaps/remove.tpl');
    }

}
