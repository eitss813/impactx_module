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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<div class="sesblog_social_share_blog<?php echo $this->design_type; ?> sesbasic_bxs">
  <?php 
    if(in_array($this->design_type, array(3,4)))
      $param = 'photoviewpage'; 
    else 
      $param = ''; 
  ?>
  <?php echo $this->partial('_socialShareIcons.tpl','sesbasic',array('resource' => $this->subject, 'param' => $param)); ?>
</div>
