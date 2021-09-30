<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  echo $this->partial('application/modules/Sitepage/views/scripts/partialPhotoWidget.tpl', array('paginator' => $this->paginator, 'showLightBox' => $this->showLightBox, 'show_detail' =>  0, 'includeCss' => 1, 'displayPageName' => $this->displayPageName, 'displayUserName' => $this->displayUserName, 'showFullPhoto' => $this->showFullPhoto, 'type' => 'view_count', 'count' => $this->count, 'urlaction' => 'popular'));
?>