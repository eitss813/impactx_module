<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="headline">
  <h2> <?php echo $this->navigationTabTitle; ?> </h2>
  <?php if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') != 3) && count($this->navigation)) :?>
    <div class="tabs">
      <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
  <?php endif;?>
</div>

<script type="text/javascript">
        $$('.core_main_user').getParent().addClass('active');
    </script>