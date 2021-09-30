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
class Sitecrowdfunding_Widget_RewardInformationController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }
        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->titleTruncation = $this->_getParam('titleTruncation', 25);
        $this->view->descriptionTruncation = $this->_getParam('descriptionTruncation', 150);
        $this->view->session = $session = new Zend_Session_Namespace('sitecrowdfunding_cart_data');

        if (!$session) {
            return $this->_forward('notfound', 'error', 'core');
        }
        $sitecrowdfundingRewardInfo = Zend_Registry::isRegistered('sitecrowdfundingRewardInfo') ? Zend_Registry::get('sitecrowdfundingRewardInfo') : null;
        $this->view->shipping_amount = 0;
        $this->view->reward_id = 0;
        if (isset($session->reward_id) && !empty($session->reward_id)) {
            $this->view->reward_id = $session->reward_id;
            $this->view->reward = $reward = Engine_Api::_()->getItem('sitecrowdfunding_reward', $session->reward_id);
            if ($reward->shipping_method == 2 || $reward->shipping_method == 3) {
                $this->view->shipping_amount = Engine_Api::_()->getDbtable('rewardshippinglocations', 'sitecrowdfunding')->findShippingCharge($project->project_id, $session->reward_id, $session->country);
            }
        }
        if (empty($sitecrowdfundingRewardInfo))
            return $this->setNoRender();

        $this->view->pledge_amount = $session->pledge_amount;
    }

}
