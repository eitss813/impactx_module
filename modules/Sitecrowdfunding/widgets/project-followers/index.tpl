<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css'); ?>
<?php if ($this->loaded_by_ajax): ?>
<script type="text/javascript">
    var params = {
        requestParams:<?php echo json_encode($this->params) ?>,
    responseContainer: $$('.layout_sitecrowdfunding_project_followers')
    };
    en4.sitecrowdfunding.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
</script>
<?php endif; ?>

<?php if ($this->showContent): ?>
    <div>
        <?php $count = 0; ?>
        <ul id="project-followers" class="grid_wrapper">
            <?php foreach ($this->paginator as $item): ?>
                <li>
                    <?php
                    $count++;
                    $user_id = $item['poster_id'];
                    $user = Engine_Api::_()->getItem('user', $user_id);
                    echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile'));
                    ?>
                    <div class='followers-name'>
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($count==0): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('No Followers'); ?>
                </span>
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>


