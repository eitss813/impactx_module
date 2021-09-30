<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

switch ($this->viewFormat) {
    case 'videoView' :
        include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/video/_video_view.tpl';
        break;
    case 'gridView' :
        include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/video/_grid_view.tpl';
        break;
    case 'listView' :
        include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/video/_list_view.tpl';
        break;
}
?>