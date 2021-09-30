<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/field_edit.js');
?>

<?php if( $this->form ): ?>
  <div style="width: 768px">
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
    parent.onFieldEdit(
      <?php echo Zend_Json::encode($this->field) ?>,
      <?php echo Zend_Json::encode($this->htmlArr) ?>
    );
    (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>

<?php endif; ?>