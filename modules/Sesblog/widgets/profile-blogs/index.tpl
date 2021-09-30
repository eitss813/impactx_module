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

<ul class="sesblogs_browse">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class='sesblogs_browse_info'>
        <p class='sesblogs_browse_info_title'>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </p>
        <p class='sesblogs_browse_info_date'>
          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($item->publish_date) ?>
        </p>
        <p class='sesblogs_browse_info_blurb'>
          <?php echo $item->getDescription(300);?>
        </p>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
