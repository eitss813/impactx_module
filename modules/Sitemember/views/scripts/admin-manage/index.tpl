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
<h2><?php echo $this->translate("Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin") ?></h2>
<?php $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitemember'); ?>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate("Manage Members") ?></h3>
<p>
  <?php echo $this->translate("The members of your social network are listed here. If you need to search for a specific member, enter your search criteria in the fields below. Here, you can also make members featured / un-featured, sponsored / un-sponsored.") ?>
</p>
<br />
<?php
$URL = $this->url(array('module' => 'user', 'controller' => 'manage'), 'admin_default', true);
echo $this->translate('<div class="tip"><span>Please <a href="%s" target="_blank"> Click here</a>, If you want to edit, delete or login to a member’s account.</span></div>', $URL);
?>
<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction) {
    // Just change direction
    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>

<div class='admin_search sm_admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<br />

<?php if (count($this->paginator) > 0): ?>
  <div class='admin_results'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s member found.", "%s members found.", $count), $this->locale()->toNumber($count))
      ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues)); ?>
    </div>
  </div>
  <br />

  <div class="admin_table_form">
    <table class='admin_table'>
      <thead>
        <tr>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
          <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('level_id', 'ASC');"><?php echo $this->translate("User Level") ?></a></th>
          <!--<th style='width: 1%;' class='admin_table_centered'><?php //echo $this->translate("Status")   ?></th>-->
          <th style='width: 1%;' class='admin_table_centered' title="<?php echo $this->translate('Overall Rating'); ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('rating_avg', 'ASC');"><?php echo $this->translate('Overall Rating'); ?></a></th>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') != 3):?>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
              <th style='width: 1%;' class='admin_table_centered' title='<?php echo $this->translate('Recommendation'); ?>'><?php echo $this->translate("R") ?></th>
            <?php endif; ?>
          <?php endif; ?>   
          <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');" title="<?php echo $this->translate('Views'); ?>" ><?php echo $this->translate('V'); ?></a></th>
          <th style='width: 1%;' class='admin_table_centered' title='<?php echo $this->translate('Likes'); ?>'><?php echo $this->translate("L") ?></th>
          <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');" title="<?php echo $this->translate('Featured'); ?>" ><?php echo $this->translate('F'); ?></a></th>
          <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'DESC');" title="<?php echo $this->translate('Sponsored'); ?>" ><?php echo $this->translate('S'); ?></a></th>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Signup Date") ?></a></th>
          <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Option") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($this->paginator)): ?>
          <?php
          foreach ($this->paginator as $item):
            $user = $this->item('user', $item->user_id);
            ?>
            <tr>
              <td><?php echo $item->user_id ?></td>
              <td class='admin_table_bold'>
                <?php
                echo $this->htmlLink($user->getHref(), $this->string()->truncate($user->getTitle(), 10), array('target' => '_blank'))
                ?>
              </td>
              <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
              <td class='admin_table_email'>
                <?php echo $item->email ?>
              </td>
              <td class="admin_table_centered nowrap">
                <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
              </td>
              <td>
                <div>
                  <span title="<?php echo $item->rating_avg . $this->translate(' rating '); ?>">
                    <?php echo $this->showRatingStarMember($item->rating_avg, 'user', 'big-star'); ?>
                  </span>
                </div>
              </td>
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.recommend', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings')): ?>
                <td class='admin_table_centered'>
                  <?php
                  $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'user', 'resource_id' => $item->user_id));
                  $totalRecommend = $recommendpaginator->getTotalItemCount();
                  ?>
                  <?php echo $totalRecommend ?>
                </td>
              <?php endif; ?>

              <td class='admin_table_centered'>
                <?php echo $item->view_count; ?>
              </td>
              <td class='admin_table_centered'>
                <?php $likeCount = Engine_Api::_()->getApi('like', 'seaocore')->likeCount('user', $item->user_id); ?>
                <?php echo $likeCount; ?>
              </td>
              <?php if (!empty($item->featured)): ?>
                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'manage', 'action' => 'featured', 'id' => $item->user_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/sitemember_goldmedal1.gif', '', array('title' => $this->translate('Make Unfeatured')))) ?></td>
              <?php else: ?>
                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'manage', 'action' => 'featured', 'id' => $item->user_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/sitemember_goldmedal0.gif', '', array('title' => $this->translate('Make Featured')))) ?></td>
              <?php endif; ?>
              <?php if (!empty($item->sponsored)): ?>
                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'manage', 'action' => 'sponsored', 'id' => $item->user_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/sponsored.png', '', array('title' => $this->translate('Make Unsponsored')))) ?></td>
              <?php else: ?>
                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemember', 'controller' => 'manage', 'action' => 'sponsored', 'id' => $item->user_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/unsponsored.png', '', array('title' => $this->translate('Make Sponsored')))) ?></td>
              <?php endif; ?>
              <td class="nowrap">
                <?php echo $this->locale()->toDate($item->creation_date) ?>
              </td>
              <td class='admin_table_options'>
                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'stats', 'id' => $item->user_id)); ?>'>
                  <?php echo $this->translate("Stats") ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="tip"><span>
      <?php echo $this->translate("No members were found.") ?></span>
  </div>
<?php endif; ?>