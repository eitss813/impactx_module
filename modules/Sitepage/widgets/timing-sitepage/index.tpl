<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h3><?php echo $this->widgetTitle;?>
  <?php
  if ($this->online_status) :?>
  <span class="sitepage_page_online" title="Online"></span>
<?php else :?>
  <span class="sitepage_page_offline" title="Offline"></span>
<?php endif; ?>
</h3>
<?php
$totalDays = array('monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday');?>
<?php
if ($this->status == true) {?>
<div class="open"><?php echo $this->translate("Always Open");?></div>
<?php
} else {
  foreach ($this->row as $values) {
    $daysarray[$values['day']] = $values['day'];
    $daysarray[$values['day'].'start'] = $values['start'];
    $daysarray[$values['day'].'end'] = $values['end'];
  }
  ?>
  <div class="container_">
    <div class="row">
      <span class="weekzone">
        Days
      </span>
      <span class="timezone">
        <?php echo $this->timezone; ?>
      </span>
    </div>
    <?php foreach ($totalDays as $key => $value) :?>
      <div class="row">
        <span class="timing_day"><?php echo $value;?></span>
        <span class="timing_time">
          <?php if(array_key_exists($key, $daysarray)) :?>
            <div class="start"><?php echo date("g:i a", strtotime($daysarray[$key.'start']));?></div>
            -
            <div class="end"><?php echo date("g:i a", strtotime($daysarray[$key.'end']));?></div>
          <?php else :?>
            <div class="closed"><?php echo "Closed";?></div>
          <?php endif;?>
        </span>
      </div>
    <?php endforeach;?>
  </div>
  <?php
}
?>