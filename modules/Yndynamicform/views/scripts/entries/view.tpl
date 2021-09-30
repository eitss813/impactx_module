<?php if(!$this->type): ?>
<div class="global_form">
    <div class="entry_breadcrum clearfix">
        <div class="yndform_title_parent">
            <h3 class="h3">
                <?php
                echo $this->yndform->getTitle();
                ?>
            </h3>
          <!--  <?php echo $this->htmlLink(array(
            'route' => 'yndynamicform_form_detail',
            'form_id' => $this->yndform->getIdentity()), '<span class="ynicon yn-arr-left"></span>'.$this->translate('Back to form'),array(
            'class' => 'yndform_backform'
            ))
            ?> -->
        </div>

        <div class='submitted-form-toggle-parent'>
            <div>
                <span class="yndform_text">
                  <?php echo $this -> htmlLink(array(
                      'module'=>'yndynamicform',
                      'action'=>'list',
                      'form_id'=> $this -> yndform -> getIdentity(),
                      'route'=>'yndynamicform_entry_general',
                  ),$this -> translate("View entry"), array()); ?>
                </span>
                <!--
                  <span class="yndform_slash">&#47;</span><span class="yndform_backslash">&#92;</span>
                  <span class="yndform_text">
                    <?php echo '#'.$this->entry->getIdentity()?>
                </span> -->
                  <i class="yn_dots"> - </i>
                <?php if ($this->entry->owner_id): ?>
                <?php echo '<span class="yndform_text_submits">'.$this->translate('Submitted by').'</span>'.' '.$this->htmlLink($this->entry->getOwner()->getHref(), $this->entry->getOwner()->getTitle(), array()); ?>
                <?php endif; ?>
            </div>
            
            <?php if( !empty($this->isSiteAdmins) || !empty($this->isPageAdmins) ): ?>
                <div id="publish-now" class="publish-now" style="float: right; display: <?php echo empty($this->entry->publish)? 'block': 'none'; ?>">
                    <button onclick="updatePublishStatus('<?php echo $this->entry->getIdentity() ?>', 'true')">Publish</button> <span id='publish-now-spinner' style="visibility: hidden;"><img src="application/modules/Yndynamicform/externals/images/loading.gif"/></span>
                </div>

                <div id="unpublish-now" class="unpublish-now" style="float: right; display: <?php echo empty($this->entry->publish)? 'none': 'block'; ?>">
                    <button onclick="updatePublishStatus('<?php echo $this->entry->getIdentity() ?>', '')">Unpublish</button> <span id='unpublish-now-spinner' style="visibility: hidden;"><img src="application/modules/Yndynamicform/externals/images/loading.gif"/></span>
                </div>
            <?php endif; ?>

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
        <?php /* if($this->layout()->orientation != 'right-to-left') echo $this -> htmlLink(array(
        'module'=>'yndynamicform',
        'action' => 'save-pdf',
        'entry_id'=> $this -> entry -> getIdentity(),
        'route'=>'yndynamicform_entry_specific',
        ), '<button><span class="ynicon yn-downloads"></span>'.$this->translate('Save as PDF').'</button>', array('target' => '_blank', 'id' => 'save_button', 'class' => 'yndform_buttons')); */ ?>
        
        <?php $savePdfURL = $this->url(array('module' => 'yndynamicform','action' => 'save-pdf', 'entry_id' => $this->entry->getIdentity()),'yndynamicform_entry_specific', true); ?>
        <button onclick="saveAsPDF('<?php echo $savePdfURL; ?>')"><span class="ynicon yn-downloads"></span><?php echo $this->translate('Save as PDF'); ?><i class="fa fa-circle-o-notch fa-spin" id="save-as-pdf-loading" style="display: none; margin-left: 8px;"></i></button>
        
        <?php if($this->layout()->orientation != 'right-to-left') echo $this -> htmlLink(array(
        'module'=>'yndynamicform',
        'action' => 'edit',
        'entry_id'=> $this -> entry -> getIdentity(),
        'route'=>'yndynamicform_entry_specific',
        ), '<button><span class="ynicon yn-edit"></span>'.$this->translate('Edit').'</button>', array('target' => '_blank', 'id' => 'edit_button', 'class' => 'yndform_buttons')); ?>
    </div>
</div>
<?php endif; ?>

<?php if($this->type): ?>
<div class="global_form">
    <div class="entry_breadcrum clearfix">

        <div class="yndform_title_parent">

            <?php if($this->type=='org'): ?>
            <?php echo $this->htmlLink(array(
            'route' => 'default',
            'module' => 'organizations',
            'controller' => 'manageforms',
            'action' => 'list',
            'page_id'=>$this->page_id,
            'form_id'=>$this->form_id
            ), '<span class="ynicon yn-arr-left" style="  color: #0087c3 !important;
           font-size: 21px !important;">'.$this->translate('Back ').'&nbsp;</span>',array(
            'class' => 'yndform_backform'
            ))
            ?>
            <?php endif; ?>
            <?php if($this->type=='project'): ?>
                <?php echo $this->htmlLink(array(
                'route' => 'default',
                'module' => 'projects',
                'controller' => 'form',
                'action' => 'index',
                'project_id'=>$this->project_id,
                'user_id'=>$this->user_id,
                'type'=>'forms_assigned'

                ), '<span class="ynicon yn-arr-left" style="  color: #0087c3 !important;
               font-size: 21px !important;">'.$this->translate('Back ').'&nbsp;</span>',array(
                'class' => 'yndform_backform'
                ))
                ?>
            <?php endif; ?>
            <?php if($this->type=='user'): ?>
                <?php $user = Engine_Api::_()->getItem('user', $this->user_id);?>
                <?php echo $this->htmlLink(
                    $user->getHref(array('tab' => 'user.forms')),
                    '<span class="ynicon yn-arr-left" style="  color: #0087c3 !important;
                   font-size: 21px !important;">'.$this->translate('Back ').'&nbsp;</span>',
                    array('class' => 'yndform_backform')
                ); ?>
            <?php endif; ?>
            <h3 class= "h3">
                View Submission :  <?php echo $this->yndform->getTitle(); ?>
            </h3>

        </div>
        <div style="display: flex;justify-content: center;    margin-bottom: -11px;">
            <!-- <span class="yndform_text">
                 <?php echo $this -> htmlLink(array(
                     'module'=>'yndynamicform',
                     'action'=>'list',
                     'form_id'=> $this -> yndform -> getIdentity(),
                     'route'=>'yndynamicform_entry_general',
                 ),$this -> translate("View entries"), array()); ?>
             </span>


               <span class="yndform_text">
                 <?php echo '#'.$this->entry->getIdentity()?>
               </span>
                   -->
            <i class="yn_dots">.</i>
            <?php if ($this->entry->owner_id): ?>
            <?php echo '<span class="" style="font-size: 19px;color: #2c2c2c;font-weight: 500;">'.$this->translate('Submitted By: ').'</span>&nbsp;&nbsp;'.' '.$this->htmlLink($this->entry->getOwner()->getHref(), $this->entry->getOwner()->getTitle(),   array()); ?>
            <?php endif; ?>



        </div>
        <?php if($this->project_id && !$this->user_id):?>
            <div style="display: flex;justify-content: center;">
                <?php echo '<span class="" style="font-size: 16px;color: #2c2c2c;font-weight: 500;">'.$this->translate('Submitted From Project: ').'</span>&nbsp;&nbsp;' ?>
                <div style="font-size: 16px !important;">
                    <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->project_id); ?>
                    <?php echo $this->htmlLink($project->getHref(), $this->translate($project->getTitle())) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!$this->project_id && $this->user_id):?>
            <div style="display: flex;justify-content: center;">
                <?php echo '<span class="" style="font-size: 16px;color: #2c2c2c;font-weight: 500;">'.$this->translate('Submitted From Member: ').'</span>&nbsp;&nbsp;' ?>
                <div style="font-size: 16px !important;">
                    <?php $user = Engine_Api::_()->getItem('user', $this->user_id); ?>
                    <?php echo $this->htmlLink($user->getHref(), $this->translate($user->getTitle())) ?>
                </div>
            </div>
        <?php endif; ?>

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
    <div id="yndform_buttons_group-element" class="form-element" style="display: flex;justify-content: center">
        <?php echo $this -> htmlLink(array(
        'module'=>'yndynamicform',
        'action' => 'print',
        'entry_id'=> $this -> entry -> getIdentity(),
        'route'=>'yndynamicform_entry_specific',
        ), '<button><span class="ynicon yn-print"></span>'.$this->translate('Print').'</button>', array('target' => '_blank', 'id' => 'print_button', 'class' => 'yndform_buttons')); ?>
        <?php /* if($this->layout()->orientation != 'right-to-left') echo $this -> htmlLink(array(
        'module'=>'yndynamicform',
        'action' => 'save-pdf',
        'entry_id'=> $this -> entry -> getIdentity(),
        'route'=>'yndynamicform_entry_specific',
        ), '<button><span class="ynicon yn-downloads"></span>'.$this->translate('Save as PDF2').'</button>', array('target' => '_blank', 'id' => 'save_button', 'class' => 'yndform_buttons')); */ ?>
        
        <?php $savePdfURL = $this->url(array('module' => 'yndynamicform','action' => 'save-pdf', 'entry_id' => $this->entry->getIdentity()),'yndynamicform_entry_specific', true); ?>
        <button onclick="saveAsPDF('<?php echo $savePdfURL; ?>')"><span class="ynicon yn-downloads"></span><?php echo $this->translate('Save as PDF'); ?><i class="fa fa-circle-o-notch fa-spin" id="save-as-pdf-loading" style="display: none; margin-left: 8px;"></i></button>
        
        <?php
          $entriesTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntriesByEntryId($this -> entry -> getIdentity());
          if($entriesTable[0]['allow_edit'] == 1):
        ?>
        <?php if($this->layout()->orientation != 'right-to-left') echo $this -> htmlLink(array(
        'module'=>'yndynamicform',
        'action' => 'edit',
        'entry_id'=> $this -> entry -> getIdentity(),
        'route'=>'yndynamicform_entry_specific',
        ), '<button><span class="ynicon yn-edit"></span>'.$this->translate('Edit').'</button>', array('target' => '_blank', 'id' => 'edit_button', 'class' => 'yndform_buttons')); ?>
     <?php endif; ?>
    </div>

</div>
<style>
    #yndform_entry-print .yndform_main_content .form-wrapper, #yndform_user_entry-print .yndform_main_content .form-wrapper {
        display: flex;
        margin-bottom: 15px;
        flex-direction: column;
        display: flex;
        margin-bottom: 15px;
        flex-direction: unset !important;
    }
    .yndform_text{
       display: none;
    }
    .entry-profile-fields{
        margin:0 !important;
    }
    .span.ynicon.yn-arr-left{
        font-size: 16px;
        display: flex;
        align-items: center;f
        margin-bottom: 10px;
    }

    .yn-arr-left:before {
        margin-right: 2px;
        margin-top: 3px;
        font-size: 18px !important;
    }
    a.yndform_backform {
        color: #44AEC1;
    }
    .entry_breadcrum a.yndform_backform {
        float: unset !important;
        font-size: 22px !important;
        font-weight: bold;
        padding-top: 6px;
    }
    .entry_breadcrum > div > a, .entry_breadcrum .yndform_text_submit {
        font-size: 19px !important;
        font-weight: 500;
        color: #2c2c2c !important;
    }
    #yndform_entry-print .yndform_main_content .form-wrapper, #yndform_user_entry-print .yndform_main_content .form-wrapper {
        display: flex;
        margin-bottom: 15px;
        flex-direction: unset !important;
    }
    #yndform_entry-print .yndform_main_content .form-wrapper .form-label, #yndform_user_entry-print .yndform_main_content .form-wrapper .form-label {

    }

    .ynicon {

    }
    .entry_breadcrum > div > a, .entry_breadcrum .yndform_text_submits {
        font-size: 21px !important;
        color: #2c2c2c;
        font-weight: 500 !important;
    }
    /*.entry-profile-fields .form-label{*/
       /*    text-align:right !important;*/
       /*}*/
    /*.entry-profile-fields  .form-element{*/
       /*    margin-left: 12px;*/
       /*}*/
    .h3 {
        font-size: 18px !important;
        font-weight: 500 !important;
    }
    .entry_breadcrum > div > a, .entry_breadcrum .yndform_text_submit {
        font-size: 19px !important;
        font-weight: 500 !important;
    }
    span.ynicon.yn-arr-left {
        font-size: 17px !important;
    }
    a.yndform_backform {
        color: #0087c3 !important;
    }
    .entry_breadcrum a.yndform_backform {
        float: unset !important;
        font-size: 19px;
        font-weight: bold;
        padding-top: 6px;
    }
    #print_button , #save_button{
        margin-right: 8px;
    }
    .section_header_details {
        margin-top: 2px;
    }
    .yn-arr-left:before {
        top: 2px;
        position: relative;
    }

    </div>
      <style>
      #yndform_entry-print .yndform_main_content .form-wrapper, #yndform_user_entry-print .yndform_main_content .form-wrapper {
          display: flex;
          margin-bottom: 15px;
          flex-direction: unset !important;
      }
    .ynicon {

    }
    a#edit_button {
        margin-left: 4px;
    }
    .h3 {
        font-size: 18px !important;
        font-weight: 500 !important;
    }
    .entry_breadcrum > div > a, .entry_breadcrum .yndform_text_submit {
        font-size: 19px !important;
        font-weight: 500 !important;
    }
    span.ynicon.yn-arr-left {
        font-size: 17px !important;
    }
    a.yndform_backform {
        color: #0087c3 !important;
    }
    .entry_breadcrum a.yndform_backform {
        float: unset !important;
        font-size: 19px;
        font-weight: bold;
        padding-top: 6px;
    }
    #print_button , #save_button{
        margin-right: 8px;
    }
    .section_header_details {
        margin-top: 2px;
    }
    .yn-arr-left:before {
        top: 2px;
        position: relative;
    }


</style>
<?php endif; ?>

<style>
    #yndform_entry-print .yndform_main_content .form-wrapper, #yndform_user_entry-print .yndform_main_content .form-wrapper {
        display: flex;
        margin-bottom: 15px;
        flex-direction: column;
        display: flex;
        margin-bottom: 15px;
        flex-direction: unset !important;
    }
    element.style {
        /* display: flex; */
        /* justify-content: center; */
    }
    div#yndform_user_entry-print {
        display: flex;
        justify-content: center;
    }
    .generic_layout_container.layout_yndynamicform_browse_menu {
        display: none;
    }
    a#print_button {
        margin-right: 5px;
    }
    a#edit_button {
        margin-left: 4px;
    }
    .entry-profile-fields.form-elements.yndform_main_content {
        margin: 0 !important;
    }
    
    #smoothbox_window {
        overflow: scroll !important;
    }
</style>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/html2pdf.js' ?>"></script>
<script>
    // Update publish status in the table
    function updatePublishStatus(entry_id, publish){
        document.getElementById("publish-now-spinner").style.visibility = 'visible';
        document.getElementById("unpublish-now-spinner").style.visibility = 'visible';
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/update-entry-publish-status',
            method: 'POST',
            data: {
                format: 'json',
                entry_id: entry_id,
                status : publish
            },
            onRequest: function () {
                //console.log('debugging request',)
            },
            onSuccess: function (responseJSON) {
                document.getElementById("publish-now-spinner").style.visibility = 'hidden';
                document.getElementById("unpublish-now-spinner").style.visibility = 'hidden';
                
                if( publish == 'true' ) {
                    document.getElementById("publish-now").style.display = 'none';
                    document.getElementById("unpublish-now").style.display = 'block';
                }else {
                    document.getElementById("publish-now").style.display = 'block';
                    document.getElementById("unpublish-now").style.display = 'none';
                }
           //    location.reload();
            }
        })
        request.send();
    }
    
    // Save the PDF
    function saveAsPDF(url) {
        document.getElementById('save-as-pdf-loading').style.display = 'inherit';
        var request = new Request.HTML({
            url: url,
            method: 'POST',
            data: {
                format: 'html'
            },
            onRequest: function () {

            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                let page_content = responseHTML;
                var opt = {
                        margin: [10, 0, 10, 0],
                        filename: 'download_file.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2,  backgroundColor: '#d1d1d1' },
                        jsPDF: { unit: 'pt', format: 'A3', orientation: 'landscape' },
                };
                html2pdf().set(opt).from(page_content).save();
                setTimeout(function(){
                    document.getElementById('save-as-pdf-loading').style.display = 'none';
                }, 2000);
            }
        })
        request.send();
    }
</script>