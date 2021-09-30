<div class="headline">
    <h2>
        <?php echo $this->translate('Dynamic Form Plugin');?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
        ?>
    </div>
</div>
<h3><?php echo $this->form->getTitle() ?> &#47; Edit Form</h3>
<div class="yndform_edit_form clearfix">
    <?php echo $this->partial('_menuSettings.tpl', 'yndynamicform', array('form' => $this -> form,'editform' => $this -> editform, 'main_info' => 'yndform_active')); ?>
    <?php echo $this->editform->render($this) ?>
</div>
