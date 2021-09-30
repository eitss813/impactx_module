<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='global_form_popup'>
    <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <div>
            <h3>
                <?php echo $this->translate("Delete this initiative?") ?>
            </h3>
            <p>
                <?php echo $this->translate("Are you sure that you want to delete this initiative? This action cannot be undone.") ?>
            </p>

            <p>&nbsp;</p>

            <p>
                <input type="hidden" name="initiative_id" value="<?php echo $this->initiative_id ?>"/>
                <button type='submit'><?php echo $this->translate("Delete") ?></button>
                <?php echo $this->translate(" or ") ?>
                <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate("cancel") ?></a>
            </p>
        </div>
    </form>
</div>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
    TB_close();
</script>
<?php endif; ?>

