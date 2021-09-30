<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Jung
 */
?>

<?php if( !$this->classified): ?>
<?php echo $this->translate('The classified you are looking for does not exist or has been deleted.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        // Enable links
        $$('.classified_entrylist_entry_body').enableLinks();
    });

    var tagAction = window.tagAction = function(tag){
        $('tag').value = tag;
        $('filter_form').submit();
    }
</script>

<h2>
    <?php echo $this->htmlLink(array('route' => 'classified_general'), "Classifieds", array()); ?>
        <?php echo $this->translate('&#187;'); ?>
    <?php if($category = $this->classified->getCategoryItem()): ?>
        <?php echo $this->htmlLink($category->getHref(),$category->category_name, array()); ?>
        <?php echo $this->translate('&#187;'); ?>
    <?php endif; ?>
    <?php echo $this->classified->getTitle(); ?>
</h2>

<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display:none;'>
<input type="hidden" id="tag" name="tag" value=""/>
</form>

<div class='layout_middle'>
  <h2>
    <?php echo $this->classified->getTitle(); ?>
    <?php if( $this->classified->closed == 1 ): ?>
    <i class="fa fa-times"></i>
    <?php endif;?>
  </h2>
  <ul class='classifieds_entrylist'>
    <li>
      <div class="classified_entrylist_entry_date">
        <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->classified->getParent(), $this->classified->getParent()->getTitle()) ?>
        <?php echo $this->timestamp($this->classified->creation_date) ?>
        <?php if ($this->category):?>- <?php echo $this->translate('Filed in');?>
        <?php echo $this->translate($this->category->category_name); ?>
        <?php endif; ?>
        <?php if (count($this->classifiedTags )):?>
        -
        <?php foreach ($this->classifiedTags as $tag): ?>
        <?php if (!empty($tag->getTag()->text)):?>
        <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php echo $this->fieldValueLoop($this->classified, $this->fieldStructure) ?>

      </div>

      <?php if ($this->classified->closed == 1):?>
      <div class="tip">
          <span>
            <?php echo $this->translate('This classified listing has been closed by the poster.');?>
          </span>
      </div>
      <?php endif; ?>
      <div class="classified_entrylist_entry_body">
        <?php echo Engine_Api::_()->core()->smileyToEmoticons(nl2br($this->classified->body)); ?>
      </div>
      <ul class='classified_thumbs'>
        <?php if($this->main_photo):?>
        <li>
          <div class="classifieds_thumbs_description">
            <?php if( '' != $this->main_photo->getDescription() ): ?>
            <?php echo Engine_Api::_()->core()->smileyToEmoticons($this->main_photo->getDescription()); ?>
            <?php //echo $this->string()->chunk($this->main_photo->getDescription(), 100) ?>
            <?php endif; ?>
          </div>
          <?php echo $this->htmlImage($this->main_photo->getPhotoUrl(), $this->main_photo->getTitle(), array(
          'id' => 'media_photo'
          )); ?>
        </li>
        <?php endif; ?>

        <?php foreach( $this->paginator as $photo ): ?>
        <?php if($this->classified->photo_id != $photo->file_id):?>
        <li>
          <div class="classifieds_thumbs_description">
            <?php if( '' != $photo->getDescription() ): ?>
            <?php echo Engine_Api::_()->core()->smileyToEmoticons($photo->getDescription()); ?>
            <?php //echo $this->string()->chunk($photo->getDescription(), 100) ?>
            <?php endif; ?>
          </div>
          <?php echo $this->htmlImage($photo->getPhotoUrl(), $photo->getTitle(), array(
          'id' => 'media_photo'
          )); ?>
        </li>
        <?php endif; ?>
        <?php endforeach;?>
      </ul>
    </li>
  </ul>

  <div class="classified_stats">
    <?php if( $this->canUpload ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'classified_extended',
    'controller' => 'photo',
    'action' => 'upload',
    'classified_id' => $this->classified->getIdentity(),
    ), $this->translate('Add Photos')) ?>
    &nbsp;|&nbsp;
    <?php endif; ?>
    <?php if( $this->canEdit ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'classified_specific',
    'action' => 'edit',
    'classified_id' => $this->classified->getIdentity(),
    //'format' => 'smoothbox'
    ), $this->translate("Edit")/*, array('class' => 'smoothbox')*/); ?>
    &nbsp;|&nbsp;
    <?php endif; ?>
    <?php if( $this->canDelete ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'classified_specific',
    'action' => 'delete',
    'classified_id' => $this->classified->getIdentity(),
    'format' => 'smoothbox'
    ), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
    &nbsp;|&nbsp;
    <?php endif; ?>
    <?php if( $this->canEdit ): ?>
    <?php if( !$this->classified->closed ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'classified_specific',
    'action' => 'close',
    'classified_id' => $this->classified->getIdentity(),
    'closed' => 1,
    'QUERY' => array(
    'return_url' => $this->url(),
    ),
    ), $this->translate('Close')) ?>
    <?php else: ?>
    <?php echo $this->htmlLink(array(
    'route' => 'classified_specific',
    'action' => 'close',
    'classified_id' => $this->classified->getIdentity(),
    'closed' => 0,
    'QUERY' => array(
    'return_url' => $this->url(),
    ),
    ), $this->translate('Open')) ?>
    <?php endif; ?>
    &nbsp;|&nbsp;
    <?php endif; ?>
    <?php if( $this->viewer()->getIdentity() ): ?>
    <?php echo $this->htmlLink(array(
    'module' => 'activity',
    'controller' => 'index',
    'action' => 'share',
    'route' => 'default',
    'type' => 'classified',
    'id' => $this->classified->getIdentity(),
    'format' => 'smoothbox'
    ), $this->translate("Share"), array('class' => 'smoothbox')); ?>
    &nbsp;|&nbsp;
    <?php echo $this->htmlLink(array(
    'module' => 'core',
    'controller' => 'report',
    'action' => 'create',
    'route' => 'default',
    'subject' => $this->classified->getGuid(),
    'format' => 'smoothbox'
    ), $this->translate("Report"), array('class' => 'smoothbox')); ?>
    &nbsp;|&nbsp;
    <?php endif ?>
    <?php echo $this->translate(array('%s view', '%s views', $this->classified->view_count), $this->locale()->toNumber($this->classified->view_count)) ?>
  </div>
</div>

<script type="text/javascript">
    $$('.core_main_classified').getParent().addClass('active');
</script>
