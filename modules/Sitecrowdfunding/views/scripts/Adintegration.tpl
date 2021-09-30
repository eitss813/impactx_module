<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adintegration.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$session = new Zend_Session_Namespace();
if (!empty($session->show_hide_ads)) {
    if ($session->project_communityad_integration == 1)
        $communityad_integration = $project_communityad_integration = $session->project_communityad_integration;
    else
        $communityad_integration = $project_communityad_integration = 0;
}
else {
    $communityad_integration = $project_communityad_integration = 1;
}
?>