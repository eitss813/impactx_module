<?php

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>

<script type="text/javascript">

  function importSeBlog() {

    scriptJquery('#loading_image').show();
    scriptJquery('#ssesblog_import').hide();
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'admin/sesblog/import-blog',
      method: 'get',
      data: {
        'is_ajax': 1,
        'format': 'json',
      },
      onSuccess: function(responseJSON) {
        if (responseJSON.error_code) {
          scriptJquery('#loading_image').hide();
          scriptJquery('#ssesblog_message').html("<span>Some error might have occurred during the import process. Please refresh the page and click on “Start Importing Blog” again to complete the import process.</span>");
        } else {
          scriptJquery('#loading_image').hide();
          scriptJquery('#ssesblog_message').hide();
          scriptJquery('#ssesblog_message1').html("<span>" + '<?php echo $this->string()->escapeJavascript($this->translate("Blogs from SE Blog have been successfully imported.")) ?>' + "</span>");
        }
      }
    }));
  }
</script>
<div class='settings'>
  <form class="global_form">
    <div>
      <h3><?php echo $this->translate('Import SE Blog into this Plugin');?></h3>
      <p class="description">
        <?php echo $this->translate('Here, you can import blogs from SE Blog plugin into this plugin.'); ?>
      </p>
      <div class="clear sesblog_import_msg sesblog_import_loading" id="loading_image" style="display: none;">
        <span><?php echo $this->translate("Importing ...") ?></span>
      </div>
      <div id="ssesblog_message" class="clear sesblog_import_msg sesblog_import_error"></div>
      <div id="ssesblog_message1" class="clear sesblog_import_msg sesblog_import_success"></div>
      <?php if(count($this->seBlogResults) > 0): ?>
        <div id="ssesblog_import">
          <button class="sesblog_import_button" type="button" name="sesblog_import" onclick='importSeBlog();'>
            <?php echo $this->translate('Start Importing Blog');?>
          </button>
        </div>
      <?php else: ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('There are no blogs in SE Blog plugin to be imported into this plugin.') ?>
          </span>
        </div>
      <?php endif; ?>
    </div>
  </form>
</div>
