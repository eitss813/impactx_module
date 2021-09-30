<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10158 2014-04-10 19:07:53Z lucas $
 * @author     John
 */
?>

<ul>
  <!--<?php if( !empty($this->memberType) ): ?>
  <li>
    <?php echo $this->translate('Member Type:') ?>
    <?php // @todo implement link ?>
    <?php echo $this->translate($this->memberType) ?>
  </li>
  <?php endif; ?>-->
  <?php if( !empty($this->networks) && count($this->networks) > 0 ): ?>
  <li>
    <?php echo $this->translate('Networks:') ?>
    <?php echo $this->fluentList($this->networks, true) ?>
  </li>
  <?php endif; ?>
  <?php if( $this->profileViews): ?>
  <li>
    <?php echo $this->translate('Profile Views:') ?>
    <?php echo $this->translate(array('%s view', '%s views', $this->subject->view_count),
        $this->locale()->toNumber($this->subject->view_count)) ?>
  </li>
   <?php endif; ?>
   <?php if( $this->friendsCount): ?>
  <li>
    <?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction');
    if ( $direction == 0 ): ?>
      <?php echo $this->translate('Followers:') ?>  
      <?php echo $this->translate(array('%s follower', '%s followers', $this->subject->member_count),
        $this->locale()->toNumber($this->subject->member_count)) ?>      
    <?php else: ?>  
    <?php echo $this->translate('Friends:') ?>
    <?php echo $this->translate(array('%s friend', '%s friends', $this->subject->member_count),
        $this->locale()->toNumber($this->subject->member_count)) ?>
    <?php endif; ?>
  </li>
  <?php endif; ?>
  <?php if( $this->lastUpdateDate): ?>
  <li>
    <?php echo $this->translate('Last Update:'); ?>
    <?php 
      if($this->subject->modified_date != "0000-00-00 00:00:00"){
        echo $this->timestamp($this->subject->modified_date);
      }
      else{
          echo $this->timestamp($this->subject->creation_date);
      }
      ?>
  </li>
  <?php endif; ?>
  <?php if( $this->lastLoginDate): ?>
  <li class="profile_login">
      <span><?php echo $this->translate('Last Login:') ?></span>
      <?php if ($this->subject->lastlogin_date): ?>
      <span><?php echo $this->timestamp($this->subject->lastlogin_date) ?></span>
      <?php else: ?>
      <span><?php echo $this->translate('Never') ?></span>
      <?php endif ?>
  </li>
  <?php endif; ?>
  <?php if( $this->joinedDate): ?>
  <li>
    <?php echo $this->translate('Joined:') ?>
    <?php echo $this->timestamp($this->subject->creation_date) ?>
  </li>
  <?php endif; ?>
  <!--<?php if( $this->memberLevel): ?>
  <li class="profile_level">
    <span><?php echo $this->translate('Member Level:') ?></span>
    <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $this->subject->level_id)->getTitle()); ?>
  </li>-->
  <?php endif; ?>
    <?php if( $this->inviter ): ?>
    <?php if( $this->inviteeName ): ?>
    <li class="profile_invite">
    <span> <?php echo $this->translate('Invitee:') ?></span>
    <?php echo $this->translate($this->inviter); ?>
    </li>
    <?php endif; ?>
    <?php endif; ?>
  <?php if( !$this->subject->enabled && $this->viewer->isAdmin() ): ?>
  <li>
    <em>
      <?php echo $this->translate('Enabled:') ?>
      <?php echo $this->translate('No') ?>
    </em>
  </li>
  <?php endif; ?>
</ul>