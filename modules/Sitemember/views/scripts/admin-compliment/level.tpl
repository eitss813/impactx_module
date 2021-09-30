<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: level.tpl 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/sitemember/compliment/level/id/'+level_id;
    //alert(level_id);
  }
</script>
<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2><?php if( count($this->navigation) ): ?>
  <div class='tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php if( count($this->subNavigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='clear seaocore_settings_form'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
  </div>
</div>