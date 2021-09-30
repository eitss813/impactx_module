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
class Sitepage_Widget_PageProfilePartnersController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // DON'T RENDER IF NOT AUTHORIZED.
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // GET THE SUBJECT OF PAGE.
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

       // $this->view->partners= $partners = Engine_Api::_()->getDbtable('partners', 'sitepage')->getPartnerPages($sitepage->page_id);
        $allPages = Engine_Api::_()->getDbtable('partners', 'sitepage')->getJoinedAndAddedPartnerPages($sitepage->page_id);
        $this->view->partners= $partners = $allPages;
        $this->view->partnerOrganisationCount = count($partners );
        $partnerOrganisationCount = count($partners );
       // $partnerOrganisationCount = Engine_Api::_()->getDbtable('partners', 'sitepage')->getPartnerPagesCount($sitepage->page_id);
        if($partnerOrganisationCount ==  0 ){
            return $this->setNoRender();
        }


    }
}
?>