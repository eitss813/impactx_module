<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 1:50 PM
 */
class Yndynamicform_FormController extends Core_Controller_Action_Standard
{
    public function init()
    {
        $id = $this -> _getParam('form_id', null);
        if( $id )
        {
            $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $id);
            if( $yndform )
            {
                Engine_Api::_() -> core() -> setSubject($yndform);

                if (!$this -> _helper -> requireAuth -> setAuthParams($yndform, null, 'view') -> isValid()) {
                    return;
                }
            }
        }
    }

    public function detailAction()
    {
        $this->view->form_id = $form_id = $this -> _getParam('form_id', null);
        $this->view->page_id = $this -> _getParam('page_id', null);
        
        $this -> view-> type = $type = $this->_getParam('type',null);
        // Render
        $this -> _helper -> content -> setEnabled();

        if (!$this -> _helper -> requireSubject('yndynamicform_form') -> isValid()) return;

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $yndform = Engine_Api::_() -> core() -> getSubject();

        if (!Engine_Api::_()->authorization()->isAllowed($yndform, $viewer, 'submission')) {
            $this -> view-> error = true;
            $this -> view-> message = 'You do not have permission to submit this form.';
            return;
        }
        
        $this->view->formAction =  $this->view->url(array(
                'action' =>'create',
                'entry_id' => 1,
                'form_id' => $form_id,
                'user_id' => $viewer->getIdentity()
            ), 'yndynamicform_entry_specific');

       // if (!$yndform -> isViewable()) {
       //     $this -> _helper -> requireSubject -> forward();
       // }

        if (!$yndform -> isReachedMaximumFormsByLevel()) {
            $this -> view-> error = true;
            $this -> view-> message = 'Number of your submitted forms is maximum. Please try again later or delete some entries for submitting new.';
            return;
        }

        // Increase view count
        $yndform -> view_count += 1;
        $yndform -> save();

        // Get new entry form
        $topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('yndynamicform_entry');
        if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
            $profileTypeField = $topStructure[0] -> getChild();
        }

        $this->view->ajaxform_option_id = $yndform->option_id;
        $this->view->ajaxform_field_id = $profileTypeField->field_id;
        $this -> view -> new_entry_form = $new_entry_form = new Yndynamicform_Form_Standard(
            array(
                'item' => new Yndynamicform_Model_Entry(array()),
                'topLevelId' => $profileTypeField -> field_id,
                'topLevelValue' => $yndform -> option_id,
                'mode' => 'create',
            ));
        
        // Set the Preview hidden form field
        $new_entry_form->addElement('Hidden', 'submission_status', array( 'value' => 'preview', 'order' => 8000));
        
        $valss = (array)$new_entry_form->getValues();
        foreach ($valss as $key=>$value){ ?>




            <script>
                var newnode = document.createElement("span");                 // Create a <li> node
                newnode.setAttribute("class", "phn_span_element");
                newnode.innerHTML= "<?php echo $new_entry_form->getElement($key)->getDescription(); ?>";

                document.getElementById("<?php echo $key; ?>-label").appendChild(newnode);

            </script>
           <?php

            if( $new_entry_form->getElement($key)->getType() == 'Fields_Form_Element_Phone') {

                $finalKeyARR = explode("_",$key);
                $finalKey = $finalKeyARR[count($finalKeyARR) - 1];


                 ?>

                <script>

                    var myParent = document.body;

                    //Create array of options to be added
                               //Create array of options to be added
                    var array = [{"country":"---- Select Country Code ----"},
                        {"id":1,"country":"Algeria (+213)","digit":9,"code":"+213"},
                        {"id":2,"country":"Andorra (+376)","digit":6,"code":"+376"},
                        {"id":3,"country":"Angola (+244)","digit":9,"code":"+244"},
                        {"id":4,"country":"Anguilla (+1264)","digit":10,"code":"+1264"},
                        {"id":5,"country":"Antigua & Barbuda (+1268)","digit":10,"code":"+1268"},
                        {"id":6,"country":"Argentina (+54)","digit":9,"code":"+54"},
                        {"id":7,"country":"Armenia (+374)","digit":6,"code":"+374"},
                        {"id":8,"country":"Aruba (+297)","digit":7,"code":"+297"} ,
                        {"id":9,"country":"Australia (+61)","digit":9,"code":"+61"}  ,
                        {"id":10,"country":"Austria (+43)","digit":10,"code":"+43"} ,
                        {"id":11,"country":"Azerbaijan (+994)","digit":9,"code":"+994"},
                        {"id":12,"country":"Bahamas (+1242)","digit":10,"code":"+1242"} ,
                        {"id":13,"country":"Bahrain (+973)","digit":8,"code":"+973"} ,
                        {"id":14,"country":"Bangladesh (+880)","digit":10,"code":"+880"},
                        {"id":15,"country":"Barbados (+1246)","digit":10,"code":"+1246"},


                        {"id":16,"country":"Belarus (+375)","digit":9,"code":"+375"} ,
                        {"id":17,"country":"Belgium (+32)","digit":9,"code":"+32"},
                        {"id":18,"country":"Belize (+501)","digit":7,"code":"+501"},
                        {"id":19,"country":"Benin (+229)","digit":9,"code":"+229"} ,
                        {"id":20,"country":"Bermuda (+1441)","digit":10,"code":"+1441"},
                        {"id":21,"country":"Bhutan (+975)","digit":9,"code":"+975"} ,
                        {"id":22,"country":"Bolivia (+591)","digit":9,"code":"+591"},
                        {"id":23,"country":"Bosnia Herzegovina (+387)","digit":8,"code":"+387"},


                        {"id":24,"country":"Botswana (+267)","digit":9,"code":"+267"},
                        {"id":25,"country":"Brazil (+55)","digit":11,"code":"+55"} ,
                        {"id":26,"country":"Brunei (+673)","digit":9,"code":"+673"} ,
                        {"id":27,"country": "Bulgaria (+359)","digit":9,"code":"+359"},
                        {"id":28,"country": "Burkina Faso (+226)","digit":8,"code":"+226"},
                        {"id":29,"country":"Burundi (+257)","digit":9,"code":"+257"},
                        {"id":30,"country":"Cambodia (+855)","digit":9,"code":"+855"},
                        {"id":31,"country":"Cameroon (+237)","digit":9,"code":"+237"},
                        {"id":32,"country":"Canada (+1)","digit":10,"code":"+1"},
                        {"id":33,"country":"Cape Verde Islands (+238)","digit":9,"code":"+238"},

                        {"id":34,"country":"Cayman Islands (+1345)","digit":10,"code":"+1345"},
                        {"id":35,"country":"Central African Republic (+236)","digit":9,"code":"+236"},
                        {"id":36,"country":"Chile (+56)","digit":9,"code":"+56"},
                        {"id":37,"country":"China (+86)","digit":11,"code":"+86"},
                        {"id":38,"country":"Colombia (+57)","digit":10,"code":"+57"},
                        {"id":39,"country":"Comoros (+269)","digit":9,"code":"+269"},
                        {"id":40,"country":"Congo (+242)","digit":9,"code":"+242"},
                        {"id":41,"country":"Cook Islands (+682)","digit":5,"code":"+682"},
                        {"id":42,"country":"Costa Rica (+506)","digit":8,"code":"+506"},
                        {"id":43,"country":"Croatia (+385)","digit":9,"code":"+385"},
                        {"id":44,"country":"Cuba (+53)","digit":9,"code":"+53"},
                        {"id":45,"country":"Cyprus North (+90392)","digit":8,"code":"+90392"},
                        {"id":46,"country":"Cyprus South (+357)","digit":8,"code":"+357"},


                        {"id":47,"country":"Czech Republic (+42)","digit":9,"code":"+42"},
                        {"id":48,"country":"Denmark (+45)","digit":8,"code":"+45"},
                        {"id":49,"country":"Djibouti (+253)","digit":9,"code":"+253"},
                        {"id":50,"country":"Dominica (+1809)","digit":10,"code":"+1809"},
                        {"id":51,"country":"Dominican Republic (+1809)","digit":10,"code":"+1809"},
                        {"id":52,"country":"Ecuador (+593)","digit":9,"code":"+593"},
                        {"id":53,"country":"Egypt (+20)","digit":10,"code":"+20"},
                        {"id":54,"country":"El Salvador (+503)","digit":8,"code":"+503"},
                        {"id":55,"country":"Equatorial Guinea (+240)","digit":9,"code":"+240"},
                        {"id":56,"country":"Eritrea (+291)","digit":9,"code":"+291"},
                        {"id":57,"country":"Estonia (+372)","digit":9,"code":"+372"},
                        {"id":58,"country":"Ethiopia (+251)","digit":9,"code":"+251"},
                        {"id":59,"country":"Falkland Islands (+500)","digit":9,"code":"+500"},
                        {"id":60,"country":"Faroe Islands (+298)","digit":5,"code":"+298"},
                        {"id":61,"country":"Fiji (+679)","digit":5,"code":"+679"},
                        {"id":62,"country":"Finland (+358)","digit":10,"code":"+358"},
                        {"id":63,"country":"France (+33)","digit":9,"code":"+33"},
                        {"id":64,"country":"French Guiana (+594)","digit":9,"code":"+594"},
                        {"id":65,"country":"French Polynesia (+689)","digit":6,"code":"+689"},
                        {"id":66,"country":"Gabon (+241)","digit":7,"code":"+241"},
                        {"id":67,"country": "Gambia (+220)","digit":9,"code":"+220"},
                        {"id":68,"country":"Georgia (+7880)","digit":9,"code":"+7880"},
                        {"id":69,"country":"Germany (+49)","digit":10,"code":"+49"},
                        {"id":70,"country":"Ghana (+233)","digit":9,"code":"+233"},
                        {"id":71,"country":"Gibraltar (+350)","digit":9,"code":"+350"},
                        {"id":72,"country":"Greece (+30)","digit":10,"code":"+30"},
                        {"id":73,"country":"Greenland (+299)","digit":6,"code":"+299"},
                        {"id":74,"country":"Grenada (+1473)","digit":10,"code":"+1473"},
                        {"id":75,"country":"Guadeloupe (+590)","digit":9,"code":"+590"},
                        {"id":76,"country": "Guam (+671)","digit":10,"code":"+671"},
                        {"id":77,"country":"Guatemala (+502)","digit":8,"code":"+502"},
                        {"id":78,"country":"Guinea (+224)","digit":9,"code":"+224"},
                        {"id":79,"country":"Guinea - Bissau (+245)","digit":9,"code":"+245"},


                        {"id":80,"country":"Guyana (+592)","digit":9,"code":"+592"},
                        {"id":81,"country":"Haiti (+509)","digit":9,"code":"+509"},
                        {"id":82,"country":"Honduras (+504)","digit":8,"code":"+504"},
                        {"id":83,"country":"Hong Kong (+852)","digit":8,"code":"+852"},
                        {"id":84,"country":"Hungary (+36)","digit":9,"code":"+36"},
                        {"id":85,"country":"Iceland (+354)","digit":9,"code":"+354"},
                        {"id":86,"country":"India (+91)","digit":10,"code":"+91"},
                        {"id":87,"country":"Indonesia (+62)","digit":10,"code":"+62"},
                        {"id":88,"country":"Iran (+98)","digit":10,"code":"+98"},
                        {"id":89,"country":"Ireland (+353)","digit":9,"code":"+353"},
                        {"id":90,"country":"Israel (+972)","digit":9,"code":"+972"},
                        {"id":91,"country":"Italy (+39)","digit":9,"code":"+39"},
                        {"id":92,"country": "Jamaica (+1876)","digit":10,"code":"+1876"},
                        {"id":93,"country":"Japan (+81)","digit":10,"code":"+81"},
                        {"id":94,"country":"Jordan (+962)","digit":9,"code":"+962"},
                        {"id":95,"country": "Kazakhstan (+7)","digit":10,"code":"+376"},
                        {"id":96,"country": "Kenya (+254)","digit":10,"code":"+376"},
                        {"id":97,"country": "Kiribati (+686)","digit":8,"code":"+376"},
                        {"id":98,"country":"Korea North (+850)","digit":9,"code":"+850"},
                        {"id":99,"country": "Korea South (+82)","digit":9,"code":"+82"},
                        {"id":100,"country": "Kuwait (+965)","digit":8,"code":"+965"},
                        {"id":101,"country": "Kyrgyzstan (+996)","digit":9,"code":"+996"},
                        {"id":102,"country": "Laos (+856)","digit":9,"code":"+856"},
                        {"id":103,"country": "Latvia (+371)","digit":8,"code":"+371"},
                        {"id":104,"country": "Lebanon (+961)","digit":8,"code":"+961"},
                        {"id":105,"country": "Lesotho (+266)","digit":9,"code":"+266"},
                        {"id":106,"country":"Liberia (+231)","digit":7,"code":"+231"},
                        {"id":107,"country":"Libya (+218)","digit":10,"code":"+218"},
                        {"id":108,"country":"Liechtenstein (+417)","digit":9,"code":"+417"},



                        {"id":109,"country": "Lithuania (+370)","digit":8,"code":"+370"},
                        {"id":110,"country":"Luxembourg (+352)","digit":9,"code":"+352"},
                        {"id":111,"country":"Macao (+853)","digit":9,"code":"+853"},
                        {"id":112,"country":"Macedonia (+389)","digit":8,"code":"+389"},
                        {"id":113,"country":"Madagascar (+261)","digit":9,"code":"+261"},
                        {"id":114,"country":"Malawi (+265)","digit":9,"code":"+265"},
                        {"id":115,"country":"Malaysia (+60)","digit":7,"code":"+60"},
                        {"id":116,"country":"Maldives (+960)","digit":7,"code":"+960"},
                        {"id":117,"country":"Mali (+223)","digit":8,"code":"+223"},
                        {"id":118,"country":"Malta (+356)","digit":9,"code":"+356"},


                        {"id":119,"country":"Marshall Islands (+692)","digit":7,"code":"+692"},
                        {"id":120,"country":"Martinique (+596)","digit":9,"code":"+596"},
                        {"id":121,"country":"Mauritania (+222)","digit":9,"code":"+222"},
                        {"id":122,"country":"Mayotte (+269)","digit":9,"code":"+269"},
                        {"id":123,"country":"Mexico (+52)","digit":10,"code":"+52"},
                        {"id":124,"country":"Micronesia (+691)","digit":7,"code":"+691"},
                        {"id":125,"country":"Moldova (+373)","digit":8,"code":"+373"},
                        {"id":126,"country":"Monaco (+377)","digit":9,"code":"+377"},
                        {"id":127,"country":"Mongolia (+976)","digit":8,"code":"+976"},


                        {"id":128,"country":"Montserrat (+1664)","digit":10,"code":"+1664"},
                        {"id":129,"country": "Mozambique (+258)","digit":12,"code":"+258"},
                        {"id":130,"country": "Myanmar (+95)","digit":9,"code":"+95"},
                        {"id":131,"country":"Namibia (+264)","digit":9,"code":"+264"},
                        {"id":132,"country":"Nauru (+674)","digit":9,"code":"+674"},
                        {"id":133,"country": "Nepal (+977)","digit":10,"code":"+977"},
                        {"id":134,"country": "Netherlands (+31)","digit":9,"code":"+31"},
                        {"id":135,"country":"New Caledonia (+687)","digit":6,"code":"+687"},
                        {"id":136,"country":"New Zealand (+64)","digit":9,"code":"+64"},
                        {"id":137,"country": "Nicaragua (+505)","digit":8,"code":"+505"},
                        {"id":138,"country": "Niger (+227)","digit":8,"code":"+227"},
                        {"id":139,"country":"Nigeria (+234)","digit":8,"code":"+234"},
                        {"id":140,"country":"Niue (+683)","digit":4,"code":"+683"},
                        {"id":141,"country":"Norfolk Islands (+672)","digit":6,"code":"+672"},
                        {"id":142,"country": "Northern Marianas (+670)","digit":10,"code":"+670"},
                        {"id":143,"country":"Norway (+47)","digit":8,"code":"+47"},
                        {"id":144,"country":"Oman (+968)","digit":8,"code":"+968"},
                        {"id":145,"country":"Palau (+680)","digit":7,"code":"+680"},
                        {"id":146,"country":"Panama (+507)","digit":8,"code":"+507"},
                        {"id":147,"country":"Papua New Guinea (+675)","digit":9,"code":"+675"},

                        {"id":148,"country":"Paraguay (+595)","digit":9,"code":"+595"},
                        {"id":149,"country": "Peru (+51)","digit":9,"code":"+51"},
                        {"id":150,"country": "Philippines (+63)","digit":10,"code":"+63"},
                        {"id":151,"country":"Poland (+48)","digit":9,"code":"+48"},
                        {"id":152,"country":"Portugal (+351)","digit":9,"code":"+351"},
                        {"id":153,"country": "Puerto Rico (+1787)","digit":10,"code":"+1787"},
                        {"id":154,"country": "Qatar (+974)","digit":8,"code":"+974"},
                        {"id":155,"country": "Reunion (+262)","digit":9,"code":"+262"},
                        {"id":156,"country":"Romania (+40)","digit":10,"code":"+40"},
                        {"id":157,"country":"Russia (+7)","digit":10,"code":"+7"},
                        {"id":158,"country":"Rwanda (+250)","digit":9,"code":"+250"},
                        {"id":159,"country": "San Marino (+378)","digit":9,"code":"+378"},
                        {"id":160,"country":"Sao Tome &amp Principe (+239)","digit":9,"code":"+239"},
                        {"id":161,"country": "Saudi Arabia (+966)","digit":9,"code":"+966"},
                        {"id":162,"country":"Senegal (+221)","digit":9,"code":"+221"},
                        {"id":163,"country": "Serbia (+381)","digit":9,"code":"+381"},
                        {"id":164,"country":"Seychelles (+248)","digit":9,"code":"+248"},
                        {"id":165,"country": "Sierra Leone (+232)","digit":9,"code":"+232"},
                        {"id":166,"country":"Singapore (+65)","digit":8,"code":"+65"},
                        {"id":167,"country": "Slovak Republic (+421)","digit":9,"code":"+421"},
                        {"id":168,"country":"Slovenia (+386)","digit":9,"code":"+386"},
                        {"id":169,"country":"Solomon Islands (+677)","digit":7,"code":"+677"},
                        {"id":170,"country":"Somalia (+252)","digit":7,"code":"+252"},
                        {"id":171,"country": "South Africa (+27)","digit":9,"code":"+27"},
                        {"id":172,"country":"Spain (+34)","digit":9,"code":"+34"},
                        {"id":173,"country":"Sri Lanka (+94)","digit":7,"code":"+94"},
                        {"id":174,"country":"St. Helena (+290)","digit":9,"code":"+290"},
                        {"id":175,"country": "St. Kitts (+1869)","digit":9,"code":"+1869"},
                        {"id":176,"country":"St. Lucia (+1758)","digit":9,"code":"+1758"},



                        {"id":177,"country":"Sudan (+249)","digit":9,"code":"+249"},
                        {"id":178,"country":"Suriname (+597)","digit":9,"code":"+597"},
                        {"id":179,"country": "Swaziland (+268)","digit":9,"code":"+268"},
                        {"id":180,"country":"Sweden (+46)","digit":7,"code":"+46"},
                        {"id":181,"country":"Switzerland (+41)","digit":9,"code":"+41"},
                        {"id":182,"country":"Syria (+963)","digit":9,"code":"+963"},
                        {"id":183,"country": "Taiwan (+886)","digit":9,"code":"+886"},
                        {"id":184,"country":"Tajikstan (+7)","digit":9,"code":"+7"},
                        {"id":185,"country":"Thailand (+66)","digit":9,"code":"+66"},
                        {"id":186,"country":"Togo (+228)","digit":8,"code":"+228"},
                        {"id":187,"country": "Tonga (+676)","digit":9,"code":"+676"},
                        {"id":188,"country":"Trinidad &amp Tobago (+1868)","digit":10,"code":"+1868"},
                        {"id":189,"country":"Tunisia (+216)","digit":8,"code":"+216"},
                        {"id":190,"country":"Turkey (+90)","digit":11,"code":"+90"},
                        {"id":191,"country":"Turkmenistan (+993)","digit":9,"code":"+993"},
                        {"id":192,"country":"Turks &amp Caicos Islands (+1649)","digit":10,"code":"+1649"},




                        {"id":193,"country":"Tuvalu (+688)","digit":9,"code":"+688"},
                        {"id":194,"country": "Uganda (+256)","digit":9,"code":"+256"},
                        {"id":195,"country":"UK (+44)","digit":10,"code":"+44"},
                        {"id":196,"country":"Ukraine (+380)","digit":9,"code":"+380"},
                        {"id":197,"country":"United Arab Emirates (+971)","digit":9,"code":"+971"},
                        {"id":198,"country": "Uruguay (+598)","digit":9,"code":"+598"},
                        {"id":199,"country":"USA (+1)","digit":10,"code":"+1"},
                        {"id":200,"country":"Uzbekistan (+7)","digit":9,"code":"+7"},
                        {"id":201,"country":"Vanuatu (+678)","digit":9,"code":"+678"},
                        {"id":202,"country": "Vatican City (+379)","digit":10,"code":"+379"},
                        {"id":203,"country":"Venezuela (+58)","digit":7,"code":"+58"},
                        {"id":204,"country":"Vietnam (+84)","digit":9,"code":"+84"},
                        {"id":205,"country":"Virgin Islands - British (+1284)","digit":10,"code":"+1284"},
                        {"id":206,"country":"Virgin Islands - US (+1340)","digit":10,"code":"+1340"},
                        {"id":207,"country":"Futuna (+681)","digit":9,"code":"+681"},
                        {"id":208,"country":"Yemen (North)(+969)","digit":9,"code":"+969"},
                        {"id":209,"country": "Yemen (South)(+967)","digit":9,"code":"+967"},
                        {"id":210,"country":"Zambia (+260)","digit":9,"code":"+260"},
                        {"id":211,"country":"Zimbabwe (+263)","digit":9,"code":"+263"}




                    ];


                    //Create and append select list
                    var selectList = document.createElement("select");
                    selectList.id = "<?php echo $key; ?>-mySelect";
                    selectList.name = "<?php echo $key; ?>-mySelect";
                    selectList.for = "<?php echo $key; ?>-mySelect";
                    myParent.appendChild(selectList);

                    //Create and append the options
                    for (var i = 0; i < array.length; i++) {
                        var option = document.createElement("option");
                        option.value =array[i]['country'];
                        option.text = array[i]['country'];
                        selectList.appendChild(option);
                    }
                    var node = document.createElement("span");                 // Create a <li> node
                    var textnode = document.getElementById("<?php echo $key; ?>-mySelect");         // Create a text node
                    textnode.onchange = function(){
                        let arr = document.getElementById("<?php echo $key; ?>-mySelect").value.split("("); let aar2=arr[1].split(")");
                        document.getElementsByClassName('field_<?php echo $finalKey; ?>')[0].value= aar2[0]+"-";
                    };
                    node.setAttribute("class", "phn_span_element");
                    node.appendChild(textnode);
                    // Append the text to <li>
                    document.getElementById("<?php echo $key; ?>-element").setAttribute("class", "phn_element");

                    document.getElementById("<?php echo $key; ?>-element").appendChild(node);
                </script>
           <?php
            }?>

                <script>
                    let selectedVal =  '<?php echo $_POST[1]; ?>';

                  </script>
            <?php
            $labelss = str_replace("#540","'",$new_entry_form->getElement($key)->getLabel());
            $new_entry_form->getElement($key)->setLabel($labelss);

            // display min & max value for number field
            $keyArr = explode("_",$key);
            $num = $keyArr[count($keyArr) - 1];
            $db = Engine_Db_Table::getDefaultAdapter();
            $fieldsLabel =  $db->select()
                ->from('engine4_yndynamicform_entry_fields_meta')
                ->where('field_id = ?', $num)
                ->limit()
                ->query()
                ->fetchAll();

            // set min & max label for number field
            if($fieldsLabel[0]['type'] == 'float' || $fieldsLabel[0]['type'] == 'integer'){
                $config = json_decode($fieldsLabel[0]['config']);
                if(isset($config->min_value)){
                    $min_value = $config->min_value;
                    if($min_value > 0)
                        $labelss = $labelss . ' ( Minimum: '. $min_value.')';
                }
                if(isset($config->max_value)){
                    $max_value = $config->max_value;
                    if($max_value > 0)
                        $labelss = $labelss . ' ( Maximum: '. $max_value.')';
                }
                if(isset($config->default_value)){
                    $default_value = $config->default_value;
                    $new_entry_form->getElement($key)->setValue($default_value);
                }
                $new_entry_form->getElement($key)->setLabel($labelss);
            }
        }

        if (!$yndform -> isSubmittable()) {
            $new_entry_form -> removeElement('submit_button');
        }

        // Get data for conditional logic
        $conditional_params = Engine_Api::_()-> yndynamicform() -> getParamsConditionalLogic($yndform, true);
        $conf_params = Engine_Api::_() -> yndynamicform() -> getConditionalLogicConfirmations($yndform -> getIdentity());
        $noti_params = Engine_Api::_() -> yndynamicform() -> getConditionalLogicNotifications($yndform -> getIdentity());
        $this -> view -> prefix = '1_'.$yndform -> option_id.'_';
        $this -> view -> form = $yndform;
        $this -> view -> fieldsValues = $conditional_params['arrConditionalLogic'];
        $this -> view -> fieldIds = $conditional_params['arrFieldIds'];
        $this -> view -> totalPageBreak = $conditional_params['pageBreak'];
        $this -> view -> arrErrorMessage = $conditional_params['arrErrorMessage'];
        $this -> view -> pageBreakConfigs = $yndform -> page_break_config;
        $this -> view -> doCheckConditionalLogic = true;
        $this -> view -> viewer = $viewer;
        $this -> view -> confConditionalLogic = $conf_params['confConditionalLogic'];
        $this -> view -> confOrder = $conf_params['confOrder'];
        $this -> view -> notiConditionalLogic = $noti_params['notiConditionalLogic'];
        $this -> view -> notiOrder = $noti_params['notiOrder'];

        // Check post
        if (!$this -> getRequest() -> isPost()) {
            return;
        }

        /*
         * Cheat: Because some field are not valid conditional logic but admin config they are required.
         *          So we need to ignore them when validate form.
         *          arrayIsValid is very important to make this work. So if some one change it in front-end
         *          will make this not work or work not correctly.
         *        If they are not valid conditional logic we will clear value of them.
         */
        $arrayIsValid = $this -> getRequest() -> getParam('arrayIsValid');
        $arrayIsValid = json_decode($arrayIsValid);

        foreach ($arrayIsValid as $key => $value) {
            if (!$value) {
                $ele = $new_entry_form -> getElement($key);
                if ($ele instanceof Zend_Form_Element)
                    if ($ele -> isRequired()) {
                        $ele = $ele -> setRequired(false);
                        $ele = $ele -> setValue('');
                    }
            }
        }

        // Validate file upload
        if (isset($_FILES) && $viewer -> getIdentity()) {
            $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
            foreach ($_FILES as $key => $value)
            {
                $array_filtered = array_filter($value['name']);
                if (empty($array_filtered) || !count($array_filtered)) continue;

                // Validate file extension
                $field_id = explode('_', $key)[2];
                $map = $mapData -> getRowMatching('child_id', $field_id);
                $field = $map->getChild();

                $max_file = $field->config['max_file'];
                if ($max_file && count($array_filtered) > $max_file) {
                    $new_entry_form -> addError('You have input reached maximum allowed files.');
                    return;
                }

                $allowed_extension = $field->config['allowed_extensions'];
                if ($allowed_extension == '*') continue;
                $allowed_extension = str_replace('.', '', $allowed_extension);
                $allowed_extension = str_replace(' ', '', $allowed_extension);
                $allowed_extension = explode(',', $allowed_extension);

                $max_file_size = $field->config['max_file_size'];
                foreach ($value['name'] as $k => $filename) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(!in_array($ext,$allowed_extension) ) {
                        $new_entry_form -> addError('File type or extension is not allowed.');
                        return;
                    }
                    if ($max_file_size && $value['size'][$k] > $max_file_size*1024) {
                        $new_entry_form -> addError($this->view->translate('%s file size exceeds the allowable limit.', $value['name'][$k]));
                        return;
                    }
                }
            }
        }

        // check the number fields and its values
        if($this->getRequest()->isPost() && $new_entry_form->isValid($this->getRequest()->getPost())) {
            $val = (array)$new_entry_form->getValues();
            $fffFlag = true;
            $entryArray=[];
            foreach ($val as $key=>$value){

                ?>
                <script>

                    document.getElementById('<?php echo $key.'-mySelect'; ?>').value = '<?php echo $_POST[$key.'-mySelect']; ?>';
                </script>
                <?php




                if( $new_entry_form->getElement($key)->getType() == 'Fields_Form_Element_Phone') {


                //    $_POST[$key.'-mySelect'] = 'Anguilla (+1264)';
                    ?>
                    <script>

                        let selectedVal =  '<?php echo $_POST[$key.'-mySelect']; ?>';
                        document.getElementById('<?php echo $key.'-mySelect'; ?>').value = $_POST[$key.'-mySelect'];
                        console.log('selectedVal--++++-',selectedVal);
                    </script>
                    <?php








                    //$phpVar = "<script>document.writeln(jj);</script>";
                    $val = $new_entry_form->getElement($key)->getValue();
                    $valArr = (explode("-",$val));



                    $array = '[{"country":"---- Select Country Code ----"},
                        {"id":1,"country":"Algeria (+213)","digit":9,"code":"+213"},
                        {"id":2,"country":"Andorra (+376)","digit":6,"code":"+376"},
                        {"id":3,"country":"Angola (+244)","digit":9,"code":"+244"},
                        {"id":4,"country":"Anguilla (+1264)","digit":10,"code":"+1264"},
                        {"id":5,"country":"Antigua & Barbuda (+1268)","digit":10,"code":"+1268"},
                        {"id":6,"country":"Argentina (+54)","digit":9,"code":"+54"},
                        {"id":7,"country":"Armenia (+374)","digit":6,"code":"+374"},
                        {"id":8,"country":"Aruba (+297)","digit":7,"code":"+297"} ,
                        {"id":9,"country":"Australia (+61)","digit":9,"code":"+61"}  ,
                        {"id":10,"country":"Austria (+43)","digit":10,"code":"+43"} ,
                        {"id":11,"country":"Azerbaijan (+994)","digit":9,"code":"+994"},
                        {"id":12,"country":"Bahamas (+1242)","digit":10,"code":"+1242"} ,
                        {"id":13,"country":"Bahrain (+973)","digit":8,"code":"+973"} ,
                        {"id":14,"country":"Bangladesh (+880)","digit":10,"code":"+880"},
                        {"id":15,"country":"Barbados (+1246)","digit":10,"code":"+1246"},


                        {"id":16,"country":"Belarus (+375)","digit":9,"code":"+375"} ,
                        {"id":17,"country":"Belgium (+32)","digit":9,"code":"+32"},
                        {"id":18,"country":"Belize (+501)","digit":7,"code":"+501"},
                        {"id":19,"country":"Benin (+229)","digit":9,"code":"+229"} ,
                        {"id":20,"country":"Bermuda (+1441)","digit":10,"code":"+1441"},
                        {"id":21,"country":"Bhutan (+975)","digit":9,"code":"+975"} ,
                        {"id":22,"country":"Bolivia (+591)","digit":9,"code":"+591"},
                        {"id":23,"country":"Bosnia Herzegovina (+387)","digit":8,"code":"+387"},


                        {"id":24,"country":"Botswana (+267)","digit":9,"code":"+267"},
                        {"id":25,"country":"Brazil (+55)","digit":11,"code":"+55"} ,
                        {"id":26,"country":"Brunei (+673)","digit":9,"code":"+673"} ,
                        {"id":27,"country": "Bulgaria (+359)","digit":9,"code":"+359"},
                        {"id":28,"country": "Burkina Faso (+226)","digit":8,"code":"+226"},
                        {"id":29,"country":"Burundi (+257)","digit":9,"code":"+257"},
                        {"id":30,"country":"Cambodia (+855)","digit":9,"code":"+855"},
                        {"id":31,"country":"Cameroon (+237)","digit":9,"code":"+237"},
                        {"id":32,"country":"Canada (+1)","digit":10,"code":"+1"},
                        {"id":33,"country":"Cape Verde Islands (+238)","digit":9,"code":"+238"},

                        {"id":34,"country":"Cayman Islands (+1345)","digit":10,"code":"+1345"},
                        {"id":35,"country":"Central African Republic (+236)","digit":9,"code":"+236"},
                        {"id":36,"country":"Chile (+56)","digit":9,"code":"+56"},
                        {"id":37,"country":"China (+86)","digit":11,"code":"+86"},
                        {"id":38,"country":"Colombia (+57)","digit":10,"code":"+57"},
                        {"id":39,"country":"Comoros (+269)","digit":9,"code":"+269"},
                        {"id":40,"country":"Congo (+242)","digit":9,"code":"+242"},
                        {"id":41,"country":"Cook Islands (+682)","digit":5,"code":"+682"},
                        {"id":42,"country":"Costa Rica (+506)","digit":8,"code":"+506"},
                        {"id":43,"country":"Croatia (+385)","digit":9,"code":"+385"},
                        {"id":44,"country":"Cuba (+53)","digit":9,"code":"+53"},
                        {"id":45,"country":"Cyprus North (+90392)","digit":8,"code":"+90392"},
                        {"id":46,"country":"Cyprus South (+357)","digit":8,"code":"+357"},


                        {"id":47,"country":"Czech Republic (+42)","digit":9,"code":"+42"},
                        {"id":48,"country":"Denmark (+45)","digit":8,"code":"+45"},
                        {"id":49,"country":"Djibouti (+253)","digit":9,"code":"+253"},
                        {"id":50,"country":"Dominica (+1809)","digit":10,"code":"+1809"},
                        {"id":51,"country":"Dominican Republic (+1809)","digit":10,"code":"+1809"},
                        {"id":52,"country":"Ecuador (+593)","digit":9,"code":"+593"},
                        {"id":53,"country":"Egypt (+20)","digit":10,"code":"+20"},
                        {"id":54,"country":"El Salvador (+503)","digit":8,"code":"+503"},
                        {"id":55,"country":"Equatorial Guinea (+240)","digit":9,"code":"+240"},
                        {"id":56,"country":"Eritrea (+291)","digit":9,"code":"+291"},
                        {"id":57,"country":"Estonia (+372)","digit":9,"code":"+372"},
                        {"id":58,"country":"Ethiopia (+251)","digit":9,"code":"+251"},
                        {"id":59,"country":"Falkland Islands (+500)","digit":5,"code":"+500"},
                        {"id":60,"country":"Faroe Islands (+298)","digit":5,"code":"+298"},
                        {"id":61,"country":"Fiji (+679)","digit":5,"code":"+679"},
                        {"id":62,"country":"Finland (+358)","digit":10,"code":"+358"},
                        {"id":63,"country":"France (+33)","digit":9,"code":"+33"},
                        {"id":64,"country":"French Guiana (+594)","digit":9,"code":"+594"},
                        {"id":65,"country":"French Polynesia (+689)","digit":6,"code":"+689"},
                        {"id":66,"country":"Gabon (+241)","digit":7,"code":"+241"},
                        {"id":67,"country": "Gambia (+220)","digit":9,"code":"+220"},
                        {"id":68,"country":"Georgia (+7880)","digit":9,"code":"+7880"},
                        {"id":69,"country":"Germany (+49)","digit":10,"code":"+49"},
                        {"id":70,"country":"Ghana (+233)","digit":9,"code":"+233"},
                        {"id":71,"country":"Gibraltar (+350)","digit":9,"code":"+350"},
                        {"id":72,"country":"Greece (+30)","digit":10,"code":"+30"},
                        {"id":73,"country":"Greenland (+299)","digit":6,"code":"+299"},
                        {"id":74,"country":"Grenada (+1473)","digit":10,"code":"+1473"},
                        {"id":75,"country":"Guadeloupe (+590)","digit":9,"code":"+590"},
                        {"id":76,"country": "Guam (+671)","digit":10,"code":"+671"},
                        {"id":77,"country":"Guatemala (+502)","digit":8,"code":"+502"},
                        {"id":78,"country":"Guinea (+224)","digit":9,"code":"+224"},
                        {"id":79,"country":"Guinea - Bissau (+245)","digit":9,"code":"+245"},


                        {"id":80,"country":"Guyana (+592)","digit":9,"code":"+592"},
                        {"id":81,"country":"Haiti (+509)","digit":9,"code":"+509"},
                        {"id":82,"country":"Honduras (+504)","digit":8,"code":"+504"},
                        {"id":83,"country":"Hong Kong (+852)","digit":8,"code":"+852"},
                        {"id":84,"country":"Hungary (+36)","digit":9,"code":"+36"},
                        {"id":85,"country":"Iceland (+354)","digit":9,"code":"+354"},
                        {"id":86,"country":"India (+91)","digit":10,"code":"+91"},
                        {"id":87,"country":"Indonesia (+62)","digit":10,"code":"+62"},
                        {"id":88,"country":"Iran (+98)","digit":10,"code":"+98"},
                        {"id":89,"country":"Ireland (+353)","digit":9,"code":"+353"},
                        {"id":90,"country":"Israel (+972)","digit":9,"code":"+972"},
                        {"id":91,"country":"Italy (+39)","digit":9,"code":"+39"},
                        {"id":92,"country": "Jamaica (+1876)","digit":10,"code":"+1876"},
                        {"id":93,"country":"Japan (+81)","digit":10,"code":"+81"},
                        {"id":94,"country":"Jordan (+962)","digit":9,"code":"+962"},
                        {"id":95,"country": "Kazakhstan (+7)","digit":10,"code":"+376"},
                        {"id":96,"country": "Kenya (+254)","digit":10,"code":"+376"},
                        {"id":97,"country": "Kiribati (+686)","digit":8,"code":"+376"},
                        {"id":98,"country":"Korea North (+850)","digit":9,"code":"+850"},
                        {"id":99,"country": "Korea South (+82)","digit":9,"code":"+82"},
                        {"id":100,"country": "Kuwait (+965)","digit":8,"code":"+965"},
                        {"id":101,"country": "Kyrgyzstan (+996)","digit":9,"code":"+996"},
                        {"id":102,"country": "Laos (+856)","digit":9,"code":"+856"},
                        {"id":103,"country": "Latvia (+371)","digit":8,"code":"+371"},
                        {"id":104,"country": "Lebanon (+961)","digit":8,"code":"+961"},
                        {"id":105,"country": "Lesotho (+266)","digit":9,"code":"+266"},
                        {"id":106,"country":"Liberia (+231)","digit":7,"code":"+231"},
                        {"id":107,"country":"Libya (+218)","digit":10,"code":"+218"},
                        {"id":108,"country":"Liechtenstein (+417)","digit":9,"code":"+417"},



                        {"id":109,"country": "Lithuania (+370)","digit":8,"code":"+370"},
                        {"id":110,"country":"Luxembourg (+352)","digit":9,"code":"+352"},
                        {"id":111,"country":"Macao (+853)","digit":9,"code":"+853"},
                        {"id":112,"country":"Macedonia (+389)","digit":8,"code":"+389"},
                        {"id":113,"country":"Madagascar (+261)","digit":9,"code":"+261"},
                        {"id":114,"country":"Malawi (+265)","digit":9,"code":"+265"},
                        {"id":115,"country":"Malaysia (+60)","digit":7,"code":"+60"},
                        {"id":116,"country":"Maldives (+960)","digit":7,"code":"+960"},
                        {"id":117,"country":"Mali (+223)","digit":8,"code":"+223"},
                        {"id":118,"country":"Malta (+356)","digit":9,"code":"+356"},


                        {"id":119,"country":"Marshall Islands (+692)","digit":7,"code":"+692"},
                        {"id":120,"country":"Martinique (+596)","digit":9,"code":"+596"},
                        {"id":121,"country":"Mauritania (+222)","digit":9,"code":"+222"},
                        {"id":122,"country":"Mayotte (+269)","digit":9,"code":"+269"},
                        {"id":123,"country":"Mexico (+52)","digit":10,"code":"+52"},
                        {"id":124,"country":"Micronesia (+691)","digit":7,"code":"+691"},
                        {"id":125,"country":"Moldova (+373)","digit":8,"code":"+373"},
                        {"id":126,"country":"Monaco (+377)","digit":9,"code":"+377"},
                        {"id":127,"country":"Mongolia (+976)","digit":8,"code":"+976"},


                        {"id":128,"country":"Montserrat (+1664)","digit":10,"code":"+1664"},
                        {"id":129,"country": "Mozambique (+258)","digit":12,"code":"+258"},
                        {"id":130,"country": "Myanmar (+95)","digit":9,"code":"+95"},
                        {"id":131,"country":"Namibia (+264)","digit":9,"code":"+264"},
                        {"id":132,"country":"Nauru (+674)","digit":9,"code":"+674"},
                        {"id":133,"country": "Nepal (+977)","digit":10,"code":"+977"},
                        {"id":134,"country": "Netherlands (+31)","digit":9,"code":"+31"},
                        {"id":135,"country":"New Caledonia (+687)","digit":6,"code":"+687"},
                        {"id":136,"country":"New Zealand (+64)","digit":9,"code":"+64"},
                        {"id":137,"country": "Nicaragua (+505)","digit":8,"code":"+505"},
                        {"id":138,"country": "Niger (+227)","digit":8,"code":"+227"},
                        {"id":139,"country":"Nigeria (+234)","digit":8,"code":"+234"},
                        {"id":140,"country":"Niue (+683)","digit":4,"code":"+683"},
                        {"id":141,"country":"Norfolk Islands (+672)","digit":6,"code":"+672"},
                        {"id":142,"country": "Northern Marianas (+670)","digit":10,"code":"+670"},
                        {"id":143,"country":"Norway (+47)","digit":8,"code":"+47"},
                        {"id":144,"country":"Oman (+968)","digit":8,"code":"+968"},
                        {"id":145,"country":"Palau (+680)","digit":7,"code":"+680"},
                        {"id":146,"country":"Panama (+507)","digit":8,"code":"+507"},
                        {"id":147,"country":"Papua New Guinea (+675)","digit":9,"code":"+675"},



                        {"id":148,"country":"Paraguay (+595)","digit":9,"code":"+595"},
                        {"id":149,"country": "Peru (+51)","digit":9,"code":"+51"},
                        {"id":150,"country": "Philippines (+63)","digit":10,"code":"+63"},
                        {"id":151,"country":"Poland (+48)","digit":9,"code":"+48"},
                        {"id":152,"country":"Portugal (+351)","digit":9,"code":"+351"},
                        {"id":153,"country": "Puerto Rico (+1787)","digit":10,"code":"+1787"},
                        {"id":154,"country": "Qatar (+974)","digit":8,"code":"+974"},
                        {"id":155,"country": "Reunion (+262)","digit":9,"code":"+262"},
                        {"id":156,"country":"Romania (+40)","digit":10,"code":"+40"},
                        {"id":157,"country":"Russia (+7)","digit":10,"code":"+7"},
                        {"id":158,"country":"Rwanda (+250)","digit":9,"code":"+250"},
                        {"id":159,"country": "San Marino (+378)","digit":9,"code":"+378"},
                        {"id":160,"country":"Sao Tome &amp Principe (+239)","digit":9,"code":"+239"},
                        {"id":161,"country": "Saudi Arabia (+966)","digit":9,"code":"+966"},
                        {"id":162,"country":"Senegal (+221)","digit":9,"code":"+221"},
                        {"id":163,"country": "Serbia (+381)","digit":9,"code":"+381"},
                        {"id":164,"country":"Seychelles (+248)","digit":9,"code":"+248"},
                        {"id":165,"country": "Sierra Leone (+232)","digit":9,"code":"+232"},
                        {"id":166,"country":"Singapore (+65)","digit":8,"code":"+65"},
                        {"id":167,"country": "Slovak Republic (+421)","digit":9,"code":"+421"},
                        {"id":168,"country":"Slovenia (+386)","digit":9,"code":"+386"},
                        {"id":169,"country":"Solomon Islands (+677)","digit":7,"code":"+677"},
                        {"id":170,"country":"Somalia (+252)","digit":7,"code":"+252"},
                        {"id":171,"country": "South Africa (+27)","digit":9,"code":"+27"},
                        {"id":172,"country":"Spain (+34)","digit":9,"code":"+34"},
                        {"id":173,"country":"Sri Lanka (+94)","digit":7,"code":"+94"},
                        {"id":174,"country":"St. Helena (+290)","digit":9,"code":"+290"},
                        {"id":175,"country": "St. Kitts (+1869)","digit":9,"code":"+1869"},
                        {"id":176,"country":"St. Lucia (+1758)","digit":9,"code":"+1758"},



                        {"id":177,"country":"Sudan (+249)","digit":9,"code":"+249"},
                        {"id":178,"country":"Suriname (+597)","digit":9,"code":"+597"},
                        {"id":179,"country": "Swaziland (+268)","digit":9,"code":"+268"},
                        {"id":180,"country":"Sweden (+46)","digit":7,"code":"+46"},
                        {"id":181,"country":"Switzerland (+41)","digit":9,"code":"+41"},
                        {"id":182,"country":"Syria (+963)","digit":9,"code":"+963"},
                        {"id":183,"country": "Taiwan (+886)","digit":9,"code":"+886"},
                        {"id":184,"country":"Tajikstan (+7)","digit":9,"code":"+7"},
                        {"id":185,"country":"Thailand (+66)","digit":9,"code":"+66"},
                        {"id":186,"country":"Togo (+228)","digit":8,"code":"+228"},
                        {"id":187,"country": "Tonga (+676)","digit":9,"code":"+676"},
                        {"id":188,"country":"Trinidad &amp Tobago (+1868)","digit":10,"code":"+1868"},
                        {"id":189,"country":"Tunisia (+216)","digit":8,"code":"+216"},
                        {"id":190,"country":"Turkey (+90)","digit":11,"code":"+90"},
                        {"id":191,"country":"Turkmenistan (+993)","digit":9,"code":"+993"},
                        {"id":192,"country":"Turks &amp Caicos Islands (+1649)","digit":10,"code":"+1649"},




                        {"id":193,"country":"Tuvalu (+688)","digit":9,"code":"+688"},
                        {"id":194,"country": "Uganda (+256)","digit":9,"code":"+256"},
                        {"id":195,"country":"UK (+44)","digit":10,"code":"+44"},
                        {"id":196,"country":"Ukraine (+380)","digit":9,"code":"+380"},
                        {"id":197,"country":"United Arab Emirates (+971)","digit":9,"code":"+971"},
                        {"id":198,"country": "Uruguay (+598)","digit":9,"code":"+598"},
                        {"id":199,"country":"USA (+1)","digit":10,"code":"+1"},
                        {"id":200,"country":"Uzbekistan (+7)","digit":9,"code":"+7"},
                        {"id":201,"country":"Vanuatu (+678)","digit":9,"code":"+678"},
                        {"id":202,"country": "Vatican City (+379)","digit":10,"code":"+379"},
                        {"id":203,"country":"Venezuela (+58)","digit":7,"code":"+58"},
                        {"id":204,"country":"Vietnam (+84)","digit":9,"code":"+84"},
                        {"id":205,"country":"Virgin Islands - British (+1284)","digit":10,"code":"+1284"},
                        {"id":206,"country":"Virgin Islands - US (+1340)","digit":10,"code":"+1340"},
                        {"id":207,"country":"Futuna (+681)","digit":9,"code":"+681"},
                        {"id":208,"country":"Yemen (North)(+969)","digit":9,"code":"+969"},
                        {"id":209,"country": "Yemen (South)(+967)","digit":9,"code":"+967"},
                        {"id":210,"country":"Zambia (+260)","digit":9,"code":"+260"},
                        {"id":211,"country":"Zimbabwe (+263)","digit":9,"code":"+263"}

			   ]';
                    $Arr = json_decode($array);

                       $arr = explode("(",$valArr[0]);
                       $aar2 = explode("(",$arr[1]);



                    $f = array_filter($Arr,  function($k , $vs)  use ($arr){
                        return $k->code == $arr[0];
                    },ARRAY_FILTER_USE_BOTH);



                    $Country = array_filter($Arr,  function($k , $vs)  use ($key){
                        return $k->country == $_POST[$key.'-mySelect'];
                    },ARRAY_FILTER_USE_BOTH);

                    foreach ($Country as $tt) {
                        $countryCode = (array)$tt;
                    }

                    array_push($entryArray,array('country_code_id'=>$countryCode['id'],'field_id'=>$key));



                    $temp= array_values($f);
                    $digit = $temp[0]->digit;


                    if(strlen($valArr[1]) !=  $digit){
                      ?>


                        <script>


                            var node = document.createElement("p");                 // Create a <li> node
                            var textnode = document.createTextNode("Please enter <?php echo $digit;?> digit number ");         // Create a text node
                            node.appendChild(textnode);
                            node.setAttribute('id',"phn_err")// Append the text to <li>
                            document.getElementById("<?php echo $key; ?>-wrapper").appendChild(node);
                        </script>
                       <?php
                        $fffFlag = false;
                    }


                }



                $valueSaved = $new_entry_form->getValue($key);

                $keyArr = explode("_",$key);
                $num = $keyArr[count($keyArr) - 1];
                $db = Engine_Db_Table::getDefaultAdapter();
                $fieldsLabel =  $db->select()
                    ->from('engine4_yndynamicform_entry_fields_meta')
                    ->where('field_id = ?', $num)
                    ->limit()
                    ->query()
                    ->fetchAll();

                if($fieldsLabel[0]['type'] == 'float' || $fieldsLabel[0]['type'] == 'integer'){

                    $config = json_decode($fieldsLabel[0]['config']);
                    $min_value = null;
                    $max_value = null;
                    $default_value = null;

                    if(isset($config->min_value)){
                        $min_value = $config->min_value;
                    }
                    if(isset($config->max_value)){
                        $max_value = $config->max_value;
                    }
                    if(isset($config->default_value)){
                        $default_value = $config->default_value;
                    }

                    // if both filled
                    if($min_value && $max_value && $valueSaved){
                        if( !($min_value <= $valueSaved  && $valueSaved <= $max_value) ){
                            $new_entry_form->addError(
                                $this->view->translate('%s must be between %s to %s.', $valueSaved,$min_value,$max_value)
                            );
                            return;
                        }
                    }
                    // if anyone filled
                    elseif($min_value && !$max_value && $valueSaved){
                        if( !($min_value <= $valueSaved) ){
                            $new_entry_form->addError(
                                $this->view->translate('%s must be greater than %s.', $valueSaved,$min_value,$max_value)
                            );
                            return;
                        }
                    }
                    // if anyone filled
                    elseif (!$min_value && $max_value && $valueSaved){
                        if( !($valueSaved <= $max_value) ){
                            $new_entry_form->addError(
                                $this->view->translate('%s must be lesser than %s.', $valueSaved,$max_value)
                            );
                            return;
                        }
                    }
                    // if not passed then set default value
                    if($default_value && ($valueSaved==null || $valueSaved=='')){
                        $new_entry_form->getElement($key)->setValue($default_value);
                    }

                }
            }
            if($fffFlag == false) {
                return;
            }
        }

        if(!$new_entry_form -> isValid($this -> getRequest() -> getPost())) {
            foreach ($arrayIsValid as $key => $value) {
                if (!$value) {
                    $ele = $new_entry_form -> getElement($key);
                    if ($ele instanceof Zend_Form_Element)
                        $ele = $ele -> setRequired(true);
                }
            }
            return;
        }

        $tableEntries = Engine_Api::_() -> getDbTable('entries', 'yndynamicform');

        // Process to save entry
        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();
        try {
            $new_entry = $tableEntries -> createRow();
            $new_entry -> form_id = $yndform -> getIdentity();
            if (!$viewer -> getIdentity()) {
                $ipObj = new Engine_IP();
                $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
                $new_entry -> ip = $ipExpr;
                $new_entry -> user_email = $this -> getRequest() -> getParam('email_guest');
            }
            $new_entry -> owner_id = $viewer -> getIdentity();
            $new_entry -> submission_status = 'preview';
            $new_entry -> save();



              foreach ($entryArray as $val) {

                  // tab on profile
                  $db->insert('engine4_yndynamicform_entry_countrycode', array(
                      'form_id'     => $yndform -> getIdentity(),
                      'field_id'    => $val['field_id'],
                      'country_code_id'    => $val['country_code_id'],
                      'entryid'     => $new_entry -> getIdentity()

                  ));
              }

            
            $yndform -> total_entries++;
            $yndform -> save();

            if (isset($_FILES) && $viewer -> getIdentity()) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                foreach ($_FILES as $key => $value)
                {
                    $array_filtered = array_filter($value['name']);
                    if (empty($array_filtered) || !count($array_filtered)) continue;

                    // Validate file extension
                    $field_id = explode('_', $key)[2];
                    $map = $mapData -> getRowMatching('child_id', $field_id);
                    $field = $map->getChild();

                    $elementFile = $new_entry_form -> getElement($key);
                    $file_ids = $new_entry -> saveFiles($value);
                    unset($value['tmp_name']);
                    unset($value['error']);
                    $value['file_ids'] = $file_ids;
                    $elementFile -> setValue(json_encode($value));
                }
            }

            $new_entry_form -> setItem($new_entry);
            $new_entry_form -> saveValues();

            // Auth
            $auth = Engine_Api::_() -> authorization() -> context;
            $auth -> setAllowed($new_entry, 'owner', 'view', 1);

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // Send notifications
        $moderators = $yndform -> getAllModeratorsID();
        $supperAdmins = $yndform -> getSuperAdminsID();
        $user_ids = array_merge($moderators,$supperAdmins);
        $user_ids = array_unique($user_ids);

        if (count($user_ids) > 0) {
            // Prepare params send notification
            $users = Engine_Api::_()->getItemMulti('user', $user_ids);
            if ($viewer -> getIdentity()) {
                $notificationType = 'yndynamicform_user_submitted';
            } else {
                $notificationType = 'yndynamicform_anonymous_submitted';
            }

            $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach( $users as $user ) {
                if (!$viewer->isSelf($user)) {
                    $notificationTable->addNotification($user, $viewer, $yndform, $notificationType);
                }
            }

            // Get notification email
            $selected_notification = Engine_Api::_() -> getItem('yndynamicform_notification', $this->getRequest()->getParam('selected_notification'));
            if ($selected_notification && $selected_notification instanceof Yndynamicform_Model_Notification) {
                // Prepare params send email notification
                $mail_api = Engine_Api::_() -> getApi('mail', 'core');
                $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
                $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');
                $subjectTemplate = $selected_notification -> notification_email_subject;
                $bodyTextTemplate = $selected_notification -> notification_email_body;

                $rParams['website_name'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'My Communication');
                $rParams['website_link'] = $this->view->baseUrl();
                $rParams['form_name'] = $yndform -> title;
                $rParams['form_link'] = $yndform -> getHref();

                foreach( $rParams as $var => $val )
                {
                    $var = '[' . $var . ']';
                    // Fix nbsp
                    $val = str_replace('&amp;nbsp;', ' ', $val);
                    $val = str_replace('&nbsp;', ' ', $val);
                    // Replace
                    $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
                }

                foreach( $rParams as $var => $val )
                {
                    $var = '[' . $var . ']';
                    // Fix nbsp
                    $val = str_replace('&amp;nbsp;', ' ', $val);
                    $val = str_replace('&nbsp;', ' ', $val);
                    // Replace
                    $subjectTemplate = str_replace($var, $val, $subjectTemplate);
                }

                $notificationSettingsTable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');


                foreach( $users as $user )
                {
                    if (!$viewer->isSelf($user)) {
                        if ($notificationSettingsTable->checkEnabledNotification($user, $notificationType)) {
                            $recipientEmail = $user->email;
                            $recipientName = $user->displayname;

                            $mail = $mail_api->create()
                                ->addTo($recipientEmail, $recipientName)
                                ->setFrom($fromAddress, $fromName)
                                ->setSubject($subjectTemplate)
                                ->setBodyText($bodyTextTemplate);
                            $mail_api->sendRaw($mail);
                        }
                    }
                }
            }
        }


        // Remove old confirmation
        session_start();
        unset($_SESSION["confirmation_id"]);
        // Get confirmation
        $selected_confirmation = Engine_Api::_() -> getItem('yndynamicform_confirmation', $this->getRequest()->getParam('selected_confirmation'));
        if ($selected_confirmation instanceof Yndynamicform_Model_Confirmation) {
            $_SESSION["confirmation_id"] = $this->getRequest()->getParam('selected_confirmation');
            if ($selected_confirmation -> type == 'url') {
                $conf_url = $selected_confirmation -> confirmation_url;
                if (strpos($conf_url, 'http://') == -1 && strpos($conf_url, 'https://') == -1)
                    $conf_url = 'http://'.$conf_url;
                header('Location: '. $conf_url);
            } else {
                return $this -> _helper -> redirector -> gotoRoute(array('action' => 'confirmation'), 'yndynamicform_form_general');
            }
        } else {
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'action' => 'view',
                    'entry_id' => $new_entry -> getIdentity()
                ), 'yndynamicform_entry_specific', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
    }

    public function showPopUpEmailAction()
    {
        $this -> _helper -> layout -> setLayout('default-simple');

        $require_email = $this -> _getParam('require_email');
        $this -> view -> form_email = $form_email = new Yndynamicform_Form_Entry_EmailPopUp(array('requireEmail' => $require_email));

        if ($this -> getRequest() -> isPost() && $form_email -> isValid($this -> getRequest() -> getParams())) {
            $values = $form_email -> getValues();
            $this -> view -> closeSmoothbox = true;
            $this -> view -> email = $values['email'];
        }

        // Ouput
        $this -> renderScript('form/email-popup.tpl');
    }

    public function confirmationAction()
    {
        session_start();

        if (!$_SESSION["confirmation_id"]) {
            $this -> _helper -> requireAuth -> forward();
        }

        $this -> _helper -> content -> setEnabled();
        $this -> view -> confirmation = $confirmation = Engine_Api::_() -> getItem('yndynamicform_confirmation', $_SESSION["confirmation_id"]);
    }


}