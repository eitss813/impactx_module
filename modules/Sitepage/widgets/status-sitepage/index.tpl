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
if($this->status == true) {
  $value = "online";
} else {
  $value = "closed";
}
?>
<div class="<?php echo $value;?>">
  <button class="<?php echo $value;?>"><?php echo $value;?></button>
<div>