# Kundenhistorie für HumHub aka. 'CRM-light'

Das Modul erweitert im Rahmen meiner Bachelorarbeit HumHub Spaces um Funktionen für das Beziehungsmanagement zu Organisationen und ihren Mitgliedern (Customer-Relationship-Management - 'CRM'). Es ermöglicht die Erfassung und Pflege von Kontakten, Organisationen, Interaktionen und Veranstaltungen direkt im Space.

## Features

* **Interaktionen:** Erfassen von Kontaktaufnahmen jeglicher Art (Emails, Anrufe, Treffen etc) mit Status als Labels (Geplant, Erledigt, Überfällig, Abgesagt).
    * 2 Views: Kompakte Listen-Ansicht & Akkordion-Liste (aufklappbar)
    * Automatisches Ermitteln & Updaten zu "Überfällig"-Status, sobald eine geplante Interaktion in der Vergangenheit liegt
    * Ampelsystem für visuelles Nutzerfeedback beim Erfassen zum Wahren der Datenqualität durch Nudgen des Users zum Vervollständigen des Interaktions-Datensatzes
    * Notification-System zum Nachholen verpasster-Erfassungen
* **Veranstaltungen (Events):** Planung und Dokumentation von Events inkl. Teilnehmererfassung
* **Organisationen & Kontaktpersonen:** Zentrale Datenbank von Organisationen (wie etwa Firmen, Projekte, Behörden etc.) sowie ihren zugehörigen Kontaktpersonen (z.B. Mitarbeitende, Teammitlieder etc.)
* **Space-Integration:** CRM-Daten space-spezifisch bzw. space-intern 
* **Aktivitäten-Stream:** Integration in sowohl HumHubs globalen, als auch Space-Stream für Transparenz im Team

## ⚙️ Technische Anforderungen

* HumHub >= 1.15
* PHP >= 8.0

## 🔧 Installation & Setup

1.  Modul in den Ordner `protected/modules/crm` kopieren.
2.  In der Administration unter **Module** aktivieren.
3.  In den gewünschten Spaces das Modul aktivieren.

### CronJob
Damit die Con Jobs laufen, z.B. der Status von Interaktionen automatisch von `GEPLANT` auf `ÜBERFÄLLIG` wechselt und am Monatsende Erinnerungen für das Nachtragen mangelhaft erfasster Interaktionen zu versenden, muss der **Standard HumHub CronJob** auf dem Server eingerichtet sein.

Das Modul klinkt sich daraufhin automatisch in den `CronController::EVENT_ON_DAILY_RUN` ein.
