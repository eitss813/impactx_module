<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Jung
 */
?>
<div class="layout_middle">
  <div class="generic_layout_container">
    <div class="headline">
      <h2>
        <?php echo $this->translate('Videos');?>
      </h2>
      <div class="tabs">
        <?php
          // Render the menu
          echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
        ?>
      </div>
    </div>
  </div>
</div>
<div class="layout_middle">
  <div class="generic_layout_container">
<?php
  echo $this->form->render();
?>
  </div>
</div>

<script type="text/javascript">
  $$('.core_main_video').getParent().addClass('active');
</script>
