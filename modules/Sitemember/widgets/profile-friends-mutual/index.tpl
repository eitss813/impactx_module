<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css')
?>

<?php $totalCount = $this->paginator->getTotalItemCount(); ?>
<?php if ($this->show == 'friends') : ?>
  <h3>
    <?php
    echo '<a class="smoothbox" href="' . $this->url(array('module' => 'sitemember', 'controller' => 'index', 'action' => 'view-more', 'show' => $this->show, 'user_id' => $this->subject->user_id), 'default', true) . '">' . $this->translate(array('%s Friend', '%s Friends', $totalCount), $this->locale()->toNumber($totalCount)) . '</a>';
    ?>
  </h3>
<?php else: ?>
  <h3>
    <?php
    echo '<a class="smoothbox" href="' . $this->url(array('module' => 'sitemember', 'controller' => 'index', 'action' => 'view-more', 'show' => $this->show, 'user_id' => $this->subject->user_id), 'default', true) . '">' . $this->translate(array('%s Mutual Friend', '%s Mutual Friends', $totalCount), $this->locale()->toNumber($totalCount)) . '</a>';
    ?>
  </h3>
<?php endif; ?>
<ul class="seaocore_sidebar_list <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">
  <?php foreach ($this->paginator as $user): ?>
    <?php if ($this->show == 'friends') : ?>
      <?php
      if (!isset($this->friendUsers[$user->resource_id]))
        continue;
      $user = $this->friendUsers[$user->resource_id];
      ?>
    <?php else: ?>
      <?php $user = Engine_Api::_()->getItem('user', $user['user_id']); ?>
    <?php endif; ?>

    <li>
      <div>
        <a href="<?php echo $user->getHref() ?>" class ="sitemember_thumb">
          <?php
          $url = $user->getPhotoUrl('thumb.profile');
          if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
          endif;
          ?>
          <span style="background-image: url(<?php echo $url; ?>); height:<?php echo $this->photoHeight ?>px; width:<?php echo $this->photoWidth ?>px;"></span>
        </a>
        <?php if ($this->titlePosition): ?>
          <div>
            <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle())) ?>
          </div>
        <?php endif; ?>
      </div>
      <?php if (!$this->titlePosition): ?>
        <div class="sitemember_title_outside" style="width:<?php echo $this->photoWidth ?>px">
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title' => $user->getTitle())); ?>
        </div>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
  <div class="fright o_hidden clr mtop5">
    <?php if ($this->show == 'friends'): ?>
      <?php if ($this->paginator->getTotalItemCount() == 1): ?>
        <?php $more_link = $this->translate(array('%s Friend', '%s Friends', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount()); ?>
      <?php else: ?>
        <?php $more_link = $this->translate('All %s Friends', $this->paginator->getTotalItemCount()); ?>
      <?php endif; ?>
    <?php elseif ($this->show == 'mutualfriends') : ?>
      <?php if ($this->paginator->getTotalItemCount() == 1): ?>
        <?php $more_link = $this->translate(array('%s Mutual Friend', '%s Mutual Friends', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount()); ?>
      <?php else: ?>
        <?php $more_link = $this->translate('All %s Mutual Friends', $this->paginator->getTotalItemCount()); ?>
      <?php endif; ?>
    <?php endif; ?>
    <a class="sm_up_overall_rating_more_link" href="javascript:void(0);" onclick='showMemberFriendsTab("<?php echo $this->show; ?>");
        return false;'><?php echo $more_link; ?> &nbsp;&raquo;
    </a>
  </div>
</ul>


<script type="text/javascript">

      function showMemberFriendsTab(show) {

        if ($('main_tabs')) {
          tabContainerSwitch($('main_tabs').getElement('.tab_' + '<?php echo $this->contentDetails->content_id ?>'));
        }

        if (show == 'friends') {
<?php
if (!empty($this->contentDetails)) {
  $this->contentDetails->params = array_merge($this->contentDetails->params, array('mutual' => 0));
}
?>

          var params = {
            requestParams:<?php echo json_encode($this->contentDetails->params) ?>,
            responseContainer: $$('.layout_sitemember_profile_friends_sitemember')
          }
          params.requestParams.content_id = '<?php echo $this->contentDetails->content_id ?>';
          en4.sitemember.ajaxTab.sendReq(params);

          if ($('main_tabs')) {
            location.hash = 'main_tabs';
          }
        } else if (show == 'mutualfriends') {

<?php
if (!empty($this->contentDetails)) {
  $this->contentDetails->params = array_merge($this->contentDetails->params, array('mutual' => 1));
}
?>
          var paramss = {
            requestParams:<?php echo json_encode($this->contentDetails->params) ?>,
            responseContainer: $$('.layout_sitemember_profile_friends_sitemember')
          }
          paramss.requestParams.content_id = '<?php echo $this->contentDetails->content_id ?>';
          en4.sitemember.ajaxTab.sendReq(paramss);

          if ($('main_tabs')) {
            location.hash = 'main_tabs';
          }
        }

      }

</script>