<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        // Enable links
        $$('.classifieds_browse_info_blurb').enableLinks();
    });
</script>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="classifieds_browse">
    <?php foreach( $this->paginator as $item ): ?>
    <li>
        <div class='classifieds_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?>
        </div>
        <div class='classifieds_browse_info'>
            <div class='classifieds_browse_info_title'>
                <h3>
                    <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                    <?php if( $item->closed ): ?>
                    <i class="fa fa-times"></i>
                    <?php endif;?>
                </h3>
            </div>
            <div class='classifieds_browse_info_date'>
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
            </div>
            <div class='classifieds_browse_info_des'>
                <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 120) ?>
            </div>
            <div class='classifieds_browse_info_blurb'>
                <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
                <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php else:?>
<div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted a classified listing yet.');?>
    </span>
</div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true,'query' => $this->formValues)); ?>
