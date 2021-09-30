<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Plugin_Core extends Zend_Controller_Plugin_Abstract {

   
  public function onItemDeleteBefore($event)
    {
        $yndform = $event -> getPayLoad();
            // Remove entries
            if ($yndform instanceof Yndynamicform_Model_Form) {
                $list_entries = Engine_Api::_() -> getDbTable('entries', 'yndynamicform') -> getEntriesPaginator(array('form_id' => $yndform -> getIdentity()));
                if (count($list_entries) > 0) {
                    foreach ($list_entries as $item) {
                        Engine_Api::_()->getApi('core', 'fields') -> removeItemValues($item);
                        $item -> delete();
                    }
                }

                // Remove confirmations
                $list_confirmations = Engine_Api::_() -> getDbTable('confirmations', 'yndynamicform') -> getConfirmations(array('form_id' => $yndform -> getIdentity()));
                if (count($list_confirmations) > 0 ) {
                    foreach ($list_confirmations as $item)
                        $item -> delete();
                }

                // Remove notifications
                $list_notifications = Engine_Api::_() -> getDbTable('notifications', 'yndynamicform') -> getNotifications(array('form_id' => $yndform -> getIdentity()));
                if (count($list_notifications) > 0 ) {
                    foreach ($list_notifications as $item)
                        $item -> delete();
                }

                // Remove moderators
                $list_moderators = Engine_Api::_() -> getDbTable('moderators', 'yndynamicform') -> getModerators(array('form_id' => $yndform -> getIdentity()));
                if (count($list_moderators) > 0 ) {
                    foreach ($list_moderators as $item)
                        $item -> delete();
                }

                // Remove fields
                $fields_api = Engine_Api::_()->getApi('core', 'fields');
                $mapsTable = $fields_api -> getTable('yndynamicform_entry', 'maps');
                $maps = $mapsTable -> fetchAll($mapsTable->select()->where('option_id = ?', $yndform -> option_id)->order('order'));
                if (count($maps) > 0) {
                    foreach ($maps as $map) {
                        if ($map -> field_id && $map -> option_id == $yndform -> option_id)
                        {
                            $fields_api -> deleteMap($map);
                        }
                    }
                }
                $fields_api -> deleteOption('yndynamicform_entry', $yndform -> option_id);
            }
        }

    public function onUserSignupAfter($event)
    {
        $user = $event -> getPayLoad();
        if ($user instanceof User_Model_User) {
            $entries_table = Engine_Api::_() -> getDbTable('entries', 'yndynamicform');
            $select = $entries_table->select()->where('owner_id = ? ', 0)->where('user_email = ? ', $user -> email);
            $entries = $entries_table -> fetchAll($select);
            if (count($entries) > 0) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    foreach ($entries as $entry) {
                        $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $entry -> form_id);
                        if ($yndform) {
                            $entry->owner_id = $user->getIdentity();
                            $entry->user_email = null;
                            $entry->save();
                        }
                    }
                    Engine_Api::_()->getDbTable('notifications', 'activity')->addNotification($user,$user,$user,'yndynamicform_linked_form_submission', array(
                        'email_address' => $user -> email,
                        'site_name' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'My Communication'),
                        'manage_entries_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'yndynamicform_entry_general', true),
                    ));

                    $db -> commit();
                } catch (Exception $e) {
                    $db -> rollBack();
                    throw $e;
                }
            }
        }
    }
   public function routeShutdown(Zend_Controller_Request_Abstract $request) {

        if (substr($request->getPathInfo(), 1, 5) == "admin") {
            $module = $request->getModuleName();
            $controller = $request->getControllerName();
            $action = $request->getActionName();
            if ($module == 'core' && $controller == 'admin-content' && $action == 'index') {
                $sitepageLayoutCreate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate');
                if (!empty($sitepageLayoutCreate)) {
                    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page', null);
                    if (!empty($page_id)) {
                        $corepageTable = Engine_Api::_()->getDbtable('pages', 'core');
                        $corepageTableName = $corepageTable->info('name');
                        $select = $corepageTable->select()
                                ->from($corepageTableName)
                                ->where('page_id' . ' = ?', $page_id)
                                ->where('name' . ' = ?', 'sitepage_index_view')
                                ->limit(1);
                        $corepageTableInfo = $corepageTable->fetchRow($select);
                    }
                    if (!empty($corepageTableInfo)) {
                        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                        $redirector->gotoRoute(array('module' => 'sitepage', 'controller' => 'layout', 'action' => 'layout', 'page' => $page_id), 'admin_default', false);
                    }
                }
            }
        }

        //CHECK IF ADMIN
        if (substr($request->getPathInfo(), 1, 5) == "admin") {
            return;
        }


        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

// SITEPAGEURL WORK START
        $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
        if (!empty($sitepageUrlEnabled) && (($module == 'sitepage' && ($controller == 'index' || $controller == 'mobi') && $action == 'view') || ($module == 'core'))) {
            $front = Zend_Controller_Front::getInstance();

            // GET THE URL OF PAGE
            $urlO = $request->getRequestUri();
            $pageurl = '';

            // GET THE ROUTE BY WHICH PAGE WILL BE OPEN IF SHORTEN PAGEURL IS DISABLED
            $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlS', "pageitem");

            // GET THE BASE URL
            $base_url = $front->getBaseUrl();

            // MAKE A STRING OF BASEUL WITH ROUTESTART
            $string_url = $base_url . '/' . $routeStartS . '/';

            // FIND OUT THE POSITION OF ROUTESTART IF EXIST
            $pos_routestart = strpos($urlO, $string_url);
            if ($pos_routestart === false) {
                $index_routestart = 0;
                $pageurlArray = explode($base_url . '/', $urlO);
                $mainPageurl = strstr($pageurlArray[1], '/');

                // CHECK BASEDIRECTORY IS EXIST OR NOT
                if (empty($mainPageurl)) {
                    if (isset($pageurlArray[1])) {
                        $pageurl = $pageurlArray[1];
                    }
                } else {
                    $pageurl = $mainPageurl;
                }
            } else {
                $index_routestart = 1;
                $pageurlArray = explode($string_url, $urlO);
                $final_url = $pageurlArray[1];
                $mainPageurl = explode('/', $final_url);
                if (isset($mainPageurl[1]))
                    $pageurl = $mainPageurl[1];
            }

            // GET THE PAGE LIKES AFTER WHICH SHORTEN PAGEURL WILL BE WORK 
            $page_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.likelimit.forurlblock', "5");
            $params_array = array();
            if ($front->getBaseUrl() == '' && empty($index_routestart)) {
                $params_array = $pageurlArray;
                $params_array[0] = NULL;
                array_shift($params_array);
            } else {
                $params_array = explode('/', $pageurlArray[1]);
            }

            if (!empty($index_routestart)) {
                if (isset($params_array['1']))
                    $pageurl = $params_array['1'];
            }
            else {
                $pageurl = $params_array['0'];
            }

            $pageurl = explode('?', $pageurl);
            $pageurl = $pageurl[0];

            // MAKE THE OBJECT OF SITEPAGE
            $sitepageObject = Engine_Api::_()->getItem('sitepage_page', Engine_Api::_()->sitepage()->getPageId($pageurl));

            $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');

            // GET THE ARRAY OF BANNED PAGEURLS
            $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
                            ->where('word = ?', $pageurl)
                            ->query()->fetchColumn();
            $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1);
            if (empty($urlArray) && (!empty($change_url)) && !empty($sitepageObject) && ($sitepageObject->like_count >= $page_likes)) {
                if ((!empty($index_routestart))) {
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    unset($params_array[0]);
                    $redirector->gotoUrl(implode("/", $params_array));
                }
                $request->setModuleName('sitepage');
                $request->setControllerName('index');
                $request->setActionName('view');
                $request->setParam("page_url", $pageurl);
                $count = count($params_array);
                for ($i = 1; $i <= $count; $i++) {
                    if (array_key_exists($i, $params_array)) {
                        $j = $i + 1;
                        if (isset($params_array[$i]) && isset($params_array[$j]) && !empty($params_array[$i])) {
                            $request->setParam($params_array[$i], $params_array[$j]);
                        }
                    }
                }
                if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                    $sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
                }
            }
        }

        // SITEPAGEURL WORK END
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
            return;

        $mobile = $request->getParam("mobile");
        $session = new Zend_Session_Namespace('mobile');

        if ($mobile == "1") {
            $mobile = true;
            $session->mobile = true;
        } elseif ($mobile == "0") {
            $mobile = false;
            $session->mobile = false;
        } else {
            if (isset($session->mobile)) {
                $mobile = $session->mobile;
            } else {
                //CHECK TO SEE IF MOBILE
                if (Engine_Api::_()->mobi()->isMobile()) {
                    $mobile = true;
                    $session->mobile = true;
                } else {
                    $mobile = false;
                    $session->mobile = false;
                }
            }
        }

        if (!$mobile) {
            return;
        }
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($module == "sitepage") {
            if ($controller == "index" && $action == "home") {
                $request->setControllerName('mobi');
                $request->setActionName('home');
            }

            if ($controller == "index" && $action == "index") {
                $request->setControllerName('mobi');
                $request->setActionName('index');
            }

            if ($controller == "index" && $action == "view") {

                $request->setControllerName('mobi');
                $request->setActionName('view');
            }

            if ($controller == "index" && $action == "map") {
                $request->setControllerName('index');
                $request->setActionName('mobilemap');
            }
        }

        //CREATE LAYOUT
        $layout = Zend_Layout::startMvc();

        //SET OPTIONS
        $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
                ->setViewSuffix('tpl')
                ->setLayout(null);
    }
    
    public function onRenderLayoutDefaultSimple($event) {
        return $this->onRenderLayoutDefault($event, 'simple');
    }
    
    public function onRenderLayoutDefault($event, $mode = null) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();
        $membercategory_ids = '';
        $sitepage_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layout.setting', 1);
        $sitepage_hide_left_container = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.hide.left.container', 0);
        $sitepage_slding_effect = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.slding.effect', 1);
        $show_option = 1;
        if ($module == "sitepage") {
            if ($controller == "index" && $action == "index") {
                $siteinfo = $view->layout()->siteinfo;
                if (Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null)) {
                    $siteinfo['keywords'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
                }
                if (Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null)) {
                    $siteinfo['keywords'] .= ',' . Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
                }

                if (!empty($_GET['location'])) {
                    if (Zend_Controller_Front::getInstance()->getRequest()->getParam('location', null)) {
                        $siteinfo['keywords'] .= ',';
                    }
                    $siteinfo['keywords'] .= $_GET['location'];
                }
                $view->layout()->siteinfo = $siteinfo;
            }

            $category_name = "";
            $subcategory_name = "";
            if (Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null)) {
                $currentpageid = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
                $sitepageObject = Engine_Api::_()->getItem('sitepage_page', $currentpageid);
                $siteinfo = $view->layout()->siteinfo;
                $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepageObject->category_id);
                if (!empty($row->category_name)) {
                    $category_name = $row->category_name;
                    $siteinfo['keywords'] = $category_name;
                }
                $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepageObject->subcategory_id);
                if (!empty($row->category_name)) {
                    $subcategory_name = $row->category_name;
                    $siteinfo['keywords'] .= ',' . $subcategory_name;
                }

                if (!empty($sitepageObject->location)) {
                    $siteinfo['keywords'] .= ',' . $sitepageObject->location;
                }
                $view->layout()->siteinfo = $siteinfo;
            }
            if ($controller == "index" && $action == "view") {
                if (Zend_Controller_Front::getInstance()->getRequest()->getParam('page_url', null)) {
                    $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', 0);
                    $page_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_url', null);
                    $contentinformation = 0;
                    $view = $event->getPayload();
                    $id = Engine_Api::_()->sitepage()->getPageId($page_url);
                    $sitepage_title = '';
                    if (!empty($id)) {
                        $sitepage = Engine_Api::_()->getItem('sitepage_page', $id);
                        $sitepage_title = Engine_Api::_()->sitepage()->parseString($sitepage->title);
                        $sitepage_title = $view->string()->escapeJavascript($sitepage_title);
                    }

                    $content_id = 0;
                    $content_id3 = 0;
                    $tab_id = 0;
                    $tab_id3 = 0;
                    $tab_id4 = 0;
                    $widgetinformation = 0;
                    $tempcontent_name = "";
                    $tempcontent_id = 0;
                    $newtabid = 0;
                    $itemAlbumCount = 10;
                    $itemPhotoCount = 100;
                    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0) && !empty($sitepage)) {

                        $row = Engine_Api::_()->getDbtable('contentpages', 'sitepage')->getContentPageId($id);
                        if ($row !== null) {
                            $contentpage_id = $row->contentpage_id;
                            $siteinfo = $view->layout()->siteinfo;
                            $siteinfo['description'] = $row->description;
                            $siteinfo['keywords'] = $row->keywords;
                            $view->layout()->siteinfo = $siteinfo;
                            $rowinfo = Engine_Api::_()->getDbtable('content', 'sitepage')->getContentInformation($contentpage_id);

                            if (!empty($rowinfo)) {
                                foreach ($rowinfo as $key => $value) {
                                    if ($value->name == 'advancedactivity.home-feeds') {
                                        $content_id = $tab_id = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } elseif ($value->name == 'seaocore.feed') {
                                        $content_id = $tab_id = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } elseif ($value->name == 'activity.feed') {
                                        $content_id = $tab_id = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } else if ($value->name == 'core.profile-links') {
                                        $content_id3 = $tab_id3 = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id3;
                                        }
                                    } else if ($value->name == 'core.html-block') {
                                        $content_id4 = $tab_id4 = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id4;
                                        }
                                    }
                                }
                            }
                            if (!empty($contentpage_id)) {
                                $contentinfo = Engine_Api::_()->getDbtable('content', 'sitepage')->getContentByWidgetName('core.container-tabs', $contentpage_id);
                                if (empty($contentinfo)) {
                                    $contentinformation = 0;
                                } else {
                                    $contentinformation = 1;
                                }

                                $contentwidgetinfo = Engine_Api::_()->getDbtable('content', 'sitepage')->getContentByWidgetName('sitepage.widgetlinks-sitepage', $contentpage_id);
                                if (empty($contentwidgetinfo)) {
                                    $widgetinformation = 0;
                                } else {
                                    $widgetinformation = 1;
                                }
                            }
                            $default_content_id = Engine_Api::_()->getDbtable('content', 'sitepage')->getContentId($contentpage_id, $sitepage);
                            $tempcontent_name = $default_content_id['content_name'];
                            $tempcontent_id = $default_content_id['content_id'];
                            if (empty($default_content_id['itemAlbumCount'])) {
                                $itemAlbumCount = 10;
                            } else {
                                $itemAlbumCount = $default_content_id['itemAlbumCount'];
                            }
                            if (empty($default_content_id['itemPhotoCount'])) {
                                $itemPhotoCount = 100;
                            } else {
                                $itemPhotoCount = $default_content_id['itemPhotoCount'];
                            }
                            if (empty($default_content_id['resource_type_integration'])) {
                                $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                                $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);
                            } else {
                                $resource_type_integration = $default_content_id['resource_type_integration'];
                                $ads_display_integration = $default_content_id['ads_display_integration'];
                            }

                            $newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);
                            $tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
                            if ($tab_main) {
                                $tempcontent_name = Engine_Api::_()->getDbtable('content', 'sitepage')->getCurrentTabName($tab_main);
                                $tempcontent_id = $tab_main;
                            }
                        } else {
                            $contentpage_id = Engine_Api::_()->sitepage()->getWidgetizedPage()->page_id;
                            $siteinfo = $view->layout()->siteinfo;
                            $siteinfo['description'] = Engine_Api::_()->sitepage()->getWidgetizedPage()->description;
                            $siteinfo['keywords'] = Engine_Api::_()->sitepage()->getWidgetizedPage()->keywords;
                            $view->layout()->siteinfo = $siteinfo;
                            $rowinfo = Engine_Api::_()->getDbtable('admincontent', 'sitepage')->getContentInformation($contentpage_id);

                            if (!empty($rowinfo)) {
                                foreach ($rowinfo as $key => $value) {
                                    if ($value->name == 'advancedactivity.home-feeds') {
                                        $content_id = $tab_id = $value->admincontent_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } elseif ($value->name == 'seaocore.feed') {
                                        $content_id = $tab_id = $value->admincontent_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } elseif ($value->name == 'activity.feed') {
                                        $content_id = $tab_id = $value->admincontent_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } else if ($value->name == 'core.profile-links') {
                                        $content_id3 = $tab_id3 = $value->admincontent_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id3;
                                        }
                                    } else if ($value->name == 'core.html-block') {
                                        $content_id4 = $tab_id4 = $value->admincontent_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id4;
                                        }
                                    }
                                }
                            }
                            if (!empty($contentpage_id)) {
                                $contentinfo = Engine_Api::_()->getDbtable('admincontent', 'sitepage')->getContentByWidgetName('core.container-tabs', $contentpage_id);
                                if (empty($contentinfo)) {
                                    $contentinformation = 0;
                                } else {
                                    $contentinformation = 1;
                                }

                                $contentwidgetinfo = Engine_Api::_()->getDbtable('admincontent', 'sitepage')->getContentByWidgetName('sitepage.widgetlinks-sitepage', $contentpage_id);
                                if (empty($contentwidgetinfo)) {
                                    $widgetinformation = 0;
                                } else {
                                    $widgetinformation = 1;
                                }
                            }
                            $default_content_id = Engine_Api::_()->getDbtable('admincontent', 'sitepage')->getContentId($contentpage_id, $sitepage);
                            $tempcontent_name = $default_content_id['content_name'];
                            $tempcontent_id = $default_content_id['content_id'];
                            if (empty($default_content_id['itemAlbumCount'])) {
                                $itemAlbumCount = 10;
                            } else {
                                $itemAlbumCount = $default_content_id['itemAlbumCount'];
                            }
                            if (empty($default_content_id['itemPhotoCount'])) {
                                $itemPhotoCount = 100;
                            } else {
                                $itemPhotoCount = $default_content_id['itemPhotoCount'];
                            }
                            if (empty($default_content_id['resource_type_integration'])) {
                                $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                                $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);
                            } else {
                                $resource_type_integration = $default_content_id['resource_type_integration'];
                                $ads_display_integration = $default_content_id['ads_display_integration'];
                            }

                            $newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);
                            $tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
                            if ($tab_main) {
                                $tempcontent_name = Engine_Api::_()->getDbtable('admincontent', 'sitepage')->getCurrentTabName($tab_main);
                                $tempcontent_id = $tab_main;
                            }
                        }
                    } else {
                        $table = Engine_Api::_()->getDbtable('pages', 'core');
                        $select = $table->select()
                                ->where('name = ?', 'sitepage_index_view')
                                ->limit(1);
                        $row = $table->fetchRow($select);
                        if ($row !== null) {
                            $page_id = $row->page_id;
                            $table = Engine_Api::_()->getDbtable('content', 'core');
                            $select = $table->select()
                                    ->where("name IN ('sitepage.info-sitepage', 'seaocore.feed', 'advancedactivity.home-feeds', 'activity.feed', 'sitepage.location-sitepage', 'core.profile-links', 'core.html-block')")
                                    ->where('page_id = ?', $page_id)
                                    ->order('content_id ASC');
                            $rowinfo = $table->fetchAll($select);
                            if (!empty($rowinfo)) {
                                foreach ($rowinfo as $key => $value) {
                                    if ($value->name == 'advancedactivity.home-feeds') {
                                        $content_id = $tab_id = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } elseif ($value->name == 'seaocore.feed') {
                                        $content_id = $tab_id = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } elseif ($value->name == 'activity.feed') {
                                        $content_id = $tab_id = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id;
                                        }
                                    } else if ($value->name == 'core.profile-links') {
                                        $content_id3 = $tab_id3 = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id3;
                                        }
                                    } else if ($value->name == 'core.html-block') {
                                        $content_id4 = $tab_id4 = $value->content_id;
                                        if (empty($currenttabid)) {
                                            $currenttabid = $content_id4;
                                        }
                                    }
                                }
                            }
                        }

                        $table = Engine_Api::_()->getDbtable('content', 'core');
                        $tablename = $table->info('name');
                        if (!empty($page_id)) {
                            $selectContent = $table->select()
                                    ->from($tablename)
                                    ->where('name =?', 'core.container-tabs')
                                    ->where('page_id =?', $page_id)
                                    ->limit(1);
                            $contentinfo = $selectContent->query()->fetchAll();
                            if (empty($contentinfo)) {
                                $contentinformation = 0;
                            } else {
                                $contentinformation = 1;
                            }
                        }

                        if (!empty($page_id)) {
                            $selectContent = $table->select()
                                    ->from($tablename)
                                    ->where('name =?', 'sitepage.widgetlinks-sitepage')
                                    ->where('page_id =?', $page_id)
                                    ->limit(1);
                            $contentwidgetinfo = $selectContent->query()->fetchAll();
                            if (empty($contentwidgetinfo)) {
                                $widgetinformation = 0;
                            } else {
                                $widgetinformation = 1;
                            }
                        }

                        $selectPage = Engine_Api::_()->sitepage()->getWidgetizedPage();
                        if (!empty($selectPage)) {
                            $pageid = $selectPage->page_id;
                            if (!empty($pageid)) {
                                $tableCore = Engine_Api::_()->getDbtable('content', 'core');
                                $select = $tableCore->select();
                                $select_content = $select
                                        ->from($tableCore->info('name'))
                                        ->where('page_id = ?', $pageid)
                                        ->where('type = ?', 'container')
                                        ->where('name = ?', 'main')
                                        ->limit(1);
                                $content = $select_content->query()->fetchAll();
                                if (!empty($content)) {
                                    $select = $tableCore->select();
                                    $select_container = $select
                                            ->from($tableCore->info('name'), array('content_id'))
                                            ->where('page_id = ?', $pageid)
                                            ->where('type = ?', 'container')
                                            ->where('name = ?', 'middle')
                                            ->where('parent_content_id = ?', $content[0]['content_id'])
                                            ->limit(1);
                                    $container = $select_container->query()->fetchAll();
                                    if (!empty($container)) {
                                        $select = $tableCore->select();
                                        $container_id = $container[0]['content_id'];
                                        $select_middle = $select
                                                ->from($tableCore->info('name'))
                                                ->where('parent_content_id = ?', $container_id)
                                                ->where('type = ?', 'widget')
                                                ->where('name = ?', 'core.container-tabs')
                                                ->where('page_id = ?', $pageid)
                                                ->limit(1);
                                        $middle = $select_middle->query()->fetchAll();
                                        if (!empty($middle)) {
                                            $content_id = $middle[0]['content_id'];
                                        } else {
                                            $content_id = $container_id;
                                        }
                                    }
                                }

                                if (!empty($content_id) && !empty($sitepage)) {
                                    $select = $tableCore->select();
                                    $select_middle = $select
                                            ->from($tableCore->info('name'), array('content_id', 'name', 'params'))
                                            ->where('parent_content_id = ?', $content_id)
                                            ->where('type = ?', 'widget')
                                            ->where("name NOT IN ('sitepage.title-sitepage', 'seaocore.like-button','seaocore.seaocore-follow', 'sitepage.photorecent-sitepage', 'Facebookse.facebookse-commonlike', 'sitepage.thumbphoto-sitepage', 'sitepage.contactdetails-sitepage','sitelike.common-like-button', 'sitepage.page-profile-breadcrumb', 'sitepage.page-cover-information-sitepage')")
                                            ->where('page_id = ?', $pageid);

                                    $middle = $select_middle->query()->fetchAll();

                                    $itemAlbumCount = 10;
                                    $itemPhotoCount = 100;
                                    $select = $tableCore->select();
                                    $select_photo = $select
                                            ->from($tableCore->info('name'), array('params'))
                                            ->where('parent_content_id = ?', $content_id)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitepage.photos-sitepage')
                                            ->where('page_id = ?', $pageid)
                                            ->order('order DESC');

                                    $middlePhoto = $select_photo->query()->fetchColumn();
                                    if (!empty($middlePhoto)) {
                                        $photoParamsDecodedArray = Zend_Json_Decoder::decode($middlePhoto);
                                        if (isset($photoParamsDecodedArray['itemCount']) && !empty($photoParamsDecodedArray)) {
                                            $itemAlbumCount = $photoParamsDecodedArray['itemCount'];
                                        }
                                        if (isset($photoParamsDecodedArray['itemCount_photo']) && !empty($photoParamsDecodedArray)) {
                                            $itemPhotoCount = $photoParamsDecodedArray['itemCount_photo'];
                                        }
                                    }

                                    $flag = false;
                                    $editpermission = '';
                                    $isManageAdmin = '';
                                    $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                                    $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);
                                    $viewer = Engine_Api::_()->user()->getViewer();
                                    $viewer_id = $viewer->getIdentity();
                                    if (!empty($viewer_id)) {
                                        $level_id = $viewer->level_id;
                                    } else {
                                        $level_id = 0;
                                    }
                                    foreach ($middle as $value) {
                                        $content_name = $value['name'];
                                        switch ($content_name) {
                                            case 'sitepage.overview-sitepage':
                                                if (!empty($sitepage)) {
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
                                                    if (!empty($isManageAdmin)) {
                                                        $editpermission = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
                                                        if (!empty($editpermission) && empty($sitepage->overview)) {
                                                            $flag = true;
                                                        } elseif (empty($editpermission) && empty($sitepage->overview)) {
                                                            $flag = false;
                                                        } elseif (!empty($editpermission) && !empty($sitepage->overview)) {
                                                            $flag = true;
                                                        } elseif (empty($editpermission) && !empty($sitepage->overview)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                }
                                                break;
                                            case 'sitepage.location-sitepage':
                                                if (!empty($sitepage)) {
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
                                                    if (!empty($isManageAdmin)) {
                                                        $value['id'] = $sitepage->getIdentity();
                                                        $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);
                                                        if (!empty($location)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                }
                                                break;
                                            case 'seaocore.feed':
                                                $flag = true;
                                                break;
                                            case 'advancedactivity.home-feeds':
                                                $flag = true;
                                                break;
                                            case 'activity.feed':
                                                $flag = true;
                                                break;
                                            case 'sitepage.info-sitepage':
                                                $flag = true;
                                                break;
                                            case 'core.html-block':
                                                $flag = true;
                                                break;
                                            case 'core.profile-links':
                                                $flag = true;
                                                break;
                                            case 'sitepageintegration.profile-items':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration')) {
                                                    $content_params = $value['params'];
                                                    $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
                                                    $resource_type_integration = $paramsDecodedArray['resource_type'];
                                                    $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);

                                                    if (Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0)) {
                                                        $resource_type_integration = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', 0);
                                                        $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad.$resource_type_integration", 3);
                                                    }
                                                    //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", $resource_type_integration)) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, $resource_type_integration);
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
                                                    $resource_type = $resource_type_integration;

                                                    $pieces = explode("_", $resource_type);
                                                    if ($resource_type == 'document_0' || $resource_type == 'folder_0' || $resource_type == 'quiz_0') {
                                                        $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[1];
                                                        $paramsIntegration['resource_type'] = $resource_type = $pieces[0];
                                                    } else {
                                                        $paramsIntegration['listingtype_id'] = $listingTypeId = $pieces[2];
                                                        $paramsIntegration['resource_type'] = $resource_type = $pieces[0] . '_' . $pieces[1];
                                                    }

                                                    $paramsIntegration['page_id'] = $sitepage->page_id;
                                                    $paginator = Engine_Api::_()->getDbtable('contents', 'sitepageintegration')->getResults($paramsIntegration);
                                                    if ($paginator->getTotalItemCount() <= 0) {
                                                        $flag = false;
                                                    } else {
                                                        $flag = true;
                                                    }
                                                }
                                                break;
                                            case 'sitepagenote.profile-sitepagenotes':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL NOTES
                                                    $noteCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagenote', 'notes');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
                                                    if (!empty($isManageAdmin) || !empty($noteCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepageevent.profile-sitepageevents':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL EVENTS
                                                    $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageevent', 'events');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
                                                    if (!empty($isManageAdmin) || !empty($eventCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'siteevent.contenttype-events':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL EVENTS
                                                    $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'siteevent', 'events');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
                                                    if (!empty($isManageAdmin) || !empty($eventCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                                case 'document.contenttype-documents':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL DOCUMENTS
                                                    $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'document', 'documents');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
                                                    if (!empty($isManageAdmin) || !empty($documentCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepage.discussion-sitepage':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL TOPICS
                                                    $topicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'topics');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
                                                    if (!empty($isManageAdmin) || !empty($topicCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepage.photos-sitepage':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL ALBUMS
                                                    $albumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'albums');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
                                                    if (!empty($isManageAdmin) || !empty($albumCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepagemusic.profile-sitepagemusic':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemusic") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smcreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL PLAYLISTS
                                                    $musicCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagemusic', 'playlists');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smcreate');
                                                    if (!empty($isManageAdmin) || !empty($musicCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;

                                            case 'sitepagemember.profile-sitepagemembers':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL PLAYLISTS
                                                    $memberCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'membership');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smecreate');
                                                    if (!empty($isManageAdmin) || !empty($memberCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;

                                            case 'sitepagedocument.profile-sitepagedocuments':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL DOCUMENTS
                                                    $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagedocument', 'documents');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
                                                    if (!empty($isManageAdmin) || !empty($documentCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepagereview.profile-sitepagereviews':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
                                                    //TOTAL REVIEW
                                                    $reviewCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagereview', 'reviews');
                                                    $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepagereview_review', 'create');
                                                    if (!empty($level_allow) || !empty($reviewCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepagevideo.profile-sitepagevideos':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL VIDEO
                                                    $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagevideo', 'videos');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
                                                    if (!empty($isManageAdmin) || !empty($videoCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepagepoll.profile-sitepagepolls':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL POLL
                                                    $pollCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepagepoll', 'polls');
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
                                                    if (!empty($isManageAdmin) || !empty($pollCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepageoffer.profile-sitepageoffers':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageoffer") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'offer');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                    //TOTAL OFFERS
                                                    $can_edit = 1;
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
                                                    if (empty($isManageAdmin)) {
                                                        $can_edit = 0;
                                                    }

                                                    $can_offer = 1;
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'offer');

                                                    if (empty($isManageAdmin)) {
                                                        $can_offer = 0;
                                                    }

                                                    $can_create_offer = '';

                                                    //OFFER CREATION AUTHENTICATION CHECK
                                                    if ($can_edit == 1 && $can_offer == 1) {
                                                        $can_create_offer = 1;
                                                    }

                                                    //TOTAL OFFER
                                                    $offerCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepageoffer', 'offers');
                                                    if (!empty($can_create_offer) || !empty($offerCount)) {
                                                        $flag = true;
                                                    } else {
                                                        $flag = false;
                                                    }
                                                }
                                                break;
                                            case 'sitepageform.sitepage-viewform':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform')) {
                                                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                                                        if (Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageform") == 1) {
                                                            $flag = true;
                                                        }
                                                    } else {
                                                        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'form');
                                                        if (!empty($isPageOwnerAllow)) {
                                                            $flag = true;
                                                        }
                                                    }
                                                }
                                                break;
                                            case 'sitepagetwitter.feeds-sitepagetwitter':
                                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) {
                                                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'twitter');
                                                    if (!empty($isManageAdmin)) {
                                                        $flag = true;
                                                    }
                                                }
                                                break;
                                        }
                                        if (!empty($flag)) {
                                            $content_ids = $value['content_id'];
                                            $content_names = $value['name'];
                                            break;
                                        }
                                    }
                                }

                                $params = array('content_id' => $content_ids, 'content_name' => $content_names);

                                $tempcontent_name = $params['content_name'];
                                $tempcontent_id = $params['content_id'];

                                $newtabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', $tempcontent_id);

                                //////////////////////
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') && $newtabid) {
                                    $select = $tableCore->select();
                                    $select_member = $select
                                            ->from($tableCore->info('name'), array('params'))
                                            ->where('content_id = ?', $newtabid)
                                            ->where('type = ?', 'widget')
                                            ->where('name = ?', 'sitepagemember.profile-sitepagemembers');
                                    $member_params = $select_member->query()->fetchColumn(); //print_R($member_params);die;
                                    if (!empty($member_params)) {
                                        $photoParamsDecodedArray = Zend_Json_Decoder::decode($member_params);
                                        if (isset($photoParamsDecodedArray['show_option']) && !empty($photoParamsDecodedArray)) {
                                            $show_option = $photoParamsDecodedArray['show_option'];
                                        } else {
                                            $show_option = 1;
                                        }
                                        if (isset($photoParamsDecodedArray['membercategory_id']) && !empty($photoParamsDecodedArray)) {
                                            $membercategory_id = $photoParamsDecodedArray['membercategory_id'];
                                            $membercategory_ids = json_encode($membercategory_id);
                                        }
                                    } else {
                                        $show_option = 1;
                                        $membercategory_ids = null;
                                    }
                                }
                                //////////

                                $tab_main = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
                                if ($tab_main) {
                                    $current_tab_name = $tableCore->select()
                                            ->from($tableCore->info('name'), array('name'))
                                            ->where('content_id = ?', $tab_main)
                                            ->query()
                                            ->fetchColumn();
                                    $tempcontent_name = $current_tab_name;
                                    $tempcontent_id = $tab_main;
                                }
                            }
                        }
                    }

                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && !empty($contentinfo)) {
                        $page_communityads = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1);
                    } else {
                        $page_communityads = 0;
                    }

                    $siteinfo = $view->layout()->siteinfo;
                    if (!empty($sitepage)) {
                        if ($sitepage->category_id) {
                            $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->category_id);
                            if (!empty($row->category_name)) {
                                $category_name = $row->category_name;
                                $siteinfo['keywords'] = $category_name;
                            }
                            $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->subcategory_id);
                            if (!empty($row->category_name)) {
                                $subcategory_name = $row->category_name;
                                $siteinfo['keywords'] .= ',' . $subcategory_name;
                            }
                        }
                        if (!empty($sitepage->location)) {
                            $siteinfo['keywords'] .= ',' . $sitepage->location;
                        }
                    }
                    $script = null;
                    $view->layout()->siteinfo = $siteinfo;
                    $is_ajax = Zend_Controller_Front::getInstance()->getRequest()->getParam('isajax', null);
                    if (empty($is_ajax)) {
                        $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlS', "pageitem");
                        $page_url_integration = $routeStartS . '/' . Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id);
                        $overview = $view->translate('Overview');
                        $form = $view->translate('Form');
                        $review = $view->translate('Reviews');
                        $document = $view->translate('Documents');
                        $offer = $view->translate('Offers');
                        $poll = $view->translate('Polls');
                        $event = $view->translate('Events');
                        $note = $view->translate('Notes');
                        $photo = $view->translate('Photos');
                        $discussion = $view->translate('Discussions');
                        $map = $view->translate('Map');
                        $link = $view->translate('Links');
                        $video = $view->translate('Videos');
                        $music = $view->translate('Music');
                        $member = $view->translate('Member');
                        $page_url = Engine_Api::_()->sitepage()->getPageUrl($sitepage->page_id);
                        $script = <<<EOF
      var sitepage_layout_setting = '$sitepage_layout_setting';
	    var page_communityads = '$page_communityads';
	    var contentinformation = '$contentinformation';
      var page_hide_left_container = '$sitepage_hide_left_container';
      var sitepage_slding_effect = '$sitepage_slding_effect';
	    var page_showtitle = 0;
	    var prev_tab_class = '';
	    if(contentinformation == 0) {
	      page_showtitle = 1;
	    }
      window.addEvent('domready', function() {
        var globalContentElement = en4.seaocore.getDomElements('content');
	    	if($('main_tabs')) {
					switch ("$tempcontent_name") {
            case 'sitepage.photos-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Photo, '$tempcontent_id', 'photo', 'sitepage', 'photos-sitepage', page_showtitle, 'null', photo_ads_display, page_communityad_integration, adwithoutpackage,$itemAlbumCount, $itemPhotoCount);
						  	if($(globalContentElement).getElement('.layout_sitepage_photos_sitepage')) {
									hideLeftContainer (photo_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepage_photos_sitepage';
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepage_photos_sitepage')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepage_photos_sitepage'));
                }
							}
            break;
            case 'sitepagevideo.profile-sitepagevideos':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Video, '$tempcontent_id', 'video', 'sitepagevideo', 'profile-sitepagevideos', page_showtitle, 'null', video_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagevideo_profile_sitepagevideos')) {
									hideLeftContainer (video_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagevideo_profile_sitepagevideos';
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepagevideo_profile_sitepagevideos')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagevideo_profile_sitepagevideos'));
                }
							}
            break;
            case 'sitepagenote.profile-sitepagenotes':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Note, '$tempcontent_id', 'note', 'sitepagenote', 'profile-sitepagenotes', page_showtitle, 'null', note_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagenote_profile_sitepagenotes')) {
									hideLeftContainer (note_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagenote_profile_sitepagenotes';
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepagenote_profile_sitepagenotes')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagenote_profile_sitepagenotes'));
                }
							}
            break;
            case 'sitepagereview.profile-sitepagereviews':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Review, '$tempcontent_id', 'review', 'sitepagereview', 'profile-sitepagereviews', page_showtitle,'$page_url', review_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagereview_profile_sitepagereviews')) {
									hideLeftContainer (review_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagereview_profile_sitepagereviews';
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepagereview_profile_sitepagereviews')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagereview_profile_sitepagereviews'));
                }
							}
            break;
            case 'sitepageform.sitepage-viewform':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Form, '$tempcontent_id', 'form', 'sitepageform', 'sitepage-viewform', page_showtitle, '$page_url', form_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepageform_sitepage_viewform')) {
									hideLeftContainer (form_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepageform_sitepage_viewform';
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepageform_sitepage_viewform')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepageform_sitepage_viewform'));
                }
							}
            break;
            case 'sitepagedocument.profile-sitepagedocuments':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Document, '$tempcontent_id', 'document', 'sitepagedocument', 'profile-sitepagedocuments', page_showtitle, 'null', document_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagedocument_profile_sitepagedocuments')) {
									hideLeftContainer (document_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagedocument_profile_sitepagedocuments';
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepagedocument_profile_sitepagedocuments')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagedocument_profile_sitepagedocuments'));
                }
							}
            break;
            case 'sitepageevent.profile-sitepageevents':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'sitepageevent', 'profile-sitepageevents', page_showtitle,'null', event_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepageevent_profile_sitepageevents')) {
									hideLeftContainer (event_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepageevent_profile_sitepageevents';            
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepageevent_profile_sitepageevents')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepageevent_profile_sitepageevents'));
                }
							}
            break;
            case 'siteevent.contenttype-events':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'siteevent', 'contenttype-events', page_showtitle,'null', event_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_siteevent_contenttype_events')) {
									hideLeftContainer (event_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_siteevent_contenttype_events';            
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_siteevent_contenttype_events')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_siteevent_contenttype_events'));
                }
							}
            break;
            case 'sitepagepoll.profile-sitepagepolls':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Poll, '$tempcontent_id', 'poll', 'sitepagepoll', 'profile-sitepagepolls', page_showtitle,'null', poll_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagepoll_profile_sitepagepolls')) {
									hideLeftContainer (poll_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagepoll_profile_sitepagepolls';            
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepagepoll_profile_sitepagepolls')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagepoll_profile_sitepagepolls'));
                }
							}
            break;
            case 'sitepagemusic.profile-sitepagemusic':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Music, '$tempcontent_id', 'music', 'sitepagemusic', 'profile-sitepagemusic', page_showtitle,'null', music_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagemusic_profile_sitepagemusic')) {
									hideLeftContainer (music_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagemusic_profile_sitepagemusic';            
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepagemusic_profile_sitepagemusic')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagemusic_profile_sitepagemusic'));
                }
							}
            break;
            
            case 'sitepagemember.profile-sitepagemembers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Member, '$tempcontent_id', 'member', 'sitepagemember', 'profile-sitepagemembers', page_showtitle,'null', member_ads_display, page_communityad_integration,adwithoutpackage, 'null', 'null', 'null', 'null', 'null', 'null', 'null','$show_option', '$membercategory_ids', '1');
						  	if($(globalContentElement).getElement('.layout_sitepagemember_profile_sitepagemember')) {
									hideLeftContainer (member_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagemember_profile_sitepagemember';            
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepagemember_profile_sitepagemember')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagemember_profile_sitepagemember'));
                }
							}
            break;
            
            case 'sitepageoffer.profile-sitepageoffers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Offer, '$tempcontent_id', 'offer', 'sitepageoffer', 'profile-sitepageoffers', page_showtitle,'null', offer_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepageoffer_profile_sitepageoffers')) {
									hideLeftContainer (offer_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepageoffer_profile_sitepageoffers';
								page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepageoffer_profile_sitepageoffers')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepageoffer_profile_sitepageoffers'));
                }
							}
            break;
            case 'sitepage.discussion-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Discusssion, '$tempcontent_id', 'discussion', 'sitepage', 'discussion-sitepage', page_showtitle, 'null', discussion_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepage_discussion_sitepage')) {
									hideLeftContainer (discussion_ads_display, page_communityad_integration, adwithoutpackage);
							  }

                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepage_discussion_sitepage';
                page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepage_discussion_sitepage')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepage_discussion_sitepage'));
                }
							}
            break;
            case 'sitepage.overview-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideLeftContainer (overview_ads_display, page_communityad_integration, adwithoutpackage);
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepage_overview_sitepage';
          
                page_showtitle = 0;
                if($('main_tabs').getElement('.tab_layout_sitepage_overview_sitepage')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepage_overview_sitepage'));
                }
							}
            break;
            case 'core.profile-links':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {

                if($('main_tabs').getElement('.tab_layout_core_profile_links')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_profile_links'));
                }
								page_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_core_profile_links';
							}
            break;
            case 'sitepage.location-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
      					hideLeftContainer (location_ads_display, page_communityad_integration, adwithoutpackage);

                if($('main_tabs').getElement('.tab_layout_sitepage_location_sitepage')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepage_location_sitepage'));
                }
								page_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepage_location_sitepage';
							}
            break;
            case 'sitepage.info-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
								page_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepage_info_sitepage';
                if($('main_tabs').getElement('.tab_layout_sitepage_info_sitepage')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepage_info_sitepage'));
                }
							}
            break;
          case 'sitepageintegration.profile-items':   
             if(is_ajax_divhide == '' && "$tab_main" == '') {
               if($newtabid == "$tempcontent_id" && $newtabid != 0) {         
                ShowContent('$tempcontent_id', execute_Request_$resource_type_integration, '$tempcontent_id', 'null', 'sitepageintegration', 'profile-items', page_showtitle, '$page_url_integration', $ads_display_integration, page_communityad_integration,
  adwithoutpackage, null,null,'$resource_type_integration', null, 1);
                  prev_tab_id = "$newtabid";
                  prev_tab_class = 'layout_sitepageintegration_profile_items';
                  if($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
                    $(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'block';
                  }
               }
             }
            break;
            case 'sitepagetwitter.feeds-sitepagetwitter':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
								page_showtitle = 0;
                prev_tab_id = "$newtabid";
                prev_tab_class = 'layout_sitepagetwitter_feeds_sitepagetwitter';
                if($('main_tabs').getElement('.tab_layout_sitepagetwitter_feeds_sitepagetwitter')) {
                  tabContainerSwitch($('main_tabs').getElement('.tab_layout_sitepagetwitter_feeds_sitepagetwitter'));
                }
							}
            break;  
            case 'core.html-block':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_html_block'));
              break;
            case 'activity.feed':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_activity_feed'));
              break;
            case 'seaocore.feed':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_seaocore_feed'));
              break;
            case 'advancedactivity.home-feeds':
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_advancedactivity_home_feeds'));
              break;
					 }
			    if($('main_tabs').getElement('.tab_$tab_id')){
			      $('main_tabs').getElement('.tab_$tab_id').addEvent('click', function() {

            if($(globalContentElement).getElement('.layout_seaocore_feed')) {
							$(globalContentElement).getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
							scrollToTopForPage($('layout_seaocore_feed'));
            } else if($(globalContentElement).getElement('.layout_activity_feed')) {
							$(globalContentElement).getElement('.layout_activity_feed').id = "layout_activity_feed";
							scrollToTopForPage($('layout_activity_feed'));
            } else if($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
							$(globalContentElement).getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
							scrollToTopForPage($('layout_advancedactivity_home_feeds'));
            }
			      if($('profile_status')) {
			        $('profile_status').innerHTML = "<h2>$sitepage_title</h2>";
            }
            if($('main_tabs').getElement('.tab_layout_activity_feed')) {
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_activity_feed'));
            }

						setLeftLayoutForPage();
			      prev_tab_id = '$tab_id';

					});
				  }          
          
          if($('main_tabs').getElement('.tab_$tab_id3')){
			      $('main_tabs').getElement('.tab_$tab_id3').addEvent('click', function() {
            if($('main_tabs').getElement('.tab_layout_core_profile_links')) {
              tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_profile_links'));
            }

			      prev_tab_id = '$tab_id3';
						setLeftLayoutForPage();
            });
				  }
          if($('main_tabs').getElement('.tab_$tab_id4')){
			      $('main_tabs').getElement('.tab_$tab_id4').addEvent('click', function() {
//             if($('main_tabs').getElement('.tab_layout_core_html_block')) {
//               tabContainerSwitch($('main_tabs').getElement('.tab_layout_core_html_block'));
//             }

			      prev_tab_id = '$tab_id4';
						setLeftLayoutForPage();
            });
				  }
				}
				else
	      {      
	       switch ("$tempcontent_name") {
            case 'sitepage.photos-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $photo </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepage_photos_sitepage > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $photo</div>";
		            ShowContent('$tempcontent_id', execute_Request_Photo, '$tempcontent_id', 'photo', 'sitepage', 'photos-sitepage', page_showtitle, 'null', photo_ads_display, page_communityad_integration, adwithoutpackage,$itemAlbumCount, $itemPhotoCount);
						  	if($(globalContentElement).getElement('.layout_sitepage_photos_sitepage')) {
									$(globalContentElement).getElement('.layout_sitepage_photos_sitepage').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepage_photos_sitepage';
							  }
							  hideWidgets();
							}
            break;
            case 'sitepagevideo.profile-sitepagevideos':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $video </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepagevideo_profile_sitepagevideos > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $video</div>";
		            ShowContent('$tempcontent_id', execute_Request_Video, '$tempcontent_id', 'video', 'sitepagevideo', 'profile-sitepagevideos', page_showtitle, 'null', video_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagevideo_profile_sitepagevideos')) {
									$(globalContentElement).getElement('.layout_sitepagevideo_profile_sitepagevideos').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepagevideo_profile_sitepagevideos';
							  }
								hideWidgets();
							}
            break;
            case 'sitepagenote.profile-sitepagenotes':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $note </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepagenote_profile_sitepagenotes > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $note</div>";
		            ShowContent('$tempcontent_id', execute_Request_Note, '$tempcontent_id', 'note', 'sitepagenote', 'profile-sitepagenotes', page_showtitle, 'null', note_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagenote_profile_sitepagenotes')) {
									$(globalContentElement).getElement('.layout_sitepagenote_profile_sitepagenotes').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepagenote_profile_sitepagenotes';
							  }
								hideWidgets();
							}
            break;
            case 'sitepagereview.profile-sitepagereviews':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $review </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepagereview_profile_sitepagereviews > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $review </div>";
		            ShowContent('$tempcontent_id', execute_Request_Review, '$tempcontent_id', 'review', 'sitepagereview', 'profile-sitepagereviews', page_showtitle,'null', review_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagereview_profile_sitepagereviews')) {
									$(globalContentElement).getElement('.layout_sitepagereview_profile_sitepagereviews').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepagereview_profile_sitepagereviews';
							  }
								hideWidgets();
							}
            break;
            case 'sitepageform.sitepage-viewform':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $form </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepageform_sitepage_viewform > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $form </div>";
		            ShowContent('$tempcontent_id', execute_Request_Form, '$tempcontent_id', 'form', 'sitepageform', 'sitepage-viewform', page_showtitle, '$page_url', form_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepageform_sitepage_viewform')) {
									$(globalContentElement).getElement('.layout_sitepageform_sitepage_viewform').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepageform_sitepage_viewform';
							  }
								hideWidgets();
							}
            break;
            case 'sitepagedocument.profile-sitepagedocuments':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $document </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepagedocument_profile_sitepagedocuments > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $document </div>";
		            ShowContent('$tempcontent_id', execute_Request_Document, '$tempcontent_id', 'document', 'sitepagedocument', 'profile-sitepagedocuments', page_showtitle, 'null', document_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagedocument_profile_sitepagedocuments')) {
									$(globalContentElement).getElement('.layout_sitepagedocument_profile_sitepagedocuments').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepagedocument_profile_sitepagedocuments';
							  }
								hideWidgets();
							}
            break;
            case 'sitepageevent.profile-sitepageevents':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $event </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepageevent_profile_sitepageevents > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $event </div>";
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'sitepageevent', 'profile-sitepageevents', page_showtitle,'null', event_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepageevent_profile_sitepageevents')) {
									$(globalContentElement).getElement('.layout_sitepageevent_profile_sitepageevents').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepageevent_profile_sitepageevents';
							  }
								hideWidgets();
							}
            break;
            case 'siteevent.contenttype-events':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $event </h2>";
								}
								$(globalContentElement).getElement('.layout_siteevent_contenttype_events > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $event </div>";
		            ShowContent('$tempcontent_id', execute_Request_Event, '$tempcontent_id', 'event', 'siteevent', 'contenttype-events', page_showtitle,'null', event_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_siteevent_contenttype_events')) {
									$(globalContentElement).getElement('.layout_siteevent_contenttype_events').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_siteevent_contenttype_events';
							  }
								hideWidgets();
							}
            break;
            case 'sitepagepoll.profile-sitepagepolls':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $poll </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepagepoll_profile_sitepagepolls > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $poll </div>";
		            ShowContent('$tempcontent_id', execute_Request_Poll, '$tempcontent_id', 'poll', 'sitepagepoll', 'profile-sitepagepolls', page_showtitle,'null', poll_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagepoll_profile_sitepagepolls')) {
									$(globalContentElement).getElement('.layout_sitepagepoll_profile_sitepagepolls').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepagepoll_profile_sitepagepolls';
							  }
								hideWidgets();
							}
            break;
            case 'sitepagemusic.profile-sitepagemusic':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $music </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepagemusic_profile_sitepagemusic > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $music </div>";
		            ShowContent('$tempcontent_id', execute_Request_Music, '$tempcontent_id', 'music', 'sitepagemusic', 'profile-sitepagemusic', page_showtitle,'null', music_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepagemusic_profile_sitepagemusic')) {
									$(globalContentElement).getElement('.layout_sitepagemusic_profile_sitepagemusic').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepagemusic_profile_sitepagemusic';
							  }
								hideWidgets();
							}
            break;
            
            case 'sitepagemember.profile-sitepagemembers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $member </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepagemember_profile_sitepagemember > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $member </div>";
		            ShowContent('$tempcontent_id', execute_Request_Member, '$tempcontent_id', 'member', 'sitepagemember', 'profile-sitepagemembers', page_showtitle,'null', member_ads_display, page_communityad_integration,adwithoutpackage, 'null', 'null', 'null', 'null', 'null', 'null', 'null','$show_option', '$membercategory_ids', '1');
						  	if($(globalContentElement).getElement('.layout_sitepagemember_profile_sitepagemember')) {
									$(globalContentElement).getElement('.layout_sitepagemember_profile_sitepagemember').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepagemember_profile_sitepagemember';
							  }
								hideWidgets();
							}
            break;
            
            
            case 'sitepageoffer.profile-sitepageoffers':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $offer </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepageoffer_profile_sitepageoffers > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $offer </div>";
		            ShowContent('$tempcontent_id', execute_Request_Offer, '$tempcontent_id', 'offer', 'sitepageoffer', 'profile-sitepageoffers', page_showtitle,'null', offer_ads_display, page_communityad_integration,adwithoutpackage);
						  	if($(globalContentElement).getElement('.layout_sitepageoffer_profile_sitepageoffers')) {
									$(globalContentElement).getElement('.layout_sitepageoffer_profile_sitepageoffers').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepageoffer_profile_sitepageoffers';
							  }
								hideWidgets();
							}
            break;
            case 'sitepage.discussion-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
		            ShowContent('$tempcontent_id', execute_Request_Discusssion, '$tempcontent_id', 'discussion', 'sitepage', 'discussion-sitepage', page_showtitle, 'null', discussion_ads_display, page_communityad_integration,adwithoutpackage);
		            if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $discussion </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepage_discussion_sitepage > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $discussion </div>";
						  	if($(globalContentElement).getElement('.layout_sitepage_discussion_sitepage')) {
									$(globalContentElement).getElement('.layout_sitepage_discussion_sitepage').style.display = 'block';
									prev_tab_id = "$newtabid";
									prev_tab_class = 'layout_sitepage_discussion_sitepage';
							  }
								hideWidgets();
							}
            break;
            case 'sitepage.overview-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {

                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $overview </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepage_overview_sitepage > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $overview</div>";

						    if($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
									 $(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'block';
									 prev_tab_id = "$newtabid";
									 prev_tab_class = 'layout_sitepage_overview_sitepage';
							  }
								hideWidgetsForModule('sitepageoverview');
							}
            break;
            case 'core.profile-links':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $link </h2>";
								}
								$(globalContentElement).getElement('.layout_core_profile_links > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's  $link</div>";
                hideWidgetsForModule('sitepagelink');
							}
            break;
            case 'sitepage.location-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                if($('profile_status')) {
		    			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $map </h2>";
								}
								$(globalContentElement).getElement('.layout_sitepage_location_sitepage > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's $map</div>";
						    if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
									 $(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'block';
									 prev_tab_id = "$newtabid";
									 prev_tab_class = 'layout_sitepage_location_sitepage';
							  }
								hideWidgetsForModule('sitepagelocation');
							}
            break;
            case 'sitepageintegration.profile-items':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {                
                ShowContent('$tempcontent_id', execute_Request_$resource_type_integration, '$tempcontent_id', 'null', 'sitepageintegration', 'profile-items', page_showtitle, '$page_url_integration', $ads_display_integration, page_communityad_integration,
  adwithoutpackage, null,null,'$resource_type_integration', null, 1);
                  prev_tab_id = "$newtabid";
                  prev_tab_class = 'layout_sitepageintegration_profile_items';
                  if($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
                    $(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'block';
                  }
                 hideWidgetsForModule('sitepageintegration');
               }
            break;          
            case 'activity.feed':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitepageactivityfeed');

							}
            break;
           case 'seaocore.feed':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideWidgetsForModule('sitepageseaocoreactivityfeed');

								if($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) { 	                            $(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'block';
								}
								if($(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            $(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
								}
		
								if($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) { 	                            	$(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'block';
								}
						  }
            break;
           case 'advancedactivity.home-feeds':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
                hideWidgetsForModule('sitepageadvancedactivityactivityfeed');
	
						  }
            break;            
            case 'sitepage.info-sitepage':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitepageinfo');

							}
            break;
            case 'sitepagetwitter.feeds-sitepagetwitter':
              if($newtabid == "$tempcontent_id" && $newtabid != 0) {
						    hideWidgetsForModule('sitepagetwitter');

							}
            break;
					}
				  if($widgetinformation == 0) {
				    if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
							$(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'block';
						}
						if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
							$(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'block';
						}

						if($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) { 	                            $(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'block';
					}
 								if($(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo')) { 	                            $(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
								}
						if($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) { 	                            $(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'block';
					}
						if($(globalContentElement).getElement('.layout_core_profile_links')) {
							$(globalContentElement).getElement('.layout_core_profile_links').style.display = 'block';
						}
					}

    			$$('.tab_$tab_id').addEvent('click', function() {
    			  if($('profile_status')) {
    			    $('profile_status').innerHTML = "<h2>$sitepage_title</h2>";
						}
						if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
							$(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'none';
						}
            if($(globalContentElement).getElement('.layout_sitepage_photos_sitepage')) {
					    $(globalContentElement).getElement('.layout_sitepage_photos_sitepage').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepagevideo_profile_sitepagevideos')) {
					    $(globalContentElement).getElement('.layout_sitepagevideo_profile_sitepagevideos').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepage_discussion_sitepage')) {
					    $(globalContentElement).getElement('.layout_sitepage_discussion_sitepage').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepageoffer_profile_sitepageoffers')) {
					    $(globalContentElement).getElement('.layout_sitepageoffer_profile_sitepageoffers').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepagedocument_profile_sitepagedocuments')) {
					    $(globalContentElement).getElement('.layout_sitepagedocument_profile_sitepagedocuments').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepagereview_profile_sitepagereviews')) {
					    $(globalContentElement).getElement('.layout_sitepagereview_profile_sitepagereviews').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepagepoll_profile_sitepagepolls')) {
					    $(globalContentElement).getElement('.layout_sitepagepoll_profile_sitepagepolls').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepagenote_profile_sitepagenotes')) {
					    $(globalContentElement).getElement('.layout_sitepagenote_profile_sitepagenotes').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepageevent_profile_sitepageevents')) {
					    $(globalContentElement).getElement('.layout_sitepageevent_profile_sitepageevents').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_siteevent_contenttype_events')) {
					    $(globalContentElement).getElement('.layout_siteevent_contenttype_events').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {					    
              $$('.layout_sitepageintegration_profile_items').setStyle('display', 'none');
            } 
            if($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
					    $(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'none';
            }
            if($(globalContentElement).getElement('.layout_sitepagemusic_profile_sitepagemusic')) {
					    $(globalContentElement).getElement('.layout_sitepagemusic_profile_sitepagemusic').style.display = 'none';
            }
      			if($(globalContentElement).getElement('.layout_sitepageform_sitepage_viewform')) {
						  $(globalContentElement).getElement('.layout_sitepageform_sitepage_viewform').style.display = 'none';
            }
						if($(globalContentElement).getElement('.layout_core_profile_links')) {
							$(globalContentElement).getElement('.layout_core_profile_links').style.display = 'none';
						}
						if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
							$(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'none';
						}
// 						if($(globalContentElement).getElement('.layout_sitepagetwitter_feeds_sitepagetwitter')) {
// 							$(globalContentElement).getElement('.layout_sitepagetwitter_feeds_sitepagetwitter').style.display = 'none';
// 						}
						if($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
							$(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
					  }
						if($(globalContentElement).getElement('.layout_activity_feed')) {
							$(globalContentElement).getElement('.layout_activity_feed').style.display = 'block';
						}
        	  if($(globalContentElement).getElement('.layout_seaocore_feed')) {
							$(globalContentElement).getElement('.layout_seaocore_feed').style.display = 'block';
						}
            if($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
							$(globalContentElement).getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
						}

						setLeftLayoutForPage();
            if($(globalContentElement).getElement('.layout_seaocore_feed')) {
							$(globalContentElement).getElement('.layout_seaocore_feed').id = "layout_seaocore_feed";
							scrollToTopForPage($('layout_seaocore_feed'));
            } else if($(globalContentElement).getElement('.layout_activity_feed')) {
							$(globalContentElement).getElement('.layout_activity_feed').id = "layout_activity_feed";
							scrollToTopForPage($('layout_activity_feed'));
            } else if($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
							$(globalContentElement).getElement('.layout_advancedactivity_home_feeds').id = "layout_advancedactivity_home_feeds";
							scrollToTopForPage($('layout_advancedactivity_home_feeds'));
            }


//				    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '$tab_id') {
//				      $('id_' + prev_tab_id).style.display = "none";
//				    }
			    prev_tab_id = '$tab_id';
				 });

  			$$('.tab_$tab_id3').addEvent('click', function() {
  			  if($('profile_status')) {
  			    $('profile_status').innerHTML = "<h2>$sitepage_title &raquo; $link </h2>";
					}
					$(globalContentElement).getElement('.layout_core_profile_links > h3').innerHTML = "<div class='layout_simple_head'>$sitepage_title's $link</div>";

					if($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
						$(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'none';
					}
					if($(globalContentElement).getElement('.layout_core_profile_links')) {
						$(globalContentElement).getElement('.layout_core_profile_links').style.display = 'block';
					}
					if($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
						$(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'none';
					}

					if($(globalContentElement).getElement('.layout_activity_feed')) {
						$(globalContentElement).getElement('.layout_activity_feed').style.display = 'none';
					}
        	if($(globalContentElement).getElement('.layout_seaocore_feed')) {
						$(globalContentElement).getElement('.layout_seaocore_feed').style.display = 'none';
					}
          if($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
					 	$(globalContentElement).getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
					}
					if($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
						$(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
				  }
			    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '$tab_id3') {
			      $('id_' + prev_tab_id).style.display = "none";
			    }
		    prev_tab_id = '$tab_id3';
		 	});        
     
          
     }
	 });
// 	 window.addEvent('domready', function() {
// 
//       if($('thumb_icon')) {
// 	      if($currenttabid == 0) {
// 	       $('thumb_icon').style.display = 'none';
// 			  }
// 	    }
// 		});
EOF;
                    }
                    if ("$tempcontent_name" == 'sitepage.discussion-sitepage' || "$tempcontent_name" == 'sitepage.photos-sitepage' || "$tempcontent_name" == 'sitepagevideo.profile-sitepagevideos' || "$tempcontent_name" == 'sitepagenote.profile-sitepagenotes' || "$tempcontent_name" == 'sitepagereview.profile-sitepagereviews' || "$tempcontent_name" == 'sitepageform.sitepage-viewform' || "$tempcontent_name" == 'sitepagedocument.profile-sitepagedocuments' || "$tempcontent_name" == 'sitepageevent.profile-sitepageevents' || "$tempcontent_name" == 'sitepagepoll.profile-sitepagepolls' || "$tempcontent_name" == 'sitepagemusic.profile-sitepagemusic' || "$tempcontent_name" == 'sitepagemember.profile-sitepagemembers' || "$tempcontent_name" == 'sitepageoffer.profile-sitepageoffers' || "$tempcontent_name" == 'sitepagetwitter.feeds-sitepagetwitter' || "$tempcontent_name" == 'sitevent.contenttype-events') {
                        Engine_Api::_()->sitepage()->showAdWithPackage($sitepage);
                        $view->headScript()
                                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideWidgets.js');
                    }

                    $view->headScript()
                            ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideTabs.js')
                            ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');


                    $view->headScript()
                            ->appendScript("   var page_communityads = '$page_communityads';
	    var contentinformation = '$contentinformation';
	    var page_showtitle = 0;      
	    var prev_tab_class = '';");
                    if (!empty($script)) {
                        $view->headScript()
                                ->appendScript($script);
                    }
                }
            }
        }
    }

    public function onStatistics($event) {
        $table = Engine_Api::_()->getDbTable('pages', 'sitepage');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count');
        $event->addResponse($select->query()->fetchColumn(0), 'page');
    }

    public function onUserDeleteBefore($event) {
        $payload = $event->getPayload();

        if ($payload instanceof User_Model_User) {

            $user_id = $payload->getIdentity();

            //GET PAGE TABLE
            $sitepageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');

            Engine_Api::_()->getDbtable('claims', 'sitepage')->delete(array('user_id =?' => $user_id));

            Engine_Api::_()->getDbtable('listmemberclaims', 'sitepage')->delete(array('user_id = ?' => $user_id));

            Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->delete(array('user_id = ?' => $user_id));

            //START ALBUM CODE
            $table = Engine_Api::_()->getItemTable('sitepage_photo');
            $select = $table->select()->where('user_id = ?', $user_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $photo) {
                    $photo->delete();
                }
            }

            $table = Engine_Api::_()->getItemTable('sitepage_album');
            $select = $table->select()->where('owner_id = ?', $user_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $album) {
                    $album->delete();
                }
            }
            //END ALBUM CODE
            //START DISUCSSION CODE
            $sitepageDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
            if ($sitepageDiscussionEnabled) {

                $table = Engine_Api::_()->getItemTable('sitepage_topic');
                $select = $table->select()->where('user_id = ?', $user_id);
                $rows = $table->fetchAll($select);
                if (!empty($rows)) {
                    foreach ($rows as $topic) {
                        $topic->delete();
                    }
                }

                $table = Engine_Api::_()->getItemTable('sitepage_post');
                $select = $table->select()->where('user_id = ?', $user_id);
                $rows = $table->fetchAll($select);
                if (!empty($rows)) {
                    foreach ($rows as $post) {
                        $post->delete();
                    }
                }

                Engine_Api::_()->getDbtable('topicwatches', 'sitepage')->delete(array('user_id = ?' => $user_id));
            }
            //END DISUCSSION CODE

            $sitepageSelect = $sitepageTable->select()->where('owner_id = ?', $user_id);

            foreach ($sitepageTable->fetchAll($sitepageSelect) as $sitepage) {
                Engine_Api::_()->sitepage()->onPageDelete($sitepage->page_id);
            }

            //LIKE COUNT DREASE FORM PAGE TABLE.
            $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
            $likesTableSelect = $likesTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'sitepage_page');
            $results = $likesTable->fetchAll($likesTableSelect);
            foreach ($results as $user) {
                $resource = Engine_Api::_()->getItem('sitepage_page', $user->resource_id);
                $resource->like_count--;
                $resource->save();
            }
        }
    }

    public function addActivity($event) {
        $payload = $event->getPayload();
        $subject = $payload['subject'];
        $object = $payload['object'];

        // Only for object=event
        if (strpos($payload['type'], 'like_') === false && $object instanceof Sitepage_Model_Page /* &&
          Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view') */) {
            $event->addResponse(array(
                'type' => 'sitepage_page',
                'identity' => $object->getIdentity()
            ));
        }
    }

    public function getActivity($event) {
        // Detect viewer and subject
        $payload = $event->getPayload();
        $user = null;
        $subject = null;
        if ($payload instanceof User_Model_User) {
            $user = $payload;
        } else if (is_array($payload)) {
            if (isset($payload['for']) && $payload['for'] instanceof User_Model_User) {
                $user = $payload['for'];
            }
            if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract) {
                $subject = $payload['about'];
            }
        }
        if (null === $user) {
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                $user = $viewer;
            }
        }
        if (null === $subject && Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
        }


        // Get like pages
        if ($user && empty($subject)) {
            $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');



            if ($settingsCoreApi->sitepage_feed_onlyliked) {
                $data = Engine_Api::_()->sitepage()->getMemberLikePagesOfIds($user);
                if (!empty($data) && is_array($data)) {
                    $event->addResponse(array(
                        'type' => 'sitepage_page',
                        'data' => $data,
                    ));
                }
            } else if (!$settingsCoreApi->sitepage_feed_onlyliked && Engine_Api::_()->hasModuleBootstrap('sitepagemember')) {
                $data = Engine_Api::_()->getDbtable('membership', 'sitepage')->getMembershipsOfIds($user);
                if (!empty($data) && is_array($data)) {
                    $event->addResponse(array(
                        'type' => 'sitepage_page',
                        'data' => $data,
                    ));
                }
            }
        } else if ($subject && ($subject->getType() == 'sitepage_page')) {
            $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
            if ($settingsCoreApi->sitepage_feed_type || 1) {
                $event->addResponse(array(
                    'type' => 'sitepage_page',
                    'data' => array($subject->getIdentity()),
                ));
            }
        } else if ($subject && ($subject->getType() == 'user')) {
            if (Engine_Api::_()->hasModuleBootstrap('sitepagemember')) {
                $data = Engine_Api::_()->getDbtable('membership', 'sitepage')->getMembershipsOfIds($user);
            } else {
                $data = Engine_Api::_()->getApi('subCore', 'sitepage')->getMemberFeedsForPageOfIds($subject);
            }
            $event->addResponse(array(
                'type' => 'sitepage_page',
                'data' => $data,
            ));
        }
    }

    public function onActivityActionCreateAfter($event) {

        $payload = $event->getPayload();
        if ($payload->object_type == 'sitepage_page' && ($payload->getTypeInfo()->type == 'sitepage_post_self' || $payload->getTypeInfo()->type == 'sitepage_post') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {

            $viewer = Engine_Api::_()->user()->getViewer();
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

            $notidicationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feed.type', 0);

            $page_id = $payload->getObject()->page_id;
            $user_id = $payload->getSubject()->user_id;

            $subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
            $owner = $subject->getOwner();

            $notifications = Engine_Api::_()->getDbtable('notifications', 'activity');

            //previous notification is delete.
            $notifications->delete(array('type =?' => "sitepage_notificationpost", 'object_type = ?' => "sitepage_page", 'object_id = ?' => $page_id, 'subject_id = ?' => $user_id));

            //GET PAGE TITLE
            $pagetitle = $subject->title;

            //PAGE URL
            $page_url = Engine_Api::_()->sitepage()->getPageUrl($subject->page_id);

            //GET PAGE URL
            $page_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);

            //MAKING PAGE TITLE LINK
            $page_title_link = '<a href="' . $page_baseurl . '">' . $pagetitle . '</a>';

            //GET LOGGED IN USER INFORMATION
            $viewer = Engine_Api::_()->user()->getViewer();

            //Poster title and photo with link.
            $posterTitle = $viewer->getTitle();
            $posterUrl = $viewer->getHref();
            $poster_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $posterUrl;
            $poster_title_link = "<a href='$poster_baseurl' style='font-weight:bold;text-decoration:none;'>" . $posterTitle . " </a>";

//			if($viewer->photo_id) {
//				$photo = 'http://' . $_SERVER['HTTP_HOST'] . $viewer->getPhotoUrl('thumb.icon');
//			}
//			else {
//				$photo = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() .  '/application/modules/Sitepage/externals/images/nophoto_user_thumb_icon.png';
//			}
            $moduletype = 'user';
            //GETTING THE USER PHOTO.
            $file = $viewer->getPhotoUrl('thumb.icon');
            if (empty($file)) {
                $photo = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/' . ucfirst($moduletype) . '/externals/images/nophoto_' . $moduletype . '_thumb_icon.png';
            } else {
                if (strpos($file, 'http') === FALSE)
                    $photo = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $file;
                else
                    $photo = $file;
            }
            $image = "<img src='$photo' />";


            $image = "<img src='$photo' />";
            $post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $payload->getHref();
            $posted_your_page = $view->translate(' posted in page: ');
            $post = $posterTitle . $posted_your_page . $pagetitle;
            $postbody = $payload->body;
            $body_content = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . $post . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='font-
			size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;'>" . $poster_title_link . "<br /><span style='color:#333333;margin-top:5px;display:block;'>" . $postbody . "</span></td></tr></table></td></tr></table></td></tr></table>";

            $manageTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
            $payload_body = strip_tags($payload->body);
            $payload_body = Engine_String::strlen($payload_body) > 50 ? Engine_String::substr($payload_body, 0, (53 - 3)) . '...' : $payload_body;

            //FETCH DATA
            $manageAdminsIds = $manageTable->getManageAdmin($page_id, $user_id);
            $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');

            foreach ($manageAdminsIds as $value) {
                $action_notification = unserialize($value['action_notification']);
                $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                if (empty($sitepagememberEnabled)) {
                    if (!empty($value['notification']) && in_array('posted', $action_notification)) {
                        $notifications->addNotification($user_subject, $viewer, $subject, "sitepage_notificationpost");
                       
                    }

                    //EMAIL SEND TO ALL MANAGEADMINS.
                    $action_email = json_decode($value['action_email']);
                    if (!empty($value['email']) && in_array('posted', $action_email)) {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEPAGE_POSTNOTIFICATION_EMAIL', array(
                            'page_title' => $pagetitle,
                            'body_content' => $body_content,
                            'post_body_body' => $payload_body,
                        ));
                    }
                }
            }

            //START SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE PAGE INCLUDE MANAGE ADMINS.
            if (!empty($sitepagememberEnabled)) {
                $membersIds = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id, $viewer->getIdentity(), $viewer->getIdentity(), 0, 1);
                foreach ($membersIds as $value) {
                    $action_email = json_decode($value['action_email']);
                    $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                    if (!empty($value['email_notification']) && $action_email->emailposted == 1) {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEPAGE_POSTNOTIFICATION_EMAIL', array(
                            'page_title' => $pagetitle,
                            'body_content' => $body_content,
                            'post_body_body' => $payload_body,
                        ));
                    } elseif (!empty($value['email_notification']) && $action_email->emailposted == 2) {
                        $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
                        if (in_array($value['user_id'], $friendId)) {
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEPAGE_POSTNOTIFICATION_EMAIL', array(
                                'page_title' => $pagetitle,
                                'body_content' => $body_content,
                                'post_body_body' => $payload_body,
                            ));
                        }
                    }
                }
            }
            //END SEND EMAIL TO ALL MEMBER WHO HAVE JOINED THE PAGE INCLUDE MANAGE ADMINS.
            //START NOTIFICATION TO ALL FOLLOWERS.
            $isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
            if (!empty($isPageAdmins)) {
                $followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('sitepage_page', $page_id, $viewer->getIdentity());
                $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
                if (!empty($followersIds)) {
                    //previous notification is delete.
                    $notificationsTable->delete(array('type =?' => "sitepage_notificationpost", 'object_type = ?' => "sitepage_page", 'object_id = ?' => $page_id, 'subject_id = ?' => $page_id, 'subject_type = ?' => 'sitepage_page'));
                    foreach ($followersIds as $value) {
                        $user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
                        if (!empty($notidicationSettings)) {
                            $notify_subject = $subject;
                        } else {
                            $notify_subject = $viewer;
                        }
                        
                        $notificationsTable->addNotification($user_subject, $notify_subject, $subject, "sitepage_notificationpost");
                    }
                }
            }
            //END NOTIFICATION TO ALL FOLLOWERS.
        }
    }

}
