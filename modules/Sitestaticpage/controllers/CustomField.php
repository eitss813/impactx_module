<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CustomField.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$occupations = array('admn' => 'Administrative / Secretarial',
    'arch' => 'Architecture / Interior design',
    'crea' => 'Artistic / Creative / Performance',
    'educ' => 'Education / Teacher / Professor',
    'mngt' => 'Executive / Management',
    'fash' => 'Fashion / Model / Beauty',
    'fina' => 'Financial / Accounting / Real Estate',
    'labr' => 'Labor / Construction',
    'lawe' => 'Law enforcement / Security / Military',
    'legl' => 'Legal',
    'medi' => 'Medical / Dental / Veterinary / Fitness',
    'nonp' => 'Nonprofit / Volunteer / Activist',
    'poli' => 'Political / Govt / Civil Service / Military',
    'retl' => 'Retail / Food services',
    'retr' => 'Retired',
    'sale' => 'Sales / Marketing',
    'self' => 'Self-Employed / Entrepreneur',
    'stud' => 'Student',
    'tech' => 'Technical / Science / Computers / Engineering',
    'trav' => 'Travel / Hospitality / Transportation',
    'othr' => 'Other profession'
);

$education = array(
    'high_school' => 'High School',
    'some_college' => 'Some College',
    'associates' => 'Associates Degree',
    'bachelors' => 'Bachelors Degree',
    'graduate' => 'Graduate Degree',
    'phd' => 'PhD / Post Doctoral'
);

$relationshipstatus = array(
    'single' => 'Single',
    'relationship' => 'In a Relationship',
    'engaged' => 'Engaged',
    'married' => 'Married',
    'complicated' => 'Its Complicated',
    'open' => 'In an Open Relationship',
    'widow' => 'Widowed'
);

$weight = array(
    'slender' => 'Slender',
    'average' => 'Average',
    'athletic' => 'Athletic',
    'heavy' => 'Heavy',
    'stocky' => 'Stocky',
    'little_fat' => 'A few extra pounds'
);

$religion = array(
    'agnostic' => 'Agnostic',
    'atheist' => 'Atheist',
    'buddhist' => 'Buddhist',
    'taoist' => 'Taoist',
    'catholic' => 'Christian (Catholic)',
    'mormon' => 'Christian (LDS)',
    'protestant' => 'Christian (Protestant)',
    'hindu' => 'Hindu',
    'jewish' => 'Jewish',
    'muslim' => 'Muslim ',
    'spiritual' => 'Spiritual',
    'other' => 'Other'
);

$Ethnicity = array(
    'asian' => 'Asian',
    'black' => 'Black / African descent',
    'hispanic' => 'Latino / Hispanic',
    'pacific' => 'Pacific Islander',
    'white' => 'White / Caucasian',
    'other' => 'Other'
);

$political_views = array(
    'mid' => 'Middle of the Road',
    'far_right' => 'Very Conservative',
    'right' => 'Conservative',
    'left' => 'Liberal',
    'far_left' => 'Very Liberal',
    'anarchy' => 'Non-conformist',
    'libertarian' => 'Libertarian',
    'green' => 'Green',
    'other' => 'Other'
);

$incomesource = array(
    '0' => 'Less than $25,000',
    '25_35' => '$25,001 to $35,000',
    '35_50' => '$35,001 to $50,000',
    '50_75' => '$50,001 to $75,000',
    '75_100' => '$75,001 to $100,000',
    '100_150' => '$100,001 to $150,000',
    '1' => '$150,001'
);
$lookingfor = array(
    'friendship' => 'Friendship',
    'dating' => 'Dating',
    'relationship' => 'A Relationship',
    'networking' => 'Networking'
);
$interestedin = array(
    'men' => 'Men',
    'women' => 'Women'
);
$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$values_form = $values = $_POST;
$form_option_id = $_POST['profile_id'];
$message = null;
$option_value = array();
$final_result = array();
$req_empty_ids = array();
$this->view->success = 0;
$form_data_save = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestaticpage.saveformdata', 0);
$i = 0;

foreach ($values as $key => $value) {
    $parts = explode('_', $key);
    if (count($parts) != 3)
        continue;

    list($parent_id, $option_id, $field_id) = $parts;
    //if ($parts[0] == 1 && $parts[1] == $option_id) {
    //GET FORM LABEL
    $table_option = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'options');
    $table_option_name = $table_option->info('name');

    //GET THE OBJECT OF SITESTATCIPAGE META TABLE
    $table_meta = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'meta');

    // GET THE REQUIRED FIELDS INFORMATION
    $select_meta = $table_meta->select()->where('field_id = ?', $parts[2]);
    $select_meta_result = $select_meta->from($table_meta->info('name'), array('type', 'label', 'field_id', 'required'));
    $result = $table_meta->fetchRow($select_meta_result);

    if (empty($result)) {
        $this->view->error_message = 1;
        return;
    }

    // CHECK IF REQUIRED FIELDS ARE EMPTY
    if (!empty($result['required']) && empty($values[$key])) {
        $req_empty_ids[$key] = $key;
    }

    if ($result->canHaveDependents()) {
        if (is_array($value)) {
            $RESOURCE_TYPE_STRING = "'";
            $RESOURCE_TYPE_STRING .= implode($value, "','");
            $RESOURCE_TYPE_STRING.="'";

            $select_options = $table_option->select();
            $select_options->from($table_option_name, array('label', 'option_id'))
                    ->where($table_option_name . '.option_id IN (' . $RESOURCE_TYPE_STRING . ')');
            $resultAns = $table_option->fetchAll($select_options);
            $final_result[$i] ['answer'] = $resultAns->toarray();
        } else {
            //GET THE OBJECT OF SITEPSITESTATCIPAGE OPTION TABLE
            $select_options = $table_option->select();
            $select_options->from($table_option_name, array('label', 'option_id'))
                    ->where($table_option_name . '.option_id =?', $value);
            $resultAns = $table_option->fetchAll($select_options);
            $final_result[$i] ['answer'] = $resultAns->toarray();
        }
    } else {
        $final_result[$i] ['answer'] = $value;
    }
    $final_result[$i] ['Question'] = $result->toarray();
    if ($result->type == 'Radio') {
        $option_value[] = $value;
    } else {
        $option_value = $value;
    }

    // WORK FOR SAVING USER DATA FILLED IN THE FORMS
    $required_count = count($req_empty_ids);
    if (!empty($form_data_save) && $viewer_id && empty($required_count)) {
        $table_values = Engine_Api::_()->fields()->getTable('sitestaticpage_page', 'values');

        if (is_array($value)) {
            // Lookup
            $values_select = $table_values->select()
                    ->where('field_id =?', $field_id)
                    ->where('item_id =?', $staticpage_id)
                    ->where('member_id=?', $viewer_id)
                    ->where('form_id=?', $form_option_id);
            $valuesRows = $table_values->fetchAll($values_select);

            // Delete all
            foreach ($valuesRows as $valueRow) {
                $valueRow->delete();
            }

            // Insert all
            $indexIndex = 0;
            if (is_array($value) || !empty($value)) {
                if ($result->type == 'date' || $result->type == 'birthdate') {
                    $singleValue = $value['year'] . '-' . $value['month'] . '-' . $value['day'];
                    $valueRow = $table_values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $staticpage_id;
                    $valueRow->member_id = $viewer_id;
                    $valueRow->index = $indexIndex++;
                    $valueRow->value = $singleValue;
                    $valueRow->form_id = $form_option_id;
                    $valueRow->save();
                } else {
                    foreach ((array) $value as $singleValue) {
                        $valueRow = $table_values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $staticpage_id;
                        $valueRow->member_id = $viewer_id;
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->form_id = $form_option_id;
                        $valueRow->save();
                    }
                }
            } else {
                $valueRow = $table_values->createRow();
                $valueRow->field_id = $field_id;
                $valueRow->item_id = $staticpage_id;
                $valueRow->member_id = $viewer_id;
                $valueRow->index = 0;
                $valueRow->value = '';
                $valueRow->form_id = $form_option_id;
                $valueRow->save();
            }
        }

        // Scalar mode
        elseif (!empty($value)) {
            $values_select = $table_values->select()
                    ->where('field_id =?', $field_id)
                    ->where('item_id =?', $staticpage_id)
                    ->where('member_id =?', $viewer_id)
                    ->where('form_id=?', $form_option_id);
            $valueRow = $table_values->fetchRow($values_select);

            if ($valueRow && empty($value)) {
                $valueRow->delete();
            }
            // Create if missing
            $isNew = false;
            if (!$valueRow) {
                $isNew = true;
                $valueRow = $table_values->createRow();
                $valueRow->field_id = $field_id;
                $valueRow->item_id = $staticpage_id;
                $valueRow->member_id = $viewer_id;
                $valueRow->form_id = $form_option_id;
            }

            $valueRow->value = htmlspecialchars($value);
            $valueRow->save();
        }
    }
    //}
    $i++;
}

if(isset($values['g-recaptcha-response']) && empty($values['g-recaptcha-response'])) {
   $req_empty_ids['g-recaptcha-response'] ='grecaptcharesponse';
}

$count = 0;
if (!empty($req_empty_ids)) {
    foreach ($req_empty_ids as $span_id) {
        $count++;
        echo '<span  id="form_field_' . $span_id . '">';
        echo '</span>';
        ?>
        <?php if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')): ?>
            <script type="text/javascript">
                sm4.core.runonce.add(function () {
                    var Field_container = $.mobile.activePage.find('#<?php echo $span_id; ?>-element');
                    var span = $.mobile.activePage.find('#form_field_<?php echo $span_id; ?>');
                    var language1 = '<?php echo Zend_Registry::get('Zend_Translate')->_('*Please complete this field - it is required.'); ?>';
                    span.html(language1);
                    span.css('font', 'italic normal 11px tahoma');
                    span.css('color', 'red');
                    span.css('display', 'block');
                    span.css('clear', 'both');
                    Field_container.append(span);
                });
            </script>
        <?php else: ?>
            <script type="text/javascript">
                var Field_container = $('<?php echo $span_id; ?>-element');
                var span = $('form_field_<?php echo $span_id; ?>');
                var language1 = '<?php echo Zend_Registry::get('Zend_Translate')->_('*Please complete this field - it is required.'); ?>';
                span.innerHTML = language1;
                span.style.font = "italic normal 11px tahoma";
                span.style.color = "red";
                span.style.display = "block";
                span.style.clear = "both";
                span.inject(Field_container);




            </script>
        <?php endif; ?>
        <?php
    }
    ?>

    <?php
}
?>



<?php
if (!empty($count)) {
    $this->view->fields_error_message = 1;
    return;
}
// IF EMAIL IS PROVIDED FOR THE FORM
$db = Engine_Db_Table::getDefaultAdapter();
$email = $db->select()->from('engine4_sitestaticpage_page_fields_options', 'email')
                ->where('option_id = ?', $form_option_id)->limit(1)
                ->query()->fetchColumn();
$form_label = $db->select()->from('engine4_sitestaticpage_page_fields_options', 'label')
                ->where('option_id = ?', $form_option_id)->limit(1)
                ->query()->fetchColumn();

if (empty($email)) {
    $email = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact');
}
if (!empty($email)) {
    $i = 0;
    $Question_ans = null;
    foreach ($final_result as $key2 => $values) {

        $Question_ans .= '<b>' . $values['Question']['label'] . ' : </b>';

        if ($values['Question']['type'] == 'country') {
            $locale = Zend_Registry::get('Zend_Translate')->getLocale();
            $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
            $country = $territories[$values['answer']];
            $Question_ans .= $country;
        } elseif ($values['Question']['type'] == 'occupation') {
            foreach ($occupations as $keys => $occupation) {
                if ($keys == $values['answer']) {
                    $Question_ans .= $occupation;
                }
            }
        } elseif ($values['Question']['type'] == 'education_level') {
            foreach ($education as $keys => $educations) {
                if ($keys == $values['answer']) {
                    $Question_ans .= $educations;
                }
            }
        } elseif ($values['Question']['type'] == 'looking_for') {
            $k = 97;
            $k = chr($k);
            $space = "";
            $count = $values['answer'];
            foreach ($values['answer'] as $keys => $lookingfors) {
                --$count;
                if (!empty($lookingfor[$lookingfors])) {
                    if ($count != 0)
                        $Question_ans .= $lookingfor[$lookingfors] . ", ";
                    else
                        $Question_ans .= $lookingfor[$lookingfors];
                    $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
            }
        } elseif ($values['Question']['type'] == 'partner_gender') {
            $k = 97;
            $k = chr($k);
            $space = "";
            $count = $values['answer'];
            foreach ($values['answer'] as $keys => $interestedins) {
                --$count;
                if (!empty($interestedin[$interestedins])) {
                    if ($count != 0)
                        $Question_ans .= $interestedin[$interestedins] . ", ";
                    else
                        $Question_ans .= $interestedin[$interestedins];
                    $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
            }
        } elseif ($values['Question']['type'] == 'relationship_status') {
            foreach ($relationshipstatus as $keys => $relationship) {
                if ($keys == $values['answer']) {
                    $Question_ans .= $relationship;
                }
            }
        } elseif ($values['Question']['type'] == 'weight') {
            foreach ($weight as $keys => $weighted) {
                if ($keys == $values['answer']) {
                    $Question_ans .= $weighted;
                }
            }
        } elseif ($values['Question']['type'] == 'religion') {
            foreach ($religion as $keys => $religions) {
                if ($keys == $values['answer']) {
                    $Question_ans .= $religions;
                }
            }
        } elseif ($values['Question']['type'] == 'political_views') {
            foreach ($political_views as $keys => $political_view) {
                if ($keys == $values['answer']) {
                    $Question_ans .= $political_view;
                }
            }
        } elseif ($values['Question']['type'] == 'checkbox') {
            if (!empty($values['answer']))
                $Question_ans .= 'Yes';
            else
                $Question_ans .= 'No';
        }
        elseif ($values['Question']['type'] == 'income') {
            foreach ($incomesource as $keys => $income) {
                if ($keys == $values['answer']) {
                    $Question_ans .= $income;
                }
            }
        } elseif ($values['Question']['type'] == 'ethnicity') {
            $k = 97;
            $k = chr($k);
            $space = '';
            $count = $values['answer'];
            foreach ($values['answer'] as $keys => $Ethnicities) {
                --$count;
                if (!empty($Ethnicity[$Ethnicities])) {
                    if ($count != 0)
                        $Question_ans .= $Ethnicity[$Ethnicities] . ", ";
                    else
                        $Question_ans .= $Ethnicity[$Ethnicities];
                    $space = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
            }
        } elseif (isset($values['answer'][0]) && (!empty($values['answer'][0]) && is_array($values['answer'][0]))) {
            $j = 97;
            $j = chr($j);
            $space = '';
            $count = count($values['answer']);
            foreach ($values['answer'] as $k => $value) {
                --$count;
                if ($count > 0)
                    $Question_ans .= $value['label'] . ", ";
                else
                    $Question_ans .= $value['label'];
            }
        } elseif (!is_array($values['answer']) && !empty($values['answer'])) {
            $Question_ans .= $values['answer'];
        } elseif ($values['Question']['type'] == 'birthdate') {
            $date = $values['answer']['year'] . '-' . $values['answer']['month'] . '-' . $values['answer']['day'] . ' 00:00:00';
            if (!empty($values['answer']['year']))
                $Question_ans .= gmdate('M d,Y', strtotime($date));
            else
                $Question_ans .= gmdate('M d', strtotime($date));
        }
        elseif ($values['Question']['type'] == 'date') {
            $date = $values['answer']['year'] . '-' . $values['answer']['month'] . '-' . $values['answer']['day'] . ' 00:00:00';
            if (!empty($values['answer']['year']))
                $Question_ans .= gmdate('M d,Y', strtotime($date));
            else
                $Question_ans .= gmdate('M d', strtotime($date));
        }
        elseif (is_array($values['answer']) && !empty($values['answer'])) {
            $j = 97;
            $j = chr($j);
            $space = '';
            $count = count($values['answer']);
            foreach ($values['answer'] as $k => $value) {
                --$count;
                if ($count > 0)
                    $Question_ans .= $value . ', ';
                else
                    $Question_ans .= $value;
            }
        }
        $Question_ans .= '<br /><br />';
    }

// MAIL SENDING..
    $message .= $Question_ans;
    $page_url = end(explode("/", $_SERVER['HTTP_REFERER']));
    $page_name = $db->select()
            ->from('engine4_sitestaticpage_pages', 'title')
            ->where('page_url = ?', $page_url)
            ->limit(1)
            ->query()
            ->fetchColumn();

    $emailArray = explode(',', $email);
    $emailArray = array_map('trim', $emailArray);
   
    if (!empty($viewer_id)) {
        $content = $viewer['username'] . "    " . '(' . ($viewer['email']) . ')' . '  has submitted the below response for the form:' . "  " . '"' . $form_label . '"' . '  at:' . "  " . '"' . $page_name . '"' . "    " . '(' . $_SERVER['HTTP_REFERER'] . ')';
    } else {
        $content = ' A visitor has submitted the below response for the form:' . "  " . '"' . $form_label . '"' . "   " . 'at:' . "  " . '"' . $page_name . '"' . "   " . '(' . $_SERVER['HTTP_REFERER'] . ')';
    }
    Engine_Api::_()->getApi('mail', 'core')->sendSystem($emailArray, 'SITESTATICPAGE_PROFILEQUESTIONS_EMAIL', array(
        'host' => $_SERVER['HTTP_HOST'],
        'page_name' => $page_name,
        'form_label' => $form_label,
        'content' => $content,
        'heading' => '',
        'message' => $message,
        'object_link' => $_SERVER['HTTP_REFERER'],
        'queue' => false
    ));
}
$this->view->success = 1;
