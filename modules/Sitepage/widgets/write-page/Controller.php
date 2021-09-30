<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_WritePageController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DON'T RENDER IF NOT AUTHORIZED.
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET THE SUBJECT OF PAGE.
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        $page_id = $sitepage->page_id;

        //CALLING FUNCTON AND PASS PAGE ID.
        $writetContent = Engine_Api::_()->getDbtable('writes', 'sitepage')->writeContent($page_id);

        $this->view->userPagestext = '';
        if (!empty($writetContent)) {
            $this->view->userPagestext = $writetContent->text;
        }

        if (!Engine_Api::_()->sitepage()->isPageOwner($this->view->sitepage) && !$this->view->userPagestext) {
            return $this->setNoRender();
        }
    }

}

?>