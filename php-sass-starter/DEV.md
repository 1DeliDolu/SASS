# Entwicklungsplan (DEV)

Diese Datei enthält die Architektur und Aufgaben, die in den nächsten Iterationen des Projekts umgesetzt werden sollen. Ziel ist es, rollenbasierte Autorisierung, Projektmanagement und Benutzer-/Kunden-Workflows zu integrieren.

Rollen und Berechtigungen

- Admin: Verwalten aller Benutzer und Projekte; Zuweisungen, Rollenvergabe, Berichte.
- Mitarbeiter: Sieht/aktualisiert zugewiesene Projekte und eigene Aufgaben.
- Kunde: Sieht eigene Projekte, zugewiesene Mitarbeiter und Status; kann eigene Daten aktualisieren.

Datenmodell (Vorschlag)

- users
  - id, vorname, nachname, mail (unique), passwort, rolle ENUM('admin','mitarbeiter','kunde')
  - erstellt_am, aktualisiert_am, letzter_login
- projects
  - id, name, beschreibung, kunde_id (FK → users.id, rolle='kunde'), status ENUM('neu','in_bearbeitung','abgeschlossen','abgebrochen')
  - erstellt_am, aktualisiert_am
- project_assignments
  - id, project_id (FK → projects.id), mitarbeiter_id (FK → users.id, rolle='mitarbeiter')
  - rolle_im_projekt (z.B. 'verantwortlich','mitglied'), zugewiesen_am, status (optional)

SQL-Vorlagen

- Spalte rolle zur users-Tabelle hinzufügen:
  ALTER TABLE users ADD COLUMN rolle ENUM('admin','mitarbeiter','kunde') NOT NULL DEFAULT 'mitarbeiter' AFTER passwort;
- projects-Tabelle:
  CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  beschreibung TEXT NULL,
  kunde_id INT NULL,
  status ENUM('neu','in_bearbeitung','abgeschlossen','abgebrochen') NOT NULL DEFAULT 'neu',
  erstellt_am DATETIME NOT NULL,
  aktualisiert_am DATETIME NOT NULL,
  CONSTRAINT fk_projects_kunde FOREIGN KEY (kunde_id) REFERENCES users(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
- project_assignments-Tabelle:
  CREATE TABLE project_assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  mitarbeiter_id INT NOT NULL,
  rolle_im_projekt VARCHAR(50) NULL,
  zugewiesen_am DATETIME NOT NULL DEFAULT NOW(),
  CONSTRAINT fk_pa_project FOREIGN KEY (project_id) REFERENCES projects(id),
  CONSTRAINT fk_pa_user FOREIGN KEY (mitarbeiter_id) REFERENCES users(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

Autorisierungsstrategie (ACL)

- Session-basierte Kontrolle von $\_SESSION['user']['rolle'].
- Helfer:
  - requireRole(...rollen) → Andernfalls 403/Weiterleitung.
  - canAccessProject(user, project) → Admin true; Mitarbeiter, wenn zugewiesen; Kunde, wenn eigenes Projekt.
- Schutz auf Router-Ebene; früher Ausstieg im Controller.

Controller und Seiten (Plan)

- AdminController
  - Benutzerliste/Rollenverwaltung (Admin weist zu), Kunden/Mitarbeiter anlegen
  - Projekt-CRUD, Zuweisungsmanagement (Mitarbeiter ↔ Projekt)
- ProjectController
  - index: Sichtbare Projekte je nach Rolle (admin=alle, mitarbeiter=zugewiesene, kunde=eigene)
  - show: Projektdetails (zugewiesene Mitarbeiter, Kunde, Status, Notizen)
  - create/update/delete (Rolle: admin)
  - assign/unassign (Rolle: admin)
- AccountController
  - me: Eigene Seite des Benutzers (eigene Daten und Projekte)
  - update: Daten aktualisieren (bestehender Ablauf wird erweitert)

Ansichten (UI)

- Navbar
  - Rollenbasierte Links: Admin-Panel, Meine Projekte, Kundenprojekte usw.
- Panels
  - Admin-Panel: Tabellen für Benutzer, Projekte, Zuweisungen
  - Mitarbeiter-Panel: „Meine Projekte“-Liste, Filter nach Status
  - Kunden-Panel: „Meine Projekte“ und zugewiesene Mitarbeiter
- Projektseite
  - Übersicht (Kunde, Status), Zuweisungen, Aktivitäten (zukünftig)

Workflows

- Benutzer anlegen
  - Admin legt Benutzer an und weist Rolle zu (Registrierung bevorzugt über Admin-Panel statt Standardformular).
- Projekt anlegen
  - Admin erstellt neues Projekt; verknüpft mit Kunde (Benutzer); weist Mitarbeiter zu.
- Anzeige
  - Mitarbeiter sieht nur zugewiesene Projekte.
  - Kunde sieht nur eigene Projekte und zugewiesene Mitarbeiter.

Sicherheit und Qualität

- Passwort-Policy aktiv (12+ und komplexe Regeln).
- Weiterhin Verwendung von PDO prepared statements.
- CSRF-Schutz: Token zu Formularen hinzufügen (ausstehend).
- Begrenzung der Login-Versuche und Audit-Log (ausstehend).

Roadmap (TODO)

1. DB-Schema-Updates und Seed (Rollen, Beispielbenutzer + Projekte)
2. Modellschicht: ProjectModel, AssignmentModel
3. Controller und Routen: Admin/Project/Account
4. Ansichten: Admin-Panel, Projektliste/-details, rollenbasierte Navbar
5. ACL-Helfer und Controller-Schutz
6. CSRF und grundlegendes Audit-Log
7. UX-Verbesserungen (Filter, Pagination, Suche)

Akzeptanzkriterien (Beispiel)

- Admin eingeloggt: Benutzer/Projekt/Zuweisung CRUD vollständig.
- Mitarbeiter eingeloggt: Nur zugewiesene Projekte werden gelistet; andere Zugriffe gesperrt.
- Kunde eingeloggt: Sieht nur eigene Projekte; sieht zugewiesene Mitarbeiter.
- Alle Formulare sind mit CSRF-Token geschützt (wird ergänzt).

Offene Fragen

- Soll die Rollenauswahl im Registrierungsprozess offen sein oder vom Admin zugewiesen werden?
- Sollen Projektstatus und Workflows (Kanban/Statushistorie) detailliert werden?
- Sollen auf Kundenseite Rechnungs-/Vertragsdaten gespeichert werden?

Technische Hinweise

- Kompatibilität mit PHP 7.4 bleibt erhalten (z.B. strpos).
- Windows-/Linux-Pfadkompatibilität (wie im docs-Modul umgesetzt) als Beispiel für den allgemeinen Ansatz.
- Für Sass wird das `sass:color`-Modul verwendet, neue Styles werden in dieser Struktur geschrieben.

Änderungsprotokoll – 2025-08-29

- Nutzer-Schema bereinigt: users.type entfernt, Code komplett auf rolle umgestellt; automatische Migration im Database::ensureUsersColumns() (DROP COLUMN, falls vorhanden).
- Projekte Milestones: Neue Tabelle project_milestones + Model-Methoden (getMilestones, getNextMilestone, addMilestone, markMilestoneDone).
  - Admin-UI: Projekt-Bearbeiten Seite um Milestone-Bereich (anlegen/abschließen) erweitert.
  - Liste/Detail: Nächster Milestone in Projekten sichtbar; Farbcodierung (grün >7T, gelb ≤7T, orange ≤3T, rot überfällig).
- Seed: Beispiel-Milestones (+10/+20 Tage) pro Projekt, nur rolle genutzt.
- I18n: Einfache Übersetzung TR/DE für Projektstatus (yeni/devam/tamam/iptal → Neu/Läuft/Fertig/Abgebrochen); in Listen, Details und Admin-Select angewendet.
- Tabellen-UI: Einheitlicher moderner Tabellenstil via SASS (%table-base), auf .table und Doku-Tabellen angewendet; Sticky-Header (.table-sticky), Wrapper (.table-wrap), Hover-Zeile und kompakte Variante (.table-compact).
- Projekte/Listen UI: Projekteseite modernisiert; „Termin“-Spalte hinzugefügt, Kunden-E-Mail gekürzt (Tooltip), Aktionsspalte standardisiert.
- Buttons: Im Admin-Projektlisting „Bearbeiten/Löschen“-Buttons im einheitlichen Stil (.btn-action, .btn-edit, .btn-delete), Maße vereinfacht (Bearbeiten schmaler).
- Projektdetail: Im Titel für Admin „Bearbeiten“-Button hinzugefügt (admin_project_edit#edit).
- Admin Benutzer hinzufügen: Separate Seite und Ablauf hinzugefügt.
  - GET /index.php?action=admin_user_new → app/views/admin/users/create.php Formular.
  - POST /index.php?action=admin_user_create → AdminController::userCreate(); UserModel::createUserWithRole(...) zum Hinzufügen.
- Markdown/Dokument: Bildpfad in oku.md korrigiert; Bild nach public/image.png verschoben und als /image.png referenziert.
- Diagnose: Mit scripts/db_inspect.php schneller Check und Übersicht für users/projects/assignments.
