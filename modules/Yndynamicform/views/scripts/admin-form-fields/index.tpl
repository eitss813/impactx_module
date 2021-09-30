<?php
    // Render the admin js
    echo $this->render('_jsAdmin.tpl');
?>

<h2><?php echo $this->translate("Dynamic Form Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->form->getTitle() . ' &#187; ' . $this->translate('Manage Fields') ?></h3>

<div class="yndform_manage_fields_back">
    <?php echo $this->htmlLink(array('controller' => 'manage','action' => 'index', 'reset' => false), '<span class="ynicon yn-arrow-left"></span>'.$this->translate('Back to Manage Forms'), array('class' => '')) ?>
</div>

<div class="admin_fields_options">
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate("Save Order") ?></a>
</div>

<div class="admin_fields_type">
</div>

<div class="yndform_manage_fields_fields droppable">
    <div class="yndform_manage_fields_fields_bg">
    <ul class="admin_fields">
        <?php foreach( $this->secondLevelMaps as $map ): ?>
            <?php echo $this->adminFieldMeta($map) ?>
        <?php endforeach; ?>
    </ul>
    </div>
    <div class="yndform_manage_fields_desc">
        <?php echo $this->translate('YNDYNAMICFORM_MANAGE_FIELDS_DESCRIPTION') ?>
    </div>
</div>

<div class="yndform_manage_fields_options">
    <div class="yndform_fields_option_items">
        <div class="yndform_item_name">
            <span class="yndform_item_name_fields"><?php echo $this->translate('Standard fields') ?></span>
            <span class="ynicon yn-arr-down yndform_collap  yndform_show"></span>
            <span class="ynicon yn-arr-up yndform_collap yndform_hide"></span>
        </div>
        <?php foreach($this->standardFields as $catLabel => $fieldByCat): ?>
            <div class="yndform_item_label_fields"><?php echo $catLabel ?></div>
            <ul class="yndform_manage_fields_items clearfix">
                <?php foreach($fieldByCat as $type => $label): ?>
                    <li class="yndform_manage_fields_item">
                        <span class="yndform_draggable" data_type="<?php echo $type ?>"><?php echo $label ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>
    <div class="yndform_fields_option_items">
        <div class="yndform_item_name">
            <span class="yndform_item_name_fields"><?php echo $this->translate('Advanced fields') ?></span>
            <span class="ynicon yn-arr-down yndform_collap yndform_show"></span>
            <span class="ynicon yn-arr-up yndform_collap yndform_hide"></span>
        </div>
        <ul class="yndform_manage_fields_items clearfix">
            <?php foreach($this->advancedFields as $type => $label): ?>
            <li class="yndform_manage_fields_item">
                <span class="yndform_draggable" data_type="<?php echo $type ?>"><?php echo $label ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="yndform_fields_option_items">
        <div class="yndform_item_name">
            <span class="yndform_item_name_fields"><?php echo $this->translate('User analytics fields') ?></span>
            <span class="ynicon yn-arr-down yndform_collap yndform_show"></span>
            <span class="ynicon yn-arr-up yndform_collap yndform_hide"></span>
        </div>
        <ul class="yndform_manage_fields_items clearfix">
            <?php foreach($this->analyticsFields as $type => $label): ?>
                <li class="yndform_manage_fields_item">
                    <span class="yndform_draggable" data_type="<?php echo $type ?>"><?php echo $this->translate($label) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    var fieldContainer;
    var draggableObject = {};

    // These fields can be inserted once only
    var singleFields = ['recaptcha'];

    <?php foreach($this->analyticsFields as $field => $label): ?>
        singleFields.push('<?php echo $field ?>');
    <?php endforeach; ?>

    window.addEvent('domready', function() {
        $$('.yndform_item_name').addEvent('click', function() {
            $$('.yndform_draggable').each(function(el){
                yndformRemoveDragEvent(el);
            });
            this.getParent('.yndform_fields_option_items').toggleClass('yndform_show_less');
            $$('.yndform_draggable').each(function(el){
                yndformAddDragEvent(el);
            });
        });


        fieldContainer = $$('.yndform_manage_fields_fields')[0];
        $$('.yndform_draggable').each(function(el){
            yndformAddDragEvent(el);
        });
        yndformUpdateFieldsBG();
    });

    function yndformAddDragEvent(el) {
        if (el.hasClass('disabled')) {
            return;
        }
        draggableObject[el.get('data_type')] = new Drag.Move(el, {

            droppables: '.droppable',

            onStart: function() {
                var clonedButton = el.clone(true);
                var container = el.getParent('.yndform_manage_fields_item');
                el.inject(container, 'before');
                el.setStyle('z-index', '2');
                el.addClass('yndform_manage_fields_dragging');
                clonedButton.inject(container, 'top');
                yndformAddDragEvent(clonedButton);
            },

            onDrop: function (element, droppable, event) {
                if (droppable){
                    uiSmoothTopFieldCreate(element.get('data_type'));
                }
                element.destroy();
                yndformUpdateFieldsBG();
            },

            onEnter: function (element, droppable) {
            },

            onLeave: function (element, droppable) {
            }
        });
    }

    function yndformRemoveDragEvent(el) {
        if (el.hasClass('disabled')) {
            return;
        }
        if (draggableObject.hasOwnProperty(el.get('data_type'))) {
            draggableObject[el.get('data_type')].detach();
            el.setStyle('position', '');
            el.setStyle('top', '');
            el.setStyle('left', '');
        }
    }

    function yndformUpdateFieldsBG() {
        var fieldList = fieldContainer.getElement('.admin_fields').getChildren('.admin_field');
        if (fieldList.length) {
            fieldContainer.addClass('yndform_manage_fields_bg');
        } else {
            fieldContainer.removeClass('yndform_manage_fields_bg');
        }
        yndformUpdateButtons();
    }

    function yndformDisableSingleButtons(type) {
        var el = $$('.yndform_draggable[data_type=' + type + ']')[0];
        yndformRemoveDragEvent(el);
        el.addClass('disabled');
    }

    function yndformEnableSingleButtons(type) {
        var el = $$('.yndform_draggable[data_type=' + type + ']')[0];
        if (el && el.hasClass('disabled')) {
            el.removeClass('disabled');
            yndformAddDragEvent(el);
        }
    }

    function yndformUpdateButtons() {
        var fieldList = fieldContainer.getElement('.admin_fields').getChildren('.admin_field');
        var fieldExist = false;
        singleFields.each(function(type){
            fieldExist = false;
            fieldList.each(function(field) {
                // this field is added
                if (field.get('type') == type) {
                    fieldExist = true;
                }
            });
            if (fieldExist) {
                yndformDisableSingleButtons(type);
            } else {
                yndformEnableSingleButtons(type);
            }
        });
    }
</script>
