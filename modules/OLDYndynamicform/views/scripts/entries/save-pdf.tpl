<div id="yndform_entry-print">
    <div class="yndform_form_detail_info">
        <div class="yndform_form_detail_info_title">
            <h3 class="h3"><?php echo $this -> yndform -> getTitle() ?></h3>
        </div>
        <div class="yndform_form_category_entries">
            <div class="yndform_form_category_parent">
                <span class="ynicon yn-folder"></span>
                <?php $category = $this -> yndform -> getCategory() ?>
                <?php if($category):?>
                    <?php foreach ( $category -> getBreadCrumNode() as $node): ?>
                        <?php if ($node -> level != 0): ?>
                            <?php echo $this->htmlLink(
                                $node->getHref(),
                                $this->string()->truncate($this->translate($node->getTitle()), 20),
                                array('title' => $this->translate($node->getTitle()))); ?>
                            <span class="yndform_slash">&#47;</span><span class="yndform_backslash">&#92;</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if($category):?>
                <?php
                if (count($category -> getBreadCrumNode()) > 0):
                    echo $this->htmlLink(
                        $category->getHref(),
                        $this->string()->truncate($this->translate($category->getTitle()), 20),
                        array('title' => $this->translate($category->getTitle())));
                else:
                    echo $this -> translate("Uncategoried");
                endif; ?>
                <?php endif; ?>
            </div>
            <i class="yn_dots">.</i>
            <div class="yndform_form_detail_info_creation_date">
                <?php echo $this->translate('Created on %1$s', $this -> timestamp($this -> yndform->creation_date)) ?>
            </div>
        </div>
        <div class="yndform_form_detail_info_description">
            <?php echo $this -> yndform -> description ?>
        </div>
    </div>
    <div class="yndform_submitby">
    <?php if ($this->entry->owner_id)
            echo $this->translate('Submitted by').' '.$this->htmlLink($this->entry->getOwner()->getHref(), $this->entry->getOwner()->getTitle(), array());
          else
            echo $this->translate('Submitted by Anonymous');

        echo ' '.$this->translate('on').' '.$this->timestamp($this->entry->creation_date);
    ?>
    </div>

    <div class="global_form">
        <!-- Field answers -->
        <?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> entry); ?>
        <?php if($this -> yndformFieldValueLoop($this -> entry, $fieldStructure)):?>
            <div class="yndform_main_content entry-profile-fields form-elements">
                <?php echo $this -> yndformFieldValueLoop($this -> entry, $fieldStructure); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php exit; ?>
<script type="text/javascript">
    /*
    window.addEvent('load', function () {
        saveHTMLPDF();
    })
    */
</script>


<?php
// $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/html2pdf.js');
?>
<script>
    /*
    function saveHTMLPDF() {
        document.getElementById("global_header").innerHTML = "";
        document.getElementById("global_footer").innerHTML = "";

        let page_content = document.body.innerHTML; // const element = document.getElementById("content");

        // Remove the header
        const element_1 = page_content.split("<header>");
        const element_2 = page_content.split("</header>");

        if(element_1[0] && element_1[0] != '' && element_2[1] && element_2[1] != ''){
                page_content = element_1[0] + element_2[1];
        }
        // Choose the element and save the PDF for our user.
        var opt = {
                margin: [10, 0, 10, 0],
                filename: 'download_file.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2,  backgroundColor: '#d1d1d1' },
                jsPDF: { unit: 'pt', format: 'A3', orientation: 'landscape' },
                // pagebreak: { after: '.page-break', avoid: 'img' }
        };
        html2pdf().set(opt).from(page_content).save();

        // setTimeout(function(){window.close();}, 2000);
    }
    */
</script>