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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php
$id = $this->identity;
$class = "slide_box_" . $id;
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/_class.noobSlide.packed.js');
?>
<?php $span = ""; ?>
<?php if ($this->showSlide == 1) : ?> 
    <div id="featured_slideshow_wrapper_<?php echo $id; ?>"  >
        <div id="featured_slideshow_mask_<?php echo $id; ?>" class="featured_slideshow_mask"  >
            <div id="sitecrowdfunding_featured_channel_im_te_advanced_box_<?php echo $id; ?>" class="featured_slideshow_advanced_box"> 
                <?php foreach ($this->paginator as $reward) : ?>
                    <div class='sitecrowdfunding_rewards_listing_box mtop10 featured_slidebox <?php echo $class; ?>'>
                        <div class='featured_slidshow_content'>
                            <div class="sitecrowdfunding_rewards_list_container"> 
                                <?php $pledgeAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($reward->pledge_amount); ?>
                                <span class="reward_amount"><?php echo $this->translate("%s or more", $pledgeAmount); ?></span>
                                <?php if ($reward->photo_id): ?>
                                    <?php $src = Engine_Api::_()->storage()->get($reward->photo_id, '')->getPhotoUrl(); ?>
                                    <img src="<?php echo $src; ?>" title = '<?php echo $reward->title; ?>'>
                                <?php endif; ?>
                                <h3><?php echo $this->translate($reward->title); ?></h3> 
                                <p title="<?php echo $reward->description; ?>">
                                    <?php echo $this->string()->truncate($this->string()->stripTags($reward->description), $this->descriptionTruncation) ?>
                                </p>  
                                <div class="sitecrowdfunding_rewards_listing_info">
                                    <div>
                                        <?php echo $this->translate("Quantity"); ?> :
                                        <?php if ($reward->quantity == 0): ?>
                                            <span><?php echo $this->translate("Unlimited"); ?></span>       
                                        <?php else : ?> 
                                            <span><?php echo $this->translate(array('%s Backer', '%s Backers', $reward->quantity), $this->locale()->toNumber($reward->quantity)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php echo $this->translate("Estimated Delivery :"); ?>
                                        <span><?php echo date('F Y', strtotime($reward->delivery_date)); ?></span>
                                    </div> 
                                    <div>
                                        <?php echo $this->translate("Ships to : "); ?>
                                        <span><?php
                                            if ($reward->shipping_method == 1)
                                                echo $this->translate("No shipping invloved");
                                            else if ($reward->shipping_method == 2)
                                                echo $this->translate("Shipping to certain area");
                                            else
                                                echo $this->translate("Anywhere in the world");
                                            ?></span>
                                    </div>
                                </div>
                            </div> 
                            <?php if ((!$this->project->isExpired()) && $this->project->status == 'active'): ?>
                                <div class="sitecrowdfunding_rewards_list_hover">
                                    <div class="sitecrowdfunding_rewards_list_hover_content">
                                        <?php if ($reward->quantity == 0 || $reward->spendRewardQuantity() < $reward->quantity): ?>
                                            <a href="<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'reward-selection', 'project_id' => $this->project->project_id, 'reward_id' => $reward->getIdentity()), 'default', true); ?>" class="more_link seaocore_txt_green">
                                                <span><?php echo 'Select Reward'; ?></span>
                                            </a> 
                                        <?php else: ?>
                                            <span class="seaocore_txt_red"><?php echo 'Reward is no longer available'; ?></span>
                                        <?php endif; ?> 
                                    </div>
                                </div>
                            <?php endif; ?>  
                        </div>
                    </div> 
                <?php endforeach; ?>
            </div>
        </div> 
        <div class="featured_slideshow_option_bar" style="display:block">
            <div>
                <p class="buttons">
                    <span style="float:left;" id="sitecrowdfunding_featured_channel_prev8_<?php echo $id; ?>" class="mleft5 featured_slideshow_controllers-prev featured_slideshow_controllers prev" title=<?php echo $this->translate("Previous") ?> ></span> 
                    <span id="counter"></span>
                    <span id="slideshow_stop_button_<?php echo $id; ?>" ></span><span id="slideshow_play_button_<?php echo $id; ?>" ></span>
                    <span style="float:right;" id="sitecrowdfunding_featured_channel_next8_<?php echo $id; ?>" class="featured_slideshow_controllers-next featured_slideshow_controllers" title=<?php echo $this->translate("Next") ?> ></span>
                </p>
            </div>
        </div>
    </div>
<?php else : ?>
    <?php foreach ($this->paginator as $reward) : ?>
        <div class='sitecrowdfunding_rewards_list_box sitecrowdfunding_rewards_grid_box mtop10 featured_slidebox <?php echo $class; ?>' >
            <div style="height: <?php echo $this->slideHeight; ?>px" class="sitecrowdfunding_rewards_list_container"> 
                <?php $pledgeAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($reward->pledge_amount); ?>
                <span class="reward_amount"><?php echo $this->translate("%s or more", $pledgeAmount); ?></span>
                <?php if ($reward->photo_id): ?>
                    <?php $src = Engine_Api::_()->storage()->get($reward->photo_id, '')->getPhotoUrl(); ?>
                    <img src="<?php echo $src; ?>" title = '<?php echo $reward->title; ?>'>
                <?php endif; ?>
                <h4><?php echo $this->translate($reward->title); ?></h4> 
                <p title="<?php echo $reward->description; ?>">
                    <?php echo $this->string()->truncate($this->string()->stripTags($reward->description), $this->descriptionTruncation) ?>
                </p> 
                <div class="sitecrowdfunding_rewards_listing_info">
                    <div>
                        <?php echo $this->translate("Quantity"); ?> :
                        <?php if ($reward->quantity == 0): ?>
                            <span><?php echo $this->translate("Unlimited"); ?></span>      
                        <?php else : ?> 
                            <span><?php echo $this->translate(array('%s Backer', '%s Backers', $reward->quantity), $this->locale()->toNumber($reward->quantity)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php echo $this->translate("Estimated Delivery :"); ?>
                        <span><?php echo date('F Y', strtotime($reward->delivery_date)); ?></span>
                    </div> 
                    <div>
                        <?php echo $this->translate("Ships to : "); ?>
                        <span>
                            <?php
                            if ($reward->shipping_method == 1)
                                echo $this->translate("No shipping invloved");
                            else if ($reward->shipping_method == 2)
                                echo $this->translate("Shipping to certain area");
                            else
                                echo $this->translate("Anywhere in the world");
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php if ((!$this->project->isExpired()) && $this->project->status == 'active'): ?>
                <div class="sitecrowdfunding_rewards_list_hover">
                    <div class="sitecrowdfunding_rewards_list_hover_content">
                        <?php if ($reward->quantity == 0 || $reward->spendRewardQuantity() < $reward->quantity): ?>
                            <a href="<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'reward-selection', 'project_id' => $this->project->project_id, 'reward_id' => $reward->getIdentity()), 'default', true); ?>" class="more_link seaocore_txt_green">
                                <span><?php echo 'Select Reward'; ?></span>
                            </a> 
                        <?php else: ?>
                            <span class="seaocore_txt_red"><?php echo 'Reward is no longer available'; ?></span>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>  
        </div>
    <?php endforeach; ?> 
<?php endif; ?> 

<script type="text/javascript">
    en4.core.runonce.add(function () {


        if (document.getElementsByClassName == undefined) {
            document.getElementsByClassName = function (className)
            {
                var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
                var allElements = document.getElementsByTagName("*");
                var results = [];

                var element;
                for (var i = 0; (element = allElements[i]) != null; i++) {
                    var elementClass = element.className;
                    if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
                        results.push(element);
                }
                return results;
            }
        }
        SlideShow = function ()
        {
            this.width = 0;
            this.slideElements = [];
            this.noOfSlideShow = 0;
            this.id = 0;
            this.handles8_more = '';
            this.handles8 = '';
            this.interval = 0;
            this.slideBox = '';
            this.set = function (arg)
            {
                this.noOfSlideShow = arg.noOfSlideShow;
                this.id = arg.id;
                this.interval = arg.interval;
                this.slideBox = arg.slideBox;

                this.width = $('featured_slideshow_wrapper_<?php echo $id; ?>').getWidth();
                this.height = <?php echo $this->slideHeight ?>;

                var globalContentElement = en4.seaocore.getDomElements('content');
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.width = (this.width) + "px";
                $(globalContentElement).getElement("#featured_slideshow_mask_" + this.id).style.height = (this.height) + "px";
                this.slideElements = document.getElementsByClassName(this.slideBox);
                for (var i = 0; i < this.slideElements.length; i++)
                    this.slideElements[i].style.width = (this.width) + "px";
                this.handles8_more = $$('#handles8_more_' + this.id + ' span');
                this.handles8 = $$('#handles8_' + this.id + ' span');

            }

            this.walk = function ()
            {
                var uid = this.id;
                var noOfSlideShow = this.noOfSlideShow;
                var handles8 = this.handles8;
                var nS8 = new noobSlide({
                    box: $('sitecrowdfunding_featured_channel_im_te_advanced_box_' + this.id),
                    items: $$('#sitecrowdfunding_featured_channel_im_te_advanced_box_' + this.id + ' .featured_slidshow_content'),
                    size: (this.width),
                    handles: this.handles8,
                    addButtons: {previous: $('sitecrowdfunding_featured_channel_prev8_' + this.id), next: $('sitecrowdfunding_featured_channel_next8_' + this.id), stop: $('slideshow_stop_button_' + this.id), play: $('slideshow_play_button_' + this.id)},
                    interval: this.interval,
                    fxOptions: {
                        duration: 1000,
                        transition: '',
                        wait: false,
                    },
                    autoPlay: true,
                    mode: 'horizontal',
                    onWalk: function (currentItem, currentHandle) {

                        $$(this.handles, handles8).removeClass('active');
                        $$(currentHandle, handles8[this.currentIndex]).addClass('active');
                        $('counter').innerHTML = (this.currentIndex + 1) + "/" + this.items.length;
                        if ((this.currentIndex + 1) == (this.items.length))
                            $('sitecrowdfunding_featured_channel_next8_' + uid).hide();
                        else
                            $('sitecrowdfunding_featured_channel_next8_' + uid).show();

                        if (this.currentIndex > 0)
                            $('sitecrowdfunding_featured_channel_prev8_' + uid).show();
                        else
                            $('sitecrowdfunding_featured_channel_prev8_' + uid).hide();
                    }
                });
                //more handle buttons
                nS8.addHandleButtons(this.handles8_more);
                //walk to item 3 witouth fx
                nS8.walk(0, false, true);
            }
        }

        var slideshow = new SlideShow();
        slideshow.set({
            id: '<?php echo $id; ?>',
            noOfSlideShow: <?php echo $this->totalCount; ?>,
            interval: 5000,
            slideBox: '<?php echo $class; ?>'
        });
        slideshow.walk();
    });
<?php if ($this->showSlide == 1) : ?>
        $('featured_slideshow_wrapper_<?php echo $id; ?>').addEvents({
            mouseover: function () {
                $('slideshow_stop_button_<?php echo $id; ?>').click();
            },
            mouseleave: function () {
                $('slideshow_play_button_<?php echo $id; ?>').click();
            }
        });
<?php endif; ?>
</script>


