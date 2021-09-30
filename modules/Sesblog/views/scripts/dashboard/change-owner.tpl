<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2017-2018 SocialEngineSolutions
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: change-owner.tpl  2018-04-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $base_url = $this->layout()->staticBaseUrl;?>
<?php $this->headScript()
->appendFile($base_url . 'externals/autocompleter/Observer.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Request.js');?>


<?php if(!$this->is_ajax) {
  echo $this->partial('dashboard/left-bar.tpl', 'sesblog', array('blog' => $this->blog));	
?>
<div class="sesbasic_dashboard_content sesbm sesbasic_clearfix">
<?php }  ?>

<h2><?php echo $this->translate('Transfer Ownership') ?></h2>
<p>
  <?php echo $this->translate('Here you can transfer the ownership of this Blog to some other site member. [Note: You cannot revert this action, so we suggest you to take this action only if you are pretty much sure about this.]') ?>
</p>
<br />
<form id="sesblog_change_owner" action="<?php echo $this->url(array('blog_id' => $this->blog->custom_url, 'action'=>'change-owner'), 'sesblog_dashboard', true); ?>" method="post">
  <input type="hidden" value="" id="user_id" name="user_id" />
  <input type="text" name="search_text" id="search_text" value="" placeholder="<?php echo $this->translate("Select a new owner"); ?>" />
  <button type="submit"><?php echo $this->translate("Change"); ?></button>
</form>
    <?php if(!$this->is_ajax){ ?>
  </div>
</div>
</div>
<?php  } ?>

<script type='text/javascript'>
 var Searchurl = "<?php echo $this->url(array('blog_id' => $this->blog->custom_url,'action'=>'search-member'), 'sesblog_dashboard', true); ?>";
  en4.core.runonce.add(function() {
    var contentAutocomplete = new Autocompleter.Request.JSON('search_text', Searchurl, {
      'postVar': 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'className': 'sesbasic-autosuggest',
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
      }
    });
    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      var to =  selected.retrieve('autocompleteChoice');
      sesJqueryObject('#user_id').val(to.user_id);
    });
  });
</script>
