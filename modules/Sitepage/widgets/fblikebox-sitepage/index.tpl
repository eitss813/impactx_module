<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script type="text/javascript">
var call_advfbjs = '1';
var fbappid = '<?php echo Engine_Api::_()->getApi("settings", "core")->core_facebook_appid;?>';
window.addEvent('domready', function () { 
en4.seaocore.facebook.runFacebookSdk();
});

</script>
<div id="like-box">
  <fb:like-box href="<?php echo $this->fbpage_url;?>" width="<?php echo $this->LikeboxSetting['fb_width'];?>" height="<?php echo $this->LikeboxSetting['fb_height'];?>" show_faces="<?php echo $this->LikeboxSetting['widget_show_faces'];?>" colorscheme="<?php echo $this->LikeboxSetting['widget_color_scheme'];?>" header="<?php echo $this->LikeboxSetting['show_header'];?>" stream="<?php echo $this->LikeboxSetting['show_stream'];?>" border_color="<?php echo $this->LikeboxSetting['border_color'];?>" ></fb:like-box>   
</div>