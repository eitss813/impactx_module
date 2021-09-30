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
<?php if (!empty($this -> message)): ?>
    <ul class="form-notices"><li><?php echo $this -> message ?></li></ul>
<?php endif; ?>
<div class='clear'>
    <div class='yndform_edit_form clearfix'>
        <?php echo $this->partial('_menuSettings.tpl', 'yndynamicform', array('form' => $this->form,'editform' => $this -> editform, 'notification' => 'yndform_active')); ?>

        <form class="global_form" action="<?php $this->url() ?>" method="post">
            <div class="yndform_confirmation_col_right">
                <div class="yndform_confirmation_col_right_desc">
                    <?php echo $this->translate("YNDYNAMICFORM_VIEWS_SCRIPTS_NOTIFICATION_INDEX_DESCRIPTION")?>
                </div>
                <?php echo $this->htmlLink(array(
                    'route' => 'admin_default',
                    'module' => 'yndynamicform',
                    'controller' => 'notification',
                    'form_id' => $this->form->getIdentity(),
                    'action' => 'create'),
                    '<button>'.$this->translate('Add New Email Notification').'</button>', array('class' => 'smoothbox')) ?>
                <?php if(count($this->notifications)>0):?>
                    <table style="position: relative;" class="yndform_table admin_table">
                        <thead>
                        <tr>
                            <th><?php echo $this->translate("Notification Name") ?></th>
                            <th><?php echo $this->translate("Enable") ?></th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody id="yndform_options">
                        <?php foreach ($this->notifications as $item): ?>
                            <tr id='notification_item_<?php echo $item->getIdentity() ?>'>
                                <td><?php echo $item->name?></td>
                                <td>
                                    <div id='notification_content_<?php echo $item->notification_id; ?>' style ="text-align: center;" >
                                        <?php if($item->enable): ?>
                                            <input type="checkbox" id='enable_<?php echo $item->notification_id; ?>' onclick="enable_notification(<?php echo $item->notification_id; ?>,this)" checked />
                                        <?php else: ?>
                                            <input type="checkbox" id='enable_<?php echo $item->notification_id; ?>' onclick="enable_notification(<?php echo $item->notification_id; ?>,this)" />
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <ul class="yndform_action_items clearfix">
                                        <li class="yndform_action_btn">
                                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'notification', 'action' => 'edit', 'id' =>$item->notification_id, 'form_id' => $this->form->getIdentity()), $this->translate('edit'), array(
                                            'class' => 'smoothbox',
                                        )) ?>
                                        </li>
                                        <li class="yndform_action_btn">
                                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'notification', 'action' => 'delete', 'id' =>$item->notification_id), $this->translate('delete'), array(
                                            'class' => 'smoothbox',
                                        )) ?>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <div>
                    <button type='submit'><?php echo $this->translate("Save") ?></button>
                    <?php echo $this->translate("or") ?>
                    <a href='<?php echo $this->url(array('module' => 'yndynamicform','controller' => 'admin-manage'),'default', true); ?>' onclick='javascript;'>
                        <?php echo $this->translate("cancel") ?>
                    </a>
                </div>
                <?php else:?>
                    <br/>
                    <div class="tip">
                        <span><?php echo $this->translate("There are currently no notifications.") ?></span>
                    </div>
                <?php endif;?>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    function enable_notification(id, checkbox) {
        var element = document.getElementById('notification_content_' + id);
        var enable = 0;

        if(checkbox.checked == true) enable = 1;
        else enable = 0;

        var content = element.innerHTML;
        element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Yndynamicform/externals/images/loading.gif'></img>";
        new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('module' => 'yndynamicform', 'controller' => 'notification', 'action' => 'enable'), 'admin_default') ?>',
            'data' : {
                'format' : 'json',
                'notification_id' : id,
                'enable' : enable
            },
            'onRequest' : function(){
            },
            'onSuccess' : function(responseJSON, responseText)
            {
                element.innerHTML = content;
                checkbox = document.getElementById('enable_' + id);
                if( enable == 1) checkbox.checked = true;
                else checkbox.checked = false;
            }
        }).send();
    }
    en4.core.runonce.add(function(){
        new Sortables("yndform_options", {
            contrain: false,
            clone: false,
            handle: 'span',
            opacity: 0.5,
            revert: true,
            onStart: function (element) {
                element.addClass('yndform_draging');
            },
            onComplete: function(element){
                element.removeClass('yndform_draging');
                new Request.JSON({
                    url: '<?php echo $this->url(array('controller'=>'notification','action'=>'sort'), 'admin_default') ?>',
                    noCache: true,
                    data: {
                        'format': 'json',
                        'order': this.serialize().toString(),
                        'form_id' : <?php echo $this->form->getIdentity()?>,
                    }
                }).send();
            }
        });
    });
</script>

