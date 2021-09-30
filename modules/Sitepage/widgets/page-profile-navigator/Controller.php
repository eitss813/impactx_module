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
class Sitepage_Widget_PageProfileNavigatorController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
            return $this->setNoRender();
        }

        // GET THE SUBJECT OF PAGE.
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

        // get count
        $this->view->partnerOrganisationCount = Engine_Api::_()->getDbtable('partners', 'sitepage')->getPartnerPagesCount($sitepage->page_id);
        $this->view->projectsCount = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getPageProjectCount($sitepage->page_id);
        $this->view->albumcount = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount(array('page_id'=>$sitepage->page_id));

        $videoParams = array();
        $videoParams['parent_type'] = 'sitepage_page';
        $videoParams['parent_id'] = $sitepage->page_id;
        $this->view->videoCount = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideosCountByParentType($videoParams);

       // $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($sitepage->page_id);
        $initiatives = Engine_Api::_()->getDbtable('initiatives', 'sitepage')->getAllInitiativesByPageId($sitepage->page_id);
        $this->view->initiativesCount = count($initiatives) ;

    }
}
