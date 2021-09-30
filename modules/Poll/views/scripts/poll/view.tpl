<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
?>

<h2>
  <?php if($this->poll->getParentItem()): ?>
    <?php echo $this->poll->getParentItem()->__toString(); ?>
    <?php echo $this->translate('&#187;'); ?>
  <?php else: ?>
    <?php echo $this->htmlLink(array('route' => 'poll_general'), "Polls", array()); ?>
    <?php echo $this->translate('&#187;'); ?>
  <?php endif; ?>
  <?php echo $this->poll->getTitle(); ?>
</h2>

<h2>
  <?php echo $this->translate('%s\'s Polls', $this->htmlLink($this->owner, $this->owner->getTitle())) ?>
</h2>

<div class='polls_view'>
  <h3>
    <?php echo $this->poll->title ?>

    <?php if( $this->poll->closed ): ?>
    <i class="fa fa-lock" alt="<?php echo $this->translate('Closed') ?>"></i>
    <?php endif ?>
  </h3>

  <div class="poll_desc">
    <?php echo Engine_Api::_()->core()->smileyToEmoticons($this->poll->description); ?>
  </div>

  <?php
    // poll, pollOptions, canVote, canChangeVote, hasVoted, showPieChart
    echo $this->render('_poll.tpl')
  ?>
</div>


<script type="text/javascript">
    $$('.core_main_poll').getParent().addClass('active');
</script>
