<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PostController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_PostController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //RETURN IF SUBJECT IS SET
        if (Engine_Api::_()->core()->hasSubject())
            return;

        //SET POST OR TOPIC SUBJECT
        if (0 != ($post_id = (int) $this->_getParam('post_id')) &&
                null != ($post = Engine_Api::_()->getItem('sitevideo_post', $post_id))) {
            Engine_Api::_()->core()->setSubject($post);
        } else if (0 != ($topic_id = (int) $this->_getParam('topic_id')) &&
                null != ($topic = Engine_Api::_()->getItem('sitevideo_topic', $topic_id))) {
            Engine_Api::_()->core()->setSubject($topic);
        }

        $this->_helper->requireUser->addActionRequires(array(
            'edit',
            'delete',
        ));

        $this->_helper->requireSubject->setActionRequireTypes(array(
            'edit' => 'sitevideo_post',
            'delete' => 'sitevideo_post',
        ));
    }

    //ACTION FOR EDIT THE POST
    public function editAction() {
        //GET POST SUBJECT
        $post = Engine_Api::_()->core()->getSubject('sitevideo_post');

        //GET CHANNEL
        $sitevideo = $post->getParent('sitevideo_channel');

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($sitevideo, null, "view")->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$sitevideo->isOwner($viewer) && !$post->isOwner($viewer)) {
            return $this->_helper->requireAuth->forward();
        }

        //MAKE FORM
        $this->view->form = $form = new Sitevideo_Form_Post_Edit();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            $form->populate($post->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $table = Engine_Api::_()->getDbTable('posts', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $post->setFromArray($form->getValues());
            $post->modified_date = date('Y-m-d H:i:s');
            $post->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'closeSmoothbox' => true,
                    'parentRefresh' => true,
        ));
    }

    //ACTION FOR DELETE THE POST
    public function deleteAction() {

        //GET POST SUBJECT
        $post = Engine_Api::_()->core()->getSubject('sitevideo_post');

        //GET CHANNEL SUBJECT
        $sitevideo = $post->getParent('sitevideo_channel');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($sitevideo->isOwner($viewer) && !$post->isOwner($viewer)) {
            return $this->_helper->requireAuth->forward();
        }

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($sitevideo, null, "view")->isValid())
            return;

        //MAKE FORM
        $this->view->form = $form = new Sitevideo_Form_Post_Delete();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $table = Engine_Api::_()->getDbTable('posts', 'sitevideo');
        $db = $table->getAdapter();
        $db->beginTransaction();
        $topic_id = $post->topic_id;
        try {
            $post->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //GET TOPIC
        $topic = Engine_Api::_()->getItem('sitevideo_topic', $topic_id);

        $href = ( null == $topic ? $sitevideo->getHref() : $topic->getHref() );
        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'closeSmoothbox' => true,
                    'parentRedirect' => $href,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Post deleted.')),
        ));
    }

}
