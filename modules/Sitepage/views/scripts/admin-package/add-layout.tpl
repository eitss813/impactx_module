

<?php
	$getFormUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
					'module' => 'sitepage',
					'controller' => 'package',
	      'action' => 'add-layout',
	      ), 'admin_default', true);
?>

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

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'package', 'action' => 'manage-layouts'), $this->translate('Back to Manage Layouts'), array('class'=> 'buttonlink', 'style'=> 'padding-left:0px;')) ?>
<br /><br />

<div class="settings">
  <?php echo $this->form->render($this); ?>
</div>

<style type="text/css">
.settings div.form-element .description {
    min-width: 100%;
}
</style>