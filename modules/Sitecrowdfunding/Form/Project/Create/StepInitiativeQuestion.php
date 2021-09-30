<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Project_Create_StepInitiativeQuestion extends Engine_Form
{

    public $_error = array();

    public function init()
    {

        $this->loadDefaultDecorators();
        $this->setAttrib('id', 'sitecrowdfunding_create_project_step_initiative_question')
            ->getDecorator('Description')->setOption('escape', false);

        $initiative_id = $this->_attribs['initiative_id'];
        $page_type = $this->_attribs['page_type'];
        $project_id = $this->_attribs['project_id'];

        // get questions
        $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsByInitiativeId($initiative_id);
        if (count($initiativeQuestions) > 0) {
            $i = 0;
            foreach ($initiativeQuestions as $key => $initiativeQuestion) {

                $id = $initiativeQuestion['initiativequestion_id'];

                // id
                $this->addElement('Hidden', 'id_' . $id, array(
                    'value' => $id,
                    'order' => $i++,
                ));

                // question
                $this->addElement('Hidden', 'title_' . $id, array(
                    'value' => $initiativeQuestion['initiativequestion_title'],
                    'order' => $i++,
                ));

                // question-hint
                $this->addElement('Hidden', 'hint_' . $id, array(
                    'value' => $initiativeQuestion['initiativequestion_hint'],
                    'order' => $i++,
                ));

                // question-field-type
                $this->addElement('Hidden', 'fieldtype_' . $id, array(
                    'value' => $initiativeQuestion['initiativequestion_fieldtype'],
                    'order' => $i++,
                ));

                // answer
                if ($initiativeQuestion['initiativequestion_fieldtype'] === 'number_box') {

                    $this->addElement('Text', 'answer_' . $id, array(
                        'label' => $initiativeQuestion['initiativequestion_title'],
                        'description' => $initiativeQuestion['initiativequestion_hint'],
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Float', false),
                            array('GreaterThan', false, array(0))
                        ),
                        'order' => $i++,
                        'filters' => array(
                            'StripTags',
                            new Engine_Filter_Censor(),
                        )));

                } elseif ($initiativeQuestion['initiativequestion_fieldtype'] === 'larger_text_box') {

                    $this->addElement('textarea', 'answer_' . $id, array(
                        'label' => $initiativeQuestion['initiativequestion_title'],
                        'description' => $initiativeQuestion['initiativequestion_hint'],
                        'required' => true,
                        'allowEmpty' => false,
                        'attribs' => array('rows' => 5, 'cols' => 180),
                        'filters' => array(
                            'StripTags',
                            new Engine_Filter_HtmlSpecialChars(),
                            new Engine_Filter_EnableLinks(),
                            new Engine_Filter_Censor(),
                        ),
                        'order' => $i++,
                    ));

                } else {
                    $this->addElement('Text', 'answer_' . $id, array(
                        'label' => $initiativeQuestion['initiativequestion_title'],
                        'description' => $initiativeQuestion['initiativequestion_hint'],
                        'required' => true,
                        'allowEmpty' => false,
                        'order' => $i++,
                        'filters' => array(
                            'StripTags',
                            new Engine_Filter_Censor(),
                        )));
                }

            }
        }

        // append the already saved answers in form too
        if(!empty($page_type) && !empty($project_id) && $page_type === 'editInitiativeAnswers' ){
            $initiativeAnswerTable = Engine_Api::_()->getDbtable('initiativeanswers', 'sitecrowdfunding');
            $projectInitiativeAnswers = $initiativeAnswerTable->getProjectInitiativeAnswers($project_id);
            if(count($projectInitiativeAnswers) > 0){
                foreach ($projectInitiativeAnswers as $key => $projectInitiativeAnswer) {

                    $id = $projectInitiativeAnswer['initiativequestion_id'];

                    // id
                    $this->addElement('Hidden', 'id_' . $id, array(
                        'value' => $id,
                        'order' => $i++,
                    ));

                    // question
                    $this->addElement('Hidden', 'title_' . $id, array(
                        'value' => $projectInitiativeAnswer['initiative_question'],
                        'order' => $i++,
                    ));

                    // question-hint
                    $this->addElement('Hidden', 'hint_' . $id, array(
                        'value' => $projectInitiativeAnswer['initiative_question_hint'],
                        'order' => $i++,
                    ));

                    // question-field-type
                    $this->addElement('Hidden', 'fieldtype_' . $id, array(
                        'value' => $projectInitiativeAnswer['initiative_question_fieldtype'],
                        'order' => $i++,
                    ));

                    // answer
                    if ($projectInitiativeAnswer['initiative_question_fieldtype'] === 'number_box') {

                        $this->addElement('Text', 'answer_' . $id, array(
                            'label' => $projectInitiativeAnswer['initiative_question'],
                            'description' => $projectInitiativeAnswer['initiative_question_hint'],
                            'required' => true,
                            'allowEmpty' => false,
                            'validators' => array(
                                array('Float', false),
                                array('GreaterThan', false, array(0))
                            ),
                            'order' => $i++,
                            'filters' => array(
                                'StripTags',
                                new Engine_Filter_Censor(),
                            )));

                    } elseif ($initiativeQuestion['initiativequestion_fieldtype'] === 'larger_text_box') {

                        $this->addElement('textarea', 'answer_' . $id, array(
                            'label' => $projectInitiativeAnswer['initiative_question'],
                            'description' => $projectInitiativeAnswer['initiative_question_hint'],
                            'required' => true,
                            'allowEmpty' => false,
                            'attribs' => array('rows' => 5, 'cols' => 180),
                            'filters' => array(
                                'StripTags',
                                new Engine_Filter_HtmlSpecialChars(),
                                new Engine_Filter_EnableLinks(),
                                new Engine_Filter_Censor(),
                            ),
                            'order' => $i++,
                        ));

                    } else {
                        $this->addElement('Text', 'answer_' . $id, array(
                            'label' => $projectInitiativeAnswer['initiative_question'],
                            'description' => $projectInitiativeAnswer['initiative_question_hint'],
                            'required' => true,
                            'allowEmpty' => false,
                            'order' => $i++,
                            'filters' => array(
                                'StripTags',
                                new Engine_Filter_Censor(),
                            )));
                    }

                }
            }
        }

    }

}
