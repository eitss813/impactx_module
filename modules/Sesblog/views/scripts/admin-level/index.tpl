<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>
<script type="text/javascript">
  var fetchLevelSettings = function(level_id) {
    window.location.href = en4.core.baseUrl + 'admin/sesblog/level/index/id/' + level_id;
  }
</script>

<div class='clear'>
  <div class='settings sesbasic_admin_form'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">
  scriptJquery(window).load(function() { 
    enablsesblogdesignview();
  });
   scriptJquery( document ).ready(function() {
    continuereadingbutton(scriptJquery("input[name='cotinuereading']:checked").val());
    showHideHeight(scriptJquery("input[name='cntrdng_dflt']:checked").val());
  });

  function continuereadingbutton(value) {
    if (value == 1) {
      scriptJquery('#cntrdng_dflt-wrapper').hide();
      scriptJquery('#continue_height-wrapper').hide();
    } else {
      scriptJquery('#cntrdng_dflt-wrapper').show();
      scriptJquery('#continue_height-wrapper').show();
    }
  }

  function showHideHeight(value) {
    if (value == 1) {
      scriptJquery('#continue_height-wrapper').show();
    } else {
      scriptJquery('#continue_height-wrapper').hide();
    }
  }
  
  function enablsesblogdesignview() {
    var values=document.querySelector('input[name="sesblog_endes"]:checked').value;
    if(values == 1) {
       scriptJquery('#sesblog_cholay-wrapper').show();
        scriptJquery('#sesblog_deflay-wrapper').hide();
    } else {
        scriptJquery('#sesblog_cholay-wrapper').hide();
        scriptJquery('#sesblog_deflay-wrapper').show();
    }
  }
</script>
