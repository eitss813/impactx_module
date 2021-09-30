<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<?php
$form_id = $this->id;
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesmultipleform/views/scripts/dismiss_message.tpl';?>
<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected forms ?") ?>");
}
function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>
<h3><?php echo $this->translate("Manage Multiple Forms"); ?></h3>
<p><?php echo $this->translate("This page lists all of the Forms you have created using this plugin. <br />
You can use this page to manage these forms, manage their categories, form entries and confirmation message to be send to your users when they fill up the form."); ?>	
</p>
<br class="clear" />
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'create-form'), $this->translate("Add New Form"), array('class'=>'smoothbox sesbasic_icon_add buttonlink')) ?>
</div>
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s form found.', '%s forms found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  	<div class="sesbasic_manage_table">
    	<div class="sesbasic_manage_table_head" style="width:100%;">
        <div style="width:5%"><input onclick='selectAll();' type='checkbox' class='checkbox' /></div>
        <div style="width:5%">ID</div>
        <div style="width:20%"><?php echo $this->translate("Form Title") ?></div>
        <div style="width:10%" class="admin_table_centered"><?php echo $this->translate("Total Entries") ?></div>
        <div style="width:15%"><?php echo $this->translate("Creation Date") ?></div>
        <div style="width:10%" class="admin_table_centered"><?php echo $this->translate("Status") ?></div>      
        <div style="width:35%"><?php echo $this->translate("Options") ?></div>
      </div>
      <ul  class="sesbasic_manage_table_list" id='menu_list' style="width:100%;">
        <?php foreach ($this->paginator as $item): ?>
          <li class="item_label" id="form_<?php echo $item->form_id ?>">
          <input type='hidden'  name='order[]' value='<?php echo $item->form_id; ?>'>
             <div style="width:5%"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->form_id;?>' value='<?php echo $item->form_id ?>' /></div>
            <div style="width:5%"><?php echo $item->form_id ?></div>
            <div style="width:20%"><?php echo $item->title; ?></div>  
            <div style="width:10%" class="admin_table_centered"><?php echo Engine_Api::_()->getDbtable('entries', 'sesmultipleform')->totalEntries(array('form_id' => $item->form_id)); ?></div>  
            <div style="width:15%"><?php echo $item->creation_date; ?></div>  
            <?php $active = $item->active; ?>
              <div style="width:10%" class="admin_table_centered">
            <?php echo $item->active == 1 ? $this->htmlLink(
                    array('route' => 'default', 'module' => 'sesmultipleform', 'controller' => 'admin-forms', 'action' => 'active', 'id' => $item->form_id,'active' =>0),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Disable')))) : $this->htmlLink(
                    array('route' => 'default', 'module' => 'sesmultipleform', 'controller' => 'admin-forms', 'action' => 'active', 'id' => $item->form_id,'active' =>1),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Enable'))))  ; ?>
             </div>
            <div style="width:35%">
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'settings', 'action' => 'advance-setting', 'id' => $item->form_id), $this->translate("Edit"), array()) ?>
            |
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'index', 'id' => $item->form_id), $this->translate("Manage Categories"), array()) ?>
             |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'entry', 'id' => $item->form_id), $this->translate("Manage Entries"), array()) ?>                
              <br />
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'email-confirmation', 'id' => $item->form_id), $this->translate("Send Confirmation Email"), array()) ?>
              |
              <?php echo $this->htmlLink(
                  array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'delete-form', 'id' => $item->form_id),
                  $this->translate("Delete"),
                  array('class' => 'smoothbox')) ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  	<div class='buttons'>
    <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  </form>
  <br />
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no form by you.") ?>
    </span>
  </div>
<?php endif; ?>
<script type="text/javascript"> 
  
  var SortablesInstance;

  window.addEvent('load', function() {

    SortablesInstance = new Sortables('menu_list', {
      clone: true,
      constrain: false,
      handle: '.item_label',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

 var reorder = function(e) {
     var menuitems = e.parentNode.childNodes;
     var ordering = {};
     var i = 1;
     var totallength = menuitems.length;
     for (var menuitem in menuitems)
     {
       var child_id = menuitems[menuitem].id;
       

       if ((child_id != undefined))
       { 
         ordering[child_id] = totallength;
         totallength--;
       }
     }
 
    ordering['format'] = 'json';

    //Send request
    var url = '<?php echo $this->url(array("action" => "order")) ?>';
    var request = new Request.JSON({
      'url' : url,
      'method' : 'POST',
      'data' : ordering,
      onSuccess : function(responseJSON) {
      }
    });
    request.send();
  }
</script>