<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<style type="text/css">
select{
  float:left;
  margin-right:10px;
}
</style>
<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/composer.js');
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
$this->headScript()
		 ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
     ->appendFile($this->layout()->staticBaseUrl .'application/modules/Seaocore/externals/scripts/autocompleter/Autocompleter.js')
 		 ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
 		 ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
var maxRecipients = 1;

function removeFromToValue(id, elmentValue,element) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $(elmentValue).value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1) {
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray, elmentValue);
      }
    } else {
      removeToValue(id, toValueArray, elmentValue);
    }

    // hide the wrapper for element if it is empty
    if ($(elmentValue).value==""){
      $(elmentValue+'-wrapper').setStyle('height', '0');
      $(elmentValue+'-wrapper').setStyle('display', 'none');
    }
    $(element).disabled = false;
  }
 
  function removeToValue(id, toValueArray, elmentValue) {
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }
    toValueArray.splice(toValueIndex, 1);
    $(elmentValue).value = toValueArray.join();
  } 
 
  var projectidsAutocomplete;
  en4.core.runonce.add(function() {

		projectidsAutocomplete = new Autocompleter.Request.JSON('project_name', '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'report', 'action' => 'suggest-projects'), 'admin_default', true) ?>', {
			'postVar' : 'search',
      'postData' : {'project_id': $('project_id').value},
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'elementValues': 'project_id',
			'autocompleteType': 'message',
			'multiple': true,
			'className': 'seaocore-autosuggest',
			'filterSubset' : true,
			'tokenFormat' : 'object',
			'tokenValueKey' : 'label',
			'injectChoice': function(token) {
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
				if ($('project_id-wrapper')) {
					$('project_id-wrapper').style.display='block';
				}
				
				if( $(this.options.elementValues).value.split(',').length >= maxRecipients ) {
					this.element.disabled = true;
				}
        projectidsAutocomplete.setOptions({
          'postData' : {'project_id': $('project_id').value}
        });
        
			}
		});


})

</script>

<script type="text/javascript">

  window.addEvent('domready', function() { 
    $('from_month-day').hide();
    $('to_month-day').hide();
    var values = [];
    $('from_month-year').getElements('option').each(function (elem) {
        values.push(elem.get('text'));

    });
    selectedVal = $('from_month-year').value;
    values.sort();
    $('from_month-year').empty();
    $each(values, function (value) {
        new Element('option')
                .set('text', value)
                .set('value', value)
                .inject($('from_month-year'));
    });
    $('from_month-year').value = selectedVal;

    var values1 = [];
    $('to_month-year').getElements('option').each(function (elem) {
        values1.push(elem.get('text'));

    });
    selectedVal = $('to_month-year').value;
    values1.sort();
    $('to_month-year').empty();
    $each(values1, function (value) {
        new Element('option')
                .set('text', value)
                .set('value', value)
                .inject($('to_month-year'));
    });
    $('to_month-year').value = selectedVal;



    $('start_cal-minute').style.display= 'none';
    if($('start_cal-ampm'))
      $('start_cal-ampm').style.display= 'none';
    $('start_cal-hour').style.display= 'none';
    $('end_cal-minute').style.display= 'none';
    if($('end_cal-ampm'))
      $('end_cal-ampm').style.display= 'none';
    $('end_cal-hour').style.display= 'none';

    var empty = '<?php echo $this->empty; ?>';
    var no_ads = '<?php echo $this->no_ads ?>';
   
    form = $('adminreport_form');
    form.setAttribute("method","get");
    
    var e3 = $('project_name-wrapper');
    e3.setStyle('display', 'none');
    
    var e4 = $('project_id-wrapper');
    e4.setStyle('display', 'none');
    
    onProjectChange($('select_project'));
    onchangeFormat($('format_report'));
    onReportTypeChange($('report_type'));

    // display message tip
    if(empty == 1) { 
        $('tip').style.display= 'block';
    }
  $('project_id').value = '';
  });

  function onProjectChange(formElement) {
    var e1 = formElement.value; 

    if(e1 == 'specific_project') {
      $('project_name-wrapper').setStyle('display', 'block');
      $('project_name').disabled = false;  
    } else {
      $('project_name-wrapper').setStyle('display', 'none');
      $('project_id-wrapper').style.display='none';  
      if($('project_id').value)
      {
        $('project_id').value = null;
        $('project_id-element').getElements('.tag').destroy();
        
        projectidsAutocomplete.setOptions({
          'postData' : {'project_id': $('project_id').value}
        }); 
      }
    }
  } 
  function onReportTypeChange(formElement) { 
      var el = formElement.value;
      if(el == 'summarised') {
        $('to_month-wrapper').hide();
        $('from_month-wrapper').hide();
        $('start_cal-wrapper').show();
        $('end_cal-wrapper').show();
      } else if(el == 'monthwise'){
        $('to_month-wrapper').show();
        $('from_month-wrapper').show();
        $('start_cal-wrapper').hide();
        $('end_cal-wrapper').hide();
      }
  }

  function onchangeFormat(formElement) {
    form = $('adminreport_form');
		if(formElement.value == 1) {
      $('tip').style.display= 'none';
    }
  }
    
</script>

<h2 class="fleft">
  <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin');?>
</h2> 
  
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>

<div class="tip" id = 'tip' style='display:none;'>
	<span>
		<?php echo $this->translate("No data found in the selected criteria.") ?>
	</span>
</div> 
<br />
<div class="seaocore_settings_form">
	<div class="settings">
		<?php echo $this->reportform->render($this) ?>
	</div>
</div>	