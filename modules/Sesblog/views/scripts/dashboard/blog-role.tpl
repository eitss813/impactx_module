<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: blog-role.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?> 
<?php if(!$this->is_ajax):?> 
	<?php $base_url = $this->layout()->staticBaseUrl;?>
	<?php $this->headScript()
	->appendFile($base_url . 'externals/autocompleter/Observer.js')
	->appendFile($base_url . 'externals/autocompleter/Autocompleter.js')
	->appendFile($base_url . 'externals/autocompleter/Autocompleter.Local.js')
	->appendFile($base_url . 'externals/autocompleter/Autocompleter.Request.js');
	?>
  <?php echo $this->partial('dashboard/left-bar.tpl', 'sesblog', array('blog' => $this->blog));	?>
<div class="sesbasic_dashboard_content sesblog_manage_role_form sesbm sesbasic_clearfix">
	<div class="sesblog_manage_role_form_top sesbasic_clearfix">
		<p class="heading_desc"><?php echo $this->translate('Below, you can add admins to your blog who all will be able to do anything on your blog as you do including editing, creating sub blog, etc.');?></p>
		<?php endif; ?>
		<form id="blog_admin_form" action="<?php echo $this->url(array('action' => 'save-blog-admin', 'blog_id' => $this->blog->blog_id), 'sesblog_dashboard', true) ?>" method="post">
			<div id="manage_admin_input">
				<div class="sesblog_manage_roles_item">
					<span class="show_img" id="show_default_img"></span>
          <div class="_input">
            <input type='text' id="blog_admin" name='blog_admin' size='20' placeholder='<?php echo $this->translate('Type Member Name') ?>' />
            <input type="hidden" id="user_id" name="blog_admins[]" value=""/>
					</div>
        </div>
			</div>
			<a href="javascript:void(0);" onclick="addMore();"><i class="fa fa-plus"></i>&nbsp;<?php echo $this->translate('Add Another Member');?></a>
			<button onclick="saveForm();return false;" id="save_button_admin" disabled><?php echo $this->translate("Add Member"); ?></button>
	  </form>
<?php if(!$this->is_ajax){ ?>
  </div>
	<div class="sesblog_footer_contant">
		<b><?php echo $this->translate('Admins');?></b>
		<p><?php echo $this->translate('Here, you can see a list of admins added by you for this Blog.');?></p>
		<div id="manage_admin">
			<?php foreach($this->paginator as $blogAdmin):?>
				<div class="admin_manage" id="admin_manage_<?php echo $blogAdmin->role_id;?>">
					<?php $user = Engine_Api::_()->getItem('user', $blogAdmin->user_id);?>
					<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle())) ?>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
          <?php if($blogAdmin->user_id != $this->blog->owner_id):?>
						<a class="remove_blog" href="javascript:void(0);" onclick="removeUser('<?php echo $blogAdmin->blog_id;?>','<?php echo $blogAdmin->role_id;?>');"><i class="fa fa-times"></i></a>
          <?php endif;?>
					
          <br />
				</div>
			<?php endforeach;?>
		</div>
	</div>
</div>
</div>
<?php  } ?>
<?php if($this->is_ajax) die; ?>
<script type="text/javascript">
	function showAutosuggest(blogAdmin, imageId) {
	  var contentAutocomplete1 =  'contentAutocomplete-'+blogAdmin
		contentAutocomplete1 = new Autocompleter.Request.JSON(blogAdmin, "<?php echo $this->url(array('module' => 'sesblog', 'controller' => 'dashboard', 'action' => 'get-members', 'blog_id' => $this->blog->blog_id), 'default', true) ?>", {
			'postVar': 'text',
			'minLength': 1,
			'selectMode': '',
			'autocompleteType': 'tag',
			'customChoices': true,
			'filterSubset': true,
			'maxChoices': 20,
			'cache': false,
			'multiple': false,
			'className': 'sesbasic-autosuggest',
			'indicatorClass':'input_loading',
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
		contentAutocomplete1.addEvent('onSelection', function(element, selected, value, input) {
			if($('user_id').value != '')
			 $('user_id').value = $('user_id').value+','+selected.retrieve('autocompleteChoice').id;
			else
			 $('user_id').value = selected.retrieve('autocompleteChoice').id;
      $(imageId).innerHTML = selected.retrieve('autocompleteChoice').photo;
			sesJqueryObject('#'+blogAdmin).attr('rel', selected.retrieve('autocompleteChoice').id);
			sesJqueryObject('#save_button_admin').removeAttr('disabled');
		});
	}
	en4.core.runonce.add(function() {
	  showAutosuggest('blog_admin','show_default_img');
	});
	
	function saveForm() {
	  var UserIds = $('user_id').value;
		new Request.HTML({
			url : en4.core.baseUrl + 'sesblog/dashboard/save-blog-admin/blog_id/' + <?php echo $this->blog->blog_id ?>,
			method: 'post',
			data : {
				format : 'html',
				data: UserIds,
				is_ajax: 1,
			},
			onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				$('manage_admin').innerHTML = responseHTML;
			}
		}).send()
	}
	
	sesJqueryObject(document).on('keyup', 'input[id^="blog_admin"]', function(event) {
    var value = sesJqueryObject(this);
		if(!value.val()){
			var id = value.attr('rel');
			if(typeof id == 'undefined')
				return false;
			var str = $('user_id').value;
			var res = str.replace(id, "");
			sesJqueryObject('#user_id').val(res);
			if(res == '' || res == ',')
				sesJqueryObject('#save_button_admin').attr('disabled', true);
			value.parent().find('.show_img').html('');				
		}
	});
	
	var count = 1;
	function addMore() {
		var itemCount = sesJqueryObject('#manage_admin_input').children().length - 1;
		var currentElem = sesJqueryObject('#manage_admin_input').children().eq(itemCount).find('input').first().val();
		if(!currentElem || !sesJqueryObject('#manage_admin_input').children().eq(itemCount).find('input').first().attr('rel'))
			return false;
	  var ColumnId = 'blog_admin_'+count;
	  sesJqueryObject('#manage_admin_input').append('<div class="sesblog_manage_roles_item"><span class="show_img" id="show_default_img_'+count+'"'+'></span> <input type="text" placeholder="Type Member Name" size="20" name="'+ColumnId+'"' +'id="'+ColumnId+'"'+'autocomplete="off" rel="'+count+'"><a class="remove_icon" href="javascript:void(0);" onclick="removeInputForm('+"'"+ColumnId+"'"+');"><i class="fa fa-times" id="close_option_'+count+'"'+'></i></a></div>');
	  showAutosuggest('blog_admin_'+count, 'show_default_img_'+count);
	  count = count+1;
	}
	
  function removeInputForm(id) {
    var explodedstr = id.split("_"); 
    var countNumber = explodedstr['2'];
    var str = $('user_id').value;
    var res = str.replace(sesJqueryObject('#'+id).attr('rel'), "");
		var itemS = sesJqueryObject('#show_default_img_'+countNumber);
		itemS.parent().remove();
    sesJqueryObject('#user_id').val(res);
    if(res == '' || res == ',') {
			sesJqueryObject('#save_button_admin').attr('disabled', true)
    }
  }
  
  function removeUser(blogId, roleId) {
		new Request.JSON({
			url : en4.core.baseUrl + 'sesblog/dashboard/delete-blog-admin',
			method: 'post',
			data : {
				format : 'json',
				role_id: roleId,
				blog_id: blogId,
				is_ajax: 1,
			},
			onSuccess: function(responseJSON) {
				sesJqueryObject('#admin_manage_'+roleId).remove();
			}
		}).send()
  }
</script>
