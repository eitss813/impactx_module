
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Delete this layout?") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to delete this layout? It will not be recoverable after being deleted.") ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->layout_id; ?>"/>
      <button type='submit'><?php echo $this->translate("Delete") ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>cancel</a>
    </p>
  </div>
</form>