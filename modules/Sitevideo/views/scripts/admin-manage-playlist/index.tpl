<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate("Advanced Videos / Channels / Playlists Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate("Manage Playlists") ?></h3>
<p>
    <?php echo $this->translate("This page lists all the playlists your users have created. You can use this page to monitor these playlists and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific playlist entries. Leaving the filter fields blank will show all the playlist entries on your social network. ") ?>
</p>
<br />

<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {
        // Just change direction
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }

        $('filter_form').submit();
    };

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected playlists ?")) ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;

        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>

<div class="admin_search sitevideo_admin_video_search">
    <div class="search">
        <form method="post" class="global_form_box" action="" width="100%">

            <div>
                <label>
                    <?php echo $this->translate("Title") ?>
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->translate($this->title) ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Owner") ?>
                </label>	
                <?php if (empty($this->owner)): ?>
                    <input type="text" name="owner" /> 
                <?php else: ?> 
                    <input type="text" name="owner" value="<?php echo $this->translate($this->owner) ?>" />
                <?php endif; ?>
            </div>   
            <div>
              <?php 
                //MAKE THE STARTTIME AND ENDTIME FILTER
                $attributes = array();
                $attributes['dateFormat'] = 'ymd';
                $form = new Engine_Form_Element_CalendarDateTime('starttime');
                $attributes['options'] = $form->getMultiOptions();
                $attributes['id'] = 'starttime';
                $starttime['date'] = $this->starttime;
                echo '<label>Date</label><div>';
                echo $this->FormCalendarDateTime('starttime', $starttime, array_merge(array('label' => 'From'), $attributes), $attributes['options'] );
                echo '</div>';
              ?>
            </div>     
            <div class="clear mtop10">
                <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
            </div>
        </form>
    </div>
</div>
<br />

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<?php if (count($this->paginator) > 0): ?>
    <div class='admin_members_results'>
        <div>
            <?php $count = $this->paginator->getTotalItemCount() ?>
            <?php echo $this->translate(array("%s playlist found.", "%s playlists found.", $count), $this->locale()->toNumber($count))
            ?>
        </div>
    <?php else: ?>
        <div class="tip"><span>
                <?php echo $this->translate("No playlists were found.") ?></span>
        </div>
    <?php endif; ?>
    <div style="margin-top:5px">
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>
</div>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete();">
        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr>
                    <th><input onclick="selectAll();" type='checkbox' class='checkbox'></th>

                    <?php $class = ( $this->order == 'playlist_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('playlist_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>

                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>

                    <?php $class = ( $this->order == 'displayname' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>

                    <?php $class = ( $this->order == 'view_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center" class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');" title="<?php echo $this->translate('Views'); ?>" ><?php echo $this->translate('Views'); ?></a></th>

                    <?php $class = ( $this->order == 'like_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center"  class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'ASC');" title="<?php echo $this->translate('Likes'); ?>" ><?php echo $this->translate('Likes'); ?></a></th>
                    
                    <?php $class = ( $this->order == 'video_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?> center"  class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('video_count', 'ASC');" title="<?php echo $this->translate('Total Videos'); ?>" ><?php echo $this->translate('Total Videos'); ?></a></th>

                    <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Creation Date") ?></a></th>
                    <th class="<?php echo $class ?>" class='admin_table_centered'><?php echo $this->translate("Option") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->paginator)): ?>
                    <?php
                    foreach ($this->paginator as $item):
                        ?>
                        <tr>
                            <td><input name='delete_<?php echo $item->playlist_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->playlist_id ?>"/></td>
                            <td><?php echo $item->playlist_id ?></td>
                            <td class='admin_table_bold'>
                                <?php
                                echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 10), array('target' => '_blank'))
                                ?>
                            </td>
                            <td class='admin_table_user'>
                                <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('target' => '_blank')) ?>
                            </td>

                            <td class="center"><?php echo $this->locale()->toNumber($item->view_count) ?></td>
                            <td class="center"><?php echo $this->locale()->toNumber($item->like_count) ?></td>
                            <td class="center"><?php echo $this->locale()->toNumber($item->video_count) ?></td>
                            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>             

                            <td class='admin_table_options'>
                                <?php echo $this->htmlLink($item->getHref(), 'View',array('target' => '_blank')); ?>    
                                <?php
                                echo $this->htmlLink(array(
                                    'route' => 'default',
                                    'module' => 'sitevideo',
                                    'controller' => 'playlist',
                                    'action' => 'edit',
                                    'playlist_id' => $item->playlist_id,
                                    'admin' =>true
                                        ), $this->translate('Edit'), array('class' => 'smoothbox'));
                                ?>
                                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitevideo', 'controller' => 'admin-manage-playlist', 'action' => 'Delete', 'id' => $item->playlist_id), $this->translate("delete"), array('class' => 'smoothbox')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
        </div>
    </form>
<?php endif; ?>


<?php
$dateFormat = $this->locale()->useDateLocaleFormat();
$calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
$calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
?>
<script type="text/javascript">
    seao_dateFormat = '<?php echo $this->locale()->useDateLocaleFormat(); ?>';
    var showMarkerInDate = "<?php echo $this->showMarkerInDate ?>";
    en4.core.runonce.add(function()
    {
        en4.core.runonce.add(function init()
        {
            monthList = [];
            myCal = new Calendar({'start_cal[date]': '<?php echo $calendarFormatString; ?>', 'end_cal[date]': '<?php echo $calendarFormatString; ?>'}, {
                classes: ['event_calendar'],
                pad: 0,
                direction: 0
            });
        });
    });

    var cal_starttime_onHideStart = function() {
        if (showMarkerInDate == 0)
            return;
        var cal_bound_start = seao_getstarttime(document.getElementById('startdate-date').value);
        // check end date and make it the same date if it's too
        cal_endtime.calendars[0].start = new Date(cal_bound_start);
        // redraw calendar
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    }
  

    en4.core.runonce.add(function() {
        cal_starttime_onHideStart();
    });

    window.addEvent('domready', function() {
        if ($('starttime-minute')) {
            $('starttime-minute').destroy();
        }
        if ($('starttime-ampm') ) {
            $('starttime-ampm').destroy();
        }
        if ($('starttime-hour') ) {
            $('starttime-hour').destroy();
        }

        if ($('calendar_output_span_starttime-date')) {
            $('calendar_output_span_starttime-date').style.display = 'none';
        }
        if ($('starttime-date')) {
            $('starttime-date').setAttribute('type', 'text');
        }
    });
</script>
