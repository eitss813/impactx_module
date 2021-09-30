<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProjectController.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_InitiativeController extends Seaocore_Controller_Action_Standard
{

    protected $_hasPackageEnable;

    public function init()
    {
        //SET THE SUBJECT
        if (0 !== ($project_id = (int)$this->_getParam('project_id')) && null !== ($project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id)) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($project);
            Engine_Api::_()->sitecrowdfunding()->setPaymentFlag($project_id);
        }
        $this->_hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
    }

    // edit initiative answers
    public function editInitiativeAnswersAction()
    {

        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        /****** get project details ****/
        $this->view->project_id = $project_id = $this->_getParam('project_id');
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if (empty($project)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        /**** get initiative details ****/
        // get project-tags
        $projectTags = $project->tags()->getTagMaps();
        $tagString = array();
        foreach ($projectTags as $tagmap) {
            $tagString[] = $tagmap->getTag()->getTitle();
        }

        // get project-organisation page
        $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project_id);
        if (empty($parentOrganization)) {
            $parentOrganization = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding')->getParentOrganization($project_id);
        }

        // if initiative_id not present, then take check by project tags
        if (empty($project->initiative_id)) {

            // if both tags and page_id is not empty only, then get initiative_id
            if (!empty($parentOrganization['page_id']) && count($tagString) > 0) {
                $page_id = $parentOrganization['page_id'];
                // check if initiative_id is there, then go to step-5 else to step-6
                $initiatives = Engine_Api::_()->getDbTable('pages', 'sitecrowdfunding')->getProjectInitiatives($parentOrganization['page_id'], $tagString);
                if (!empty($initiatives)) {
                    if (count($initiatives) > 0) {
                        if (!empty($initiatives[0]['initiative_id'])) {
                            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $initiatives[0]['initiative_id']);
                            $initiative_id = $initiatives[0]['initiative_id'];
                        } else {
                            return $this->_forwardCustom('notfound', 'error', 'core');
                        }
                    } else {
                        return $this->_forwardCustom('notfound', 'error', 'core');
                    }
                } else {
                    return $this->_forwardCustom('notfound', 'error', 'core');
                }
            } else {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }
        } else {
            $initiative = Engine_Api::_()->getItem('sitepage_initiative', $project->initiative_id);
            $initiative_id = $project->initiative_id;
        }

        $this->view->initiative = $initiative;

        // get questions for initiative
        $initiativeQuestions = Engine_Api::_()->getItemTable('sitepage_initiativequestion')->getAllInitiativesQuestionsByInitiativeId($initiative_id);

        // answer table
        $initiativeAnswerTable = Engine_Api::_()->getDbtable('initiativeanswers', 'sitecrowdfunding');
        $projectInitiativeAnswers = $initiativeAnswerTable->getProjectInitiativeAnswers($project_id);

        if(count($initiativeQuestions) > 0 || count($projectInitiativeAnswers) > 0){
            $this->view->showContent = true;
        }else{
            $this->view->showContent = false;
        }

        $this->view->form = $form = new Sitecrowdfunding_Form_Project_Create_StepInitiativeQuestion(array(
            'initiative_id' => $initiative_id,
            'page_type' => 'editInitiativeAnswers',
            'project_id' => $project_id
        ));

        // populate the values based on question and already saved answers
        foreach ($initiativeQuestions as $key => $initiativeQuestion) {

            // get id
            $question_id = $initiativeQuestion['initiativequestion_id'];

            // get answer
            $initiativeAnswer = $initiativeAnswerTable->getInitiativeAnswerRow($project_id, $initiative_id, $question_id);

            // field names
            $idFieldName = 'id_' . $question_id;
            $titleFieldName = 'title_' . $question_id;
            $answerFieldName = 'answer_' . $question_id;
            $hintFieldName = 'hint_' . $question_id;
            $fieldTypeFieldName = 'fieldtype_' . $question_id;

            // check if id there, then based on it, populate value
            if (($idFieldValue = $form->getElement($idFieldName)) && !$idFieldValue->getValue()) {
                $idFieldValue->setValue($question_id);
            }

            // check if question there, then based on it, populate value
            if (($titleFieldValue = $form->getElement($titleFieldName)) && !$titleFieldValue->getValue()) {
                $titleFieldValue->setValue($initiativeAnswer->initiative_question);
            }

            // check if question there, then based on it, populate value
            if (($answerFieldValue = $form->getElement($answerFieldName)) && !$answerFieldValue->getValue()) {
                $answerFieldValue->setValue($initiativeAnswer->initiative_answer);
            }

            // check if question there, then based on it, populate value
            if (($hintFieldValue = $form->getElement($hintFieldName)) && !$hintFieldValue->getValue()) {
                $hintFieldValue->setValue($initiativeAnswer->initiative_question_hint);
            }

            // check if question there, then based on it, populate value
            if (($fieldTypeFieldValue = $form->getElement($fieldTypeFieldName)) && !$fieldTypeFieldValue->getValue()) {
                $fieldTypeFieldValue->setValue($initiativeAnswer->initiative_question_fieldtype);
            }

        }
        foreach ($projectInitiativeAnswers as $key => $projectInitiativeAnswer) {

            // get id
            $question_id = $projectInitiativeAnswer['initiativequestion_id'];

            // field names
            $idFieldName = 'id_' . $question_id;
            $titleFieldName = 'title_' . $question_id;
            $answerFieldName = 'answer_' . $question_id;
            $hintFieldName = 'hint_' . $question_id;
            $fieldTypeFieldName = 'fieldtype_' . $question_id;

            // check if id there, then based on it, populate value
            if (($idFieldValue = $form->getElement($idFieldName)) && $idFieldValue->getValue()) {
                $idFieldValue->setValue($question_id);
            }

            // check if question there, then based on it, populate value
            if (($titleFieldValue = $form->getElement($titleFieldName)) && !$titleFieldValue->getValue()) {
                $titleFieldValue->setValue($projectInitiativeAnswer['initiative_question']);
            }

            // check if question there, then based on it, populate value
            if (($answerFieldValue = $form->getElement($answerFieldName)) && !$answerFieldValue->getValue()) {
                $answerFieldValue->setValue($projectInitiativeAnswer['initiative_answer']);
            }

            // check if question there, then based on it, populate value
            if (($hintFieldValue = $form->getElement($hintFieldName)) && !$hintFieldValue->getValue()) {
                $hintFieldValue->setValue($projectInitiativeAnswer['initiative_question_hint']);
            }

            // check if question there, then based on it, populate value
            if (($fieldTypeFieldValue = $form->getElement($fieldTypeFieldName)) && !$fieldTypeFieldValue->getValue()) {
                $fieldTypeFieldValue->setValue($projectInitiativeAnswer['initiative_question_fieldtype']);
            }

        }


        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values))
            return;

        try {

            // get answers
            foreach ($initiativeQuestions as $key => $initiativeQuestion) {

                $question_id = $initiativeQuestion['initiativequestion_id'];
                $id = $question_id;
                $question = $values['title_' . $question_id];
                $answer = $values['answer_' . $question_id];
                $hint = $values['hint_' . $question_id];
                $questionFieldtype = $values['fieldtype_' . $question_id];

                // get answer
                $initiativeAnswer = $initiativeAnswerTable->getInitiativeAnswerRow($project_id, $initiative_id, $question_id);

                // check if answer is already added or not
                if (empty($initiativeAnswer)) {
                    $tablerow = $initiativeAnswerTable->createRow();
                    $tablerow->initiative_question = $question;
                    $tablerow->initiative_answer = $answer;
                    $tablerow->initiative_id = $initiative_id;
                    $tablerow->initiativequestion_id = $id;
                    $tablerow->initiative_question_hint = $hint;
                    $tablerow->initiative_question_fieldtype = $questionFieldtype;
                    $tablerow->project_id = $project_id;
                    $tablerow->user_id = $viewer_id;
                    $tablerow->save();
                } else {
                    $initiativeanswer = Engine_Api::_()->getItem('sitecrowdfunding_initiativeanswer', $initiativeAnswer->initiativeanswer_id);
                    $initiativeanswer->initiative_question = $question;
                    $initiativeanswer->initiative_answer = $answer;
                    $initiativeanswer->initiative_question_hint = $hint;
                    $initiativeanswer->initiative_question_fieldtype = $questionFieldtype;
                    $initiativeanswer->updated_date = new Zend_Db_Expr('NOW()');
                    $initiativeanswer->save();
                }


            }
            foreach ($projectInitiativeAnswers as $key => $projectInitiativeAnswer) {

                $question_id = $projectInitiativeAnswer['initiativequestion_id'];
                $id = $question_id;
                $question = $values['title_' . $question_id];
                $answer = $values['answer_' . $question_id];
                $hint = $values['hint_' . $question_id];
                $questionFieldtype = $values['fieldtype_' . $question_id];

                // get answer
                $initiativeAnswer = $initiativeAnswerTable->getInitiativeAnswerRow($project_id, $initiative_id, $question_id);

                // check if answer is already added or not
                if (empty($initiativeAnswer)) {
                    $tablerow = $initiativeAnswerTable->createRow();
                    $tablerow->initiative_question = $question;
                    $tablerow->initiative_answer = $answer;
                    $tablerow->initiative_id = $initiative_id;
                    $tablerow->initiativequestion_id = $id;
                    $tablerow->initiative_question_hint = $hint;
                    $tablerow->initiative_question_fieldtype = $questionFieldtype;
                    $tablerow->project_id = $project_id;
                    $tablerow->user_id = $viewer_id;
                    $tablerow->save();
                } else {
                    $initiativeanswer = Engine_Api::_()->getItem('sitecrowdfunding_initiativeanswer', $initiativeAnswer->initiativeanswer_id);
                    $initiativeanswer->initiative_question = $question;
                    $initiativeanswer->initiative_answer = $answer;
                    $initiativeanswer->initiative_question_hint = $hint;
                    $initiativeanswer->initiative_question_fieldtype = $questionFieldtype;
                    $initiativeanswer->updated_date = new Zend_Db_Expr('NOW()');
                    $initiativeanswer->save();
                }


            }

        } catch (Exception $e) {

        }

    }

}
