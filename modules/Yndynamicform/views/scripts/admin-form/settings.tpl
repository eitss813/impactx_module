<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimateform
 * @author     YouNet Company
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
$this->headScript()
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js');
?>

<script type="text/javascript">
    function isGuest(ele) {
        if (ele.id == 'auth_view-guest') {
            $('require_login-wrapper').setStyle('display', ele.checked ? 'block' : 'none');
            $('require_login_message-wrapper').setStyle('display', ele.checked ? 'flex' : 'none');
            $('show_email_popup-wrapper').setStyle('display', ele.checked ? 'block' : 'none');
            if (ele.checked == true) {
                var isRequireLogin = $('require_login-1').checked;
                $('require_login_message-wrapper').setStyle('display', isRequireLogin ? 'flex' : 'none');
                $('show_email_popup-wrapper').setStyle('display', isRequireLogin ? 'none' : 'block');
            }
        }
    }
    function showEditingPeriod(ele) {
        switch (ele.id) {
            case 'entries_editable-0':
                $('entries_editable_within-wrapper').setStyle('display', 'none');
                $('time_unit-wrapper').setStyle('display', 'none');
                break;
            case 'entries_editable-1':
                $('entries_editable_within-wrapper').setStyle('display', 'flex');
                $('time_unit-wrapper').setStyle('display', 'flex');
                break;
            default:
                $('entries_editable-0').setStyle('checked', 'checked');
                $('entries_editable-1').setStyle('checked', '');
                $('entries_editable_within-wrapper').setStyle('display', 'none');
                $('time_unit-wrapper').setStyle('display', 'none');
                break;
        }
    }
    
    function showEmailPopup(ele) {
        switch (ele.id) {
            case 'require_login-0':
                $('show_email_popup-wrapper').setStyle('display', 'flex');
                $('require_login_message-wrapper').setStyle('display', 'none');
                break;
            case 'require_login-1':
                $('show_email_popup-wrapper').setStyle('display', 'none');
                $('require_login_message-wrapper').setStyle('display', 'flex');
                break;
            default:
                $('require_login-0').setStyle('checked', 'checked');
                $('require_login-1').setStyle('checked', '');
                $('show_email_popup-wrapper').setStyle('display', 'none');
                $('require_login_message-wrapper').setStyle('display', 'none');
                break;
        }
    }

    function switchInputType(ele) {
        switch (ele.id) {
            case 'input_type-txt':
                textInputType();
                break;
            case 'input_type-img':
                imageInputType();
                break;
            default:
                textInputType();
                break;
        }
    }

    function textInputType() {
        // Hide all input type image
        $('btn_image-wrapper').setStyle('display', 'none');
        $('btn_hover_image-wrapper').setStyle('display', 'none');
        $('heading_btn_image-wrapper').setStyle('display', 'none');
        // Show all input type text
        $('btn_text-wrapper').setStyle('display', 'flex');
        var input_color = $$('.yndform_color_input');
        input_color.each(function (ele) {
            $(ele.id + '-wrapper').setStyle('display', 'flex');
        });
    }

    function imageInputType() {
        // Show all input type image
        $('btn_image-wrapper').setStyle('display', 'flex');
        $('btn_hover_image-wrapper').setStyle('display', 'flex');
        $('heading_btn_image-wrapper').setStyle('display', 'block');
        // Hide all input type text
        $('btn_text-wrapper').setStyle('display', 'none');
        var input_color = $$('.yndform_color_input');
        input_color.each(function (ele) {
            $(ele.id + '-wrapper').setStyle('display', 'none');
        });
    }
    
    function unlimitedTime(ele) {
        if (ele.checked == true) {
            $('valid_to_date-element').setStyle('display', 'none');
        } else {
            $('valid_to_date-element').setStyle('display', 'inline-block');
        }
    }

</script>
<script type="text/javascript">
    window.addEvent('domready', function() {
        var picker = $$('.yndform_color_input');
        picker.each(function (ele) {
            var pickerElement = '\'#'+ ele.id + '_pick-element span\'';
            var pickerId = ele.get('id');
            var innerHTML = "<input value='" + ele.value +  "' type='color' id='" + ele.id +  "' name='" + ele.id + "'/>"
            $(pickerId + '_pick-element').getChildren('span').set('html', innerHTML);
            if (pickerId + '_pick-wrapper') {
                $(pickerId + '_pick-element').getChildren('span').inject($(pickerId + '-element'), 'bottom');
                $(pickerId + '_pick-wrapper').dispose();
            }
        })
        // Switch input type
        <?php if (strcmp($this -> form -> input_type, 'txt') == 0): ?>
            textInputType();
        <?php else: ?>
            imageInputType();
        <?php endif; ?>

        // Switch editable entries
        <?php if ($this -> form ->entries_editable): ?>
            $('entries_editable_within-wrapper').setStyle('display', 'flex');
            $('time_unit-wrapper').setStyle('display', 'flex');
        <?php else: ?>
            $('entries_editable_within-wrapper').setStyle('display', 'none');
            $('time_unit-wrapper').setStyle('display', 'none');
        <?php endif; ?>

        // Switch require login to submit
        <?php if ($this -> form ->require_login): ?>
            $('show_email_popup-wrapper').setStyle('display', 'none');
            $('require_login_message-wrapper').setStyle('display', 'flex');
        <?php else: ?>
            $('show_email_popup-wrapper').setStyle('display', 'flex');
            $('require_login_message-wrapper').setStyle('display', 'none');
        <?php endif; ?>

        // Switch unlimitted time
        <?php if ($this -> form ->unlimited_time): ?>
            $('valid_to_date-wrapper').setStyle('display', 'none');
        <?php else: ?>
            $('valid_to_date-wrapper').setStyle('display', 'inline-block');
        <?php endif; ?>

        var entries_max_per = $('entries_max_per');
        entries_max_per.inject($('entries_max-element'), 'bottom');
        $('entries_max_per-wrapper').dispose();

        var unlimited_time_element = $('unlimited_time-element');
        unlimited_time_element.inject($('heading_valid_time-element'), 'top');
        $('unlimited_time-wrapper').dispose();
        unlimitedTime($('unlimited_time'));
        
        $$('.yndform_color_picker').addEvent('change', function (ele) {
            $(ele.target.name).value = ele.target.value;
        })
        
        $$('.yndform_color_input').addEvent('change', function (ele) {
            $(ele.target.name + '_pick').value = ele.target.value;
        })

        $('valid_from_date-element').inject($('heading_valid_time-element'), 'bottom');
        $('valid_from_date-wrapper').dispose();
        $('valid_to_date-element').inject($('heading_valid_time-element'), 'bottom');
        $('valid_to_date-wrapper').dispose();
    })
</script>
<script type="text/javascript">
    var ynDynamicFormCalendar= {
        currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
        monthNames: ['<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
        ],
        monthNamesShort: ['<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
        ],
        dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',
        ],
        dayNamesShort: ['<?php echo $this->translate('Su') ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        firstDay: 0,
        isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

    jQuery(document).ready(function(){
        jQuery.datepicker.setDefaults(ynDynamicFormCalendar);
        jQuery('#valid_from_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
            buttonImageOnly: true,
            buttonText: '<?php echo $this -> translate("Select date")?>'
        });

        jQuery('#valid_to_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
            buttonImageOnly: true,
            buttonText: '<?php echo $this -> translate("Select date")?>'
        });
    });
</script>

<div class="headline">
    <h2>
        <?php echo $this->translate('Dynamic Form Plugin');?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
        ?>
    </div>
</div>
<h3><?php echo $this->form->getTitle() ?> &#47; Edit Form</h3>
<?php if (!empty($this -> message)): ?>
    <ul class="form-notices"><li><?php echo $this -> message ?></li></ul>
<?php endif; ?>
<div class="yndform_edit_form clearfix">
    <?php echo $this->partial('_menuSettings.tpl', 'yndynamicform', array('form' => $this->form,'editform' => $this -> editform, 'settings' => 'yndform_active')); ?>
    <?php echo $this->editform->render($this) ?>
</div>
