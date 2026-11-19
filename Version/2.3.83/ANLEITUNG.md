# Feuer-Mannschaft Plugin Anleitung

## Übersicht

`Feuer-Mannschaft` ist ein WordPress-Plugin zur Verwaltung von Mannschaftsprofilen.
Die Profile werden als isolierter WordPress-Inhaltstyp `feuer_mannschaft`
gespeichert und über ein eigenes Admin-Menü verwaltet.

Das Plugin ist für Mannschaftsakten gedacht, zum Beispiel für Mitglieder
einer Feuerwehr oder eines Vereins.

## Hauptfunktionen

- Anlegen, Bearbeiten, Archivieren und optionales Entfernen von Profilen
- Speicherung als eigener, gegen globale Beitragsfilter isolierter Inhaltstyp
- Zusätzliche Profilfelder:
  - Vorname
  - Nachname
  - Eintrittsjahr
  - Dienstgrad
  - Funktion
  - Ausbildung
  - Beschreibung
  - Position in der Liste
- Foto-Upload in den Ordner `uploads/feuer-mannschaft`
- Text-Wasserzeichen unten im Bild
- Einzelansicht mit Bild oder Galerie
- Ausgabe über Shortcodes für Listen, Einzelprofile, Kartenblöcke und Zähler
- Automatische Vorlagenerkennung aus `templates/uebersicht` und `templates/profil`
- Archivierte Profile werden im Frontend gesperrt

## Installation

Voraussetzung: WordPress `7.1` oder neuer sowie PHP `8.1` oder neuer. Getestet mit WordPress `7.1.3`.

Bei der Aktivierung erstellt das Plugin automatisch:

- die Standard-Option `feuer_mannschaft_settings`
- den Upload-Ordner `uploads/feuer-mannschaft` fuer Profilbilder
- den geschuetzten Backup-Ordner `uploads/feuer-mannschaft-backups`
- die Log-Tabelle `wp_feuer_mannschaft_logs` mit dem aktuellen Tabellenpraefix der Installation

1. Plugin-Datei `Feuer-Mannschaft.zip` in WordPress hochladen
2. Plugin aktivieren
3. Im Admin-Menü `Feuer-Mannschaft` aufrufen
4. Unter `Einstellungen` die benötigten Optionen speichern

## Admin-Menü

Nach der Aktivierung stehen diese Bereiche zur Verfügung:

- `Feuer-Mannschaft -> Alle Profile`
- `Feuer-Mannschaft -> Neu hinzufügen`
- `Feuer-Mannschaft -> Einstellungen`

## Einstellungen

Im Bereich `Feuer-Mannschaft -> Einstellungen` können aktuell folgende Optionen
verwaltet werden:

- Watermark-Zusatztext
- Freigegebene Kategorien
- Standardvorlage für Listen
- Standardvorlage für Profile
- Bildgröße für Listenansicht
- Bildhöhe in der Profilansicht
- Autoplay des Sliders
- Galerie im Profil aktivieren oder deaktivieren
- wiederholbare Listenfelder für `Dienstgrad`, `Funktion` und `Ausbildung`
- vorbelegte Standardlisten für `Dienstgrad`, `Funktion` und `Ausbildung`, die bei leeren Einstellungen automatisch gesetzt werden
- Shortcode-Beispiele direkt in den Einstellungen

## Vorlagen und Design

Die Frontend-Ausgabe wird über PHP-Dateien in zwei Ordnern gesteuert:

- `templates/uebersicht` für Listen und Übersichten
- `templates/profil` für die Einzelansicht eines Profils

Das Plugin liest die Dateinamen und Template-Namen automatisch ein und stellt
sie in den Einstellungen zur Auswahl bereit.

Mitgelieferte Vorlagen für Übersichten:

- `standard`
- `cards`
- `cards-modal`
- `list`
- `team-grid`

Mitgelieferte Vorlagen für Profile:

- `standard`
- `compact`

## Profile anlegen

1. `Feuer-Mannschaft -> Neu hinzufügen` öffnen
2. Profildaten ausfüllen
   Das Feld zum Eintritt enthält nur das Jahr.
3. Eine Kategorie aus den freigegebenen Kategorien wählen
4. Fotos hochladen
5. Beitrag speichern oder veröffentlichen

Der Titel wird automatisch aus Vorname und Nachname erzeugt.

## Fotos

- Fotos werden in `uploads/feuer-mannschaft` gespeichert
- Das erste Bild wird als Titelbild verwendet
- Mehrere Bilder können als Galerie verwendet werden
- Das Wasserzeichen besteht aus:
  - Vorname und Nachname
  - dem Zusatztext aus den Plugin-Einstellungen

## Archivierung

Profile sollen nicht einfach gelöscht werden, damit die Historie erhalten
bleibt.

- Archivierte Profile bleiben in WordPress erhalten
- Archivierte Profile werden im Frontend nicht mehr ausgeliefert
- Nur die Hauptadministration darf Profile endgültig entfernen

## Kategorien

Das Plugin verwendet die normalen WordPress-Kategorien.

- In den Einstellungen wird festgelegt, welche Kategorien für Profile
  verwendet werden dürfen
- Beim Anlegen eines Profils wird eine dieser Kategorien ausgewählt
- Das Profil erscheint danach als normaler Beitrag in dieser Kategorie

## Frontend-Ausgabe

### Shortcodes

```text
[feuer_mannschaft]
[feuer_mannschaft_liste]
```

Die Standardseite für die komplette Mannschaft wird über `[feuer_mannschaft]`
oder `[feuer_mannschaft_liste]` in eine normale WordPress-Seite eingefügt.
Alle Kurzcodes des Plugins verwenden durchgängig den Präfix `feuer_mannschaft`.

Optional können Kategorie und Vorlagen direkt im Shortcode angegeben werden:

```text
[feuer_mannschaft template="standard" single_template="standard"]
[feuer_mannschaft_liste category="123" template="cards-modal" single_template="compact"]
```

Für die Ausgabe eines einzelnen Profils stehen mehrere Varianten zur Verfügung:

```text
[feuer_mannschaft_profil id="123" template="compact"]
[feuer_mannschaft_profil name="Max Mustermann" template="standard"]
[feuer_mannschaft_profil random="1" template="compact"]
```

Wird ein Einzelprofil über `[feuer_mannschaft_profil]` oder `[feuer_mannschaft_profile]`
auf einer normalen WordPress-Seite eingebunden, verwendet das Plugin dafür
automatisch eine eigene Seitenvorlage ohne Sidebar der Theme-`page.php`.

Für Zähler und Kartenblöcke stehen zusätzliche Shortcodes zur Verfügung:

```text
[feuer_mannschaft_anzahl]
[feuer_mannschaft_anzahl text="Aktive Mitglieder: %d"]
[feuer_mannschaft_karten name="Max Mustermann" template="cards"]
[feuer_mannschaft_karten random="1" count="2" template="cards"]
[feuer_mannschaft_karten random="1" count="4" template="team-grid"]
[feuer_mannschaft_karten random="1" count="8" template="list"]
```

Fuer Verlinkungen auf einzelne Teilnehmer innerhalb normaler Beitraege steht
zusaetzlich dieser Kurzcode zur Verfuegung:

```text
[feuer_mannschaft_person id="123"]
```

Der Kurzcode gibt den Namen des Teilnehmers als Link zur Profilseite aus.

## Einzelansicht

Die Einzelansicht eines Profils zeigt:

- großes Bild oder Galerie
- Profildaten
- Beschreibung
- Navigation zum vorherigen und nächsten Profil
- bis zu 4 weitere Profile unterhalb der Akte

Die Standard-Dateien für die Gestaltung liegen hier:

- `templates/uebersicht/standard.php` für die Mannschaftsseite per Shortcode
- `templates/profil/standard.php` für die einzelne Mannschaftsakte

## Migration

Alte markierte Beiträge und Einträge vom früheren Post Type
`feuer_mannschaft_member` werden idempotent in `feuer_mannschaft` übernommen.
Profil-IDs, Erwähnungen und Medienbeziehungen bleiben dabei erhalten.

## Hinweise

- Frontend-Slider und Modalfenster werden ueber die Plugin-eigenen Assets
  gesteuert
- Die Plugin-Texte und Verwaltungsbereiche sind auf Deutsch gehalten
- Die PHP-Bildfunktionen müssen auf dem Server verfügbar sein, wenn das
  Wasserzeichen direkt in die Bilder geschrieben werden soll
- Das Plugin lädt kein eigenes Bootstrap von externen Quellen
- Das verwendete Theme soll Bootstrap selbst bereitstellen

## Deinstallation

Bei der vollständigen Löschung über WordPress entfernt `uninstall.php` automatisch:

- alle Mannschaftsprofile des Plugins
- alte Eintraege vom Typ `feuer_mannschaft_member`
- zugehörige Galerie-Anhänge und hochgeladene Dateien
- Erwähnungs-Metadaten `mentioned_persons`
- Plugin-Optionen und temporäre Transients
- die Log-Tabelle
- Backup-Archive und temporäre Backup-Ordner im Upload-Bereich

## Versionsordner

Ab Version `2.1.14` wird jeder gesicherte Stand in einem eigenen Ordner unter
`Version/x.y.z` abgelegt.

- jeder Versionsordner enthaelt eine vollständige Kopie des Plugins
- beim Erstellen eines Snapshots werden `Version`, `release` und Repository-Metadaten nicht rekursiv kopiert
- neue Snapshots lassen sich ueber `tools/create-version-snapshot.ps1` erzeugen

## Autor

- Autor: Walter Faerber
- Website: https://wdmin.com
- Plugin-Version: 2.3.83

