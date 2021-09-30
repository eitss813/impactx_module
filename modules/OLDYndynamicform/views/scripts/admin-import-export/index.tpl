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
        <li class="yndform_item_menu <?php echo $this->export ?> yndform_active">
            <?php
            echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'import-export', 'action' => 'index'), $this->translate("Export Forms") . '<span class="ynicon yn-arr-right"></span>');
            ?>
        </li>
        <li class="yndform_item_menu <?php echo $this->import ?>">
            <?php
            echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'import-export', 'action' => 'import'), $this->translate("Import Forms") . '<span class="ynicon yn-arr-right"></span>');
            ?>
        </li>
    </ul>
    <form id='yndform_manage_form_table' class="global_form" method="post" action="<?php echo $this->url(); ?>">
        <div>
            <div>
                <div class="form-elements">
                    <?php if ($this->error): ?>
                        <ul class="form-errors">
                            <li><?php echo $this->translate('Error'); ?>
                                <ul class="errors">
                                    <li><?php echo $this->error; ?></li>
                                </ul>
                            </li>
                        </ul>
                    <?php endif; ?>
                    <?php if (count($this->paginator) > 0): ?>
                        <div class="form-wrapper">
                            <div class="form-label">
                                <label>Select Forms</label>
                            </div>
                            <div class="form-element">
                                <ul class="clearfix">
                                    <?php foreach ($this->paginator as $item): ?>
                                        <li>
                                            <input type='radio' class='radio' name='export_form'
                                                   value='<?php echo $item->form_id ?>'/><?php echo $item->title ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                            </div>
                        </div>
                        <button type='submit' name="export" value='export' id="export"><?php echo $this->translate("Export") ?></button>
                    <?php else: ?>
                        <div class="tip"><span><?php echo $this->translate('There are no available forms') ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>
