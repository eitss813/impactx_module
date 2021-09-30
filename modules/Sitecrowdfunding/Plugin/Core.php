<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function onRenderLayoutDefault() {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $view->headScript()
                ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
    }
//    public function onVideoCreateAfter($event){
//        $video = $event->getPayload();
//        $front = Zend_Controller_Front::getInstance();
//        $request = $front->getRequest();
//        $projectId = $request->getParam('project_id');
//        if(!empty($projectId)){
//            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $projectId);
//            $video->parent_type = $project->getType();
//            $video->parent_id = $project->project_id;
//            $video->save();
//        }
//    }

    public function onUserDeleteBefore($event) {
        $payload = $event->getPayload();

        if ($payload instanceof User_Model_User) {

            $owner_id = $payload->getIdentity(); 
            //START ALBUM CODE
            $table = Engine_Api::_()->getItemTable('sitecrowdfunding_project');
            $select = $table->select()->where('owner_id = ?', $owner_id);
            $rows = $table->fetchAll($select);
            if (!empty($rows)) {
                foreach ($rows as $project) {
                    $project->delete();
                }
            } 
        }
    }

}
