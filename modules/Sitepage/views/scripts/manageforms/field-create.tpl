<?php
    // Render the admin js
    echo $this->render('_jsAdmin.tpl');
?>

<div style="height: auto">
  <?php
$baseUrl = $this->layout()->staticBaseUrl;
  $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/field_edit.js');
  ?>

  <?php if( $this->form ): ?>
  <div style="">
    <?php echo $this->form->render($this) ?>
  </div>

  <script type="text/javascript">
    window.addEvent('domready', function(){
      var iframe = $$(parent.document.body).getElement('#TB_iframeContent')[0];
      if (iframe)
        iframe.setStyle('min-height', '200px');

      yndformFieldInit();
    });
  </script>

  <?php else: ?>
  <div class="global_form_popup_message">
    <?php echo $this->translate("Your changes have been saved.") ?>
  </div>

  <script type="text/javascript">
    window.addEvent('domready', function() {
      var iframe = $$(parent.document.body).getElement('#TB_iframeContent')[0];
      if (iframe)
        iframe.setStyle('min-height', '10px');
    });
    parent.onFieldCreate(
    <?php echo Zend_Json::encode($this->field) ?>,
    <?php echo Zend_Json::encode($this->htmlArr) ?>
    );
    (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>
  <?php endif; ?>
</div>
<script>
  showLimit1();
  function showLimit() {

    if( document.getElementById('label-limit'))
      document.getElementById('label-limit').remove();

    let lab = document.getElementById('label');
    console.log('-----------------------------',lab.value);
    var tag = document.createElement("p");
    var text = document.createTextNode(" ("+lab.value.length+" by 250)");
    tag.setAttribute('id','label-limit');
    tag.appendChild(text);
    var element = document.getElementById("label-label");
    element.appendChild(tag);
  }
  function showLimit1() {

    if( document.getElementById('label-limit'))
      document.getElementById('label-limit').remove();


    let lab = document.getElementById('label');
    console.log('-----------------------------',lab.value);
    var tag = document.createElement("p");
    var text = document.createTextNode(" ("+lab.value.length+" by 250)");
    tag.setAttribute('id','label-limit');
    tag.appendChild(text);
    var element = document.getElementById("label-label");
    element.appendChild(tag);
  }
  
  document.getElementById('label').onkeyup = function(e) {
    showLimit1();
  }
</script>
<style>
  p#label-limit-err {
    color: #a94442;
    border-color: #ebccd1;
    margin-top: -11px;
    margin-bottom: 6px;
  }
  label.required {
    margin-right: 6px;
  }
  div#label-label {
    display: flex;
  }
  body#global_page_sitepage-manageforms-field-create, body#global_page_sitepage-manageforms-field-edit {
    height: calc(100vh - 0px) !important;
  }
  #TB_iframeContent {
    height: calc(100vh - 170px) !important;
    min-height: calc(100vh - 270px) !important;
    max-height: calc(100vh - 170px) !important;
  }
  div#conditional_enabled-label {
    margin-top: 7px;
  }
  #global_page_sitepage-manageforms-field-create .form-elements, #global_page_sitepage-manageforms-field-edit .form-elements {
    margin-bottom: 0px !important;
  }
  fieldset#fieldset-buttons {

  }
  body#global_page_sitepage-manageforms-field-create {
    text-align: center;
    padding-top: 4px;
    padding-bottom: 35px;
    clear: both;
    background: #f5f5f5;
    overflow: auto;
  }
  .global_form_popup{
    padding-bottom: 25px !important;
  }
  #yndform_conditional_container #yndform_conditional_list .yndform_conditional_logic_item .yndform_conditional_logic_options {
    margin-top: 8px !important;
  }
  .yndform_conditional_logic_item {
    flex-wrap: wrap;
  }
  #yndform_conditional_container #yndform_conditional_list .yndform_conditional_logic_item .yndform_conditional_logic_buttons {
    margin-top: 8px !important;
  }
  #global_header_wrapper {
    text-align: center;
    clear: both;


  }
  body#global_page_sitepage-manageforms-field-create {
    text-align: center;
    padding-top: 4px;

    padding-bottom: 35px;
    clear: both;
    background: #f5f5f5;

    height: 519px;
    overflow: auto ;
}
#global_content_wrapper {
  text-align: center;
  padding-top: 30px;
  padding-bottom: 30px;
  clear: both;

}
div#style-wrapper {
  display: none;
}
div#error-wrapper {
  display: none;
}
div#show_guest-wrapper {
  display: none;
}
div#show_registered-wrapper {
  display: none;
}
  div#conditional_enabled-element {
   margin-top: 13px;
 }

  div#buttons-wrapper , div#label-wrapper{
    margin-left: 4px !important;
  }
  div#label-element {
    width: 98% !important;
    display: flex !important;
    flex-wrap: wrap-reverse;
    align-items: center !important;
    text-align: center !important;
    margin-bottom: 5px !important;

  }
  span#global_content_simple {
    height: auto;
  }
  div#max_value-label, div#min_value-label, div#default_value-label {
    margin-top: 15px;
    margin-bottom: 2px;
  }

</style>


<?php
if( isset($this->adminFormClass) && !empty($this->adminFormClass) && ($this->adminFormClass == 'Yndynamicform_Form_Admin_Field_Metrics') ):
?>

<script>
    document.getElementById("label-wrapper").style.display = 'none';
    document.getElementById("description-wrapper").style.display = 'none';
</script>

<?php endif; ?>