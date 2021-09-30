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

<div id='id_<?php echo $this->content_id; ?>'>
    <?php if (count($this->announcements) > 0): ?>
        <ul class="sitecrowdfunding_profile_announcements">
            <?php foreach ($this->announcements as $item): ?>
                <li>
                    <?php if ($this->showTitle): ?>
                        <div class="sitecrowdfunding_profile_announcement_title mbot5"><?php echo $item->title; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item->body)): ?>
                        <div class="sitecrowdfunding_profile_list_info_des show_content_body">
                            <?php echo $item->body; ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('No announcements have been created yet.'); ?>
            </span>
        </div>
    <?php endif; ?>
</div>
<script type="text/javascript">

    function scrollToTopForSitecrowdfunding(id) {
        if (document.getElement('body').get('id')) {
            var scroll = new Fx.Scroll(document.getElement('body').get('id'), {
                wait: false,
                duration: 1000,
                offset: {
                    'x': -200,
                    'y': -100
                },
                transition: Fx.Transitions.Quad.easeInOut
            });

            scroll.toElement(id);
        }
        return;
    }

    $$('.tab_<?php echo $this->identity; ?>').addEvent('click', function (project)
    {
        var globalContentElement = en4.seaocore.getDomElements('content');
        prev_tab_id = '<?php echo $this->content_id; ?>';
        prev_tab_class = 'layout_sitecrowdfunding_profile_announcements_sitecrowdfunding';
        $('id_' + <?php echo $this->content_id ?>).style.display = "block";
        if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
            $$('.' + prev_tab_class).setStyle('display', 'none');
        }

        if ($(project.target).get('tag') != 'div' && ($(project.target).getParent('.layout_sitecrowdfunding_profile_announcements_sitecrowdfunding') == null)) {
            scrollToTopForSitecrowdfunding($(globalContentElement).getElement(".layout_sitecrowdfunding_profile_announcements_sitecrowdfunding"));
        }
    });

</script>