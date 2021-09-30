<h2>
    <?php echo $this->translate("Dynamic Form Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<p>
    <?php echo $this->translate("YNDYNAMICFORM_VIEWS_SCRIPTS_ADMINIMPORTEXPORT_INDEX_DESCRIPTION") ?>
</p>


<div class="yndform_edit_form clearfix">
    <ul class="yndform_edit_form_menu">
        <li class="yndform_item_menu <?php echo $this->export ?>">
            <?php
            echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'import-export', 'action' => 'index'), $this->translate("Export Forms") . '<span class="ynicon yn-arr-right"></span>');
            ?>
        </li>
        <li class="yndform_item_menu <?php echo $this->import ?> yndform_active">
            <?php
            echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'import-export', 'action' => 'import'), $this->translate("Import Forms") . '<span class="ynicon yn-arr-right"></span>');
            ?>
        </li>
    </ul>

    <?php echo $this -> form -> render($this) ?>
</div>

