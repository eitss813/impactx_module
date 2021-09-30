<?php
/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq_help.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
    tbody td{
        text-align: center;
    }
</style>
<script type="text/javascript">
    function faq_show(id) {
        if ($(id).style.display == 'block') {
            $(id).style.display = 'none';
        } else {
            $(id).style.display = 'block';
        }
    }
</script>
<?php $style = 'display:none;' ?>
<?php $flag = 0; ?>

<div class="admin_siteuseravatar_files_wrapper">

    <ul class="admin_siteuseravatar_files siteuseravatar_faq">
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');">How does setting ‘Count of Avatar Initials’ works for available two different format of avatar initials? </a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                ‘Count of Avatar Initials’ works as follows for two different format of avatar initials: <br /><br />
                <table class="admin_table" style="float: none;">
                    <thead>
                    <th>Count Of Avatar Initials</th><th>Initials of First Name[eg: John Walker]</th><th>Initials from complete Name[eg: Marry Jane Pattrick Williams]</th>
                </thead>
                <tbody>
                    <tr><td>1</td><td>J</td><td>M</td></tr>
                    <tr><td>2</td><td>Jo</td><td>MJ</td></tr>
                    <tr><td>3</td><td>Joh</td><td>MJP</td></tr>
                    <tr><td>4</td><td>John</td><td>MJPW</td></tr>
                </tbody>
                </table>
            </div>
        </li>
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo ++$flag; ?>');"> How can I add new font styles for avatar initials? </a>
            <div class='faq' style='<?php echo $style; ?>' id='faq_<?php echo $flag; ?>'>
                Follow below steps to add avatar initials of your choice: <br /> <br />
                    a) Go to Appearance → File & Media Manager. <br />
                    b) Upload ttf file of the new font style.  <br />
                    c) New font style is added, now you can set it for avatar initials on your website.

            </div>
        </li>       

          
    </ul>
    <br/>
</div>
