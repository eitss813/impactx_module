<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="blogs_landing_page">
    <ul>
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='blogs_photo'>
          <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.main')) ?>
        </div>
          <div class="info">
            <div class="title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
            <div class="stats">
              <?php if( $this->popularType == 'view' ): ?>
                <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              <?php else /*if( $this->popularType == 'comment' )*/: ?>
                <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
              <?php endif; ?>
            </div>
            <div class="owner">
              <?php
                $owner = $item->getOwner();
                echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
              ?>
            </div>
          <div class="description">
            <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 150) ?>
          </div>
         </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
      <?php echo $this->partial('_widgetLinks.tpl', 'core', array(
        'url' => $this->url(array('action' => 'index'), 'blog_general', true),
        'param' => array('orderby' => 'view_count')
        )); ?>
    <?php endif; ?>
</div>
