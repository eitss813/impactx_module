<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_UserBiographyController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
            return $this->setNoRender();
        }

        if (@$this->_getParam('user_id', 0)) {
            $this->view->user = $subject = Engine_Api::_()->getItem('user', @$this->_getParam('user_id', 0));
        } else {
            $this->view->user = $subject = Engine_Api::_()->core()->getSubject('user');
        }

        $owner_id = $subject->user_id;
        $this->view->options = $options = $this->_getParam('userBioOption', array('biography', 'facebook', 'instagram', 'twitter', 'youtube', 'vimeo', 'website', 'email', 'phone'));
        if (empty($this->view->options) || !is_array($this->view->options))
            $this->view->options = $options = array();

        $this->view->titleTruncation = $this->_getParam('titleTruncation', 50);
        $userinfoTable = new Seaocore_Model_DbTable_UserInfo();
        $userinfoTableName = $userinfoTable->info('name');
        $select = $userinfoTable->select()->from($userinfoTableName, '*')
                        ->where('user_id = ?', $owner_id)->limit(1);
        $this->view->ownerBio = $ownerBio = $select->query()->fetch();
        $params = array();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $contactDetailArray = $coreSettings->getSetting('sitecrowdfunding.contactdetail', array('phone', 'social_media', 'email'));
        if (!is_array($contactDetailArray))
            $contactDetailArray = array();
        $this->view->show_email = in_array('email', $contactDetailArray) && in_array('email', $options);

        $this->view->show_phone = in_array('phone', $contactDetailArray) && in_array('phone', $options);
        $this->view->show_social_media = in_array('social_media', $contactDetailArray);
    }

}
