<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_ProfileBreadcrumbController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
				if (!Engine_Api::_()->core()->hasSubject()) {
					return $this->setNoRender();
				}

        //GET SUBJECT
        $this->view->album = $album = Engine_Api::_()->core()->getSubject();

        //GET CATEGORY TABLE
        $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
        if (!empty($album->category_id)) {
            $this->view->category_name = $this->view->tableCategory->getCategory($album->category_id)->category_name;

            if (!empty($album->subcategory_id)) {
                $this->view->subcategory_name = $this->view->tableCategory->getCategory($album->subcategory_id)->category_name;

                if (!empty($album->subsubcategory_id)) {
                    $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($album->subsubcategory_id)->category_name;
                }
            }
        }
    }

}