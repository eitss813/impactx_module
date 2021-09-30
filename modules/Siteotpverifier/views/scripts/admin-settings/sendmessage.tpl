<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Siteotpverifier
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    sendmessage.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Siteotpverifier/View/Helper', 'Siteotpverifier_View_Helper'); ?>
<h2>
    <?php echo 'One Time Password (OTP) Plugin'; ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='siteotpverifier_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div>
    <p> Here, you can view the information of users to whom you have send the SMS. You can also send SMS to all users, to users as per different member level or to a specific user via ‘Send SMS’ popup. </p>
</div>
<br/>
<div class="mbot10">
    <?php 
    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteotpverifier', 'controller' => 'settings', 'action' => 'message-send'), $this->translate('Send SMS'), array('class' => 'smoothbox link_button otpverifier_email_icon'));
    ?>
</div>


<div class="search transaction_search">
    <form name="siteotpverifier_message_sent_search_form" id="siteotpverifier_message_sent_search_form" method="post" class="global_form_box" action="">
        <input type="hidden" name="post_search" />
        <div>
            <label> <?php echo  $this->translate("User Name") ?> </label>
            <input type="text" id="username" name="username" value="<?php echo empty($this->username)?'':$this->username; ?>"/>
        </div>
        <div style="display: none;" id="user_id-wrapper" class="form-wrapper">
            <div id="user_id-label" class="form-label">&nbsp;</div>
            <div id="user_id-element" class="form-element">
                <input name="user_id" value="" id="user_id" type="hidden">
            </div>
        </div>
        <div>
            <?php $options=array('day'=>'Today','weekly'=>'Last 7 Days','range'=>'Specific Time Interval');?>
            <div>
                <label for="show">Time Interval</label>
                <div id="show-element" class="form-element">
                    <select name="show_time" id="show_time" onchange="onTimeChange()">
                        <option value=""></option>
                        <?php foreach($options as $key => $value): 
                        if($this->show_time == $key) :?>
                        <option value="<?php echo $key ?>" selected="selected" ><?php echo $value ?></option>
                        <?php else : ?>
                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>
            </div>
            <div id="custom_time" display="none">
                <div>
                    <?php 
                    //MAKE THE STARTTIME AND ENDTIME FILTER
                    $attributes = array();
                    $attributes['dateFormat'] = 'ymd';

                    $form = new Engine_Form_Element_CalendarDateTime('starttime');
                    $attributes['options'] = $form->getMultiOptions();
                    $attributes['id'] = 'starttime';

                    $starttime['date'] = $this->starttime;
                    $endtime['date'] = $this->endtime;

                    echo '<label>From</label><div>';
                    echo $this->FormCalendarDateTime('starttime', $starttime, array_merge(array('label' => 'From'), $attributes), $attributes['options'] );
                    echo '</div>';
                    ?>
                </div>
                <div>
                    <?php 
                    $form = new Engine_Form_Element_CalendarDateTime('endtime');
                    $attributes['options'] = $form->getMultiOptions();              
                    $attributes['id'] = 'endtime';
                    echo '<label>To</label><div>';
                    echo $this->FormCalendarDateTime('endtime', $endtime, array_merge(array('label' => 'To'), $attributes), $attributes['options'] );
                    echo '</div>';
                    ?>
                </div>
            </div>
        </div>
        <div>
            <label><?php echo  $this->translate("Message") ?></label>
            <div class="form-element">
                <input type="text" name="message"  value="<?php echo empty($this->message)?'':$this->message; ?>" style="width: 200px;" />
            </div>
        </div>
        <div>
            <label><?php echo  $this->translate("Based On") ?></label>
            <div class="form-element">
                <select name="basedon" id="basedon" onchange="onchoiceChange()">
                    <option value=""></option>
                    <?php if($this->basedon == "profile") :?>
                        <option value="profile" selected="selected">Profile Type</option>
                    <?php else : ?>
                        <option value="profile">Profile Type</option>
                    <?php endif; ?>
                    <?php if($this->basedon == "memberlevel") :?>
                        <option value="memberlevel" selected="selected">Member Level</option>
                    <?php else : ?>
                        <option value="memberlevel">Member Level</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>  
        <div id="profiletype_div">
            <label><?php echo  $this->translate("Profile Type") ?></label>
            <div class="form-element">
                <select name="profiletype" id="profiletype" >
                    <?php foreach( $this->profile_type as $key => $value ): ?>
                        <?php if($this->profiletype == $key) :?>
                        <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                        <?php else : ?>
                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div id="member_level_div">
            <label><?php echo  $this->translate("Member Level") ?></label>
            <div class="form-element">
                <select name="member_level" id="member_level">
                    <option value=""></option>
                    <?php foreach( Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level ): ?>
                        <?php if($this->member_level == $level->level_id) :?>
                            <option value="<?php echo $level->level_id ?>" selected="selected"><?php echo $level->getTitle() ?></option>
                        <?php else : ?>
                            <option value="<?php echo $level->level_id ?>"><?php echo $level->getTitle() ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div style="margin-top:16px;">
            <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
        </div>
    </form>
</div>
<div class='admin_search'> <?php echo $this->formFilter->render($this) ?> </div>

<div class="mtop10">
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s record found", "%s records found", $count), $count) ?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
    <table class='admin_table'>
        <thead>
            <tr>
                <?php $class = ( $this->order == 'sentmessage_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th class="<?php echo $class ?>" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('sentmessage_id', 'ASC');"><?php echo $this->translate("Id") ?></a></th>
                <th>Send By</th>
                <th>Send To</th>
                <th>Based On</th>
                <th>Profile Type</th>
                <?php $class = ( $this->order == 'member_level' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('member_level', 'ASC');"><?php echo $this->translate("Member Level") ?></a></th>
                <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');"><?php echo $this->translate("Date") ?></a></th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($this->paginator as $item): ?>
            <tr>
                <td><?php echo $item->sentmessage_id ?></td>
                <?php  $user = Engine_Api::_()->getItem('user', $item->owner_id); ?>
                <td><?php echo $this->htmlLink($user->getHref(), $this->string()->stripTags($user->getTitle()), array('title' => $user->getTitle(), 'target' => '_blank')); ?></td>
                <?php if($item->user_id) : 
                $user = Engine_Api::_()->getItem('user', $item->user_id); ?>
                <td><?php echo $this->htmlLink($user->getHref(), $this->string()->stripTags($user->getTitle()), array('title' => $user->getTitle(), 'target' => '_blank'));?></td>
                <?php else : ?>
                <td>All members</td>
                <?php endif;?>
                <td>
                    <?php if($item->type): echo "Member Levels"; else: echo "Profile Types"; endif; ?>

                </td>

                <td>
                    <?php if(empty($item->type)):?>
                    <?php if(empty($item->profile_type) ): 
                    echo "All Profile Type";
                    else: 
                    echo $this->profile_type[$item->profile_type];
                    endif; endif;?> 

                </td>
                <td><?php 
                    if(!empty($item->member_level) || !empty($item->user_id)):
                    if(empty($item->member_level) && !empty($item->user_id))
                    $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
                    else
                    $level = Engine_Api::_()->getItem('authorization_level', $item->member_level); 
                    if($level->getTitle()): ?>
                    <?php echo $level->getTitle() ?>
                    <?php endif; ?>
                    <?php else : ?>
                    <?php if($item->type): ?>
                    All member levels 
                    <?php endif; ?>    
                    <?php endif; ?></td>
                <td><?php echo date('dS F Y ', strtotime($item->creation_date)) ?></td>
                <td><?php echo $item->message ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br />
</form>
<br/>
<?php endif; ?>
<div style="clear:left;">
    <?php   echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    ));
    ?>
</div>
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
            };    </script>
<?php

$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
            var contentAutocomplete;
            var maxRecipients = 10;
            function removeFromToValue(id, elmentValue, element) {
            // code to change the values in the hidden field to have updated values
            // when recipients are removed.
            var toValues = $(elmentValue).value;
                    var toValueArray = toValues.split(",");
                    var toValueIndex = "";
                    var checkMulti = id.search(/,/);
                    // check if we are removing multiple recipients
                    if (checkMulti != - 1) {
            var recipientsArray = id.split(",");
                    for (var i = 0; i < recipientsArray.length; i++){
            removeToValue(recipientsArray[i], toValueArray, elmentValue);
            }
            } else {
            removeToValue(id, toValueArray, elmentValue);
            }

            // hide the wrapper for element if it is empty
            if ($(elmentValue).value == ""){
            $(elmentValue + '-wrapper').setStyle('height', '0');
                    $(elmentValue + '-wrapper').setStyle('display', 'none');
            }
            $(element).disabled = false;
            }

    function removeToValue(id, toValueArray, elmentValue) {
    for (var i = 0; i < toValueArray.length; i++){
    if (toValueArray[i] == id) toValueIndex = i;
    }
    toValueArray.splice(toValueIndex, 1);
            $(elmentValue).value = toValueArray.join();
    }
    en4.core.runonce.add(function()
    {

    contentAutocomplete = new Autocompleter.Request.JSON('username', '<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'settings', 'action' => 'getallusers'), 'admin_default', true) ?>', {
    'postVar' : 'search',
            'postData' : {'user_ids': $('user_id').value},
            'minLength': 1,
            'delay' : 250,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'siteotpverifier-autosuggest',
            'filterSubset' : true,
            'multiple' : false,
            'injectChoice': function(token){
            var choice = new Element('li', {
            'class': 'autocompleter-choices',
                    'html': token.photo,
                    'id':token.label
            });
                    new Element('div', {
                    'html': this.markQueryValue(token.label),
                            'class': 'autocompleter-choice'
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
            },
            onPush : function() {
            if ($('user_id-wrapper')) {
            $('user_id-wrapper').style.display = 'block';
            }

            if ($(this.options.elementValues).value.split(',').length >= maxRecipients) {
            this.element.disabled = true;
            }
            contentAutocomplete.setOptions({
            'postData' : {'user_ids': $('user_id').value}
            });
            }

    });
            contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
            $('user_id').value = selected.retrieve('autocompleteChoice').id;
            });
    });</script> 
<script type="text/javascript">
            window.addEvent('domready', function () {
            onTimeChange();
            onchoiceChange();
            });
            function onTimeChange(){
                var e = document.getElementById("show_time");
                var strUser = e.options[e.selectedIndex].value;
                if (strUser == "range"){
                    $("custom_time").show();
                } else {
                    $("custom_time").hide();
                }
            }
            function onchoiceChange(){
                var e = document.getElementById("basedon");
                var strUser = e.options[e.selectedIndex].value;
                if (strUser == "profile"){
                    $("profiletype_div").show();
                    $("member_level_div").hide();
                } else if (strUser == "memberlevel"){
                    $("profiletype_div").hide();
                    $("member_level_div").show();
                } else {
                    $("member_level_div").hide();
                    $("profiletype_div").hide();
                }
            }
</script>
<?php
$dateFormat = $this->useDateLocaleFormat();
$calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
$calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
?>
<script type="text/javascript">
    seao_dateFormat = '<?php echo $this->useDateLocaleFormat(); ?>';
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
                    cal_endtime.navigate(cal_endtime.calendars[0], 'm', - 1);
            }
    var cal_endtime_onHideStart = function() {
    if (showMarkerInDate == 0)
            return;
            var cal_bound_start = seao_getstarttime(document.getElementById('endtime-date').value);
            // check start date and make it the same date if it's too
            cal_starttime.calendars[0].end = new Date(cal_bound_start);
            // redraw calendar
            cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
            cal_starttime.navigate(cal_starttime.calendars[0], 'm', - 1);
    }

    en4.core.runonce.add(function() {
    cal_starttime_onHideStart();
            cal_endtime_onHideStart();
    });
            window.addEvent('domready', function() {
            if ($('starttime-minute') && $('endtime-minute')) {
            $('starttime-minute').destroy();
                    $('endtime-minute').destroy();
            }
            if ($('starttime-ampm') && $('endtime-ampm')) {
            $('starttime-ampm').destroy();
                    $('endtime-ampm').destroy();
            }
            if ($('starttime-hour') && $('endtime-hour')) {
            $('starttime-hour').destroy();
                    $('endtime-hour').destroy();
            }

            if ($('calendar_output_span_starttime-date')) {
            $('calendar_output_span_starttime-date').style.display = 'none';
            }

            if ($('calendar_output_span_endtime-date')) {
            $('calendar_output_span_endtime-date').style.display = 'none';
            }

            if ($('starttime-date')) {
            $('starttime-date').setAttribute('type', 'text');
            }

            if ($('endtime-date')) {
            $('endtime-date').setAttribute('type', 'text');
            }
            });

</script>