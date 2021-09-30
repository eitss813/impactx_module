<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: page-join.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

    function removeMember (user_id, project_id) {
        var friendUrl = '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'admin-manage', 'action' => 'delete-member'), 'default', true) ?>';
        en4.core.request.send(new Request.HTML({
            url : friendUrl,
            data : {
                format: 'html',
                user_id: user_id,
                project_id: project_id,
            },
            'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                document.getElementById('more_results_shows_'+user_id).innerHTML = "Removed Member";
                // setTimeout("hideField(" + user_id + ")", 1000);
            }
        }));
    }

    // function hideField(user_id) {
    //     document.getElementById('more_results_shows_'+user_id).destroy();
    //     if(document.getElementById('members_results_friend') == null){
    //         document.getElementById('members_results_friend').innerHTML = "There are no more members joined in this project"
    //     }
    //     if (document.getElementById('members_results_friend').getChildren().length < 0) {
    //         document.getElementById('members_results_friend').innerHTML = "There are no more members joined in this project"
    //     }
    // }

</script>

<div class="seaocore_members_popup seaocore_members_popup_notbs">
    <div class="seaocore_members_popup_content" id="members_results_friend">
        <?php if (count($this->paginator) > 0) : ?>
            <?php foreach( $this->paginator as $value ): ?>
                <?php $user_id = $value['user_id']; ?>
                <?php $user = Engine_Api::_()->getItem('user', $user_id); ?>
                <div class="item_member_list" id="more_results_shows_<?php echo $user_id; ?>">
                    <div class="item_member_thumb">
                        <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile')); ?>
                    </div>
                    <div class="item_member_option">
                        <a href="javascript:void(0);" onclick="removeMember('<?php echo $user_id ?>','<?php echo $this->project_id ?>');" class="icon_sitepagemember_leave buttonlink"><?php echo $this->translate('Remove Member')?></a>
                    </div>
                    <div class="item_member_details">
                        <div class="item_member_name">
                            <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        <?php else : ?>
            <div class="tip" id='sitepagemember_search'>
                  <span>
                      <?php echo $this->translate('No members found.');?>
                  </span>
            </div>
        <?php endif; ?>
    </div>

    <div class="seaocore_members_popup_bottom">
        <button  onclick='smoothboxclose()' ><?php echo $this->translate('Close') ?></button>
    </div>

</div>

<script type="text/javascript">
    function smoothboxclose () {
        parent.Smoothbox.close () ;
    }
</script>