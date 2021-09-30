<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: validation.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  <?php if($this->valid):?>
    var valid = true;
    
    var informationVideoContent = <?php echo json_encode($this->information);?>;
  <?php else:?>
    var valid = false;
    var informationVideoContent = false;
  <?php endif; ?>
</script>
