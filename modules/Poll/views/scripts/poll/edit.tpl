<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>
<div class="layout_middle">
  <div class="generic_layout_container">
    <div class="headline">
      <h2>
        <?php echo $this->translate('Polls');?>
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
    <?php echo $this->form->render($this) ?>
  </div>
</div>
