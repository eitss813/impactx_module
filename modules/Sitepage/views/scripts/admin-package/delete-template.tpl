
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Delete this template?") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to delete this template? It will not be recoverable after being deleted.") ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->template_id; ?>"/>
      <button type='submit'><?php echo $this->translate("Delete") ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>cancel</a>
    </p>
  </div>
</form>