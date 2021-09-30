<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_UserAdminAndOwnPagesController extends Seaocore_Content_Widget_Abstract
{
    public function indexAction()
    {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->user_and_admin_sitepages = Engine_Api::_()->getDbTable('pages', 'sitepage')->getUserPagesAndAdminPages($viewer_id);
        $this->view->user_and_admin_sitepages_count = Engine_Api::_()->getDbTable('pages', 'sitepage')->getUserPagesAndAdminPagesCount($viewer_id);

        if (!count($this->view->user_and_admin_sitepages_count)  > 0) {
            return $this->setNoRender();
        }

    }
}