<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: entry.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<script type="text/javascript">
  function showSubCategory(cat_id) {
    var url = en4.core.baseUrl + 'sesmultipleform/index/subcategory/category_id/' + cat_id;
    en4.core.request.send(new Request.HTML({
      url: url,
      data: {
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('subcat_id') && responseHTML) {
          if ($('subcat_id-wrapper')) {
            $('subcat_id-wrapper').style.display = "block";
          }
          $('subcat_id').innerHTML = responseHTML;
        } else {
          if ($('subcat_id-wrapper')) {
            $('subcat_id-wrapper').style.display = "none";
            $('subcat_id').innerHTML = '';
          }
        }
      }
    }));
  }
function showSubSubCategory(cat_id) {

    var url = en4.core.baseUrl + 'sesmultipleform/index/subsubcategory/subcategory_id/' + cat_id;

    en4.core.request.send(new Request.HTML({
      url: url,
      data: {
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('subsubcat_id') && responseHTML) {
          if ($('subsubcat_id-wrapper')) {
            $('subsubcat_id-wrapper').style.display = "block";
          }
          $('subsubcat_id').innerHTML = responseHTML;
        } else {
          if ($('subsubcat_id-wrapper')) {
            $('subsubcat_id-wrapper').style.display = "none";
            $('subsubcat_id').innerHTML = '';
          }
        }
      }
    }));
  }
  window.addEvent('domready', function() {
    if ($('category_id') && $('category_id').value == 0)
     $('subcat_id-wrapper').style.display = "none";   
		if ($('subcat_id') && $('subcat_id').value == 0)
     $('subsubcat_id-wrapper').style.display = "none"; 
  });



  function multiDelete() {
    return confirm("<?php echo $this->translate('Are you sure you want to delete the selected entries?');?>");
  }

  function selectAll() {
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
<?php include APPLICATION_PATH .  '/application/modules/Sesmultipleform/views/scripts/dismiss_message.tpl';?>
<h3><?php echo $this->translate("Manage Entries of %s Form",$this->formObj->title) ?></h3>
<p>
  <?php echo $this->translate("") ?>
</p>
<br />
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'index'), $this->translate("Back to Manage Forms"), array('class'=>'sesbasic_icon_back buttonlink')) ?>
</div>
<div class="admin_search sesbasic_search_form">
 <div class='admin_search sesmultipleform_admin_search'>
  <?php echo $this->formFilter->render($this) ?>
 </div>
</div>
<br />
<?php $counter = $this->paginator->getTotalItemCount(); ?> 
<?php if( count($this->paginator) ): ?>
  <div class="fleft">
    <b class="bold"><?php echo $this->translate(array('%s entries found.', '%s entries found.', $counter), $this->locale()->toNumber($counter)) ?></b>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'download-csv','form_id'=>$this->formObj->getIdentity()), 'Download Entries in CSV file', array('class' => 'buttonlink sesmultipleform_icon_download')); ?>
  </div>
  <br />
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
    <div class="admin_table_form">
      <table class='admin_table'>
        <thead>
          <tr>
            <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
            <th class='admin_table_short'>ID</th>
            <th><?php echo $this->translate("Name") ?></th>
            <th><?php echo $this->translate("Email") ?></th>
            <th><?php echo $this->translate("Category") ?></th>
            <th><?php echo $this->translate("Message") ?></th>
            <th><?php echo $this->translate("Date") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->paginator as $item): ?>
          <?php $category = Engine_Api::_()->getItem('sesmultipleform_category', $item->category_id);  ?>
          <?php $user = Engine_Api::_()->getItem('user', $item->owner_id);  ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->entry_id;?>' value="<?php echo $item->entry_id; ?>" /></td>
            <td><?php echo $item->entry_id ?></td>
            <td><a href="<?php echo  $user->getHref(); ?>" <?php if($user){ ?> target="_blank" <?php } ?>><?php echo $item->first_name ?></a></td>
            <td><a href='mailto:<?php echo $item->email ?>' target="_blank"><?php echo $item->email ?></a></td>
             <?php if(!empty($category) && !empty($item->category_id)): ?>
            <td><?php echo $category->title ?></td>
            <?php else: ?>
            <td><?php echo "-----" ?></td>
            <?php endif; ?>

            <td><?php  if($item->description){ echo Engine_String::strlen(strip_tags($item->description)) > 30 ? Engine_String::substr(strip_tags($item->description), 0, 27) . '...' : strip_tags($item->description); }else{ echo "--- "; } ?></td>
            <td><?php echo $this->translate($item->creation_date) ?></td>
            <td>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'view', 'id' => $item->entry_id), $this->translate("View"), array('class' => 'smoothbox')) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'reply', 'id' => $item->entry_id), $this->translate("Reply"), array('class' => 'smoothbox')) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'delete-entry', 'id' => $item->entry_id),            $this->translate("Delete"), array('class' => 'smoothbox')) ?>
              <?php if($item->file_id): ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'download-attached-file', 'file_id' => $item->file_id), 'Download', array('class' => 'buttonlink sesmultipleform_icon_download sesmultipleform_entries_option_link')); ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class='buttons'>
      <br />
      <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>
  <br/>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no entries yet.") ?>
    </span>
  </div>
<?php endif; ?>
