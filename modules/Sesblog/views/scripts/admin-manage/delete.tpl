<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: delete.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Delete Blog?") ?></h3>
    <p><?php echo $this->translate("Are you sure that you want to delete this blog entry? It will not be recoverable after being deleted.") ?></p>
    <br />
    <p>
      <input type="hidden" name="confirm" />
      <button type='submit'><?php echo $this->translate("Delete") ?></button>
      <?php echo $this->translate(" or ") ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
    </p>
  </div>
</form>
