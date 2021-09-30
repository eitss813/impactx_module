
 <form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Activate this template?") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure you want to activate this template? This action will deactivate the previously chosen template.") ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->template_id; ?>"/>
      <button type='submit'><?php echo $this->translate("Activate") ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>cancel</a>
    </p>
  </div>
</form>