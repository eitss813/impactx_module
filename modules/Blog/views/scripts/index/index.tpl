<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="blogs_browse">
    <?php foreach( $this->paginator as $item ): ?>
      <li>
        <div class='blogs_browse_photo'>
          <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.main')) ?>
        </div>
        <div class='blogs_browse_info'>
          <span class='blogs_browse_info_title'>
            <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
          </span>
          <p class='blogs_browse_info_date'>
            <?php echo $this->translate('Posted');?>
            <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            <?php echo $this->translate('by');?>
            <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
          </p>
          <p class='blogs_browse_info_blurb'>
            <?php $readMore = ' ' . $this->translate('Read More') . '...';?>
            <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 110, $this->htmlLink($item->getHref(), $readMore) ) ?>
          </p>
          <!-- <div class="stat_info">
            <?php if( $item->comment_count > 0 ) :?>
              <span>
                <i class="fa fa-comment"></i> <?php echo $this->translate(array('%s', '%s', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
              </span>
            <?php endif; ?>
            <?php if( $item->like_count > 0 ) :?>
              <span>
                <i class="fa fa-thumbs-up"></i> <?php echo $this->translate(array('%s', '%s', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
              </span>
            <?php endif; ?>
            <?php if( $item->view_count > 0 ) :?>
              <span class="views_blog">
                <i class="fa fa-eye"></i> <?php echo $this->translate(array('%s', '%s', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              </span>
            <?php endif; ?>
          </div> -->
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

<?php elseif( $this->category || $this->show == 2 || $this->search ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry with that criteria.');?>
      <?php if (TRUE): // @todo check if user is allowed to create a poll ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry yet.'); ?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  //'params' => $this->formValues,
)); ?>
