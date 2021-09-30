
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Donate Receipt', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">
                <?php echo $this->donateReceiptForm->render($this); ?>
            </div>
        </div>
    </div>
</div>

<style>
    .sitepage_edit_content{
        color: #222 !important;
        font-size: 24px !important;
        padding: 10px 10px;
        margin: -10px -10px 10px -10px;
    }
</style>
<script>
    window.addEventListener('DOMContentLoaded', function () {

       // Load image
        loadImage();

    });

    function openChangeModal(value){

        console.log('hi',value);
        var list = document.getElementById("custom-logo-wrapper");   // Get the <ul> element with id="myList"
        var no = document.getElementById("custom-logo-element");
        if(no && list.hasChildNodes()) {
            list.removeChild(document.getElementById("custom-logo-element"));
        }

        var reader = new FileReader();
        reader.onload = function(){
            if(document.getElementById('change_img'))
               document.getElementById('donate_receipt_logo-wrapper').removeChild(document.getElementById('change_img'));


            var output = document.getElementById('output');
           // output.src = reader.result;
            var img = document.createElement('img');
            img.setAttribute('id', 'change_img');
            img.src=reader.result;
            document.getElementById('donate_receipt_logo-wrapper').appendChild(img);

        };
        reader.readAsDataURL(event.target.files[0]);

    }
    function loadImage(){
        var htmlStr = `
             <div id="custom-logo-wrapper" class="form-wrapper">
                <div id="custom-logo-label" class="form-label">
                </div>
                <div id="custom-logo-element" class="form-element">
                    <img id="display_photo_custom_id" style="height: 250px;width: 300px;object-fit: contain;" src="<?php echo $this->logo; ?>" />
                <div>
             <div>
        `;

        var newNode = document.createElement('div');
        newNode.innerHTML = htmlStr;

        // Get the reference node
        var referenceNode = document.querySelector('#donate_receipt_logo-wrapper');

        // Insert the new node before the reference node
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);

        // $j('#logo_edit_options-wrapper').append('<br/>');

    }
</script>
<style>
    img#change_img {
        width: 18%;
        margin-left: 17%;
    }
</style>