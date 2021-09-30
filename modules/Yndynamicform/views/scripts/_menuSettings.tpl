<ul class="yndform_edit_form_menu">
    <li class="yndform_item_menu <?php echo $this->main_info ?>">
        <?php
        echo $this->htmlLink(
        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'main-info', 'form_id' => $this->form->form_id), $this->translate("Main Information").'<span class="ynicon yn-arr-right"></span>');
        ?>
    </li>
    <li class="yndform_item_menu <?php echo $this->settings ?>">
        <?php
        echo $this->htmlLink(
        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'settings', 'form_id' => $this->form->form_id), $this->translate("Form Settings").'<span class="ynicon yn-arr-right"></span>')
        ?>
    </li>
    <li class="yndform_item_menu <?php echo $this->confirmation ?>">
        <?php
        echo $this->htmlLink(
        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'confirmation', 'form_id' => $this->form->form_id), $this->translate("Confirmation").'<span class="ynicon yn-arr-right"></span>')
        ?>
    </li>
    <li class="yndform_item_menu <?php echo $this->notification ?>">
        <?php
        echo $this->htmlLink(
        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'notification', 'form_id' => $this->form->form_id), $this->translate("Notification").'<span class="ynicon yn-arr-right"></span>')
        ?>
    </li>
    <li class="yndform_item_menu <?php echo $this->moderators ?>">
        <?php
        echo $this->htmlLink(
        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'moderators', 'form_id' => $this->form->form_id), $this->translate("Moderators").'<span class="ynicon yn-arr-right"></span>')
        ?>
    </li>
    <?php
    if ($this -> editform)
    {
   // $canncel_link = $this->url(array('module' => 'yndynamicform','controller' => 'admin-manage'),'default', true);
    //$this->editform->cancel->setAttrib('href', $canncel_link);
    }
    ?>
</ul>