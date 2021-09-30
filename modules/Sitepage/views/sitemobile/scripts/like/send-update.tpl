<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sendupdate.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(empty($this->sitepage->like_count)): ?>
	<div class="global_form_popup">	
		<div class="tip">
			<span>
				<?php echo $this->translate('No one has liked this Page yet.'); ?>
			</span>
		</div>
		<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close");?></button>
	</div>
	<?php return; ?>
<?php elseif($this->sitepage->like_count == 1 && $this->self_liked == 1):?>
	<div class="global_form_popup">	
		<div class="tip">
			<span>
				<?php echo $this->translate('No one has liked this page other than you.'); ?>
			</span>
		</div>
		<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close");?></button>
	</div>
	<?php return; ?>
<?php endif;?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
//  var maxRecipients = 10;
//  
//  function removeFromToValue(id)
//  {
//    // code to change the values in the hidden field to have updated values
//    // when recipients are removed.
//    var toValues = $('toValues').value;
//    var toValueArray = toValues.split(",");
//    var toValueIndex = "";
//
//    var checkMulti = id.search(/,/);
//
//    // check if we are removing multiple recipients
//    if (checkMulti!=-1){
//      var recipientsArray = id.split(",");
//      for (var i = 0; i < recipientsArray.length; i++){
//        removeToValue(recipientsArray[i], toValueArray);
//      }
//    }
//    else{
//      removeToValue(id, toValueArray);
//    }
//
//    // hide the wrapper for usernames if it is empty
//    if ($('toValues').value==""){
//      $('toValues-wrapper').setStyle('height', '0');
//    }
//
//    $('to').disabled = false;
//  }
//
//  function removeToValue(id, toValueArray){
//    for (var i = 0; i < toValueArray.length; i++){
//      if (toValueArray[i]==id) toValueIndex =i;
//    }
//
//    toValueArray.splice(toValueIndex, 1);
//    $('toValues').value = toValueArray.join();
//  }
//
//  sm4.core.runonce.add(function() {
//      //var tokens = <?php echo $this->friends ?>;
//      new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>', {
//        'minLength': 1,
//        'delay' : 250,
//        'selectMode': 'pick',
//        'autocompleteType': 'message',
//        'multiple': false,
//        'className': 'message-autosuggest',
//        'filterSubset' : true,
//        'tokenFormat' : 'object',
//        'tokenValueKey' : 'label',
//        'injectChoice': function(token){
//          if(token.type == 'user'){
//            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
//            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
//            this.addChoiceEvents(choice).inject(this.choices);
//            choice.store('autocompleteChoice', token);
//          }
//          else {
//            var choice = new Element('li', {'class': 'autocompleter-choices friendlist', 'id':token.label});
//            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
//            this.addChoiceEvents(choice).inject(this.choices);
//            choice.store('autocompleteChoice', token);
//          }
//            
//        },
//        onPush : function(){
//          if( $('toValues').value.split(',').length >= maxRecipients ){
//            $('to').disabled = true;
//          }
//        }
//      });
//
//      <?php if( isset($this->toUser) && $this->toUser->getIdentity() ): ?>
//
//      var toID = <?php echo $this->toUser->getIdentity() ?>;
//      var name = '<?php echo $this->toUser->getTitle() ?>';
//      var myElement = new Element("span");
//      myElement.id = "tospan" + toID;
//      myElement.setAttribute("class", "tag");
//      myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+toID+"\");'>x</a>";
//      $('toValues-element').appendChild(myElement);
//      $('toValues-wrapper').setStyle('height', 'auto');
//      
//      <?php endif; ?>
//
//      <?php if( isset($this->multi)): ?>
//
//      var multi_type = '<?php echo $this->multi; ?>';
//      var toIDs = '<?php echo $this->multi_ids; ?>';
//      var name = '<?php echo $this->multi_name; ?>';
//      var myElement = new Element("span");
//      myElement.id = "tospan_"+name+"_"+toIDs;
//      myElement.setAttribute("class", "tag tag_likes");
//      myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+toIDs+"\");'>x</a>";
//      $('toValues-element').appendChild(myElement);
//      $('toValues-wrapper').setStyle('height', 'auto');
//
//      <?php endif; ?>
//
//    });
//
//
//  sm4.core.runonce.add(function(){
//    new OverText($('to'), {
//      'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
//      'element' : 'label',
//      'positionOptions' : {
//        position: ( sm4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
//        edge: ( sm4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
//        offset: {
//          x: ( sm4.orientation == 'rtl' ? -4 : 4 ),
//          y: 2
//        }
//      }
//    });
//  });
</script>

<?php
//    $this->headScript()
//      ->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/composer.js');
?>

<script type="text/javascript">
//  var composeInstance;
//  sm4.core.runonce.add(function() {
//    var tel = new Element('div', {
//      'id' : 'compose-tray',
//      'styles' : {
//        'display' : 'none'
//      }
//    }).inject($('submit'), 'before');
//
//    var mel = new Element('div', {
//      'id' : 'compose-menu'
//    }).inject($('submit'), 'after');
//    
//    composeInstance = new Composer('body', {
//      overText : false,
//      menuElement : mel,
//      trayElement: tel,
//      baseHref : '<?php echo $this->layout()->staticBaseUrl ?>',
//      hideSubmitOnBlur : false,
//      allowEmptyWithAttachment : false,
//      submitElement: 'submit',
//      type: 'message'
//    });
//  });
</script>
<?php foreach( $this->composePartials as $partial ): ?>
  <?php echo $this->partial($partial[0], $partial[1]) ?>
<?php endforeach; ?>

<div class="global_form_popup">
	<?php echo $this->form->render($this) ?>
</div>
<style type="text/css">
/*.global_form div.form-label {
	width: 50px;
}*/
#messages_compose .compose-content {
	min-height: 4em;
	width: 300px;
}
ul.message-autosuggest{display:none;}
#compose-menu{width:350px;}
</style>

<?php if (@$this->closeSmoothbox): ?>
        <script type="text/javascript">
          TB_close();
        </script>
<?php endif; ?>
