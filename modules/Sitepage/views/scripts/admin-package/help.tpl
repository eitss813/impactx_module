
<h2>
  <?php echo $this->translate('Create New Directory / Pages Package') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<?php endif; ?>

<?php if( count($this->subnavigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->subnavigation)->render();
    ?>
</div>
<?php endif; ?>

<div class="admin_seaocore_files_wrapper">
  <ul class="admin_seaocore_files seaocore_faq">
        <?php echo $this->translate("You can refer the below"); ?><a href="javascript:void(0);" onClick=""><?php echo $this->translate(" video "); ?></a><?php echo $this->translate("to learn how to use Custom Option for Package View in Directory / Pages Plugin for your website."); ?>
  </ul>
</div>

