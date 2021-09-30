<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _browseUsersSM.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
<?php if(!$this->autoContentLoad) : ?>
  <?php if (!empty($this->totalResults)): ?>
    <div class="ui-member-list-head">
      <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
    </div>
  <?php endif; ?>
  <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
  <?php $viewer_id = $this->viewer->getIdentity(); ?>
  <div class="sm-content-list">
    <ul id="browsesitemembers_ul" class="ui-member-list" data-role="listview" data-icon="none">
    <?php endif; ?>

    <?php foreach ($this->paginator as $user): ?>
      <?php
      $table = Engine_Api::_()->getDbtable('block', 'user');
      $select = $table->select()
        ->where('user_id = ?', $user->getIdentity())
        ->where('blocked_user_id = ?', $viewer->getIdentity())
        ->limit(1);
      $row = $table->fetchRow($select);
      ?>
      <li>
        <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
          <div class="ui-item-member-action">
            <?php echo $this->userFriendshipSM($user); ?>
            <?php
            $items = Engine_Api::_()->getItem('user', $user->user_id);
            //FOR MESSAGE LINK
            if ((Engine_Api::_()->seaocore()->canSendUserMessage($items)) && (!empty($viewer_id)) && !empty($this->links) && in_array('message', $this->links)) :
              ?>
              <a href="<?php echo $this->url(array('action' => 'compose', 'to' => $user->user_id), 'messages_general', true); ?>" class="userlink userlink-message"></a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <a href="<?php echo $user->getHref() ?>">
          <?php echo $this->itemPhoto($user, 'thumb.icon') ?>         
          <div class="ui-list-content">
            <h3>
                <?php echo $user->getTitle() ?>
                 <?php
                      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->sitemobile()->isSupportedModule('siteverify') && Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'allow_verify')) :
                        $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($user->user_id);
                        $user = Engine_Api::_()->getItem('user', $user->user_id);
                        $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                        ?>
                        <?php if (($verify_count >= $verify_limit)): ?> 
                            <i class="ui-icon ui-icon-ok-sign" style="color: rgb(63, 200, 244);"></i>
                          <?php
                        endif;
                      endif;
                      ?>
            </h3>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.location.enable', 1) && !empty($user->location)): ?>
              <p><?php echo $user->location; ?></p>
            <?php endif; ?>
            <p><?php echo $this->userMutualFriendship($user) ?></p>
          </div>
        </a>     
      </li>
    <?php endforeach; ?>
    <?php if (!$this->autoContentLoad) : ?>
    </ul>
  </div>
<?php endif; ?>
<?php if (empty($this->isajax)) : ?>
  <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
     'pageAsQuery' => true,
     'query' => $this->formValues,
    ));
    ?>
  <?php endif; ?>
<?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
    <?php echo $this->translate('No matching results were found for members.'); ?>
    </span>
  </div>
<?php endif; ?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
    var browseSiteMemberWidgetUrl = sm4.core.baseUrl + 'widget/index/mod/sitemember/name/browse-members-sitemember';
    sm4.core.runonce.add(function() {
      var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
      sm4.core.Module.core.activeParams[activepage_id] = {'currentPage': '<?php echo sprintf('%d', $this->page) ?>', 'totalPages': '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues': <?php echo json_encode($this->formValues); ?>, 'contentUrl': browseSiteMemberWidgetUrl, 'activeRequest': false, 'container': 'browsesitemembers_ul'};
    });
<?php } ?>
</script>