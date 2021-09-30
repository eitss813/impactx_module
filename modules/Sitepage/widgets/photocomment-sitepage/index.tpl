<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php  
  echo $this->partial('application/modules/Sitepage/views/scripts/partialPhotoWidget.tpl', array('paginator' => $this->paginator, 'showLightBox' => $this->showLightBox, 'show_detail' =>  1, 'show_info' => 'comment', 'type' => 'comment_count', 'count' => $this->count, 'urlaction' => 'comment'));
?>