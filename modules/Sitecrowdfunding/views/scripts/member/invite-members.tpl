<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invite-members.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepagemember/externals/styles/style_sitepagemember.css'); ?>
<?php
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">

	window.addEvent('domready', function() {
		$('toValues-wrapper').style.display='none';
    $('user_ids-label').style.display='none';
	});

  // Populate data
  var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>
 
  function removeFromToValue(id) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);
    if(toValueArray.length == 1) {
      $('toValues-wrapper').style.display='none';
    }
    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('height', '0');
    }

    $('user_ids').disabled = false;
  }
 
  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
   // if( !isPopulated ) { 
      new Autocompleter.Request.JSON('user_ids', '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'member', 'action' => 'get-members', 'project_id' => $this->project_id), 'default', true) ?>', {
      'postVar' : 'user_ids',
      'postData' : false,
			'minLength': 1,
			'delay' : 250,
			'selectMode': 'pick',
			'element': 'toValues',
			'autocompleteType': 'message',
			'multiple': false,
			'className': 'seaocore-autosuggest tag-autosuggest',
			'filterSubset' : true,
			'tokenFormat' : 'object',
			'tokenValueKey' : 'label',
        'injectChoice': function(token){
           console.log('token',token.email)
          //if(token.type == 'sitepage'){
            var choice = new Element('li', {
              'class': 'autocompleter-choices',
              'html': token.photo,
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
			new Element('div', {
				'html': this.markQueryValue(token.email),
				'class': 'autocompleter-choice'
			}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
            
        },
         onCommand : function(e) {
					//this.options.postData = { 'couponId' : $('coupon_id').value };
         },
  
        onPush : function(){
          if ($('toValues-wrapper')) {
						$('toValues-wrapper').style.display='block';
					}
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('user_ids').disabled = true;
          }
        }
      });
      new Composer.OverText($('user_ids'), {
        'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
        'element' : 'label',
        'isPlainText' : true,
        'positionOptions' : {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
  });
	en4.core.runonce.add(function(){
		//update email value
		$$('#user_ids').addEvent('change', function(event) {

			var user = document.getElementById('user_ids').value;
			var flag1 = user.includes("@");
			var flag2 = user.includes(".");
			if(flag1 && flag2 && user) {
				document.getElementById('toValues').value=user;
			}
		});
	});
	function overall_submit(){


		$(function() {
			 $('toValues').value=document.getElementById('toValues').value;
			 console.log('toValues',document.getElementById('toValues').value);
			// $('messages_compose').submit();
		});
       //$("tags").value = document.getElementById('toValues').value;
		//$("messages_compose").submit();
	}


</script>
<div class="global_form_popup sitepage_add_members_popup" >
	<div class="align-center">
		<?php echo $this->form->render($this) ?>
	</div>
	<!-- <div class="align-center">
		<h2>ORR</h2>
	</div>
	<div class="align-center">
		<?php echo $this->externalform->render($this) ?>
	</div>
	 <button name="overall_submit" id="overall_submit" type="submit" onclick="overall_submit()">Submit</button> -->
</div>
<style>
	.align-center{
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 10px;
		margin-bottom: 10px;
	}
	div#role_id-wrapper {
		display: none;
	}
	.global_form_popup.sitepage_add_members_popup{
		min-height: 250px;
		width: unset !important;
		height: 250px;
		overflow: auto;
	}
</style>