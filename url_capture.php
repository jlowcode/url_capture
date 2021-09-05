<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// Require the abstract plugin class
require_once COM_FABRIK_FRONTEND . '/models/plugin-form.php';

class PlgFabrik_FormUrl_capture extends PlgFabrik_Form
{
    public function onAfterProcess()
    {
        $formModel = $this->getModel();
        $table = $formModel->getTableName();
        $rowId = $formModel->formData[$table . '___id'];
        $elementName = $this->getElementFieldName();

        if ($elementName) {
            $elementValue = $formModel->formData[$elementName];

            $doAction = true;

            if ($elementValue) {
                if (!$this->captureOption()) {
                    $doAction = false;
                }
            }

            if ($doAction) {
                $elementValue = $this->getUrl();
                $this->updateElement($rowId, $elementName, $elementValue, $formModel->getTableName());
            }
        }
    }

    protected function captureOption() {
        $params = $this->getParams();

        $capture_option = (bool) $params->get('capture_option');

        return $capture_option;
    }

    protected function getElementFieldName() {
        $params = $this->getParams();

        $fieldId = $params->get('campo_field');
        if (!$fieldId) {
            return '';
        }
        $elementModel = FabrikWorker::getPluginManager()->getElementPlugin($fieldId)->element;
        $fieldName = $elementModel->name;

        return $fieldName;
    }

    protected function getUrl() {
        $app = $this->app;
        $baseLink = $app->getDocument()->base;

        $formModel = $this->getModel();
        $formId = $formModel->getId();
        $rowId = $formModel->formData['id'];

        $aux = '/form/' . $formId . '/' . $rowId;
        if (strpos($baseLink, $aux) === false) {
            $baseLink .= $rowId;
        }
        $baseLink = str_replace('/form/', '/details/', $baseLink);

        return $baseLink;
    }

    protected function updateElement($rowId, $name, $value, $table) {
        $obj = array();
        $obj['id'] = $rowId;
        $obj[$name] = $value;
        $obj = (Object) $obj;

        $update = JFactory::getDbo()->updateObject($table, $obj, 'id');

        return $update;
    }
}