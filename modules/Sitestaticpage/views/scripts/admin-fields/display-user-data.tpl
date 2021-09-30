

<h2><?php echo 'Static Pages, HTML Blocks and Multiple Forms Plugin'; ?></h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div>
  <?php echo $this->htmlLink(array('action' => 'index', 'reset' => false), 'Back to Manage Forms', array('class' => 'seaocore_icon_back buttonlink')) ?>
</div>
<br /><br />

<div class="admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
        <label>
          <?php echo $this->translate("User Name") ?>
        </label>
        <?php if (empty($this->user_name)): ?>
          <input type="text" name="user_name" /> 
        <?php else:?>
          <input type="text" name="user_name" value="<?php echo $this->user_name?>"/>
        <?php endif;?>
      </div>
      <div style="margin:10px 0 0 10px;">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>

<br /> <br />

<?php if(!empty($this->error_message)) : ?>
<div class="tip">
  <span> <?php echo $this->error_message; ?> </span>
  </div>

<?php else :?>
<div>
<?php echo $this->content; ?>
</div>

<div>
  <?php echo '<br>' .$this->paginationControl($this->paginator); ?>
</div>

<?php endif;?>
