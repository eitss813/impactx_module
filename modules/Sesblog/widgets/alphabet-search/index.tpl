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
<div class="sesbasic_alphabetic_search">
  <?php $URL =  $this->url(array('action' => 'browse'), 'sesblog_general', true); ?>
  <?php foreach($this->alphbet_array as $key => $alphbet): ?>
    <a href="<?php echo $URL . '?alphabet=' . urlencode($key)?>" <?php if(isset($_GET['alphabet']) && $_GET['alphabet'] == $key):?> class="sesbasic_alphabetic_search_current"<?php endif;?>><?php echo $this->translate($alphbet);?></a>  
  <?php endforeach; ?>
</div>
