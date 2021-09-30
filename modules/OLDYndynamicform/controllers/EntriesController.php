<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 4:48 PM
 */
class Yndynamicform_EntriesController extends Core_Controller_Action_Standard
{
    public function init()
    {
        $entry = null;
        $id = $this->_getParam('entry_id', $this->_getParam('id', null));
        $form_id = $this->_getParam('form_id', null);
        $project_id = $this->_getParam('project_id', null);
        $user_id = $this->_getParam('user_id', null);

        if ($id && !$form_id && !$project_id && !$user_id) {
            $entry = Engine_Api::_() -> getItem('yndynamicform_entry', $id);
            if ($entry) {
                Engine_Api::_() -> core() -> setSubject($entry);
                if(!$entry -> owner_id)
                {
                    $ipObj = new Engine_IP();
                    $ipExpr = bin2hex($ipObj->toBinary());
                    $entryIP = bin2hex($entry -> ip);
                    if ($ipExpr == $entryIP)
                        return;
                }
            }
            if (!$this -> _helper -> requireUser() -> isValid()) {
                return;
            }

        }
        if ($form_id) {

            $project_id = $this->_getParam('project_id', null);
            $user_id = $this->_getParam('user_id', null);
            if($project_id && !$user_id){
                $tableEntries = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntryIDByProjectIdAndFormId($form_id, $project_id);
            }

            if(!$project_id && $user_id){
                $tableEntries = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntryIDByUserIdAndFormId($form_id, $user_id);
            }

            if ($tableEntries) {
                $entry = Engine_Api::_()->getItem('yndynamicform_entry', $tableEntries);
                $id= $tableEntries;
                if ($id) {
                    $entry = Engine_Api::_() -> getItem('yndynamicform_entry', $id);
                    if ($entry) {
                        Engine_Api::_() -> core() -> setSubject($entry);
                        if(!$entry -> owner_id)
                        {
                            $ipObj = new Engine_IP();
                            $ipExpr = bin2hex($ipObj->toBinary());
                            $entryIP = bin2hex($entry -> ip);
                            if ($ipExpr == $entryIP)
                                return;
                        }
                    }
                    if (!$this -> _helper -> requireUser() -> isValid()) {
                        return;
                    }
                }



            } else {

                $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);
                Engine_Api::_() -> core() -> setSubject($yndform);
                if (!$this -> _helper -> requireUser() -> isValid()) {
                    return;
                }
            }
        }



    }

    public function manageAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $page = $this -> _getParam('page', 1);
        $this->view->search_form = $searchForm = new Yndynamicform_Form_EntrySearch();

        $values = array();
        if($searchForm -> isValid($this->_getAllParams())) {
            $values = $searchForm -> getValues();
            // search by user
            $values['owner_id'] = $viewer->getIdentity();
        }

        $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');

        $this->view->paginator = $paginator = $entryTable->getEntriesPaginator($values);
        $paginator->setItemCountPerPage(Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yndynamicform.number.entries.per.page', 10));
        $paginator->setCurrentPageNumber($page);

        $this -> view -> params = $values;

        // render
        $this->_helper->content->setEnabled();
    }

    public function listAction()
    {
        // CHECK FOR FORM EXISTENCE
        $id = $this -> _getParam('form_id', null);
        $page_id = $this -> _getParam('page_id', null);


        if( !$id || !$form = Engine_Api::_() -> getItem('yndynamicform_form', $id))
        {
            $this -> _helper -> requireSubject()->forward();
            return;
        }

        // REQUIRE USER PERMISSION
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$form -> isModerator($viewer) && !$viewer->isAdmin()) {
            $this -> _helper -> requireAuth()->forward();
            return;
        }

        // GET PAGE NUMBER FROM PAGINATOR
        $page = $this -> _getParam('page', 1);

        // SEARCH FORM
        $this->view->search_form = $searchForm = new Yndynamicform_Form_EntryModeratorSearch();

        // GET ADVANCED SEARCH FROM PARAMS
        $params = $this->_getAllParams();

        if(!$searchForm -> isValid($params)) {
            return;
        }
        $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');

        $this->view->paginator = $paginator = $entryTable->getEntriesPaginator($params);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yndynamicform.number.entries.per.page', 10));

        $this -> view -> params = $params;

        $this -> view -> yndform = $form;

        // render
        $this->_helper->content->setEnabled();
    }

    public function editAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return;
        }

        $this ->view -> entry = $entry = Engine_Api::_() -> core() -> getSubject();
        $this ->view -> yndform = $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $entry -> form_id);

        if (!$entry -> isViewable()) {
          //  return $this -> _helper -> requireAuth() -> forward();
        }

        if (!$entry -> isEditable()) {
           // return $this -> _helper -> requireAuth() -> forward();
        }

        // Get new entry form
        $topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('yndynamicform_entry');
        if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
            $profileTypeField = $topStructure[0] -> getChild();
        }

        $this -> view -> edit_entry_form = $edit_entry_form = new Yndynamicform_Form_Standard(
            array(
                'item' => $entry,
                'topLevelId' => $profileTypeField -> field_id,
                'topLevelValue' => $yndform -> option_id,
                'mode' => 'create',
            )
        );

        $edit_entry_form -> removeElement('submit_button');





        $valss = (array)$edit_entry_form->getValues();
        foreach ($valss as $key=>$value){ ?>




            <script>
                var newnode = document.createElement("span");                 // Create a <li> node
                newnode.setAttribute("class", "phn_span_element");
                newnode.innerHTML= "<?php echo $edit_entry_form->getElement($key)->getDescription(); ?>";

                document.getElementById("<?php echo $key; ?>-label").appendChild(newnode);

            </script>
            <?php
            $country_code_id = null;
            if( $edit_entry_form->getElement($key)->getType() == 'Fields_Form_Element_Phone') {



                $db = Engine_Db_Table::getDefaultAdapter();
                $db -> beginTransaction();
                $fieldType = $db->select()->from('engine4_yndynamicform_entry_countrycode', '*')->where('entryid = ?', $entry->getIdentity())->where('field_id = ?', $key)->query()->fetchAll();

                 if($fieldType && $fieldType[0]){
                     $country_code_id = $fieldType[0]['country_code_id'];
                 }



                $finalKeyARR = explode("_",$key);
                if(count($finalKeyARR) > 0 ) {
                    $finalKey = $finalKeyARR[count($finalKeyARR) - 1];
                }



                ?>

                <script>

                    var myParent = document.body;
                    var ccnty = '<?php echo $country_code_id;?>';

                    //Create array of options to be added
                    //Create array of options to be added 4
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
                  // let age = '<?php echo $country_code_id;?>';
                  //  console.log('age-----',age);
                   var selectedCountry = array.filter(function(number) {
                       return number.id >= ccnty && number.id <= ccnty;
                   });



                    //Create and append select list
                    var selectList = document.createElement("select");
                    selectList.id = "<?php echo $key; ?>-mySelect";
                    selectList.name = "<?php echo $key; ?>-mySelect";

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

                    if(selectedCountry[0]['country'])
                        document.getElementById("<?php echo $key; ?>-mySelect").value = selectedCountry[0]['country'];


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
            }

        }



            // Get data for conditional logic
        $conditional_params = Engine_Api::_()-> yndynamicform() -> getParamsConditionalLogic($yndform, true);
        $conf_params = Engine_Api::_() -> yndynamicform() -> getConditionalLogicConfirmations($yndform -> getIdentity());
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

        // Render
        $this -> _helper -> content -> setEnabled();

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
                if (count($array_filtered) > $max_file) {
                    $edit_entry_form -> addError('You have input reached maximum allowed files.');
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
                        $edit_entry_form -> addError('File type or extension is not allowed.');
                        return;
                    }
                    if ($max_file_size && $value['size'][$k] > $max_file_size*1024) {
                        $edit_entry_form -> addError($this->view->translate('%s file size exceeds the allowable limit.', $value['name'][$k]));
                        return;
                    }
                }
            }
        }

        // Check post
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        // Check if entries can be edited
        if (!$entry->isEditable()){
            // return;
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
                $ele = $edit_entry_form -> getElement($key);
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
                    $edit_entry_form -> addError('You have input reached maximum allowed files.');
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
                        $edit_entry_form -> addError('File type or extension is not allowed.');
                        return;
                    }
                    if ($max_file_size && $value['size'][$k] > $max_file_size*1024) {
                        $edit_entry_form -> addError($this->view->translate('%s file size exceeds the allowable limit.', $value['name'][$k]));
                        return;
                    }
                }
            }
        }

        if(!$edit_entry_form -> isValid($this -> getRequest() -> getPost())) {
            foreach ($arrayIsValid as $key => $value) {
                if (!$value) {
                    $ele = $edit_entry_form -> getElement($key);
                    if ($ele instanceof Zend_Form_Element)
                        $ele = $ele -> setRequired(true);
                }
            }
            return;
        }

        if($this->getRequest()->isPost() && $edit_entry_form->isValid($this->getRequest()->getPost())) {
            $vals = (array)$edit_entry_form->getValues();
            $fffFlag = true;
            $entryArray=[];
            foreach ($vals as $key=>$value){


                if( $edit_entry_form->getElement($key)->getType() == 'Fields_Form_Element_Phone') {

                    //$phpVar = "<script>document.writeln(jj);</script>";
                    $val = $edit_entry_form->getElement($key)->getValue();
                    $valArr = (explode("-",$val));


                     //2
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
            }
            if($fffFlag == false) {
                return;
            }
        }



        if (isset($values['removed_file'])) {
            $removed_files = Engine_Api::_() -> getItemMulti('storage_file', explode(',', $values['removed_file']));
            foreach ($removed_files as $file)
                $file -> remove();
        }
        // Process to save entry
        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();
        try {
            $entry -> modified_date = date('Y:m:d H:i:s');
            $entry -> save();

            // For update file upload
            if (isset($_FILES)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                foreach ($_FILES as $key => $value) {
                    $array_filtered = array_filter($value['name']);
                    if (empty($array_filtered) || !count($array_filtered)) continue;
                    // Add more new file
                    $elementFile = $edit_entry_form -> getElement($key);
                    $file_ids = $entry -> saveFiles($value);

                    // Get all current files of this field
                    $field_id = explode('_', $key)[2];
                    $map = $mapData -> getRowMatching('child_id', $field_id);
                    $field = $map->getChild();
                    $field_value_item = $field -> getValue($entry);

                    // Update more file to this fields if this field has values
                    $field_value = $field_value_item -> getValue();
                    if (!empty($field_value)) {
                        $field_value = json_decode(html_entity_decode($field_value));

                        // Update value to this field
                        $value['name'] = array_merge($value['name'], $field_value -> name);
                        $value['type'] = array_merge($value['type'], $field_value -> type);
                        $value['size'] = array_merge($value['size'], $field_value -> size);
                        $value['file_ids'] = array_merge($file_ids, $field_value -> file_ids);
                    } else {
                        $value['file_ids'] = $file_ids;
                    }

                    unset($value['tmp_name']);
                    unset($value['error']);
                    $elementFile -> setValue(json_encode($value));
                }
            }


            $edit_entry_form -> setItem($entry);
            $edit_entry_form -> saveValues();




            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            foreach ($entryArray as $val) {


                $fieldType = $db->select()->from('engine4_yndynamicform_entry_countrycode', '*')->where('entryid = ?', $entry->getIdentity())->where('field_id = ?', $val['field_id'])->query()->fetchAll();

               if($fieldType && $fieldType[0]) {


                   // tab on profile
                   $res =  $db->update('engine4_yndynamicform_entry_countrycode', array(
                       'form_id'     => $yndform -> getIdentity(),
                       'field_id'    => $val['field_id'],
                       'country_code_id'    => $val['country_code_id'],

                   ), array(
                    'id = ?'     => $fieldType[0]['id'],
                   ));





               }else {

                   echo 'else --------';
                   // tab on profile
                   $db->insert('engine4_yndynamicform_entry_countrycode', array(
                       'form_id'     => $yndform -> getIdentity(),
                       'field_id'    => $val['field_id'],
                       'country_code_id'    => $val['country_code_id'],
                       'entryid'     => $entry -> getIdentity()

                   ));
               }


            }











            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
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
                    'entry_id' => $entry -> getIdentity()
                ), 'yndynamicform_entry_specific', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
            ));
        }
    }


    public function createAction()
    {




        $form_id =  $this -> _getParam('form_id', null);
        $this->view->project_id = $project_id =  $this -> _getParam('project_id', null);
        $this->view->user_id = $user_id =  $this -> _getParam('user_id', null);
        
        // Validate the current user
        $viewer = Engine_Api::_()->user()->getViewer();
        if( $viewer->getIdentity() ) {
            $tempPageForm = Engine_Api::_()->getItem('yndynamicform_form', $form_id);
            $isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $tempPageForm->page_id);
            $isSiteAdmins = $viewer->isAdmin();
        }
        
        if( !empty($form_id) && !empty($user_id) ) {
            $getUserAssiginedCountByFormId = Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getUserAssiginedCountByFormId($form_id, $user_id);
            if( empty($isPageAdmins) && empty($isSiteAdmins) && empty($getUserAssiginedCountByFormId) )
                return $this->_forward('requireauth', 'error', 'core');
        }
        
        if($project_id && !$user_id){
            $tableEntries = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntryIDByProjectIdAndFormId($form_id, $project_id);
        }
        if(!$project_id && $user_id){
            $tableEntries = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntryIDByUserIdAndFormId($form_id, $user_id);
        }

        $is_saved = $_POST['save_form'];
        $is_reset = $_POST['reset_form'];

        $this->view->flag = $flag = $tableEntries ? 1 : 0;
        if($is_reset == true){
            $db = Engine_Db_Table::getDefaultAdapter();
            if($tableEntries) {
                $db->query("DELETE FROM engine4_yndynamicform_entry_fields_values WHERE item_id = '$tableEntries'");
                $db->query("DELETE FROM engine4_yndynamicform_entries WHERE entry_id = '$tableEntries'");
            }
            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                   // 'route' => 'dynamic-form/entry/67/form_id/:form_id/project_id/:project_id',
                    'action' => 'create',
                    'form_id' => $form_id,
                    'project_id'=>$project_id,
                    'user_id'=>$user_id,
                    'entry_id'=>1
                ), 'yndynamicform_entry_specific', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Reset successfully...'))
            ));




        }


        if(!$tableEntries) {

            // get project_id
            $project_id = $this->_getParam('project_id');
            $user_id = $this->_getParam('user_id');

            // Render
            //$this -> _helper -> content -> setEnabled();

            if (!$this->_helper->requireSubject('yndynamicform_form')->isValid()) return;

            $viewer = Engine_Api::_()->user()->getViewer();
            $yndform = Engine_Api::_()->core()->getSubject();

            if (!Engine_Api::_()->authorization()->isAllowed($yndform, $viewer, 'submission')) {
              //  $this->view->error = true;
             //   $this->view->message = 'You do not have permission to submit this form.';
             //   return;
            }

            //access restriction to all user
          //  if (!$yndform->isViewable()) {
           ////     $this->_helper->requireSubject->forward();
         //   }

            if (!$yndform->isReachedMaximumFormsByLevel()) {
                $this->view->error = true;
                $this->view->message = 'Number of your submitted forms is maximum. Please try again later or delete some entries for submitting new.';
                return;
            }

            // Increase view count
            $yndform->view_count += 1;
            $yndform->save();

            // Get new entry form
            $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('yndynamicform_entry');
            if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                $profileTypeField = $topStructure[0]->getChild();
            }

           //previous submit/save form
            $db = Engine_Db_Table::getDefaultAdapter();
            if($project_id && !$user_id){
                $fieldMapArray =  $db->select()
                    ->from('engine4_yndynamicform_entries')
                    ->where('project_id = ?', $project_id)
                    ->where('submission_status = ?', 'submitted')
                    ->order('entry_id DESC')
                    ->limit()
                    ->query()
                    ->fetchAll();
            }
            if(!$project_id && $user_id){
                $fieldMapArray =  $db->select()
                    ->from('engine4_yndynamicform_entries')
                    ->where('user_id = ?', $user_id)
                    ->where('submission_status = ?', 'submitted')
                    ->order('entry_id DESC')
                    ->limit()
                    ->query()
                    ->fetchAll();
            }

            $arrLabel = array();
            $item_id = null;
            if(count($fieldMapArray) > 0 ) {
              //print_r($fieldMapArray[0]['entry_id']);
              $item_id = $fieldMapArray[0]['entry_id'];
              if($item_id) {
                  $db = Engine_Db_Table::getDefaultAdapter();
                  $fieldsRes =  $db->select()
                      ->from('engine4_yndynamicform_entry_fields_values')
                      ->where('item_id = ?', $item_id)
                      ->limit()
                      ->query()
                      ->fetchAll();

                  foreach ($fieldsRes as $res) {

                      $db = Engine_Db_Table::getDefaultAdapter();
                      $fieldsMetaRes =  $db->select()
                          ->from('engine4_yndynamicform_entry_fields_meta')
                          ->where('field_id = ?', $res['field_id'])
                          ->limit()
                          ->query()
                          ->fetchAll();

                      array_push($arrLabel,array('field_label'=>$fieldsMetaRes[0]['label'],'field_value'=>$res['value']));
                    //  array_push($arrLabel,$fieldsMetaRes[0]['label']);
                  }
                  $this->view->new_entry_form = $new_entry_form = new Yndynamicform_Form_Standard(array(
                      'item' => new Yndynamicform_Model_Entry(array()),
                      'topLevelId' => $profileTypeField->field_id,
                      'topLevelValue' => $yndform->option_id,
                      'mode' => 'create',
                  ));
                  // current form
                  $val = (array)$new_entry_form->getValues();
                  foreach ($val as $key=>$value){
                      ?>
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
                              //Create array of options to be added 3
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
                      }
















                      $labelss = str_replace("#540","'",$new_entry_form->getElement($key)->getLabel());
                      $new_entry_form->getElement($key)->setLabel($labelss);


                    $finalKeyARR = explode("_",$key);
                    $finalKey = $finalKeyARR[count($finalKeyARR) - 1];


                    ?>
                    <!--  3rd phn code intefgration  -->

                      <?php

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
                          if(isset($config->min_value) && $config->min_value > 0){
                              $min_value = $config->min_value;
                              $labelss = $labelss . ' ( Minimum: '. $min_value.')';
                          }
                          if(isset($config->max_value) && $config->max_value > 0){
                              $max_value = $config->max_value;
                              $labelss = $labelss . ' ( Maximum: '. $max_value.')';
                          }
                          if(isset($config->default_value)){
                              $default_value = $config->default_value;
                              $new_entry_form->getElement($key)->setValue($default_value);
                          }
                          $new_entry_form->getElement($key)->setLabel($labelss);
                      }

                      $tempval = json_decode($fieldsLabel[0]['cloned_parent_field_mapping']);
                   //   print_r($fieldsLabel[0]['enable_prepopulate']);
                      if(count($tempval) > 0 && $fieldsLabel[0]['enable_prepopulate'] ) {
                          $fieldsValue =  $db->select()
                              ->from('engine4_yndynamicform_entry_fields_values')
                              ->where('field_id = ?', $tempval->parent_field_id)
                              ->where('item_id = ?', $item_id)
                              ->query()
                              ->fetchAll();
                          $fieldsValueARR =  $fieldsValue;
                          for($i=0; $i< count($fieldsValueARR) ; $i++) {

                              if($fieldsLabel[0]['type'] == 'multi_checkbox' || $fieldsLabel[0]['type'] == 'radio'
                                  || $fieldsLabel[0]['type'] == 'gender' || $fieldsLabel[0]['type'] == 'select'
                                  || $fieldsLabel[0]['type'] == 'multiselect') {

                                  //  fields which have optional fields

                                  $fieldsOptionValue =  $db->select()
                                      ->from('engine4_yndynamicform_entry_fields_options')
                                      ->where('option_id = ?',$fieldsValueARR[$i]['value'])
                                      ->where('field_id = ?', $fieldsValueARR[$i]['field_id'])
                                      ->limit()
                                      ->query()
                                      ->fetchAll();

                                  if(count($fieldsOptionValue) > 0 && $fieldsValueARR[$i]['value']) {


                                      $optionlists = $new_entry_form->getElement($key)->getAttribs();
                                      foreach ($optionlists['options'] as $keyss => $optionlist) {

                                          if($fieldsOptionValue[0]['label'] == $optionlist) {

                                              $temKey = $key.'-'.$keyss;
                                              if($fieldsLabel[0]['type'] == 'multi_checkbox' || $fieldsLabel[0]['type'] == 'radio') {
                                                  echo  '<script> document.getElementById("'.$temKey.'").checked= true;</script>';
                                              }

                                              if($fieldsLabel[0]['type'] == 'gender' || $fieldsLabel[0]['type'] == 'select' || $fieldsLabel[0]['type'] == 'multiselect') {
                                                  //     echo $key.'--';   print_r($fieldsValueARR[$i]['value']); echo '---'.$fieldsLabel[0]['type']; echo '<br>';
                                                  $datass = $fieldsOptionValue[0]['label'];
                                                  echo  "<script>                          
                                                    var dd = document.getElementById('".$key."');
                                                    for (var i = 0; i < dd.options.length; i++) {
                                                   
                                                        if (dd.options[i].text === '".$datass."') {
                                                         
                                                          document.getElementById('".$key."').getElementsByTagName('option')[i].selected = 'selected';
                                                        }
                                                    }
                                                </script>";
                                              }
                                          }


                                          //  $new_entry_form->getElement('1_387_754-394')->setValue(true);


                                      }


                                  }
                              }
                              else {

                                  // date and other fields which do not have optional fields
                                  if($fieldsLabel[0]['type'] == 'date') {
                                      $dateval = $fieldsValueARR[$i]['value'];
                                      echo  '<script> document.getElementById("'.$key.'").value="'.$dateval.'";</script>';
                                  }
                                  if($fieldsLabel[0]['type'] == 'agreement'){
                                      //  document.getElementById('1_466_829').checked=false;
                                      $ttempval = $fieldsValueARR[$i]['value'] == 'on' ? true: false;
                                      echo  '<script> document.getElementById("'.$key.'").checked="'.$ttempval.'";</script>';
                                  }
                                  else {
                                      //  echo $key.'--';   print_r($fieldsValueARR[$i]['value']); echo '---'.$fieldsLabel[0]['type']; echo '<br>';
                                      $new_entry_form->getElement($key)->setValue($fieldsValueARR[$i]['value']);
                                  }
                              }



                              //   for ( $x=0; $x< count($arrLabel) ; $x++) {
                              //old code
                              // if($label == $valLabel['field_label']) {
                              //   $new_entry_form->getElement($key)->setValue($valLabel['field_value']);
                              //}
                              //new code


                              //   }
                          }




                      }




                  }
              }

          }
          else {
              $this->view->new_entry_form = $new_entry_form = new Yndynamicform_Form_Standard(array(
                  'item' => new Yndynamicform_Model_Entry(array()),
                  'topLevelId' => $profileTypeField->field_id,
                  'topLevelValue' => $yndform->option_id,
                  'mode' => 'create',
              ));
              $val = (array)$new_entry_form->getValues();
              foreach ($val as $key=>$value){
                ?>
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
                          //Create array of options to be added 2
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


                $finalKeyARR = explode("_",$key);
                $finalKey = $finalKeyARR[count($finalKeyARR) - 1];


                ?>
               <!-- 2nd phn code intefgration  -->





                  <?php


                  // set min & max label for number field
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
                      if(isset($config->min_value) && $config->min_value > 0){
                          $min_value = $config->min_value;
                          $labelss = $labelss . ' ( Minimum: '. $min_value.')';
                      }
                      if(isset($config->max_value) && $config->max_value > 0){
                          $max_value = $config->max_value;
                          $labelss = $labelss . ' ( Maximum: '. $max_value.')';
                      }
                      if(isset($config->default_value)){
                          $default_value = $config->default_value;
                          $new_entry_form->getElement($key)->setValue($default_value);
                      }
                      $new_entry_form->getElement($key)->setLabel($labelss);
                  }

              }
          }







            // add save button
            $new_entry_form->addElement('Button', 'save_button', array(
                'label' => 'Save',
                'type' => 'button',
                'id' => 'save_button',
                'class' => 'yndform_button_save',
                'order' => 10001,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
            $new_entry_form->addElement('Hidden', 'save_form', array(
                'value' => false,
            ));


            if (!$yndform->isSubmittable()) {
                $new_entry_form->removeElement('submit_button');
            }

            // Get data for conditional logic
            $conditional_params = Engine_Api::_()->yndynamicform()->getParamsConditionalLogic($yndform, true);
            $conf_params = Engine_Api::_()->yndynamicform()->getConditionalLogicConfirmations($yndform->getIdentity());
            $noti_params = Engine_Api::_()->yndynamicform()->getConditionalLogicNotifications($yndform->getIdentity());
            $this->view->prefix = '1_' . $yndform->option_id . '_';
            $this->view->form = $yndform;
            $this->view->fieldsValues = $conditional_params['arrConditionalLogic'];
            $this->view->fieldIds = $conditional_params['arrFieldIds'];
            $this->view->totalPageBreak = $conditional_params['pageBreak'];
            $this->view->arrErrorMessage = $conditional_params['arrErrorMessage'];
            $this->view->pageBreakConfigs = $yndform->page_break_config;
            $this->view->doCheckConditionalLogic = true;
            $this->view->viewer = $viewer;
            $this->view->confConditionalLogic = $conf_params['confConditionalLogic'];
            $this->view->confOrder = $conf_params['confOrder'];
            $this->view->notiConditionalLogic = $noti_params['notiConditionalLogic'];
            $this->view->notiOrder = $noti_params['notiOrder'];

            // Check post
            if (!$this->getRequest()->isPost()) {
                return;
            }



            /*
             * Cheat: Because some field are not valid conditional logic but admin config they are required.
             *          So we need to ignore them when validate form.
             *          arrayIsValid is very important to make this work. So if some one change it in front-end
             *          will make this not work or work not correctly.
             *        If they are not valid conditional logic we will clear value of them.
             */

            $arrayIsValid = $this->getRequest()->getParam('arrayIsValid');
            $arrayIsValid = json_decode($arrayIsValid);

            foreach ($arrayIsValid as $key => $value) {
                if (!$value) {
                    $ele = $new_entry_form->getElement($key);
                    if ($ele instanceof Zend_Form_Element)
                        if ($ele->isRequired()) {
                            $ele = $ele->setRequired(false);
                            $ele = $ele->setValue('');
                        }
                }
            }

            // Validate file upload
            if (isset($_FILES) && $viewer->getIdentity()) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                foreach ($_FILES as $key => $value) {
                    $array_filtered = array_filter($value['name']);
                    if (empty($array_filtered) || !count($array_filtered)) continue;

                    // Validate file extension
                    $field_id = explode('_', $key)[2];
                    $map = $mapData->getRowMatching('child_id', $field_id);
                    $field = $map->getChild();

                    $max_file = $field->config['max_file'];
                    if ($max_file && count($array_filtered) > $max_file) {
                        $new_entry_form->addError('You have input reached maximum allowed files.');
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
                        if (!in_array($ext, $allowed_extension)) {
                            $new_entry_form->addError('File type or extension is not allowed.');
                            return;
                        }
                        if ($max_file_size && $value['size'][$k] > $max_file_size * 1024) {
                            $new_entry_form->addError($this->view->translate('%s file size exceeds the allowable limit.', $value['name'][$k]));
                            return;
                        }
                    }
                }
            }

            // check the number fields and its values
            if($this->getRequest()->isPost() && $new_entry_form->isValid($this->getRequest()->getPost())) {
                $fffFlag = true;
                $val = (array)$new_entry_form->getValues();
                $entryArray=[];
                foreach ($val as $key=>$value){


                    ?>
                    <script>

                        document.getElementById('<?php echo $key.'-mySelect'; ?>').value = '<?php echo $_POST[$key.'-mySelect']; ?>';
                    </script>
                    <?php




                    if( $new_entry_form->getElement($key)->getType() == 'Fields_Form_Element_Phone') {


                        ?>
                        <script>

                            let selectedVal =  '<?php echo $_POST[$key.'-mySelect']; ?>';
                            document.getElementById('<?php echo $key.'-mySelect'; ?>').value = $_POST[$key.'-mySelect'];

                        </script>
                        <?php



                        //$phpVar = "<script>document.writeln(jj);</script>";
                        $val = $new_entry_form->getElement($key)->getValue();
                        $valArr = (explode("-",$val));

                       //1

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

            if (!$new_entry_form->isValid($this->getRequest()->getPost())) {
                foreach ($arrayIsValid as $key => $value) {
                    if (!$value) {
                        $ele = $new_entry_form->getElement($key);
                        if ($ele instanceof Zend_Form_Element)
                            $ele = $ele->setRequired(true);
                    }
                }
                return;
            }

            $tableEntries = Engine_Api::_()->getDbTable('entries', 'yndynamicform');

            // Process to save entry
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $new_entry = $tableEntries->createRow();
                $new_entry->form_id = $yndform->getIdentity();
                if (!$viewer->getIdentity()) {
                    $ipObj = new Engine_IP();
                    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
                    $new_entry->ip = $ipExpr;
                    $new_entry->user_email = $this->getRequest()->getParam('email_guest');
                }
                $new_entry->project_id = $project_id;
                $new_entry->user_id = $user_id;
                // just save only
                if( isset($_REQUEST['submission_status']) && !empty($_REQUEST['submission_status']) ) {
                    $new_entry->submission_status = $_REQUEST['submission_status'];
                }else {
                    if ($is_saved == true) {
                        $new_entry->submission_status = 'draft';
                    } else {
                        $new_entry->submission_status = 'submitted';
                    }   
                }

                $new_entry->owner_id = $viewer->getIdentity();
                $new_entry->save();

                foreach ($entryArray as $val) {

                    // tab on profile
                    $db->insert('engine4_yndynamicform_entry_countrycode', array(
                        'form_id'     => $yndform -> getIdentity(),
                        'field_id'    => $val['field_id'],
                        'country_code_id'    => $val['country_code_id'],
                        'entryid'     => $new_entry -> getIdentity()

                    ));
                }



                // just save only
                if ($is_saved == true) {

                } else {
                    $yndform->total_entries++;
                }
                $yndform->save();

                if (isset($_FILES) && $viewer->getIdentity()) {
                    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                    foreach ($_FILES as $key => $value) {
                        $array_filtered = array_filter($value['name']);
                        if (empty($array_filtered) || !count($array_filtered)) continue;

                        // Validate file extension
                        $field_id = explode('_', $key)[2];
                        $map = $mapData->getRowMatching('child_id', $field_id);
                        $field = $map->getChild();

                        $elementFile = $new_entry_form->getElement($key);
                        $file_ids = $new_entry->saveFiles($value);
                        unset($value['tmp_name']);
                        unset($value['error']);
                        $value['file_ids'] = $file_ids;
                        $elementFile->setValue(json_encode($value));
                    }
                }

                $new_entry_form->setItem($new_entry);
                $new_entry_form->saveValues();

                // save metrics value in activity
                if($this->getRequest()->isPost() && $new_entry_form->isValid($this->getRequest()->getPost())) {
                    $val = (array)$new_entry_form->getValues();
                    foreach ($val as $key=>$value){

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

                        if($fieldsLabel[0]['type'] == 'metrics'){

                            $config = json_decode($fieldsLabel[0]['config']);
                            $metric_id = $config->selected_metric_id;

                            if(!empty($metric_id)){

                                $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);
                                $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

                                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $metric, 'post', '', array('form_id' => $form_id, 'metric_id' => $metric_id ,'metric_value' => $valueSaved));
                                if( $action != null ) {
                                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $metric);
                                }
                            }
                        }
                    }
                }

                // Auth
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($new_entry, 'owner', 'view', 1);

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // Send notifications
            $moderators = $yndform->getAllModeratorsID();
            $supperAdmins = $yndform->getSuperAdminsID();
            $user_ids = array_merge($moderators, $supperAdmins);
            $user_ids = array_unique($user_ids);

            if (count($user_ids) > 0) {
                // Prepare params send notification
                $users = Engine_Api::_()->getItemMulti('user', $user_ids);
                if ($viewer->getIdentity()) {
                    $notificationType = 'yndynamicform_user_submitted';
                } else {
                    $notificationType = 'yndynamicform_anonymous_submitted';
                }

                $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');

                foreach ($users as $user) {
                    if (!$viewer->isSelf($user)) {
                    //    $notificationTable->addNotification($user, $viewer, $yndform, $notificationType);
                    }
                }

                // Get notification email
                $selected_notification = Engine_Api::_()->getItem('yndynamicform_notification', $this->getRequest()->getParam('selected_notification'));
                if ($selected_notification && $selected_notification instanceof Yndynamicform_Model_Notification) {
                    // Prepare params send email notification
                    $mail_api = Engine_Api::_()->getApi('mail', 'core');
                    $fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
                    $fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');
                    $subjectTemplate = $selected_notification->notification_email_subject;
                    $bodyTextTemplate = $selected_notification->notification_email_body;

                    $rParams['website_name'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'My Communication');
                    $rParams['website_link'] = $this->view->baseUrl();
                    $rParams['form_name'] = $yndform->title;
                    $rParams['form_link'] = $yndform->getHref();

                    foreach ($rParams as $var => $val) {
                        $var = '[' . $var . ']';
                        // Fix nbsp
                        $val = str_replace('&amp;nbsp;', ' ', $val);
                        $val = str_replace('&nbsp;', ' ', $val);
                        // Replace
                        $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
                    }

                    foreach ($rParams as $var => $val) {
                        $var = '[' . $var . ']';
                        // Fix nbsp
                        $val = str_replace('&amp;nbsp;', ' ', $val);
                        $val = str_replace('&nbsp;', ' ', $val);
                        // Replace
                        $subjectTemplate = str_replace($var, $val, $subjectTemplate);
                    }

                    $notificationSettingsTable = Engine_Api::_()->getDbtable('notificationSettings', 'activity');


                    foreach ($users as $user) {
//                        if (!$viewer->isSelf($user)) {
//                            if ($notificationSettingsTable->checkEnabledNotification($user, $notificationType)) {
//                                $recipientEmail = $user->email;
//                                $recipientName = $user->displayname;
//
//                                $mail = $mail_api->create()
//                                    ->addTo($recipientEmail, $recipientName)
//                                    ->setFrom($fromAddress, $fromName)
//                                    ->setSubject($subjectTemplate)
//                                    ->setBodyText($bodyTextTemplate);
//                                $mail_api->sendRaw($mail);
//                            }
//                        }
                    }
                }
            }


            // Remove old confirmation
            session_start();
            unset($_SESSION["confirmation_id"]);
            // Get confirmation
            $selected_confirmation = Engine_Api::_()->getItem('yndynamicform_confirmation', $this->getRequest()->getParam('selected_confirmation'));
            if ($selected_confirmation instanceof Yndynamicform_Model_Confirmation) {
                $_SESSION["confirmation_id"] = $this->getRequest()->getParam('selected_confirmation');
                if ($selected_confirmation->type == 'url') {
                    $conf_url = $selected_confirmation->confirmation_url;
                    if (strpos($conf_url, 'http://') == -1 && strpos($conf_url, 'https://') == -1)
                        $conf_url = 'http://' . $conf_url;
                    header('Location: ' . $conf_url);
                } else {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'confirmation'), 'yndynamicform_form_general');
                }
            } else {
                /* return $this->_forward('success', 'utility', 'core', array(
                   'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                       'action' => 'view',
                       'entry_id' => $new_entry->getIdentity()
                   ), 'yndynamicform_entry_specific', true),
                   'messages' => array(Zend_Registry::get('Zend_Translate')->_('Please wait...'))
               ));*/
            }
            if($is_saved) {

                $msg= 'Saved successfully...';
                return $this -> _forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                        // 'route' => 'dynamic-form/entry/67/form_id/:form_id/project_id/:project_id',
                        'action' => 'create',
                        'form_id' => $form_id,
                        'project_id'=>$project_id,
                        'user_id'=>$user_id,
                        'entry_id'=>1
                    ), 'yndynamicform_entry_specific', true),
                    'messages' => array(Zend_Registry::get('Zend_Translate') -> _($msg))
                ));
            }else {

                $msg='Submitted successfully...';

                if($project_id && !$user_id){
                    return $this -> _forward('success', 'utility', 'core', array(
                        'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                            //  'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries',
                            'action' =>'view',
                            'type'=>'project',
                            'id'=>$project_id,
                            'entry_id' => $new_entry->getIdentity()
                        ), 'yndynamicform_entry_specific', true),
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _($msg))
                    ));
                }

                if(!$project_id && $user_id){
                    return $this -> _forward('success', 'utility', 'core', array(
                        'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                            //  'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries',
                            'action' =>'view',
                            'type'=>'user',
                            'id'=>$user_id,
                            'entry_id' => $new_entry->getIdentity()
                        ), 'yndynamicform_entry_specific', true),
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _($msg))
                    ));
                }

            }



        }

        else {
            //else part

            $viewer = Engine_Api::_()->user()->getViewer();
            if (!Engine_Api::_()->core()->hasSubject()) {
                return;
            }

            $this->view->entry = $entry = Engine_Api::_()->core()->getSubject();
            //  Engine_Api::_() -> core() -> setSubject($entry);
            $this->view->yndform = $yndform = Engine_Api::_()->getItem('yndynamicform_form', $entry->form_id);

            //if (!$entry->isViewable()) {
               // return $this->_helper->requireAuth()->forward();
           // }

            //any one can create/edit/submit form
           // if (!$entry->isEditable()) {
            ///    return $this->_helper->requireAuth()->forward();
          //  }

            // Get new entry form
            $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('yndynamicform_entry');
            if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                $profileTypeField = $topStructure[0]->getChild();
            }

            $this->view->edit_entry_form = $edit_entry_form = new Yndynamicform_Form_Standard(
                array(
                    'item' => $entry,
                    'topLevelId' => $profileTypeField->field_id,
                    'topLevelValue' => $yndform->option_id,
                    'mode' => 'create',
                )
            );

            // set min & max label for number field
            $val = (array)$edit_entry_form->getValues();
            foreach ($val as $key=>$value){

                ?>

                <script>
                    var newnode = document.createElement("span");                 // Create a <li> node
                    newnode.setAttribute("class", "phn_span_element");
                    newnode.innerHTML= "<?php echo $edit_entry_form->getElement($key)->getDescription(); ?>";

                    document.getElementById("<?php echo $key; ?>-label").appendChild(newnode);

                </script>
                <?php
//                  echo "-----------";
                if( $edit_entry_form->getElement($key)->getType() == 'Fields_Form_Element_Phone') {

                    $finalKeyARR = explode("_",$key);

                    $finalKey = $finalKeyARR[count($finalKeyARR) - 1];


                    ?>

                    <script>

                        var myParent = document.body;

                        //Create array of options to be added
                        //Create array of options to be added 1
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
                }




                $labelss = str_replace("#540","'",$edit_entry_form->getElement($key)->getLabel());
                $edit_entry_form->getElement($key)->setLabel($labelss);


                $finalKeyARR = explode("_",$key);
                $finalKey = $finalKeyARR[count($finalKeyARR) - 1];


                ?>

              <!--  1st phn code intefgration  -->
               <?php

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
                    if(isset($config->min_value)  && $config->min_value > 0){
                        $min_value = $config->min_value;
                        $labelss = $labelss . ' ( Minimum: '. $min_value.')';
                    }
                    if(isset($config->max_value)  && $config->max_value > 0){
                        $max_value = $config->max_value;
                        $labelss = $labelss . ' ( Maximum: '. $max_value.')';
                    }
                    if(isset($config->default_value)){
                        $default_value = $config->default_value;
                        $edit_entry_form->getElement($key)->setValue($default_value);
                    }
                    $edit_entry_form->getElement($key)->setLabel($labelss);
                }

            }


            $edit_entry_form->removeElement('submit_button');

            // Get data for conditional logic
            $conditional_params = Engine_Api::_()->yndynamicform()->getParamsConditionalLogic($yndform, true);
            $conf_params = Engine_Api::_()->yndynamicform()->getConditionalLogicConfirmations($yndform->getIdentity());
            $this->view->prefix = '1_' . $yndform->option_id . '_';
            $this->view->form = $yndform;
            $this->view->fieldsValues = $conditional_params['arrConditionalLogic'];
            $this->view->fieldIds = $conditional_params['arrFieldIds'];
            $this->view->totalPageBreak = $conditional_params['pageBreak'];
            $this->view->arrErrorMessage = $conditional_params['arrErrorMessage'];
            $this->view->pageBreakConfigs = $yndform->page_break_config;
            $this->view->doCheckConditionalLogic = true;
            $this->view->viewer = $viewer;
            $this->view->confConditionalLogic = $conf_params['confConditionalLogic'];
            $this->view->confOrder = $conf_params['confOrder'];

            // Render
            //  $this->_helper->content->setEnabled();

            // Validate file upload
            if (isset($_FILES) && $viewer->getIdentity()) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                foreach ($_FILES as $key => $value) {
                    $array_filtered = array_filter($value['name']);
                    if (empty($array_filtered) || !count($array_filtered)) continue;

                    // Validate file extension
                    $field_id = explode('_', $key)[2];
                    $map = $mapData->getRowMatching('child_id', $field_id);
                    $field = $map->getChild();

                    $max_file = $field->config['max_file'];
                    if (count($array_filtered) > $max_file) {
                        $edit_entry_form->addError('You have input reached maximum allowed files.');
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
                        if (!in_array($ext, $allowed_extension)) {
                            $edit_entry_form->addError('File type or extension is not allowed.');
                            return;
                        }
                        if ($max_file_size && $value['size'][$k] > $max_file_size * 1024) {
                            $edit_entry_form->addError($this->view->translate('%s file size exceeds the allowable limit.', $value['name'][$k]));
                            return;
                        }
                    }
                }
            }
            // add submit button
            $edit_entry_form->addElement('Button', 'save_button', array(
                'label' => 'Save',
                'type' => 'button',
                'id' => 'save_button',
                'class' => 'yndform_button_save',
                'onClick' => 'submit_form();',
                'order' => 10002,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
            $edit_entry_form->addElement('Hidden', 'save_form', array(
                'order' => 10007,
                'value' => false,
            ));

            // add reset button
            $edit_entry_form->addElement('Button', 'reset_button', array(
                'label' => 'Reset',
                'type' => 'button',
                'id' => 'reset_button',
                'class' => 'yndform_button_save',
                'order' => 10003,
                'decorators' => array(
                    'ViewHelper'
                )
            ));
            $edit_entry_form->addElement('Hidden', 'reset_form', array(
                'value' => false,
            ));
           // add submit button
            $edit_entry_form->addElement('Button', 'submit_button', array(
                'label' => 'Submit',
                'type' => 'button',
                'id' => 'submit_button',
                'onClick' => 'submit_form();',
                'class' => 'yndform_button_save',
                'order' => 10004,
                'decorators' => array(
                    'ViewHelper'
                )
            ));

            // Check post
            if (!$this->getRequest()->isPost()) {
                return;
            }
            // Check if entries can be edited
           // if (!$entry->isEditable())
             //   return;

            /*
             * Cheat: Because some field are not valid conditional logic but admin config they are required.
             *          So we need to ignore them when validate form.
             *          arrayIsValid is very important to make this work. So if some one change it in front-end
             *          will make this not work or work not correctly.
             *        If they are not valid conditional logic we will clear value of them.
             */
            $arrayIsValid = $this->getRequest()->getParam('arrayIsValid');
            $arrayIsValid = json_decode($arrayIsValid);

            foreach ($arrayIsValid as $key => $value) {
                if (!$value) {
                    $ele = $edit_entry_form->getElement($key);
                    if ($ele instanceof Zend_Form_Element)
                        if ($ele->isRequired()) {
                            $ele = $ele->setRequired(false);
                            $ele = $ele->setValue('');
                        }
                }
            }

            // Validate file upload
            if (isset($_FILES) && $viewer->getIdentity()) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                foreach ($_FILES as $key => $value) {
                    $array_filtered = array_filter($value['name']);
                    if (empty($array_filtered) || !count($array_filtered)) continue;

                    // Validate file extension
                    $field_id = explode('_', $key)[2];
                    $map = $mapData->getRowMatching('child_id', $field_id);
                    $field = $map->getChild();

                    $max_file = $field->config['max_file'];
                    if ($max_file && count($array_filtered) > $max_file) {
                        $edit_entry_form->addError('You have input reached maximum allowed files.');
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
                        if (!in_array($ext, $allowed_extension)) {
                            $edit_entry_form->addError('File type or extension is not allowed.');
                            return;
                        }
                        if ($max_file_size && $value['size'][$k] > $max_file_size * 1024) {
                            $edit_entry_form->addError($this->view->translate('%s file size exceeds the allowable limit.', $value['name'][$k]));
                            return;
                        }
                    }
                }
            }

            // check the number fields and its values
            if($this->getRequest()->isPost() && $edit_entry_form->isValid($this->getRequest()->getPost())) {
                $val = (array)$edit_entry_form->getValues();
                foreach ($val as $key=>$value){

                    $valueSaved = $edit_entry_form->getValue($key);

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
                        if($min_value && $max_value && $valueSaved) {
                            if( !($min_value <= $valueSaved  && $valueSaved <= $max_value) ){
                                $edit_entry_form->addError(
                                    $this->view->translate('%s must be between %s to %s.', $valueSaved,$min_value,$max_value)
                                );
                                return;
                            }
                        }
                        // if anyone filled
                        elseif($min_value && !$max_value && $valueSaved){
                            if( !($min_value <= $valueSaved) ){
                                $edit_entry_form->addError(
                                    $this->view->translate('%s must be greater than %s.', $valueSaved,$min_value,$max_value)
                                );
                                return;
                            }
                        }
                        // if anyone filled
                        elseif (!$min_value && $max_value && $valueSaved){
                            if( !($valueSaved <= $max_value) ){
                                $edit_entry_form->addError(
                                    $this->view->translate('%s must be lesser than %s.', $valueSaved,$max_value)
                                );
                                return;
                            }
                        }

                        // if not passed then set default value
                        if($default_value && ($valueSaved==null || $valueSaved=='')){
                            $edit_entry_form->getElement($key)->setValue($default_value);
                        }

                    }
                }
            }


            if (!$edit_entry_form->isValid($this->getRequest()->getPost())) {
                foreach ($arrayIsValid as $key => $value) {
                    if (!$value) {
                        $ele = $edit_entry_form->getElement($key);
                        if ($ele instanceof Zend_Form_Element)
                            $ele = $ele->setRequired(true);
                    }
                }
                return;
            }

            if (isset($values['removed_file'])) {
                $removed_files = Engine_Api::_()->getItemMulti('storage_file', explode(',', $values['removed_file']));
                foreach ($removed_files as $file)
                    $file->remove();
            }

            // just save only
            if ($is_saved == true) {

            } else {
                $yndform->total_entries++;
                $yndform->save();
            }
            // Process to save entry
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                // just save only
                if ($is_saved == true) {
                    $entry->submission_status = 'draft';
                } else {
                    $entry->submission_status = 'submitted';
                }


                $entry->modified_date = date('Y:m:d H:i:s');
                $entry->save();

                // For update file upload
                if (isset($_FILES)) {
                    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
                    foreach ($_FILES as $key => $value) {
                        $array_filtered = array_filter($value['name']);
                        if (empty($array_filtered) || !count($array_filtered)) continue;
                        // Add more new file
                        $elementFile = $edit_entry_form->getElement($key);
                        $file_ids = $entry->saveFiles($value);

                        // Get all current files of this field
                        $field_id = explode('_', $key)[2];
                        $map = $mapData->getRowMatching('child_id', $field_id);
                        $field = $map->getChild();
                        $field_value_item = $field->getValue($entry);

                        // Update more file to this fields if this field has values
                        $field_value = $field_value_item->getValue();
                        if (!empty($field_value)) {
                            $field_value = json_decode(html_entity_decode($field_value));

                            // Update value to this field
                            $value['name'] = array_merge($value['name'], $field_value->name);
                            $value['type'] = array_merge($value['type'], $field_value->type);
                            $value['size'] = array_merge($value['size'], $field_value->size);
                            $value['file_ids'] = array_merge($file_ids, $field_value->file_ids);
                        } else {
                            $value['file_ids'] = $file_ids;
                        }

                        unset($value['tmp_name']);
                        unset($value['error']);
                        $elementFile->setValue(json_encode($value));
                    }
                }

                $edit_entry_form->setItem($entry);
                $edit_entry_form->saveValues();

                // save metrics value in activity
                if($this->getRequest()->isPost() && $edit_entry_form->isValid($this->getRequest()->getPost())) {
                    $val = (array)$edit_entry_form->getValues();
                    foreach ($val as $key=>$value){

                        $valueSaved = $edit_entry_form->getValue($key);

                        $keyArr = explode("_",$key);
                        $num = $keyArr[count($keyArr) - 1];
                        $db = Engine_Db_Table::getDefaultAdapter();
                        $fieldsLabel =  $db->select()
                            ->from('engine4_yndynamicform_entry_fields_meta')
                            ->where('field_id = ?', $num)
                            ->limit()
                            ->query()
                            ->fetchAll();

                        if($fieldsLabel[0]['type'] == 'metrics'){

                            $config = json_decode($fieldsLabel[0]['config']);
                            $metric_id = $config->selected_metric_id;

                            if(!empty($metric_id)){

                                $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);
                                $form = Engine_Api::_() -> getItem('yndynamicform_form', $form_id);

                                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $form, 'metric_value_submitted', '', array('form_id' => $form_id, 'metric_id' => $metric_id ,'metric_value' => $valueSaved));
                                if( $action != null ) {
                                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $metric);
                                }
                            }
                        }
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // Remove old confirmation
            session_start();
            unset($_SESSION["confirmation_id"]);
            // Get confirmation
            $selected_confirmation = Engine_Api::_()->getItem('yndynamicform_confirmation', $this->getRequest()->getParam('selected_confirmation'));
            if ($selected_confirmation instanceof Yndynamicform_Model_Confirmation) {
                $_SESSION["confirmation_id"] = $this->getRequest()->getParam('selected_confirmation');
                if ($selected_confirmation->type == 'url') {
                    $conf_url = $selected_confirmation->confirmation_url;
                    if (strpos($conf_url, 'http://') == -1 && strpos($conf_url, 'https://') == -1)
                        $conf_url = 'http://' . $conf_url;
                    header('Location: ' . $conf_url);
                } else {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'confirmation'), 'yndynamicform_form_general');
                }
            } else {




            }
            if ($is_saved == true) {
                $msg='Save successfully...';
                return $this -> _forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                        // 'route' => 'dynamic-form/entry/67/form_id/:form_id/project_id/:project_id',
                        'action' => 'create',
                        'form_id' => $form_id,
                        'project_id'=>$project_id,
                        'user_id'=>$user_id,
                        'entry_id'=>1
                    ), 'yndynamicform_entry_specific', true),
                    'messages' => array(Zend_Registry::get('Zend_Translate') -> _($msg))
                ));
            } else {
                $msg='Submitted successfully...';
                if($project_id && !$user_id){
                    return $this -> _forward('success', 'utility', 'core', array(
                        'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                            //'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries',
                            'action' =>'view',
                            'type'=>'project',
                            'id'=>$project_id,
                            'entry_id' => $entry->getIdentity()
                        ), 'yndynamicform_entry_specific', true),
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _($msg))
                    ));
                }
                if(!$project_id && $user_id){
                    return $this -> _forward('success', 'utility', 'core', array(
                        'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                            //'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries',
                            'action' =>'view',
                            'type'=>'user',
                            'id'=>$user_id,
                            'entry_id' => $entry->getIdentity()
                        ), 'yndynamicform_entry_specific', true),
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _($msg))
                    ));
                }
            }

        }


    }

    public function savePdfAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return;
        }

        $this  -> view -> entry = $entry = Engine_Api::_() -> core() -> getSubject();
        $this -> view -> yndform = $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $entry -> form_id);

        if (!$entry -> isViewable()) {
//            $this -> _helper -> requireAuth -> forward();
        }

        //Get Field_View Helper
        $view = Zend_Registry::get('Zend_View');

        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Yndynamicform/View/Helper', 'Yndynamicform_View_Helper');
    }


    public function printAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return;
        }

        $this  -> view -> entry = $entry = Engine_Api::_() -> core() -> getSubject();
        $this -> view -> yndform = $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $entry -> form_id);

        if (!$entry -> isViewable()) {
//            $this -> _helper -> requireAuth -> forward();
        }

        //Get Field_View Helper
        $view = Zend_Registry::get('Zend_View');

        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Yndynamicform/View/Helper', 'Yndynamicform_View_Helper');
    }

    public function viewAction()
    {
        // Check permission
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view ->type =   $type = $this->_getParam('type',null);
        if($type == 'user'){
            $this -> view ->project_id =   $project_id = null;
            $this -> view ->user_id =   $user_id = $this->_getParam('id',null);
        }
        if($type == 'project'){
            $this -> view ->project_id =   $project_id = $this->_getParam('id',null);
            $this -> view ->user_id =   $user_id = null;
        }

        $this->view->is_popup = $this->_getParam('is_popup', 0);

        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return;
        }
        $this -> view -> entry = $entry = Engine_Api::_() -> core() -> getSubject();
        $this -> view -> yndform = $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $entry -> form_id);
        $this -> view ->form_id =  $form_id = $entry -> form_id;
        
        // Set the isSiteAdmin and isPageAdmins variable to verify the login user!
        $this->view->isSiteAdmins = $viewer->isAdmin();
        $this->view->isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $yndform->page_id);
        
        //allow all to view
        //if (!$entry -> isViewable()) {
        //  $this -> _helper -> requireAuth -> forward();
        // }

        $entry->updateView();

        //Get Field_View Helper
        $view = Zend_Registry::get('Zend_View');

        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Yndynamicform/View/Helper', 'Yndynamicform_View_Helper');

        // Render
        $this -> _helper -> content -> setEnabled();

        if (!$this -> getRequest() -> isPost()) {
            return;
        }
    }

    public function downloadAction()
    {
        $file_id = $this -> _getParam('file_id');
        $file = Engine_Api::_() -> storage() -> get($file_id);
        // Get path
        $path = $file->getHref();

        if( $file instanceof Storage_Model_File) {
            // Kill zend's ob
            while( ob_get_level() > 0 ) {
                ob_end_clean();
            }

            header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
            header("Content-Transfer-Encoding: Binary", true);
            header("Content-Type: application/force-download", true);
            header("Content-Type: application/octet-stream", true);
            header("Content-Type: application/download", true);
            header("Content-Description: File Transfer", true);
            header("Content-Length: " . filesize($path), true);
            flush();

            $fp = fopen($path, "r");
            while( !feof($fp) )
            {
                echo fread($fp, 65536);
                flush();
            }
            fclose($fp);
        }

        exit(); // Hm....
    }

    public function deleteAction() {
        // In smoothbox
        $this -> _helper -> layout -> setLayout('admin-simple');
        $id = $this -> _getParam('entry_id');
        $this -> view -> entry_id = $id;
        // Check post
        if ($this -> getRequest() -> isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db -> beginTransaction();

            try {
                $entry = Engine_Api::_() -> getItem('yndynamicform_entry', $id);
                $form = Engine_Api::_() -> getItem('yndynamicform_form', $entry->form_id);
                if ($form && $form->total_entries > 0) {
                    $form->total_entries -= 1;
                    $form->save();
                }
                if ($entry) {
                    Engine_Api::_()->getApi('core', 'fields') -> removeItemValues($entry);
                    $entry -> delete();
                }
                $db -> commit();
            } catch (Exception $e) {
                $db -> rollBack();
                throw $e;
            }

            return $this -> _forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The entry is deleted successfully.'))
            ));
        }

        // Output
        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> renderScript('entries/delete.tpl');
    }

    public function exportCsvAction()
    {
        $form = Engine_Api::_()->getItem('yndynamicform_form',  $this -> _getParam('form_id', 0));
        if (!$form->getIdentity())
            return false;
        $option_id = $form->option_id;
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
        // Get second level fields
        $secondLevelMaps = array();
        $secondLevelFields = array();
        if( !empty($option_id) ) {
            $excludedTypes = array(
                'heading',
                'profile_type',
                'recaptcha',
                'text_editor',
                'html_editor',
                'page_break',
                'section_break',
                'agreement',
            );
            $rawValueTypes = array(
                'checkbox',
                'star_rating',
                'ua_ip_address',
                'ua_browser',
                'ua_browser_version',
                'ua_country',
                'ua_state',
                'ua_city',
                'ua_longitude',
                'ua_latitude',
            );
            $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
            if( !empty($secondLevelMaps) ) {
                foreach( $secondLevelMaps as $map ) {
                    $field = $map->getChild();
                    if (!in_array($field->type, $excludedTypes))
                        $secondLevelFields[$map->child_id] = $field;
                }
            }
        }

        // DATA
        $entryTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform');
        $entries = $entryTable->fetchAll($entryTable->select()->where('form_id = ?', $form->getIdentity()));
        $view = Zend_Registry::get('Zend_View');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Yndynamicform/View/Helper', 'Yndynamicform_View_Helper');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        // Title row
        $out = $view->translate("ID") . ';';
        foreach ($secondLevelFields as $field_id => $field) {
            $out .= $field->label . ';';
        }

        $out = rtrim($out, ';');
        $out .= "\n";

        foreach ($entries as $entry) {
            $out .= '#' . $entry->getIdentity() . ';';
            foreach ($secondLevelFields as $field_id => $field) {
                $value = $field->getValue($entry);
                $helperName = Engine_Api::_()->yndynamicform()->getFieldInfo($field->type, 'helper');
                $tmp = ';';
                if (!$helperName) {
                    continue;
                }
                $helper = $view->getHelper($helperName);
                if (!$helper) {
                    continue;
                }
                if (in_array($field->type, $rawValueTypes)) {
                    $tmp = $value->value;
                } else {
                    $tmp = $helper->$helperName($entry, $field, $value);
                }
                $out .= $tmp . ';';
            }

            $out = rtrim($out, ';');
            $out .= "\n";
        }

        $filename_prefix = 'All_entries';
        $filename = $filename_prefix."_".date("Y-m-d_H-i",time());

        //Generate the CSV file header
        header("Content-type: application/vnd.ms-excel");
        header("Content-Encoding: UTF-8");
        header("Content-type: text/csv; charset=UTF-8");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header("Content-disposition: filename=".$filename.".csv");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        //Print the contents of out to the generated file.
        print $out;

        //Exit the script
        exit;
    }
}
