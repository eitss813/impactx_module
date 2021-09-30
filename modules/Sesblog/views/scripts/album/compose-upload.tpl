<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: compose-upload.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<script type="text/javascript">
  $try(function(){
    parent.en4.album.getComposer().processResponse(<?php echo $this->jsonInline($this->getVars()) ?>);
  });
  $try(function() {
    parent._composePhotoResponse = <?php echo $this->jsonInline($this->getVars()) ?>;
  });
</script>
