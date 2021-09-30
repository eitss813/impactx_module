<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminShorturlController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_AdminShorturlController extends Core_Controller_Action_Admin {

    //ACTION FOR SENDING THE EMAIL
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_shorturl');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_shorturl', array(), 'sitevideo_admin_global_url');

        $db = Engine_Db_Table::getDefaultAdapter();

        //START LANGUAGE WORK
        //  Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
        //END LANGUAGE WORK
        //GET FORM
        $this->view->form = $form = new Sitevideo_Form_Admin_Shorturl_Global();

        if (!$this->getRequest()->isPost()) {
            Engine_Api::_()->sitevideo()->setBandURL();
        }
        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        // Okay, save
        if (($values['sitevideo_channel_likelimit_forurlblock'] >= 0)) {
            foreach ($values as $key => $value) {
                if ($value != '') {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }
            }
        } else {
            $error = Zend_Registry::get('Zend_Translate')->_('The value that you enter for Likes Limit for Active Short URL should be 0 or greater.');
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        $this->view->form = $form = new Sitevideo_Form_Admin_Shorturl_Global();
        $form->addNotice('Your changes have been saved.');
    }

    //ACTION FOR BANNEDURL
    public function banningurlAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_shorturl');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_shorturl', array(), 'sitevideo_admin_blockurl');


        $this->view->formFilter = $formFilter = new Sitevideo_Form_Admin_Manage_Filter();
        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        if (isset($_POST['search'])) {
            if (!empty($_POST['word'])) {
                $this->view->word = $_POST['word'];
                $values['word'] = $_POST['word'];
            }
        }

        $values = array_merge(array(
            'order' => 'bannedpageurl_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);

        // Load all words
        $bannedChannelurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
        $this->view->paginator = $paginator = $bannedChannelurlsTable->getWords($values);
        $channel = $this->_getParam('channel', 1);
        $this->view->paginator->setItemCountPerPage(500);
        $this->view->paginator->setCurrentPageNumber($channel);
    }

    public function createUrlAction() {

        // Get form
        $this->view->form = $form = new Sitevideo_Form_Admin_Shorturl_Blockurl();

        // Load all words
        $bannedChannelurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            // Process
            $db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
            $db->beginTransaction();

            try {
                $values = $form->getValues();

                // Save Banned Words
                $bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $values['bannedwords']);

                $words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

                $data = $bannedChannelurlsTable->select()
                        ->from($bannedChannelurlsTable, 'word')
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
                if (in_array($words[0], $data)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL already exists.'));
                    return;
                }
                $bannedChannelurlsTable->setWords($bannedWordsNew);
                $db->commit();
                //$form->addNotice('Your changes have been saved.');
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }

    public function editUrlAction() {

        // Get form
        $this->view->form = $form = new Sitevideo_Form_Admin_Shorturl_Blockurl();
        $url_id = $this->_getParam('id');
        $bannedChannelurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
        $data = $bannedChannelurlsTable->select()
                ->from($bannedChannelurlsTable)
                ->where('bannedpageurl_id =?', $url_id)
                ->query()
                ->fetchAll();
        $form->setField($data);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            // Process
            $db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
            $db->beginTransaction();


            try {
                $values = $form->getValues();
                // Save Banned Words
                $bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $values['bannedwords']);

                $words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

                $data = $bannedChannelurlsTable->select()
                        ->from($bannedChannelurlsTable, 'word')
                        ->where('bannedpageurl_id !=?', $url_id)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);
                if (in_array($words[0], $data)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL already exists.'));
                    return;
                }
                $bannedChannelurlsTable->update(array('word' => $words[0]), array('bannedpageurl_id = ?' => $url_id));
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }

    public function deleteUrlAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID AND CHECK VALIDATION
        $this->view->id = $id = $this->_getParam('id');

        if (empty($id)) {
            die('No identifier specified');
        }
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $bannedChannelurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
                $selectdata = $bannedChannelurlsTable->select()
                        ->where('bannedpageurl_id =?', $id);
                $result_url = $bannedChannelurlsTable->fetchRow($selectdata);
                $result_url->delete();
                //COMMIT
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }

    public function addUrlAction() {

        // Get form
        $this->view->form = $form = new Sitevideo_Form_Admin_Shorturl_Addurl();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $bannedChannelurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
            // Process
            $db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
            $db->beginTransaction();


            try {
                $values = $form->getValues();

                $includeModules = array("sitepage" => "sitepage", "sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music", "sitegroup" => "sitegroup", "sitegroupdocument" => 'Documents', "sitegroupoffer" => 'Offers', "sitegroupform" => "Form", "sitegroupdiscussion" => "Discussions", "sitegroupnote" => "Notes", "sitegroupalbum" => "Photos", "sitegroupvideo" => "Videos", "sitegroupevent" => "Events", "sitegrouppoll" => "Polls", "sitegroupinvite" => "Invite & Promote", "sitegroupbadge" => "Badges", "sitegrouplikebox" => "External Badge", "sitegroupmusic" => "Music", "sitestore" => "sitestore", "sitestoredocument" => 'Documents', "sitestoreoffer" => 'Offers', "
sitestoreform" => "Form", "sitestorediscussion" => "Discussions", "sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoremusic" => "Music", "sitebusiness" => "sitebusiness", "sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form", "sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music", "list" => "list");
                $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
                $select = $moduleTable->select()->where('enabled = ?', 1);
                $enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);

                if (!in_array($values['module_name'], $enableModules)) {
                    $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($values['module_name']) . "/settings/manifest.php";
                    if (@file_exists($file_path)) {
                        $ret = include $file_path;
                        $is_exist = array();
                        if (isset($ret['routes'])) {
                            foreach ($ret['routes'] as $item) {
                                $route = $item['route'];
                                $route_array = explode('/', $route);
                                $route_url = strtolower($route_array[0]);

                                $data = $bannedChannelurlsTable->select()
                                        ->from($bannedChannelurlsTable, 'bannedpageurl_id')
                                        ->where('word =?', $route_url)
                                        ->query()
                                        ->fetchAll(Zend_Db::FETCH_COLUMN);

                                if (empty($data)) {
                                    if (!empty($route_url) && !in_array($route_url, $is_exist)) {
                                        $db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','" . $route_url . "')");
                                    }
                                } else {
                                    $bannedChannelurlsTable->update(array('word' => $route_url), array('bannedpageurl_id = ?' => $data[0]));
                                }
                                $is_exist[] = $route_url;
                            }
                        }
                    }
                } else {
                    if ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore' || $moduleName == 'sitevideo') {
                        $name = $moduleName . '.manifestUrlS';
                    } else {
                        $name = $moduleName . '.manifestUrl';
                    }
                    $settingTable = Engine_Api::_()->getDbtable('settings', 'core');
                    $select = $settingTable->select()
                            ->from($settingTable, 'value')
                            ->where('name = ?', $name);
                    $route_url = strtolower($select->query()->fetchAll(Zend_Db::FETCH_COLUMN));
                    if (!empty($route_url)) {
                        $db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','" . $route_url . "')");
                    }
                }


                /* data = $bannedChannelurlsTable->select()
                  ->from($bannedChannelurlsTable, 'word')
                  ->where('word !=?',$values[])
                  ->query()
                  ->fetchAll(Zend_Db::FETCH_COLUMN); */
                $bannedChannelurlsTable->update(array('word' => $words[0]), array('bannedpageurl_id = ?' => $url_id));
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }

    public function channelurlAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main', array(), 'sitevideo_admin_main_shorturl');

        $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitevideo_admin_main_shorturl', array(), 'sitevideo_admin_main_shorturl_url');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Sitevideo_Form_Admin_Manage_Filter();
        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array(
            'order' => 'bannedpageurl_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);

        $this->view->paginator = $paginator = Engine_Api::_()->sitevideo()->getBlockUrl($values);
        $channel = $this->_getParam('channel', 1);
        $this->view->paginator->setItemCountPerPage(20);
        $this->view->paginator->setCurrentPageNumber($channel);
    }

}

?>
