<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: un-featured.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Make Un-featured Channel?") ?></h3>
        <p>
            <?php echo $this->translate("Are you sure that you want to make this item un-featured? ") ?>
        </p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->id ?>"/>
            <button type='submit'><?php echo $this->translate("Make Un-featured") ?></button>
            <?php echo $this->translate(" or ") ?> 
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
                <?php echo $this->translate("cancel") ?>
            </a>
        </p>
    </div>	
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>