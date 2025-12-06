<?php

namespace humhub\modules\crm\controllers;

use humhub\modules\content\components\ContentContainerController;
use Yii;

class OverviewController extends ContentContainerController
{
    public function actionIndex()
    {
        // MOCK DATA
        $mockInteractions = [
            [
                'id' => 1,
                'title' => 'Gespräche zu Kooperationserweiterung',
                'date' => '2026-01-01 10:00:00',
                'status' => 'PLANNED', // PLANNED, COMPLETED, OVERDUE...
                'channel' => 'Face-to-Face',
                'description' => "Wir müssen Kooperationen anfragen bei folgenden Organisationen:\n- MalocherMannschaftHL\n- HAW Hamburg\nKommt was dazwischen dann bitte kommentieren!",
                'creator' => 'Atilla',
                'contacts' => [
                    ['name' => 'L. Heldin', 'org' => 'HAW Hamburg'],
                    ['name' => 'Kontaktperson ID:12', 'org' => 'Ministerium für Wirtschaft'],
                ],
                'responsible' => ['Atilla']
            ],
            [
                'id' => 2,
                'title' => 'Rücksprache wegen Messe-Stand',
                'date' => '2025-10-19 14:30:00',
                'status' => 'OVERDUE',
                'channel' => 'Telefonat',
                'description' => 'Dringend klären, ob Stromanschluss vorhanden ist.',
                'creator' => 'Max Mustermann',
                'contacts' => [
                    ['name' => 'Thomas van Maloche', 'org' => 'MalocherMannschaftHL'],
                ],
                'responsible' => ['Max Mustermann', 'Atilla']
            ]
        ];

        return $this->render('index', [
            'space' => $this->contentContainer,
            'interactions' => $mockInteractions
        ]);
    }
}
