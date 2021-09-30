<div class="global_form">
    <div class="entry_breadcrum clearfix">
        <div class="yndform_title_parent">
            <h3 class="h3">
                <?php
                echo $this->yndform->getTitle();
                ?>
            </h3>
            <!--    <?php echo $this->htmlLink(array(
              'route' => 'yndynamicform_form_detail',
              'form_id' => $this->yndform->getIdentity()), '<span class="ynicon yn-arr-left"></span>'.$this->translate('Back to form'),array(
          'class' => 'yndform_backform'
          ))
          ?> -->
        </div>

        <div>
            <!-- <span class="yndform_text">
                 <?php echo $this -> htmlLink(array(
                     'module'=>'yndynamicform',
                     'action'=>'list',
                     'form_id'=> $this -> yndform -> getIdentity(),
                     'route'=>'yndynamicform_entry_general',
                 ),$this -> translate("View entries"), array()); ?>
             </span>
             <span class="yndform_slash">&#47;</span><span class="yndform_backslash">&#92;</span>
             <span class="yndform_text">
                 <?php echo '#'.$this->entry->getIdentity()?>
             </span>
             <i class="yn_dots">.</i>
             <?php if ($this->entry->owner_id): ?>
                 <?php echo '<span class="yndform_text_submit">'.$this->translate('Submitted by').'</span>'.' '.$this->htmlLink($this->entry->getOwner()->getHref(), $this->entry->getOwner()->getTitle(), array()); ?>
             <?php endif; ?>
             -->
        </div>

    </div>

    <div class="entry_detail">
        <!-- Field answers -->
        <?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> entry);?>
        <?php if($this -> yndformFieldValueLoop($this -> entry, $fieldStructure)):?>
        <div id="yndform_user_entry-print">
            <div class="entry-profile-fields form-elements yndform_main_content">
                <?php echo $this -> yndformFieldValueLoop($this -> entry, $fieldStructure); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div id="yndform_buttons_group-element" class="form-element">
        <?php echo $this -> htmlLink(array(
        'module'=>'yndynamicform',
        'action' => 'print',
        'entry_id'=> $this -> entry -> getIdentity(),
        'route'=>'yndynamicform_entry_specific',
        ), '<button><span class="ynicon yn-print"></span>'.$this->translate('Print').'</button>', array('target' => '_blank', 'id' => 'print_button', 'class' => 'yndform_buttons')); ?>
        <?php if($this->layout()->orientation != 'right-to-left') echo $this -> htmlLink(array(
        'module'=>'yndynamicform',
        'action' => 'save-pdf',
        'entry_id'=> $this -> entry -> getIdentity(),
        'route'=>'yndynamicform_entry_specific',
        ), '<button><span class="ynicon yn-downloads"></span>'.$this->translate('Save as PDF').'</button>', array('target' => '_blank', 'id' => 'save_button', 'class' => 'yndform_buttons')); ?>
    </div>
</div>
<style>
    .generic_layout_container.layout_yndynamicform_browse_menu {
        display: none;
    }
</style>