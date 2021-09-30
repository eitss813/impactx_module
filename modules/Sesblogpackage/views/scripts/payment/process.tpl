<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    process
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Icon.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<div style="position:relative;height:200px;">
 <div class="sesbasic_loading_cont_overlay" style="display:block;"></div>
</div>
<script type="text/javascript">
  window.addEvent('load', function(){
    var url = '<?php echo $this->transactionUrl ?>';
    var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
    var request = new Request.Post({
      url : url,
      data : data
    });
    request.send();
  });
</script>