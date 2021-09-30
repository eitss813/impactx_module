<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WatchlaterController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_WatchlaterController extends Seaocore_Controller_Action_Standard {

    public function init() {
        // SET THE SUBJECT
        if (0 !== ($watchlater_id = (int) $this->_getParam('watchlater_id')) &&
                null !== ($watchlater = Engine_Api::_()->getItem('sitevideo_watchlater', $watchlater_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($watchlater);
        }
    }

    /*
     * THIS ACTION USED TO ADD A VIDEO INTO WATCHLATER
     */

    public function addToWatchlaterAction() {
        $message = Zend_Registry::get('Zend_Translate')->_("Video is added to watch later.");
        //FIND USER AND VIDEO ID
        $video_id = $this->_getParam('video_id');
        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $table = Engine_Api::_()->getDbtable('watchlaters', 'sitevideo');
        $sitevideoBestVideo = Zend_Registry::isRegistered('sitevideoBestVideo') ? Zend_Registry::get('sitevideoBestVideo') : null;
        if (empty($sitevideoBestVideo))
            return;
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $Watchlaters = new Sitevideo_Model_DbTable_Watchlaters();
            //CHECKING FOR VIDEO IS ADDED INTO WATCHLATER FOR THIS USER
            $WatchlaterModel = $Watchlaters->fetchRow($Watchlaters->select()
                            ->where('owner_id = ?', $owner_id)
                            ->where('video_id = ?', $video_id));
            //IF VIDEO IS NOT ADDED INTO WATCHLATER THEN ADD IT INTO WATCHLATER
            if (!$WatchlaterModel) {
                $watchlaterRow = $table->createRow();
                $watchlaterRow->video_id = $video_id;
                $watchlaterRow->owner_id = $owner_id;
                $watchlaterRow->owner_type = 'user';
                $watchlaterRow->save();
            } else
                $message = Zend_Registry::get('Zend_Translate')->_("This video is already added to watch later.");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $data = array();
        $data[] = array(
            'message' => $message,
        );
        //RETURN THE JSON DATA
        return $this->_helper->json($data);
    }

    /*
     * THIS ACTION USED TO REMOVE A VIDEO FROM WATCHLATER (JSON)
     */

    public function removeFromWatchlaterJsonAction() {
        $message = Zend_Registry::get('Zend_Translate')->_("Video has been removed from watch later.");
        //FIND USER AND VIDEO ID
        $video_id = $this->_getParam('video_id');
        $owner_id = Engine_Api::_()->user()->getViewer()->user_id;
        $table = Engine_Api::_()->getDbtable('watchlaters', 'sitevideo');
        $sitevideoBestVideo = Zend_Registry::isRegistered('sitevideoBestVideo') ? Zend_Registry::get('sitevideoBestVideo') : null;
        if (empty($sitevideoBestVideo))
            return;
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $Watchlaters = new Sitevideo_Model_DbTable_Watchlaters();
            //CHECKING FOR VIDEO IS ADDED INTO WATCHLATER FOR THIS USER
            $WatchlaterModel = $Watchlaters->fetchRow($Watchlaters->select()
                            ->where('owner_id = ?', $owner_id)
                            ->where('video_id = ?', $video_id));
            //IF VIDEO IS NOT ADDED INTO WATCHLATER THEN ADD IT INTO WATCHLATER
            if ($WatchlaterModel) {
                $WatchlaterModel->delete();
            } else
                $message = Zend_Registry::get('Zend_Translate')->_("This video is already removed from watch later.");
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $data = array();
        $data[] = array(
            'message' => $message,
        );
        //RETURN THE JSON DATA
        return $this->_helper->json($data);
    }

    /*
     * My Watch Later Page
     */

    public function manageAction() {
        //Checking for "Watchlater" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->_helper->content->setNoRender()->setEnabled();
    }

    /*
     *  THIS ACTION IS USED TO REMOVE THE VIDEO FROM WATCHLATER 
     */

    public function removeFromWatchlaterAction() {
        //Checking for "Watchlater" is enabled for this site
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1))
            return $this->_forwardCustom('requireauth', 'error', 'core');
        //CHECKING FOR SUBJECT IS SET OR NOT
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_watchlater')) {
            return $this->setNoRender();
        }
        //FIND THE SUBJECT
        $watchlater = Engine_Api::_()->core()->getSubject();
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = $form = new Sitevideo_Form_Watchlater_Delete();
        if (!$watchlater) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Watchlater doesn't exists or not authorized to delete");
            return;
        }
        // CHECKING FOR POST REQUEST
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $db = $watchlater->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            //DELETE THE VIDEO FROM WATCHLATER FOR THIS USER
            $watchlater->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been removed from watch later.');
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitevideo_watchlater_general', true),
                    'messages' => Array($this->view->message)
        ));
    }

    public function pageQueryAction() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $pages = $db->select()
                        ->from('engine4_core_pages', '*')
                        ->where('(page_id BETWEEN 1085 AND 1110) or page_id in(1248,1249,1155) ')->query()->fetchAll();
        $this->view->setEscape('mysql_escape_string');
        $query = "";
        foreach ($pages as $page) {
            $query .="<h4> #Page start ----" . $page['name'] . "----</h4>";
            $queryBuild = "insert into engine4_core_pages (";
            $colName = array();
            $colValue = array();
            foreach ($page as $columnName => $columnValue) {
                if ($columnName == 'page_id')
                    continue;
                $colName[] = "`" . $columnName . "`";
                $colValue[] = is_null($columnValue) ? 'NULL' : "'" . $this->view->escape($columnValue) . "'";
            }
            $queryBuild .= implode(',', $colName) . ")values(" . implode(',', $colValue) . ");<br />";
            $query .='$db->query("' . $queryBuild . '");';
            //$query .= $queryBuild;
            $contents = $db->select()
                            ->from('engine4_core_content', '*')
                            ->where('page_id = ?', $page['page_id'])
                            ->order('type')
                            ->order('content_id')
                            ->order('parent_content_id')
                            ->query()->fetchAll();
            $pageQry = "select page_id from engine4_core_pages where name = '" . $page['name'] . "'";
            $pageQry1 = "from engine4_core_pages where name = '" . $page['name'] . "'";
            $ContentsArr = array();
            foreach ($contents as $content) {
                $queryBuildContent = "insert into engine4_core_content (";
                $ContentsArr[$content['content_id']] = $content;
                $colName = array();
                $colValue = array();
                foreach ($content as $columnName => $columnValue) {
                    if ($columnName == 'content_id')
                        continue;
                    $colName[] = "`" . $columnName . "`";
                    if ($columnName == 'page_id') {
                        $colValue[] = "`" . $columnName . "`";
                    } elseif ($columnName == 'parent_content_id' && !empty($columnValue)) {
                        if (isset($ContentsArr[$columnValue]))
                            $parentContent = $ContentsArr[$columnValue];
                        else {
                            $c = $db->select()
                                            ->from('engine4_core_content', '*')
                                            ->where('content_id = ?', $columnValue)
                                            ->query()->fetchAll();
                            $parentContent = $c[0];
                        }
                        $parentSelect = "select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='" . $parentContent['type'] . "' and `name`='" . $parentContent['name'] . "' and `order`=" . $parentContent['order'] . " limit 1";
                        $colValue[] = "(" . $parentSelect . ")";
                    } else {
                        $colValue[] = is_null($columnValue) ? 'NULL' : "'" . $this->view->escape($columnValue) . "'";
                    }
                }
                $queryBuildContent .= implode(',', $colName) . ") select " . implode(',', $colValue) . " " . $pageQry1 . " ;<br />";
                $query .='$db->query("' . $queryBuildContent . '");<br />';
                //$query .= $queryBuildContent . '<br />';
            }
            $query .="<h4> #Page end ----" . $page['name'] . "----</h4>";
        }
        echo $query;
        die;
    }

}
