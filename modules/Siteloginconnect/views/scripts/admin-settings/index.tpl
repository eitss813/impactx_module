<h2>
    <?php echo $this->translate("Social Connect & Profile Sync Extension") ?>
</h2>

<?php if( count($this->navigation) ): ?>
      <div class='seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
<?php endif; ?>

<div class="settings">
	<?php echo $this->form->render($this); ?>
</div>    