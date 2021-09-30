<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: get-main-photo.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Alex
 */
?>
<div class="profile_main_photo_wrapper">
  <div class="profile_main_photo b_dark">
    <div class="item_photo">
      <?php if (empty($this->uploadDefaultCover)): ?>
      <table class="main_thumb_photo">
        <tr valign="middle">
          <td>
            <?php if($this->user->getPhotoUrl('thumb.profile')) : ?>
            <span style="background-image:url('<?php echo $this->user->getPhotoUrl('thumb.profile');?>'); text-align:left;" id="user_profile_photo"></span>
            <?php else : ?>
            <span style="background-image:url('application/modules/User/externals/images/nophoto_user_thumb_profile.png'); text-align:left;" id="user_profile_photo"></span>
            <?php endif;?>
          </td>
        </tr>
      </table>
      <?php else: ?>
      <table class="main_thumb_photo">
        <tr valign="middle">
          <td>
            <span style="background-image:url('application/modules/User/externals/images/nophoto_user_thumb_profile.png'); text-align:left;" id="user_profile_photo"></span>
          </td>
        </tr>
      </table>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($this->can_edit) && empty($this->uploadDefaultCover)) : ?>
  <div id="mainphoto_options" class="profile_cover_options
      <?php if (!empty($this->uploadDefaultCover)) : ?> profile_main_photo_options is_hidden
      <?php else: ?> profile_main_photo_options<?php endif; ?>">
    <ul class="edit-button">
      <li>
          <span class="profile_cover_btn">
            <?php if (!empty($this->user->photo_id)) : ?>
              <span class="profile_cover_btn">
                <i class="fa fa-camera" aria-hidden="true"></i>
              </span>
            <?php else: ?>
            <span class="profile_cover_btn">
                <i class="fa fa-camera" aria-hidden="true"></i>
              </span>
            <?php endif; ?>
          </span>

        <ul class="profile_options_pulldown">
          <li>
            <a href='<?php echo $this->url(array(
                'action' => 'upload-cover-photo',
            'user_id' => $this->user->user_id,
            'photoType' => 'profile'), 'user_coverphoto', true); ?>' class="profile_cover_icon_photo_upload smoothbox">
            <?php echo $this->translate('Upload Photo'); ?>
            </a>
          </li>
          <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')):?>
          <li>
            <?php echo $this->htmlLink(
            $this->url(array(
            'action' => 'choose-from-albums',
            'user_id' => $this->user->user_id,
            'photoType' => 'profile'
            ), 'user_coverphoto', true),
            $this->translate('Choose from Albums'),
            array(' class' => 'profile_cover_icon_photo_view smoothbox')); ?>
          </li>
          <?php endif; ?>
          <?php if (!empty($this->user->photo_id)) : ?>
          <li>
            <?php echo $this->htmlLink(
            array('route' => 'user_coverphoto', 'action' => 'remove-cover-photo', 'user_id' => $this->user->user_id, 'photoType' => 'profile'),
            $this->translate('Remove'),
            array(' class' => 'smoothbox profile_cover_icon_photo_delete')); ?>
          </li>
          <?php endif; ?>
        </ul>
      </li>
    </ul>
  </div>
  <?php endif; ?>
</div>
<?php if (empty($this->uploadDefaultCover)): ?>
<div class="cover_photo_profile_options">
  <div id='profile_status'>
    <h2>
      <?php if ($this->subject()) : ?>
      <?php echo $this->subject()->getTitle() ?>
      <?php endif; ?>
    </h2>
    <span class="coverphoto_navigation">
        <i class="fa fa-pencil" aria-hidden="true"></i>

        <ul>
          <?php foreach( $this->userNavigation as $link ): ?>
            <li>
              <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
                'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
                'style' => $link->get('icon') ? 'background-image: url('.$link->get('icon').');' : '',
                'target' => $link->get('target'),
              )) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </span>

    <?php if( $this->auth ): ?>
    <span class="profile_status_text" id="user_profile_status_container">
          <?php echo $this->viewMore($this->getHelper('getActionContent')->smileyToEmoticons($this->subject()->status)) ?>
      <?php if( !empty($this->subject()->status) && $this->subject()->isSelf($this->viewer())): ?>
            <a class="profile_status_clear" href="javascript:void(0);" onclick="en4.user.clearStatus();">(<?php echo $this->translate('clear') ?>)</a>
      <?php endif; ?>
        </span>
    <?php endif; ?>
    <br> <br>
    <?php if( empty($this->viewer()->getIdentity()) ): ?>
    <!--  <a class="button user_auth_link">
        Send Message
      </a> -->
    <?php else: ?>
    <?php
        //GET THE LOGGEDIN USER INFORMATION
         $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $currentID = $viewer_id;
    $toUserID = $this->user->user_id;
    ?>
    <?php if( $currentID != $toUserID ): ?>
      <div class="button smoothbox">
        <?php echo $this->userFriendship($this->user); ?>
      </div>
    <!--  <a class="button smoothbox" href="<?php echo $this->url(array('action' => 'message-profile-user', 'to' => $this->user->user_id), 'messages_general', true); ?>"  target='_blank'>
      Send Message
    </a> -->
    <?php endif; ?>
    <?php endif; ?>
    &nbsp;
    <?php foreach( $this->userNavigation as $link ): ?>
    <?php if( strpos($link->getHref(), 'confirm') !== false): ?>
    <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
    'class' => 'button' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
    'style' => $link->get('icon') ? 'background-image: url('.$link->get('icon').');' : '',
    'target' => $link->get('target'),
    )) ?>
    <?php endif; ?>

    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
<div class="clr"></div>
<style>
  .buttonlink {
    padding: 7px 16px;
    font-size: 14px;
    color: white !important;
    background-color: #44AEC1;
    color: #ffffff;
    border: 2px solid #44AEC1;
    cursor: pointer;
    outline: none;
    position: relative;
    overflow: hidden;
    -webkit-transition: all 500ms ease 0s;
    -moz-transition: all 500ms ease 0s;
    -o-transition: all 500ms ease 0s;
    transition: all 500ms ease 0s;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    -webkit-box-sizing: border-box;
    -mox-box-sizing: border-box;
    box-sizing: border-box;
  }
  .icon_friend_add:before {
    display: none;
  }
  #user_profile_status_container {
    display: none !important;
  }
  .layout_right > div, .layout_left > div, .layout_middle > div, .notifications_leftside, .notifications_rightside, #global_page_core-error-notfound #global_content {
    -moz-border-radius: 6px !important;
    /* -webkit-border-radius: 6px; */
    border-radius: 6px !important;
    -moz-box-shadow: 0 1px 8px 0 rgba(0,0,0,.05);
    /* -webkit-box-shadow: 0 1px 8px 0 rgba(0,0,0,.05); */
    /* box-shadow: 0 1px 8px 0 rgba(0,0,0,.05); */
  }
  .layout_right > div, .layout_left > div, .layout_middle > div, .notifications_leftside, .notifications_rightside, #global_page_core-error-notfound #global_content {
    background-color: #fff !important;
    padding: 15px !important;
    margin-bottom: 15px !important;
    -webkit-box-sizing: border-box !important;
    -mox-box-sizing: border-box !important;
    box-sizing: border-box !important;
  }
</style>