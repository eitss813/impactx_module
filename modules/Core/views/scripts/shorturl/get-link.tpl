<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php
    $this->headScript()
->appendFile($baseUrl . 'application/modules/Sitepage/externals/scripts/jquery.min.js');

$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/owl.carousel.min.js');
?>

<div class="global_form_popup">

     <div style="margin-bottom: 38px;" >
         <div style="float: left">  <h3><?php echo $this->translate("Share"); ?></h3>    </div>
         <div style="float: right">
             <p onclick="parent.Smoothbox.close();" style="font-size: 20px;"><i class="fa fa-close" id="close_search_icon" ></i></p>
         </div>
     </div>
    <div class="mtop10">
        <?php echo $this->translate("You can use this link to share this with anyone, even if they don't have an account on this website. Anyone with the link will be able to see it"); ?>
    </div>
    <div class="mtop10">
        <textarea style="height:65px;width:450px" id="text-box" class="text-box" onclick="select_all();"> <?php echo $this->url; ?> </textarea>
    </div>

    <button  class="fright" onclick="copyLink()" ><?php echo $this->translate('Copy Link') ?></button>

</div>

<script>
    function select_all()
    {
        var text_val = document.getElementById('text-box');
        text_val.select();
    }
    function copyLink() {
        var copyText = document.getElementById("text-box");
            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/
            console.log('hiiii');
            /* Copy the text inside the text field */
            document.execCommand("copy");

    }
</script>
<style>
    button.fright {
        margin-left: 4px;
    }
</style>