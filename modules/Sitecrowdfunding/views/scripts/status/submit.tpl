<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: delete.tpl 9853 2013-01-11 21:24:26Z john $
 * @author     Steve
 */
?>

<div class='global_form_popup'>
    <form method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <div>
            <h3>
                <?php echo $this->translate("Submit for approval") ?>
            </h3>
            <p>
                <?php echo $this->translate("Are you sure want to submit this project for approval?") ?>
            </p>

            <p>&nbsp;</p>

            <p>
                <input type="hidden" name="project_id" value="<?php echo $this->project_id ?>"/>
                <button type='submit'><?php echo $this->translate("Submit") ?></button>
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
