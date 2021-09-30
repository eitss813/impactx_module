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
<?php include APPLICATION_PATH .  '/application/modules/Sesmultipleform/views/scripts/dismiss_message.tpl';?>
<h2><?php echo $this->translate("Manage Key Contacts") ?></h2>
<p><?php echo $this->translate('Here, you can choose the members of your website to be shown as Key Contact Persons. These Key Contacts will be the primary contacts between your website and your users. You can add new Key Contacts by using the "Add New Key Contact" link below.<br />
Key Contacts chosen from here will be displayed in the "Key Contacts" widget placed on the desired widgetized page from the Layout Editor.'); ?></p><br />
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
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'contacts', 'action' => 'create-contact'), $this->translate("Add New Key Contact"), array('class'=>'smoothbox sesbasic_icon_add buttonlink')) ?>
</div>
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s key contact found.', '%s key contacts found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div class="sesbasic_manage_table">
   <div class="sesbasic_manage_table_head">
        <div style="width:5%"><input onclick='selectAll();' type='checkbox' class='checkbox' /></div>
        <div style="width:5%">ID</div>
        <div style="width:30%"><?php echo $this->translate("Name") ?></div>
         <div style="width:20%"><?php echo $this->translate("Designation") ?></div>
        <div style="width:20%" class="admin_table_centered"><?php echo $this->translate("Active") ?></div>      
        <div style="width:20%"><?php echo $this->translate("Options") ?></div>
    </div>
    <ul  class="sesbasic_manage_table_list" id='menu_list'>
      <?php foreach ($this->paginator as $item): ?>

        <li class="item_label" id="form_<?php echo $item->keycontact_id ?>">
        <input type='hidden'  name='order[]' value='<?php echo $item->keycontact_id; ?>'>
           <div style="width:5%"><input type='checkbox' class='checkbox' name='delete_<?php echo $item->keycontact_id;?>' value='<?php echo $item->keycontact_id ?>' /></div>
          <div style="width:5%"><?php echo $item->keycontact_id ?></div>
           <?php  $user = Engine_Api::_()->getItem('user', $item->user_id); ?>
          <div style="width:30%"><?php echo $this->htmlLink($user->getHref(), $user->getTitle(),array('title' => $user->getTitle()))
          ?></div>      
          <div style="width:20%"><?php echo $item->designation; ?></div>  
          <?php $active = $item->active; ?>         
            <div style="width:20%" class="admin_table_centered">
          <?php echo $item->active == 1 ? $this->htmlLink(
                  array('route' => 'default', 'module' => 'sesmultipleform', 'controller' => 'admin-contacts', 'action' => 'active', 'id' => $item->keycontact_id,'active' =>0),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Inactive')))) : $this->htmlLink(
                  array('route' => 'default', 'module' => 'sesmultipleform', 'controller' => 'admin-contacts', 'action' => 'active', 'id' => $item->keycontact_id,'active' =>1),$this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Active'))))  ; ?>
           </div>
            
          <div style="width:20%">
          	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'contacts', 'action' => 'edit-contact', 'id' => $item->keycontact_id), $this->translate("Edit"),array('class' => 'smoothbox')) ?>
            |    
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'contacts', 'action' => 'delete-contact', 'id' => $item->keycontact_id),
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
      <?php echo $this->translate("No members have been added as Key Contacts yet.") ?>
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