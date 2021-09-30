<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_ApiController extends Siteapi_Controller_Action_Standard
{
    /**
     * Auth checkup and creating the subject.
     *
     */
    public function orgAction() {
        $this->validateRequestMethod();

        // Get subject
        if (Engine_Api::_()->core()->hasSubject('sitepage_page'))
            $sitepage = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

        // Return if no subject available.
        if (empty($subject))
            $this->respondWithError('no_record');
        $params = $this->_getAllParams();
        $response = Engine_Api::_()->getApi('Siteapi_Core', 'sitepage')->getSitepage($sitepage, $params);

        $projectsIds = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectsIdsByPageIdOrganization($page_id);

        if (isset($_REQUEST['field_order']) && !empty($_REQUEST['field_order']) && $_REQUEST['field_order'] == 1) {
            $response['profile_fields'] = Engine_Api::_()->getApi('Core', 'siteapi')->responseFormat($response['profile_fields']);
        }

        $this->respondWithSuccess($response, true);
    }
}

?>