<?php

namespace humhub\modules\crm\widgets;

use yii\widgets\InputWidget;
use humhub\modules\crm\models\Contact;

/**
 * Widget for selecting contacts with Organization data attributes.
 */
class ContactMultiselectDropdown extends InputWidget

// TODO: ggf. Styling ergänzen zum Anzeigen d. Kontaktperson icons
{
    public $contentContainer;
    public $items = [];
    public $options = [];

    public function run()
    {
        // If no specific items are given, find all contacts from sapce
        if (empty($this->items)) {
            $contacts = Contact::find()
                ->contentContainer($this->contentContainer)
                ->orderBy(['name' => SORT_ASC])
                ->all();

            foreach ($contacts as $contact) {
                $this->items[$contact->id] = $contact->name;

                $orgName = $contact->organization ? $contact->organization->name : '';

                if (!isset($this->options['options'])) {
                    $this->options['options'] = [];
                }
                // get custom Data-Attributes
                $this->options['options'][$contact->id] = ['data-org' => $orgName];
            }
        }

        // Default attributes
        if (!isset($this->options['class'])) {
            $this->options['class'] = 'form-control';
        } else {
            $this->options['class'] .= ' form-control';
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel()
                ? \yii\helpers\Html::getInputId($this->model, $this->attribute)
                : $this->getId();
        }
        $this->options['multiple'] = true;

        if (!isset($this->options['placeholder'])) {
            $this->options['placeholder'] = 'Bitte Kontakte auswählen...';
        }

        return $this->render('contactMultiselectDropdown', [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'items' => $this->items,
            'options' => $this->options
        ]);
    }

}
