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


<style type="text/css"> 
.backers_marquee_container{
    position: relative;
    width: 100%; 
    height: 400px; 
    overflow: hidden; 
} 
</style> 

<?php $this->headLink()->appendStylesheet($this->seaddonsBaseUrl() . '/application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<?php if (in_array('totalCount', $this->options)) : ?>
    <span>
        <?php echo $this->translate("<strong>%s</strong>",$this->backerCount.' Backers'); ?>
    </span>
<?php endif; ?>

<!-- <marquee direction="up" behavior="scroll" onmouseover="this.stop();" onmouseout="this.start();"> --> 
<div id="marquee1" class="backers_marquee_container" onmouseover="zxcMarquee.scroll('marquee1',0);" onmouseout="zxcMarquee.scroll('marquee1',-1);"> 
    <div style="position: absolute; left: 0; right: 0;"> 
        <ul class="people_who_backed sitecrowdfunding_thumbs">

        <?php
        $container = 1;
        foreach ($this->backers as $backer) {
            ?>
            <li>
                <div class="people_who_backed_grid">
                    <div>
                        <?php if($backer->is_private_backing): ?>
                            <?php $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_user_thumb_icon.png"; ?>
                            <img src="<?php echo $url; ?>">
                        <?php else: ?>
                            <?php $user = Engine_Api::_()->getItem('user', $backer->user_id); ?>
                            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'item_photo', 'title' => $this->translate($user->getTitle()), 'target' => '_parent')); ?>
                        <?php endif; ?>
                    </div>
                    <div class="p5 mleft5">
                        <?php if (in_array('name', $this->options)) : ?>
                            <?php if($backer->is_private_backing): ?>
                                <b><?php echo $this->translate('Anonymous'); ?></b>
                            <?php else: ?>
                            <span class="dblock people_who_backed_grid_name">
                                <?php echo $this->htmlLink($user->getHref(), $this->translate(" %s ", $this->translate($user->getTitle()))); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    
                        <?php if (in_array('amount', $this->options)) : ?>
                            <span class="dblock people_who_backed_grid_text">
                                <?php $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); ?>
                                <?php echo $this->translate("%s",'<strong>'.$fundedAmount.'</strong>'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
            <?php
            $container++;
        }
        ?>

        </ul> 
    </div> 
</div> 
<!-- </marquee> --> 


<script type="text/javascript">

var zxcMarquee={

        init:function(o){
            var mde=o.Mode,
            mde=typeof(mde)=='string'&&mde.charAt(0).toUpperCase()=='H'?['left','offsetWidth','top','width']:['top','offsetHeight','left','height'],id=o.ID,srt=o.StartDelay,ud=o.StartDirection,
            p=document.getElementById(id),
            obj=p.getElementsByTagName('DIV')[0],
            sz=obj[mde[1]],clone;
            p.style.overflow='hidden';
            obj.style.position='absolute';
            obj.style[mde[0]]='0px';
            obj.style[mde[3]]=sz+'px';
            clone=obj.cloneNode(true);
            clone.style[mde[0]]=sz+'px';
            clone.style[mde[2]]='0px';
            obj.appendChild(clone);

            o=this['zxc'+id]={
                obj:obj,
                mde:mde[0],
                sz:sz
           }
        if (typeof(srt)=='number'){
            o.dly=setTimeout(function(){ zxcMarquee.scroll(id,typeof(ud)=='number'?ud:-1); },srt);
        }
        else {
            this.scroll(id,0)
        }
    },

    scroll:function(id,ud){
    var oop=this,o=this['zxc'+id],p;
        if (o){
           ud=typeof(ud)=='number'?ud:0;
           clearTimeout(o.dly);
           p=parseInt(o.obj.style[o.mde])+ud;
           if ((ud>0&&p>0)||(ud<0&&p<-o.sz)){
            p+=o.sz*(ud>0?-1:1);
           }
           o.obj.style[o.mde]=p+'px';
           o.dly=setTimeout(function(){ oop.scroll(id,ud); },10);
       }
    }
}

function init(){ 
     zxcMarquee.init({
          ID:'marquee1',     // the unique ID name of the parent DIV.                        (string)
          Mode:'Vertical',   //(optional) the mode of execution, 'Vertical' or 'Horizontal'. (string, default = 'Vertical')
          StartDelay:2000,   //(optional) the auto start delay in milli seconds'.            (number, default = no auto start)
          StartDirection:-1  //(optional) the auto start scroll direction'.                  (number, default = -1)
     }); 
}

if (window.addEventListener)
    window.addEventListener("load", init, false)
else if (window.attachEvent)
    window.attachEvent("onload", init)
else if (document.getElementById)
     window.onload=init
</script>