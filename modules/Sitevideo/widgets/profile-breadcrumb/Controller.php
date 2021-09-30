<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_ProfileBreadcrumbController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject();

        //GET CATEGORY TABLE
        $this->view->tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        if (!empty($channel->category_id)) {
            $this->view->category_name = $this->view->tableCategory->getCategory($channel->category_id)->category_name;

            if (!empty($channel->subcategory_id)) {
                $this->view->subcategory_name = $this->view->tableCategory->getCategory($channel->subcategory_id)->category_name;

                if (!empty($channel->subsubcategory_id)) {
                    $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($channel->subsubcategory_id)->category_name;
                }
            }
        }
    }

}
