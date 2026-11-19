# Feuer-Mannschaft Plugin Historie

## Version 2.4.0

- alle 15 Ausbau-Module in einer produktiven, modularen Laufzeitschicht zusammengefuehrt
- Publikationsbeziehungen mit Mehrfachauswahl, Suche, Sortierung und gezieltem Ausblenden einzelner Rueckverweise
- Organisationsrollen, Vorgesetzte, manuelle Reihenfolge und Bootstrap-kompatibles Organigramm
- vollstaendiges Filterverzeichnis mit REST-Ausgabe, Seitennavigation und teilbarem URL-Zustand
- QR-Erzeugung vollstaendig lokal; vCard und Druckansicht respektieren die Feldsichtbarkeit
- Sichtbarkeit fuer Profiltext, Kontaktdaten, Galerie, Dienstverlauf und Erwaehnungen serverseitig erzwungen
- CSV-Vorschau mit Mapping, Dry Run, Duplikatkontrolle, Bildimport und Ruecknahme inklusive Beziehungen
- ZIP-Export mit CSV, Beziehungsdatei, Manifest und Profilbildern
- Datenqualitaetsbericht fuer Fotos, Metadaten, Taxonomien, Dateien, URLs, Dubletten und defekte Beziehungen
- getrennte Karten- und Profilbilder, Medienauswahl, Fokuspunkt, Historie sowie WebP/AVIF bei Serverunterstuetzung
- Canonical, Open Graph, Person-JSON-LD, Sitemap-/Archivschutz und SEO-Plugin-Konflikterkennung
- Opt-in-Berichte, Erinnerungen, Dienstjubilaeen, Backups und Statusbenachrichtigungen
- PHP-Syntax aller 65 Laufzeitdateien und JavaScript-Syntax der produktiven Assets geprueft

## Erweiterung 2.3.83

- tägliche Integritätsprüfung verknüpfter Profile
- optionale Fehler-E-Mails und Wochenberichte
- optionale wöchentliche Sicherung mit bestehendem Backup-Manager
- Automationsseite mit Status, Empfänger und Opt-in-Schaltern
- geplante Aufgaben werden bei Deaktivierung sauber entfernt

## Erweiterung 2.3.82

- editierbare SEO-Titel und Meta-Beschreibungen pro Profil
- eindeutige Canonical-URL ohne doppeltes WordPress-Tag
- Open Graph sowie JSON-LD vom Typ `Person`
- Noindex für archivierte/gesperrte Profile und Breadcrumb-Shortcode

## Erweiterung 2.3.81

- Fokuspunkt X/Y pro Profil für kontrollierte Kartenausschnitte
- Fokus wird als `object-position` auf WordPress-Bilder übertragen
- geschützte Stapel-Neuerzeugung der WordPress-Bildgrößen
- bestehende sichere Medienbereinigung bleibt getrennt und rückverfolgbar

## Erweiterung 2.3.80

- zentrales Dashboard für Datenqualität
- Vollständigkeitsscore für Foto, Dienstgrad, Funktion und Abteilung
- problematische Profile werden zuerst angezeigt und direkt verlinkt
- aktive, archivierte und unveröffentlichte Datensätze werden geprüft

## Erweiterung 2.3.79

- UTF-8-CSV-Export für aktive, archivierte und unveröffentlichte Profile
- Dry Run mit Spaltenprüfung und Limit von 2.000 Datensätzen
- Update per ID oder URL-Slug, Taxonomie-Zuordnung und sichere Bereinigung
- Rücknahme des letzten Imports innerhalb von 24 Stunden

## Erweiterung 2.3.78

- Sichtbarkeit für E-Mail, Telefon und Dienstverlauf pro Profil
- Stufen öffentlich, angemeldete Benutzer, intern und verborgen
- zentrale Filter-Schnittstelle für eigene Felder und Erweiterungen

## Erweiterung 2.3.77

- neue Rolle `Mannschaft-Redakteur`
- getrennte Berechtigungen für Profile, Erwähnungen, interne Felder und endgültiges Löschen
- Administratoren erhalten die neuen Capabilities idempotent beim Start

## Erweiterung 2.3.76

- Profil-QR-Code `[feuer_member_qr]`
- nonce-geschützter vCard-Download und Druckansicht
- gemeinsame Aktionsleiste `[feuer_member_actions]`
- Kontaktdaten werden ausschließlich aus veröffentlichten Profilen erzeugt

## Erweiterung 2.3.75

- neues filterbares Verzeichnis `[feuer_member_directory]`
- Suche nach Name, Dienstgrad und Funktion
- Filter nach Abteilung und Fachgebiet sowie Sortierung
- Filterzustand wird in der URL gespeichert und ist teilbar
- zugängliche Ergebnisanzahl, Leerzustand und Bootstrap-kompatible Karten

## Erweiterung 2.3.74

- Kurzformen `[feuer_member]` und `[feuer_members]`
- Filter nach Kategorie, Abteilung, Gruppe, Spezialisierung und aktueller Erwaehnung
- neue Bloecke fuer Listen, Organigramm, Dienstverlauf und ehemalige Mitglieder

## Erweiterung 2.3.73

- visueller Karten-Konfigurator mit Live-Vorschau
- Presets Klassisch, Minimal, Dunkel und Offiziell
- Regler fuer Overlay, Radius, Namensgroesse und Textposition
- Dienstgrad und Funktion lassen sich separat ein- oder ausblenden

## Erweiterung 2.3.72

Die Archivierung bewahrt ausgeschiedene Mitglieder als historischen Bestand.

Geaendert:

- Archivgrund und Dienstende direkt im Profil
- datensparsame Archivkarten ohne Kontaktangaben
- Shortcode `[feuer_archived_members]` fuer eine Seite ehemaliger Mitglieder
- bestehende Erwaehnungen bleiben beim Archivieren erhalten
- Wiederaktivierung nutzt weiterhin denselben Profil-Datensatz und dieselbe ID
- Archivdaten sind Bestandteil der Sicherung und Wiederherstellung

## Erweiterung 2.3.71

Teilnehmerprofile besitzen jetzt einen strukturierten Dienstverlauf.

Geaendert:

- wiederholbare Ereignisse mit Datum, Typ, Titel und Beschreibung
- Ereignistypen fuer Eintritt, Dienstgrad, Funktion, Abteilung, Qualifikation,
  Auszeichnung, Jubilaeum und Austritt
- automatische chronologische Darstellung in allen Profilvorlagen
- Shortcode `[feuer_service_history id="123"]`
- Dienstverlauf wird in Sicherungen exportiert und wiederhergestellt

## Erweiterung 2.3.70

Profile koennen strukturiert in Abteilungen, Gruppen und Spezialisierungen
eingeordnet werden.

Geaendert:

- neue hierarchische Taxonomien fuer Abteilungen und Gruppen
- neue Taxonomie fuer Spezialisierungen
- REST- und Block-Editor-Unterstuetzung fuer alle drei Taxonomien
- neuer Shortcode `[feuer_org_chart]` fuer eine responsive Organisationsansicht

## Erweiterung 2.3.69

Die Verknuepfung zwischen Teilnehmerprofilen und redaktionellen Inhalten wurde
fuer den Block-Editor erweitert.

Geaendert:

- dynamischer Gutenberg-Block `Teilnehmer der Mannschaft`
- dynamischer Gutenberg-Block `Teilnehmer des Beitrags`
- dynamischer Gutenberg-Block `Beitraege des Teilnehmers`
- neue Shortcodes `[feuer_member_posts]` und `[feuer_mannschaft_mentioned_members]`
- einstellbare Anzahl der verknuepften Beitraege
- Plugin-Mindestversion WordPress 7.1, getestet mit WordPress 7.1.3

## Erweiterung 2.3.66

Die Mannschaftskarten und die editierbare HTML-Variante wurden auf Bootstrap
ausgerichtet.

Geaendert:

- die dynamischen Karten verwenden Bootstrap-Klassen fuer Grid, Card, Overlay und Flex-Layout
- `snippets/member-cards-bootstrap.html` liefert eine gleich gestaltete HTML-Einlage fuer WordPress-Custom-HTML-Bloecke
- `snippets/README.md` beschreibt die schnellen, pro Karte notwendigen Anpassungen

## Erweiterung 2.3.65

Die Mannschaftskarten wurden als bildbasierte Profilkacheln ueberarbeitet.

Geaendert:

- Name, Dienstgrad und Funktion liegen nun als kontraststarke Textebene auf dem Foto
- Bildkarten erhalten einen dezenten Zoom- und Kontrasteffekt bei Hover oder Tastaturfokus
- Mobilansicht und `prefers-reduced-motion` sind beruecksichtigt
- Graustufen-Karten nutzen die neue Struktur und behalten ihren Farbeffekt beim Fokus

## Erweiterung 2.3.64

Der Kompatibilitaetsstand wurde fuer aktuelle WordPress-Installationen
vereinheitlicht.

Geaendert:

- PHP `8.1` ist jetzt die verbindliche Mindestversion im Plugin-Bootstrap,
  im Update-Manifest und im Updater-Fallback
- der Plugin-Header deklariert WordPress `6.0` als Mindestversion
- das Update-Manifest ist fuer WordPress `7.0` als getestet gekennzeichnet

## Erweiterung 2.3.62

Fuer Version 2.3.62 wurde die oeffentliche Plugin-Startseite
inhaltlich professionalisiert.

Geaendert:

- `index.php` beschreibt das Plugin jetzt als Produkt fuer
  Freiwillige Feuerwehren statt technische Projektmetriken anzuzeigen
- Angaben zu Codeumfang, Zeilen und Zeichen wurden vollstaendig
  entfernt
- die Seite praesentiert jetzt Funktionen, Vorteile, Einsatzrahmen in
  Deutschland, Autor und Update-Betrieb ueber `wdmin.com`

## Erweiterung 2.3.61

Fuer Version 2.3.61 wurde der eingeklappte Administrator-Hinweis
optisch weiter reduziert.

Geaendert:

- der geschlossene Hinweis zeigt jetzt nur noch eine schmale Kopfzeile
  mit kleinem Indikator
- die auffaellige Farbmarkierung erscheint erst nach dem Oeffnen des
  internen Bereichs
- dadurch wirkt der versteckte Administrator-Hinweis deutlich
  unauffaelliger in Admin und Frontend

## Erweiterung 2.3.60

Fuer Version 2.3.60 wurde der Administrator-Hinweis diskreter
dargestellt.

Geaendert:

- das interne Administrator-Feld ist in der Profilbearbeitung jetzt
  standardmaessig eingeklappt
- auch im Frontend wird der Administrator-Hinweis erst nach dem Oeffnen
  eines ausklappbaren Bereichs sichtbar
- die Notiz bleibt damit bei offenen Profilseiten besser vor direkten
  Blicken geschuetzt

## Erweiterung 2.3.59

Fuer Version 2.3.59 wurden mehrere Fehler aus dem technischen Audit
behoben.

Geaendert:

- Backup-Restores schreiben Plugin-Einstellungen jetzt erst nach einem
  vollstaendig erfolgreichen Wiederherstellungsdurchlauf zurueck
- Galerie-Metadaten werden beim Restore nicht mehr vorab mit alten
  Attachment-IDs eingespielt und koennen dadurch keine defekten
  Bildreferenzen hinterlassen
- wiederhergestellte Einstellungen werden vor dem Speichern normalisiert
  und validiert
- Fehler bei `wp_update_post()` werden in der Profilbearbeitung jetzt als
  Admin-Hinweis ausgegeben und zusaetzlich im Plugin-Log festgehalten

## Erweiterung 2.3.58

Fuer Version 2.3.58 wurde ein internes Administrator-Feld fuer
Mitgliederprofile ergaenzt.

Geaendert:

- jedes Profil besitzt jetzt einen separaten `Administrator-Hinweis`
  als internes Textfeld
- das Feld ist im Bearbeitungsformular nur fuer Administratoren sichtbar
  und farblich hervorgehoben
- im Frontend wird der Hinweis ebenfalls nur Administratoren angezeigt
  und klar als interner Bereich markiert
- Speicherung, Log-Ausgabe und bestehende Backups bleiben mit dem neuen
  Meta-Feld kompatibel

## Erweiterung 2.3.57

Fuer Version 2.3.57 wurde die Reihenfolgeverwaltung in den
Standardfeldern erweitert.

Geaendert:

- vorhandene Eintraege in `Dienstgrad`, `Funktion` und `Ausbildung`
  koennen im Bereich `Standardfelder` jetzt direkt per `Hoch` und
  `Runter` verschoben werden
- die Reihenfolge wird vor dem Speichern sauber neu indexiert und bleibt
  dadurch in Profilen und Frontend-Ausgaben erhalten
- dieselbe Bedienlogik steht auch fuer Listen in Zusatzfeldern zur
  Verfuegung

## Erweiterung 2.3.56

Fuer Version 2.3.56 wurde die sichtbare Bezeichnung in der
WordPress-Pluginliste korrigiert.

Geaendert:

- der Plugin-Header zeigt in der Plugin-Uebersicht wieder
  `Feuer-Mannschaft`
- die Update-Metadaten verwenden ebenfalls wieder den vollstaendigen
  Plugin-Namen
- das linke Admin-Menue und die Plugin-Seiten bleiben weiterhin bei der
  verkuerzten Bezeichnung `Mannschaft`

## Erweiterung 2.3.55

Fuer Version 2.3.55 wurde ein Fehler in der Mitglieder-Ankete fuer die
Kontaktfelder behoben.

Geaendert:

- aktivierte `Kontaktfelder` aus den Einstellungen erscheinen wieder korrekt
  im Bearbeitungsformular eines Mitglieds
- die Formularlogik liest die gespeicherte Kontaktfeld-Konfiguration jetzt
  wieder aus den Plugin-Einstellungen statt aus einem leeren Standardwert
- `Email`, `Instagram`, `Facebook`, `WhatsApp`, `Telegram` und `Link`
  koennen damit wieder direkt pro Profil gepflegt werden

## Erweiterung 2.3.54

Fuer Version 2.3.54 wurde die sichtbare Bezeichnung des Plugins im
WordPress-Adminbereich auf `Mannschaft` verkuerzt.

Geaendert:

- Menueeintrag, Unterseiten und Einstellungsseite zeigen jetzt `Mannschaft`
  statt `Feuer-Mannschaft`
- Meta-Boxen, Hinweise und Update-Meldungen im Admin wurden auf den neuen
  Namen umgestellt
- die Plugin-Anzeige in der WordPress-Pluginliste und in der
  Update-Metadatenansicht verwendet jetzt ebenfalls `Mannschaft`
- technische Slugs, Dateinamen, Update-Pfade und die Plugin-Ordnerstruktur
  bleiben unveraendert

## Erweiterung 2.3.53

Fuer Version 2.3.53 wurden optionale Kontaktfelder fuer Mitgliederprofile
ergaenzt.

Geaendert:

- neue aktivierbare Kontaktfelder in `Listenfelder`: Email, Instagram,
  Facebook, WhatsApp, Telegram und ein freier Link mit Button-Name
- die Kontaktfelder erscheinen nur dann in der Profilbearbeitung, wenn sie in
  den Einstellungen aktiviert wurden
- Eingaben werden formatspezifisch validiert und bereits gespeicherte Werte
  bleiben auch dann erhalten, wenn ein Kontaktfeld spaeter in den Einstellungen
  voruebergehend deaktiviert wird
- im Frontend werden Kontaktangaben in allen Profilvorlagen als eigene Buttons
  statt als einfache Textlinks ausgegeben
- die neuen Kontaktfeld-Einstellungen und Profilwerte sind in Logging sowie
  Backup- und Restore-Ablauf kompatibel

## Erweiterung 2.3.52

Fuer Version 2.3.52 wurde die Galerie-Navigation nochmals reduziert.

Geaendert:

- die runde Umrandung und der Hintergrund der Galerie-Pfeile wurden entfernt
- sichtbar bleiben nur die Pfeile selbst, die weiterhin eine ausreichend
  grosse Klickflaeche behalten
- Hover- und Fokuszustand arbeiten jetzt nur noch ueber Farbe und leichte
  Vergroesserung statt ueber Button-Hintergruende

## Erweiterung 2.3.51

Fuer Version 2.3.51 wurde die Profil-Galerie optisch erneut verfeinert und die
Autoplay-Einstellung geprueft.

Geaendert:

- der Bildwechsel arbeitet jetzt mit einer weicheren Fade/Slide-Animation mit
  leichtem Zoom statt mit dem markanteren Seitenwechsel aus 2.3.50
- die Galerie-Pfeile wurden dezenter gestaltet und als halbtransparente
  Glas-Buttons ueberarbeitet
- die Einstellung `Slider Autoplay` ist weiterhin aktiv: der Wert wird als
  `data-feuer-mannschaft-interval` in alle Profil-Slider geschrieben und im
  Frontend per `setInterval()` ausgewertet
- `0` deaktiviert das automatische Weiterblaettern, ausserdem pausiert
  Autoplay jetzt automatisch bei versteckter Browser-Registerkarte

## Erweiterung 2.3.50

Fuer Version 2.3.50 wurde die Galerie-Navigation in den Profilansichten
ueberarbeitet.

Geaendert:

- die Profil-Galerie wechselt Bilder wieder mit einer weichen
  Slide-Animation statt mit hartem Umschalten
- die Karussellogik arbeitet ohne Bootstrap-Abhaengigkeit und respektiert
  `prefers-reduced-motion`
- die Pfeile werden nun per CSS gezeichnet, damit keine zusaetzlichen oder
  falsch codierten Zeichen mehr sichtbar werden
- fehlerhafte Sonderzeichen in der kompakten Profilvorlage wurden bereinigt

## Erweiterung 2.3.49

Fuer Version 2.3.49 wurde die Farbausgabe fuer ausgeschlossene Profilnamen in
den Frontend-Vorlagen korrigiert.

Geaendert:

- `Farbe fuer ausgeschlossene Profilnamen` wird jetzt direkt als Style auf
  den Namen gesetzt, wenn `Im Gesamtzaehler nicht mitzaehlen` aktiv ist
- die Korrektur gilt fuer `Team Raster`, `Team Raster Graustufen`,
  Standardkarten, Listenansicht und alle Profilvorlagen
- dadurch kann die Farbe nicht mehr durch `text-dark` oder Theme-CSS
  ueberdeckt werden

## Erweiterung 2.3.48

Fuer Version 2.3.48 wurde eine kontrollierte Cache-Bereinigung nach wichtigen
Plugin-Aenderungen integriert.

Geaendert:

- neue Einstellung `Cache nach Profilaenderungen automatisch leeren` im Tab
  `Allgemein`
- nach Profil-, Galerie-, Status- und Loesch-Aenderungen werden der
  WordPress-Objektcache, betroffene Anhange, Profilseiten, Kategoriearchive
  und Seiten mit Feuer-Mannschaft-Shortcodes bereinigt
- nach Template-/Listenfeld-Einstellungen und Backup-Restore werden
  betroffene Shortcode-Seiten ebenfalls bereinigt
- Kompatibilitaet mit LiteSpeed Cache, WP Rocket, W3 Total Cache,
  WP Super Cache und SiteGround Optimizer wurde ergaenzt
- zusaetzliche Hook-Schnittstelle `feuer_mannschaft_cache_purged` fuer eigene
  Cache-Integrationen

## Erweiterung 2.3.47

Fuer Version 2.3.47 wurde die Steuerung automatischer Updates in die
Plugin-Einstellungen verschoben.

Geaendert:

- im Tab `Updates` gibt es jetzt eine Option zum Ein- oder Ausschalten
  automatischer WordPress-Hintergrund-Updates
- automatische Updates sind standardmaessig deaktiviert, damit Updates nicht
  unkontrolliert ueber WP-Cron oder Server-Tools laufen
- wenn die Option aktiviert wird, erlaubt der Plugin-Filter
  `auto_update_plugin` automatische Updates fuer Feuer-Mannschaft

## Erweiterung 2.3.46

Fuer Version 2.3.46 wurden automatische Hintergrund-Updates fuer das Plugin
deaktiviert.

Geaendert:

- WordPress-Background-Updates ueber `auto_update_plugin` werden fuer
  `feuer-mannschaft/feuer-mannschaft.php` blockiert
- manuelle Updates ueber die Plugin-Uebersicht bleiben weiter moeglich
- der Update-Tab weist darauf hin, dass automatische Updates deaktiviert sind

## Erweiterung 2.3.45

Fuer Version 2.3.45 wurde der offizielle Release mit neuer
Graustufen-Teamansicht, Update-Pruefung und besserer mobiler Verwaltung
vorbereitet.

Geaendert:

- neue Listenvorlage `Team Raster Graustufen` auf Basis von `Team Raster`
  mit einstellbarer Schwarz-Weiss-Staerke
- Portraitbilder in Graustufen-Vorlagen werden beim Hover wieder farbig
  dargestellt
- fuer Profile mit `Im Gesamtzaehler nicht mitzaehlen` kann eine eigene
  Farbe nur fuer den Profilnamen eingestellt werden
- die mobile Adminliste `Alle Profile` zeigt nur noch Titel und Kategorie,
  damit die Ansicht wie die WordPress-Standardliste lesbar bleibt
- in den Einstellungen gibt es einen eigenen Tab `Updates` mit manueller
  Pruefung des Update-Servers
- `index.php` wurde als Praesentationsseite mit Plugin-Vorteilen,
  Funktionen, Einstellungen, Vorlagen, Projektdaten und Versionshistorie
  aufgebaut

## Erweiterung 2.3.44

Fuer Version 2.3.44 wurde die Entwicklerseite des Plugins vereinfacht.

Geaendert:

- `index.php` verwendet jetzt ein schlichtes Layout ohne aufwendige
  Landing-Page-Elemente
- Plugin-Informationen, Funktionen, Einstellungen, Shortcodes, Vorlagen und
  Historie werden zeilenweise und technisch klar dargestellt
- die Seite eignet sich damit besser als direkte Dokumentationsseite im
  Entwicklerordner

## Erweiterung 2.3.43

Fuer Version 2.3.43 wurden die Galeriesteuerung im Profil und eine neue
Kartenansicht fuer die Mannschaftsseite erweitert.

Geaendert:

- die Slider-Pfeile der Profilgalerien verwenden jetzt eigene
  `feuer-mannschaft`-Klassen statt allgemeiner Bootstrap-Icon-Klassen
- neue Listenvorlage `Graustufen-Karten` fuer die Ausgabe aller Teilnehmer
  mit ausgebleichten Portraitbildern
- in den Einstellungen gibt es einen Prozentwert fuer die Graustufe dieser
  Kartenansicht

## Erweiterung 2.3.42

Fuer Version 2.3.42 wurde eine eigene Landing-Page fuer den
Entwicklerordner des Plugins angelegt.

Geaendert:

- im Plugin-Hauptordner gibt es jetzt eine `index.php`, die Beschreibung,
  Funktionen, Beispiele und Links zum Plugin anzeigt
- die Versionshistorie der Landing-Page wird direkt aus `HISTORIE.md`
  geladen
- aktuelle Basisdaten wie Version, Download-Link und Vorlagen werden direkt
  aus den Projektdateien gelesen

## Erweiterung 2.3.41

Fuer Version 2.3.41 wurden Reste der frueher entfernten Site-Registry
vollstaendig aus dem Plugin entfernt.

Geaendert:

- der Tab `Verwendung` mit `Write Key` und `Read Key` wurde aus den
  Einstellungen entfernt
- alte Registry-Hooks, Dateien und Dokumentationshinweise wurden geloescht
- verbliebene Registry-Einstellungen und geplante Cron-Ereignisse werden
  einmalig bereinigt

## Erweiterung 2.3.40

Fuer Version 2.3.40 wurde die mobile Adminansicht der Teilnehmerprofile
nachgebessert.

Geaendert:

- in der mobilen Teilnehmerliste im WordPress-Backend bleibt das Foto jetzt
  sichtbar und wird links in die Zeile eingebettet
- die Bearbeitungsansicht eines Profils wird auf kleinen Displays auf volle
  Breite gesetzt
- die Fotogalerie in der Profilbearbeitung wird mobil einspaltig dargestellt

## Erweiterung 2.3.39

Fuer Version 2.3.39 wurde die Erstellung des Release-Archivs korrigiert.

Geaendert:

- das Release-ZIP wird jetzt mit standardkonformen Archivpfaden erzeugt, die
  auf Linux-Hostings sauber entpackt werden
- verschachtelte `.zip`-Dateien aus dem Projekt werden beim Packen nicht mehr
  in das Plugin-Release uebernommen
- damit wird insbesondere der Ordner `assets` beim Entpacken robuster
  behandelt

## Erweiterung 2.3.38

Fuer Version 2.3.38 wurde der Update-Mechanismus in WordPress robuster
gemacht.

Geaendert:

- verfuegbare Plugin-Updates werden jetzt auch dann in die Plugin-Liste
  eingetragen, wenn WordPress noch mit einem vorhandenen `update_plugins`-
  Transient arbeitet
- die installierte Version wird beim Vergleich aus dem WordPress-Transient
  uebernommen, falls sie dort vorhanden ist
- nach einem erfolgreichen Upgrade werden sowohl das Remote-Metadaten-Cache
  als auch der WordPress-Update-Transient geloescht

## Erweiterung 2.3.37

Fuer Version 2.3.37 wurde die Listenansicht auf der Website optisch neu
aufgebaut und gegen Theme-Konflikte abgesichert.

Geaendert:

- die Frontend-Liste nutzt jetzt eigene Header-, Karten- und Button-Stile
  statt unscharfer Mischungen aus allgemeinen Hilfsklassen
- die Listenzeilen wurden visuell sauberer gefasst, inklusive Rollenlabel,
  Titel-Link und Aktionsbutton
- die mobile Darstellung auf der Website bleibt kompakt und lesbar mit
  stabiler Bild-/Text-Aufteilung

## Erweiterung 2.3.36

Fuer Version 2.3.36 wurden die mobile Teilnehmerliste in der WordPress-
Adminansicht und das Layout der Einstellungen ueberarbeitet.

Geaendert:

- die Teilnehmerliste im Admin blendet auf kleinen Displays unnoetige
  Spalten aus und laesst Name sowie Aktionen sauber umbrechen
- die Einstellungen nutzen jetzt klarere Formular-Karten statt roh wirkender
  Tabellenzeilen
- die mobilen Formularzeilen der Einstellungen wurden fuer schmale Displays
  optisch und technisch sauber gestapelt

## Erweiterung 2.3.35

Fuer Version 2.3.35 wurde der Update-Prozess auf versionsbezogene ZIP-Pfade
umgestellt.

Geaendert:

- das Update-Manifest verweist jetzt auf
  `release/<version>/feuer-mannschaft.zip`
- das Build-Skript erzeugt Release-Pakete jetzt in
  `release/<version>/feuer-mannschaft.zip`
- zusaetzlich wird ein Alias unter `release/latest/feuer-mannschaft.zip`
  erzeugt
- `update.json` bleibt als stabile Update-Quelle im Hauptordner erhalten

## Erweiterung 2.3.34

Fuer Version 2.3.34 wurde die Listenansicht der Teilnehmer fuer mobile
Geraete neu aufgebaut.

Geaendert:

- die Listenzeile nutzt auf kleinen Displays jetzt eine kompakte Zweispalten-
  Struktur mit Bild links und Inhalten rechts
- das Teilnehmerfoto bleibt mobil als kleine Vorschau sichtbar und rutscht
  nicht mehr unter Name oder Ueberschrift
- Beschreibung, Metadaten und der Button `Profil ansehen` wurden fuer
  schmale Ansichten neu ausgerichtet

## Erweiterung 2.3.32

Fuer Version 2.3.32 wurde der Autorenname vereinheitlicht.

Geaendert:

- der Plugin-Header verwendet jetzt durchgaengig den Namen `Walter Faerber`
- Dokumentationsbereiche mit Autorenangabe wurden auf `Walter Faerber`
  aktualisiert
- das Update-Manifest wurde fuer die neue Version angehoben

## Erweiterung 2.3.31

Fuer Version 2.3.31 wurden Plugin-, Entwickler- und Update-Domaenen auf
`wdmin.com` umgestellt.

Geaendert:

- Plugin-Header, Update-Basis-URL und Manifest verweisen jetzt auf
  `https://wdmin.com/plugins/feuer-mannschaft/`
- Entwickler-Links in Plugin-Infos, Updater und Dokumentation wurden auf
  `https://wdmin.com/` umgestellt
- Release- und Deployment-Hinweise nutzen jetzt durchgaengig die neue Domain

## Erweiterung 2.3.30

Fuer Version 2.3.30 wurde der Stand fuer GitHub und den spaeteren
Release-/Update-Prozess vorbereitet.

Geaendert:

- neue Dateien `README.md` und `DEPLOYMENT.md` dokumentieren Projektstruktur,
  Release-Ablauf und Serverbereitstellung
- neue Git-Dateien `.gitignore` und `.gitattributes` bereiten das Repository
  fuer GitHub und saubere Exporte vor
- neues Werkzeug `tools/build-release-package.ps1` erstellt ein
  auslieferbares Plugin-ZIP fuer Server und Releases

## Erweiterung 2.3.29

Fuer Version 2.3.29 wurde eine eigene Update-Pruefung fuer externe
Plugin-Releases vorbereitet.

Geaendert:

- neue Klasse `includes/class-feuer-mannschaft-updater.php` prueft
  `https://wdmin.com/plugins/feuer-mannschaft/update.json`
- bei neuerer Remote-Version meldet WordPress ein verfuegbares Update und
  zeigt einen Admin-Hinweis an
- neues Manifest `update.json` beschreibt Download-URL, Versionsdaten und
  Plugin-Informationen fuer das Update-Modal

## Erweiterung 2.3.28

Fuer Version 2.3.28 wurde eine technische Projektdokumentation fuer
Dateien und PHP-Funktionen ergaenzt.

Geaendert:

- neues Werkzeug `tools/build-plugin-docs.ps1` erzeugt eine aktuelle
  Projektdokumentation direkt aus dem Quellstand
- neue Datei `DOKUMENTATION.md` dokumentiert Dateistruktur und
  Funktionsverzeichnis
- die Dokumentation ist so aufgebaut, dass Dateipfade und Zeilennummern bei
  Reviews und spaeterer GitHub-Nutzung nachvollziehbar bleiben

## Erweiterung 2.3.27

Fuer Version 2.3.27 wurde eine zweite neue Einzelansicht als
strukturiertes Dossierprofil hinzugefuegt.

Geaendert:

- neues Einzel-Template `Dossierprofil` trennt Bild, Fakten und Navigation in
  eine eigene Seitenleiste
- Hauptinhalt, Erwaehnungen und weitere Mitglieder werden als getrennte
  Inhaltskarten dargestellt
- auf grossen Displays bleibt die Seitenleiste sticky, auf kleinen Displays
  wechselt das Layout automatisch in eine untereinander lesbare Reihenfolge

## Erweiterung 2.3.26

Fuer Version 2.3.26 wurde ein weiterer Frontend-Stil fuer die
Teilnehmer-Detailseite als Magazinprofil ergaenzt.

Geaendert:

- neues Einzel-Template `Magazinprofil` kombiniert einen grossen Hero-Bereich
  mit Faktenkarten und einer klar getrennten Inhaltssektion
- Galerie-Slider, Profildaten, Erwaehnungen und Navigation wurden in das neue
  Layout integriert
- der Stil reagiert responsiv und stapelt Hero, Fakten und Inhaltsbereiche auf
  mobilen Geraeten sauber um

## Erweiterung 2.3.25

Fuer Version 2.3.25 wurde eine zweite Archivansicht mit kompakten
Verzeichnis-Kacheln hinzugefuegt.

Geaendert:

- neues Archiv-Template `Verzeichnis Kacheln` zeigt Teilnehmer in dichten,
  horizontalen Karten mit Foto, Kurztext und Fakten
- die Gesamtzahl aktiver Mitglieder wird im Kopfbereich der Ansicht
  hervorgehoben
- responsive Frontend-Stile stellen sicher, dass die Kacheln auf mobilen
  Geraeten auf eine klar lesbare Einspalten-Ansicht umschalten

## Erweiterung 2.3.24

Fuer Version 2.3.24 wurde eine neue Archivansicht mit hervorgehobenem
Spotlight-Profil ergaenzt.

Geaendert:

- neues Archiv-Template `Spotlight und Liste` hebt ein Mitglied gross im
  Seitenkopf hervor
- die restlichen Mitglieder werden darunter in einer kompakten, mobil
  lesbaren Reihenansicht dargestellt
- zusaetzliche Frontend-Stile sorgen fuer eine eigenstaendige
  Highlight-Darstellung ohne Abhaengigkeit vom Theme

## Erweiterung 2.3.23

Fuer Version 2.3.23 wurden die Standardkataloge aktualisiert und das
Speichern der Listenfelder korrigiert.

Behoben:

- die Default-Eintraege fuer Dienstgrad, Funktion und Ausbildung wurden auf
  die neuen Vorgaben umgestellt
- abgewaehlte Listenfelder bleiben beim Speichern nicht mehr als alte
  Checkbox-Werte haengen
- leere oder entfernte Zusatzfelder werden in den Profil-Metadaten jetzt
  sauber bereinigt

## Erweiterung 2.3.22

Fuer Version 2.3.22 koennen zusaetzliche Listenfelder frei in den
Plugin-Einstellungen angelegt werden.

Geaendert:

- neue Zusatzfelder koennen mit Name, Beschreibung, Auswahlmodus und eigenen
  Eintraegen angelegt werden
- Teilnehmerprofile zeigen diese Felder automatisch in der Bearbeitung und im
  Frontend an
- Archivierung, Backup und Restore uebernehmen die neue Feldstruktur und
  bestehende Teilnehmerdaten weiterhin ueber die vorhandenen Meta-Exporte

## Erweiterung 2.3.21

Fuer Version 2.3.21 wurden Listenfelder technisch auf strukturierte
Eintraege mit optionalen Links erweitert.

Geaendert:

- Dienstgrad, Funktion und Ausbildung speichern Eintraege jetzt als
  `Name + Link`
- hinterlegte Links werden im Profil und in der Schnellansicht des
  Teilnehmers als aktive Verweise ausgegeben
- die Einstellungen und die Profilpflege zeigen zu jedem Eintrag jetzt
  sowohl Text als auch optionale URL an

## Erweiterung 2.3.20

Fuer Version 2.3.20 wurde die Listenansicht auf mobilen Geraeten neu
aufgebaut.

Geaendert:

- Teilnehmerzeilen stapeln sich auf kleinen Displays jetzt sauber untereinander
- die Kurzinfos werden als besser lesbare Meta-Pills statt als enge Pipe-Zeile
  ausgegeben
- der Profil-Button nimmt mobil die volle Breite ein und ist dadurch deutlich
  leichter bedienbar

## Erweiterung 2.3.19

Fuer Version 2.3.19 wurde das interne Versions-Snapshot-Werkzeug an die
neue Ablagestruktur angepasst.

Geaendert:

- `tools/create-version-snapshot.ps1` erzeugt neue Stände jetzt unter
  `Version/<Versionsnummer>/`
- der alte Pfad `versions/v.<nummer>` wird vom Werkzeug nicht mehr benutzt
- die Snapshot-Erstellung folgt damit derselben Struktur wie die in diesem
  Durchlauf angelegten Versionen 2.3.14 bis 2.3.18

## Erweiterung 2.3.18

Fuer Version 2.3.18 wurde das Speichern von Teilnehmer-Profilen
fehlersicherer aufgebaut.

Behoben:

- der WordPress-Beitrag wird jetzt zuerst mit `wp_update_post(..., true)`
  aktualisiert und auf `WP_Error` geprueft
- Meta-Felder, Kategorien und Galerie-Dateien werden erst dann geschrieben,
  wenn die eigentliche Beitragsaktualisierung erfolgreich war
- Teil-Speicherungen mit bereits veraenderten Neben-Daten bei einem
  fehlgeschlagenen `post`-Update werden dadurch vermieden

## Erweiterung 2.3.17

Fuer Version 2.3.17 wurde der Frontend-Bereich von einer externen
Bootstrap-Abhaengigkeit geloest.

Geaendert:

- Listen, Karten, Buttons, Modal-Fenster und Slider nutzen jetzt einen
  eigenen CSS-Kompatibilitaetslayer aus dem Plugin
- die Schnellansicht arbeitet nicht mehr ueber Bootstrap-Datenattribute,
  sondern ueber ein eigenes Frontend-Skript
- das Einzelprofil-Template enthaelt keine doppelten Inline-Skripte und
  Inline-Styles mehr

Behoben:

- die Karten-Schnellansicht laesst sich jetzt wieder direkt aus der
  Modal-Ansicht oeffnen
- Frontend-Layouts und Interaktionen funktionieren jetzt auch auf Themes,
  die selbst kein Bootstrap laden

## Erweiterung 2.3.16

Fuer Version 2.3.16 wurde die Installation und Aktualisierung der
Log-Tabelle technisch ueberarbeitet.

Behoben:

- die Log-Tabelle wird bei Updates jetzt ueber `dbDelta()` an das
  erwartete Schema angepasst
- die gespeicherte Schema-Version wird erst dann aktualisiert, wenn alle
  benoetigten Spalten und Indizes vorhanden sind
- kuenftige Tabellen-Erweiterungen koennen dadurch sauber migriert werden,
  statt stillschweigend uebersprungen zu werden

## Erweiterung 2.3.15

Fuer Version 2.3.15 wurde die Galerie-Wiederherstellung aus Archiven
fehlertoleranter gemacht.

Behoben:

- beim Restore bleibt die bisherige Galerie eines Profils jetzt erhalten,
  solange die neue Galerie noch nicht vollstaendig importiert wurde
- erst nach einem erfolgreichen Komplettimport werden alte Galerie-Dateien
  geloescht und durch die wiederhergestellten Bilder ersetzt
- bricht der Import unterwegs ab, werden nur temporaer angelegte neue
  Attachments entfernt

## Erweiterung 2.3.14

Fuer Version 2.3.14 wurde das Entfernen von Galerie-Fotos im
Teilnehmer-Editor abgesichert.

Behoben:

- beim Speichern werden nur noch Anhaenge geloescht, die bereits zur
  Galerie des gerade bearbeiteten Profils gehoeren
- zusaetzlich wird vor dem Loeschen jedes Bildes die WordPress-Berechtigung
  fuer das jeweilige Attachment geprueft
- manipulierte Requests koennen dadurch keine fremden Medien mehr ueber den
  Galerie-Dialog mitloeschen

## Erweiterung 2.3.13

Fuer Version 2.3.13 wurde die Foto-Vorschau im Editor eines Teilnehmers
kompakter aufgebaut.

Geaendert:

- die Vorschaubilder in `Fotos hochladen` verwenden jetzt kleinere und dichtere
  Karten im Bearbeitungsbereich
- die Bildflaechen zeigen Fotos nicht mehr hart angeschnitten, sondern
  vollstaendiger innerhalb eines kompakten Rahmens
- auch das Standardfoto im Teilnehmer-Editor folgt jetzt derselben kompakteren
  Vorschau-Darstellung

## Erweiterung 2.3.12

Fuer Version 2.3.12 wurde die Bildflaeche in den Karten von
`Weitere Mitglieder` technisch vereinheitlicht.

Geaendert:

- die Kartenbilder verwenden jetzt eine feste Rahmenproportion aus den
  Plugin-Einstellungen fuer Kartenbild Breite und Hoehe
- unterschiedliche Originalgroessen der Fotos fuehren dadurch nicht mehr zu
  versetzten oder unterschiedlich hohen Karten
- der Bildlink der Karte wurde als Blockelement vereinheitlicht, damit die
  Bildflaeche sauber die ganze Kartenbreite nutzt

## Erweiterung 2.3.11

Fuer Version 2.3.11 wurde die Auswahl der Eintraege im Bereich
`Weitere Mitglieder` korrigiert.

Geaendert:

- der Block verwendet nicht mehr immer nur die ersten passenden Profile einer
  Kategorie
- stattdessen wird die Liste jetzt relativ zur Position des aktuell geoeffneten
  Profils aufgebaut und aendert sich beim Wechsel zu einem anderen Teilnehmer
- die Korrektur wirkt fuer Standardprofil und Kompaktprofil

## Erweiterung 2.3.10

Fuer Version 2.3.10 wurde die Darstellung der Bilder im Bereich
`Weitere Mitglieder` vereinheitlicht.

Geaendert:

- die Team-Karte verwendet in Einzelprofilen und in der Team-Uebersicht jetzt
  dieselbe saubere Spaltenstruktur ohne doppelte Bootstrap-Spalten
- die Kartenbreite und das Bildverhalten wurden so angepasst, dass alle Fotos
  in `Weitere Mitglieder` gleich gross und sauber zugeschnitten erscheinen

## Erweiterung 2.3.9

Fuer Version 2.3.9 wurde der allgemeine Teilnehmerzaehler fuer Karten mit
`Eigener Link` korrigiert.

Geaendert:

- unter `Eigener Link` gibt es jetzt eine separate Option, mit der ein Eintrag
  vom allgemeinen Teilnehmerzaehler ausgeschlossen werden kann
- der Shortcode `[feuer_mannschaft_anzahl]` zaehlt nun alle aktiven Profile und
  laesst nur noch ausdruecklich markierte Eintraege aus
- Aenderungen an dieser Option werden im Aenderungsprotokoll mit erfasst

## Erweiterung 2.3.4

Für Version 2.3.4 wurde die Idee einer gesonderten Einladungskarte wieder
entfernt und durch eine schlichte Link-Erweiterung ersetzt.

Geändert:

- Karten mit freiem Platz oder Mitgliederwerbung verwenden jetzt wieder das
  identische Layout normaler Mannschaftsprofile
- statt eines separaten Schalters gibt es in der Bearbeitungsmaske nur noch
  ein optionales Feld `Eigener Link`
- wenn dieser Link gesetzt ist, verweisen Karte und Button auf diesen Link
  statt auf die Profilseite
- das experimentelle Sonder-Layout und die zugehörigen Templates wurden
  wieder entfernt

Bereinigt:

- keine spezielle farbliche Hervorhebung mehr für Platzhalter-Karten
- keine zusätzliche Umschalt-Logik mehr in der Bearbeitungsmaske
- Verweise mit eigenem Link werden aus Erwähnungen und aus der normalen
  Profil-Navigation ausgehalten

## Erweiterung 2.3.3

Für Version 2.3.3 wurde ein neuer Eintragstyp für freie Plätze und
Mitgliederwerbung ergänzt.

Neu:

- Einträge können jetzt wahlweise als normales Mitgliedsprofil oder als
  `Einladungskarte` gespeichert werden
- Einladungskarten unterstützen einen frei pflegbaren Titel, Text aus dem
  Editor, ein Bild und einen Ziel-Link
- Karten wie `Platz frei` oder `Werde Mitglied` werden in allen
  Mannschaftslisten im gleichen Stil wie normale Profile ausgegeben
- die Bearbeitungsmaske trennt nun sauber zwischen Profilfeldern und
  Feldern für Einladungskarten
- zusätzliche interne Frontend- und Admin-Templates wurden ausgelagert,
  damit diese Bereiche einfacher angepasst werden können

Behoben:

- Erwähnungen behandeln Einladungskarten nicht als Personenprofile
- die Profil-Liste in der Administration kann jetzt auch Einladungskarten
  als normale Feuer-Mannschaft-Einträge verwalten

## Erweiterung 2.3.2

Für Version 2.3.2 wurde ein kritischer Fehler in der Profil-Liste der
WordPress-Administration behoben.

Behoben:

- fehlender Berechtigungs-Wrapper `can_remove_member()`, der beim Rendern
  der Zeilenaktionen auf `Alle Profile` zu einem Fatal Error führte
- dadurch funktionieren die Profil-Liste, die Zeilenaktionen und die
  Entfernen-Prüfung wieder korrekt

Ergebnis:

- `Feuer-Mannschaft -> Alle Profile` lässt sich wieder ohne kritischen
  Fehler öffnen
- Archivieren, Aktivieren und Entfernen können wieder sauber geprüft werden

## Projektziel

Das Plugin wurde erstellt, um Mannschaftsprofile sauber und getrennt von
anderen Einsatz- oder Statistikfunktionen zu verwalten.

Ziel war eine Lösung, mit der Personenakten mit Fotos, Zusatzfeldern und
einer einfachen Frontend-Darstellung gepflegt werden können.

## Entstehung

### 1. Grundidee

Am Anfang stand die Anforderung, Mannschaftsmitglieder mit folgenden Daten
zu verwalten:

- Foto
- Vorname
- Nachname
- Eintrittsjahr
- Dienstgrad
- Funktion
- Ausbildung
- Beschreibung
- Position in der Liste

### 2. Erste Plugin-Struktur

Zunächst wurde ein eigenständiges Plugin für Mannschaftsprofile aufgebaut.
Dabei wurden Verwaltungsfelder, Bild-Uploads, Wasserzeichen und eine
Frontend-Ausgabe eingeführt.

### 3. Diskussion zur Verbindung mit Einsatzberichte

Im weiteren Verlauf wurde untersucht, ob `Feuer-Mannschaft` mit
`Einsatzberichte` zusammengelegt oder synchronisiert werden soll.

Dabei wurde deutlich:

- Mannschaftsprofile und Einsatz-Teilnehmer haben unterschiedliche Aufgaben
- eine zu enge Kopplung führt schnell zu doppelten oder unklaren Daten
- die Profilverwaltung sollte eigenständig bleiben

### 4. Rückkehr zur klaren Trennung

Daraufhin wurde entschieden:

- `Feuer-Mannschaft` bleibt ein eigenes Plugin
- `Einsatzberichte` bleibt funktional getrennt
- das Layout von `Feuer-Mannschaft` darf sich optisch an `Einsatzberichte`
  orientieren, die Datenstruktur aber nicht

### 5. Umstellung auf normale WordPress-Beiträge

Später wurde beschlossen, Mannschaftsprofile nicht mehr als eigenen
Custom Post Type zu speichern, sondern als normale WordPress-Beiträge.

Gründe:

- bessere Einbindung in WordPress
- leichtere Nutzung von Kategorien
- bessere Nachvollziehbarkeit im Blog-System
- einfachere Erweiterbarkeit für spätere Theme-Anpassungen

### 6. Kategorien als Steuerung

Im Plugin wurde anschließend eine Einstellung eingeführt, über die
festgelegt werden kann, welche Kategorien für Mannschaftsprofile erlaubt
sind.

Beim Anlegen eines Profils wird dann aus diesen Kategorien eine passende
Kategorie ausgewählt.

### 7. Archiv-Konzept

Ein wichtiger Schritt war die Entscheidung, Profile nicht standardmäßig zu
löschen.

Stattdessen:

- können Profile archiviert werden
- bleiben historische Daten erhalten
- werden archivierte Profile auf der Website blockiert

Nur die Hauptadministration darf Dubletten oder ungültige Datensätze
endgültig entfernen.

### 8. Frontend und Bedienung

Danach wurde die Darstellung vereinfacht und an den Stil von
`Einsatzberichte` angenähert:

- einfache Bootstrap-Ausgabe
- klare Listenansicht
- Einzelprofil mit Navigation
- weitere Profile unterhalb der Akte

### 9. Vorlagen-System für das Frontend

Im nächsten Schritt wurde die Ausgabe in eigene Template-Dateien ausgelagert.

Seitdem gilt:

- Listen-Vorlagen liegen in `templates/uebersicht`
- Profil-Vorlagen liegen in `templates/profil`
- Vorlagen werden automatisch erkannt und in den Einstellungen angeboten
- Shortcodes können Vorlagen direkt pro Einsatzort bzw. pro Seite überschreiben

### 10. Erweiterte Shortcodes für Website-Bausteine

Danach wurde die Shortcode-Ausgabe erweitert, damit einzelne Bereiche der
Website flexibler aufgebaut werden können.

Seitdem sind zusätzlich möglich:

- Ausgabe der Anzahl aktiver Mannschaftsprofile
- Ausgabe eines bestimmten Profils per Name, ID oder Slug
- Ausgabe eines zufälligen Profils
- Ausgabe von 2, 4 oder 8 zufälligen Profilkarten
- Standardseite der Mannschaft per Shortcode mit eigener Standardvorlage
- freie Kombination mit den Vorlagen aus `templates/uebersicht` und `templates/profil`

### 11. Menü- und Admin-Anpassungen

Zum Schluss wurde die WordPress-Admin-Navigation so angepasst, dass
`Mannschaft` ein eigenes Menü hat und die Verwaltung über normale
WordPress-Beitragsseiten läuft.

## Aktueller Stand

Das Plugin befindet sich aktuell auf folgendem Stand:

- eigenständiges Plugin
- Speicherung als normaler WordPress-Beitrag
- deutsche Verwaltungsoberfläche
- Foto-Upload mit Text-Wasserzeichen
- Kategorienutzung über WordPress
- Archivierung mit Frontend-Sperre
- Ausgabe per Shortcode, Einzelansicht, Kartenblock und Profilzähler
- frei wählbare Frontend-Vorlagen für Listen und Profile
- Standardseite per `[feuer_mannschaft]` und Standardprofil über `templates/profil/standard.php`
- eigene Seitenvorlage für `[feuer_mannschaft_profil]` ohne Sidebar der Theme-`page.php`

## Erweiterung 2.1.0

Für Version 2.1.0 wurden folgende Punkte ergänzt oder umgebaut:

- HTML-Ausgabe aus dem Hauptcode weiter in `templates/admin` und `templates/frontend` ausgelagert
- neue Service-Klassen in `includes` fuer Logs, Archive und Erwähnungen hinzugefügt
- Erwaehnungsbereich auf Profilseiten oberhalb der Navigationsbuttons eingebaut
- Einstellung zum Aktivieren oder Deaktivieren des Erwaehnungsbereichs ergänzt
- Post-Type-Auswahl fuer Erwähnungen in den Einstellungen ergänzt
- Auswahl-Listen fuer `Dienstgrad`, `Funktion` und `Ausbildung` in den Einstellungen umgesetzt
- Mehrfachauswahl fuer `Ausbildung` in der Teilnehmerakte umgesetzt
- Archivverwaltung mit Erstellen, Hochladen, Wiederherstellen, Download und Löschen ergänzt
- Log-Tabelle fuer Profil-, Archiv- und Einstellungsaktionen eingefuehrt
- Plugin-Version und Dokumentation aktualisiert

## Erweiterung 2.1.1

Für Version 2.1.1 wurden die Listenfelder und Profilschalter weiter umgebaut:

- `Listenfelder` in den Einstellungen auf einzelne wiederholbare Eingabefelder mit `hinzufuegen` und `entfernen` umgestellt
- Pflichtauswahl einer Ersetzung eingebaut, wenn entfernte Werte noch in Profilen verwendet werden
- `Dienstgrad` im Profil auf exklusive Switch-Auswahl umgestellt
- `Funktion` und `Ausbildung` im Profil auf mehrfache Switch-Auswahl umgestellt
- einmalige Leerung der bisherigen Werte fuer `Dienstgrad`, `Funktion` und `Ausbildung` in aktiven Profilen beim Update vorbereitet
- Admin-JavaScript und CSS fuer wiederholbare Felder und Switch-Darstellung erweitert

## Erweiterung 2.1.2

Für Version 2.1.2 wurden die Erwähnungen im Beitragseditor erweitert:

- neuer Kurzcode `[feuer_mannschaft_person id="..."]` fuer die verlinkte Ausgabe eines Teilnehmernamens hinzugefügt
- Hilfebereich im Beitragseditor eingebaut, der zu jeder ausgewählten Person Vorschau, Profil-Link und Kurzcode anzeigt
- Kopieren- und Einfuegen-Funktion fuer die Kurzcodes im Editor ergänzt
- ACF-Relationship-Feld und Plugin-Fallback fuer `Erwaehnte Personen` mit derselben Kurzcode-Hilfe verbunden
- weitere sichtbare Plugin-Texte in Richtung korrekter deutscher Umlaute überarbeitet

## Erweiterung 2.1.3

Für Version 2.1.3 wurde die Kurzcode-Übersicht in den Einstellungen wieder eingebaut:

- Abschnitt `Kurzcode-Übersicht` unter `Feuer-Mannschaft -> Einstellungen -> Allgemein` zurückgebracht
- Übersicht für Mannschaftsliste, einzelnes Profil, Profil-Link-Name, Profilanzahl und Profilkarten ergänzt
- neuer Kurzcode `[feuer_mannschaft_person]` direkt in die Einstellungen aufgenommen

## Erweiterung 2.1.4

Für Version 2.1.4 wurden Aktivierung und vollständige Deinstallation des Plugins erweitert:

- bei der Aktivierung werden Standard-Einstellungen, Log-Tabelle, `uploads/feuer-mannschaft` und `uploads/feuer-mannschaft-backups` wieder sauber angelegt
- Schutzdateien wie `index.php` und `.htaccess` werden fuer die vom Plugin erzeugten Upload-Ordner automatisch erstellt
- neues `uninstall.php` entfernt Mannschaftsprofile, Alt-Daten, Galerie-Anhänge, Erwähnungs-Metadaten, Log-Tabelle, Optionen, Transients und Backup-Dateien komplett
- übrig gebliebene temporäre Backup-Ordner werden bei der Deinstallation ebenfalls gelöscht

## Erweiterung 2.1.5

Für Version 2.1.5 wurde die Darstellung von Mehrfachwerten in Profilen verbessert:

- `Funktion` und `Ausbildung` werden in der Profilansicht nicht mehr als durch Kommas getrennte Zeichenkette ausgegeben
- jeder einzelne Wert wird nun als eigenes `span`-Element mit eigener Gestaltung gerendert
- die neue Darstellung gilt fuer Standardprofil, Kompaktprofil und die Schnellansicht im Modal

## Erweiterung 2.1.6

Für Version 2.1.6 wurden Listenfelder und Auswahlbereiche weiter verbessert:

- Standardwerte fuer `Dienstgrad`, `Funktion` und `Ausbildung` werden bei leeren Listen automatisch in die Einstellungen übernommen
- die Listenfelder in den Einstellungen wurden als getrennte, volle Breite einnehmende Bereiche neu aufgebaut
- die Auswahlfelder in der Teilnehmerakte fuer `Dienstgrad`, `Funktion` und `Ausbildung` erscheinen jetzt als volle Breite einnehmende Auswahlbereiche mit angenehmerer Anordnung
- die neuen Standardlisten werden auch bei Neuinstallationen direkt als Grundeinstellung gesetzt

## Erweiterung 2.1.7

Für Version 2.1.7 wurde das Verhalten der Standardlisten an das gewünschte Setup angepasst:

- `Dienstgrad`, `Funktion` und `Ausbildung` bleiben in den Einstellungen vollständig bearbeitbar
- Standardwerte werden nur noch als Ausgangsbasis fuer eine saubere Neuinstallation verwendet
- es findet keine automatische Nachbefuellung leerer Listen mehr in bereits bestehenden Installationen statt

## Erweiterung 2.1.8

Für Version 2.1.8 wurde das doppelte Beschreibungsfeld in der Teilnehmerakte bereinigt:

- das untere Feld `Beschreibung` unterhalb von `Position in der Liste` und `Kategorie` wurde entfernt
- die Beschreibung wird nun nur noch ueber den regulären WordPress-Editor gepflegt
- beim Speichern wird der Editor-Inhalt weiterhin mit den Plugin-Daten synchronisiert

## Erweiterung 2.1.9

Für Version 2.1.9 wurde die Anzeige der Bearbeiter in den Revisionen verbessert:

- in `Revisionen` wird bei Mannschaftsprofilen nicht mehr nur der Login des Administrators verwendet
- wenn im Benutzerprofil Vorname und Nachname gepflegt sind, wird stattdessen dieser Klarname angezeigt
- die Anpassung greift fuer die Bearbeitungsansicht und die zugehoerigen Revisionsdaten

## Erweiterung 2.1.10

Für Version 2.1.10 wurde die Admin-Kontexterkennung für Mannschaftsprofile korrigiert:

- die Umschaltung auf `Feuer-Mannschaft > Alle Profile` greift nur noch auf echten Profil-Bearbeitungsseiten
- `Beitraege > Alle Beitraege` bleibt wieder die normale WordPress-Beitragsliste
- die Menue-Markierung des Plugins wird nicht mehr irrtümlich auf Standard-Beitragsseiten übernommen

## Erweiterung 2.1.11

Für Version 2.1.11 wurde ein Standard-Profilfoto für Teilnehmer ohne eigenes Bild ergänzt:

- in den allgemeinen Einstellungen kann jetzt ein Standardfoto über die WordPress-Mediathek ausgewählt oder entfernt werden
- wenn einem Teilnehmer noch kein eigenes Foto zugeordnet ist, wird automatisch dieses Standard-Profilfoto angezeigt
- die Vorschau gilt fuer Profilseiten, Uebersichten und die Admin-Liste der Profile

## Erweiterung 2.1.12

Für Version 2.1.12 wurde die Einstellung fuer das Standard-Profilfoto in den Einstellungen sichtbarer platziert:

- das Feld fuer das Standard-Profilfoto steht jetzt im Tab `Allgemein` direkt unter `Watermark`
- die Beschriftung weist jetzt explizit darauf hin, dass das Bild bei fehlendem Teilnehmerfoto verwendet wird

## Erweiterung 2.1.13

Für Version 2.1.13 wurde die Bearbeitung von Teilnehmernamen korrigiert:

- `Quick Edit` steht fuer Mannschaftsprofile wieder zur Verfuegung
- Namensänderungen ueber den normalen Beitrags-Titel werden wieder in `Vorname` und `Nachname` übernommen
- beim schnellen Bearbeiten bleiben bestehende Profilfelder wie Kategorie, Funktion und Ausbildung erhalten

## Erweiterung 2.1.14

Für Version 2.1.14 wurde eine lokale Versionsablage für den gesamten Plugin-Stand hinzugefügt:

- neue Snapshots werden in `versions/v.x.y` als vollständige Plugin-Kopie gespeichert
- dafuer wurde das Hilfsskript `tools/create-version-snapshot.ps1` angelegt
- der Ordner `versions` wird beim Kopieren bewusst ausgeschlossen, damit keine rekursiven Sicherungen entstehen

## Erweiterung 2.1.15

Für Version 2.1.15 wurde das Standard-Profilfoto enger mit dem Teilnehmerprofil verknüpft:

- wenn ein Profil kein eigenes Foto hat, wird das konfigurierte Standard-Profilfoto automatisch auch als Beitragsbild gesetzt
- dieses Standardfoto kann im Profil nun manuell entfernt oder wieder aktiviert werden, ohne die zentrale Datei aus den Einstellungen zu loeschen
- bestehende Profile ohne Foto werden nach dem Update einmalig mit dem Standard-Profilfoto synchronisiert

## Erweiterung 2.1.16

Für Version 2.1.16 wurden Kommentare fuer Mannschaftsprofile standardmäßig deaktiviert:

- beim Speichern von Mannschaftsprofilen werden `Kommentare` und `Pings` nun immer auf `geschlossen` gesetzt
- die zugehoerigen WordPress-Diskussionsboxen werden im Profil-Editor ausgeblendet
- bereits bestehende Profile mit offenen Kommentaren werden nach dem Update einmalig auf `geschlossen` synchronisiert

## Erweiterung 2.1.17

Für Version 2.1.17 wurde der sichtbare Plugin-Name auf `Feuer-Mannschaft` umgestellt:

- der Plugin-Name in der WordPress-Pluginliste wurde auf `Feuer-Mannschaft` geändert
- die begleitende Dokumentation und Historie wurden auf den neuen Namen umgestellt

## Erweiterung 2.1.18

Für Version 2.1.18 wurde die eigene Log-Tabelle auf den neuen Tabellenpraefix `feuer_mannschaft` umgestellt:

- bei Neuinstallationen wird die Log-Tabelle jetzt als `wp_feuer_mannschaft_logs` beziehungsweise mit dem WordPress-Tabellenpraefix der jeweiligen Installation angelegt
- die Deinstallation entfernt sowohl die neue Tabellenbezeichnung als auch die bisherige Legacy-Tabelle
- vorhandene alte Log-Tabellen können beim Installieren automatisch auf den neuen Namen übernommen werden

## Erweiterung 2.1.19

Für Version 2.1.19 wurden alle sichtbaren Plugin-Kurzcodes auf den neuen Präfix `feuer_mannschaft` umgestellt:

- die Kurzcode-Übersicht in den Einstellungen zeigt jetzt durchgängig die neuen `feuer_mannschaft_*`-Varianten
- die Anleitung wurde fuer Liste, Profil, Zaehler, Karten und Personen-Link auf den neuen Kurzcode-Präfix aktualisiert
- der alte Kurzcode-Präfix wurde aus der Dokumentation entfernt und durchgängig auf `feuer_mannschaft_*` umgestellt

## Erweiterung 2.3.1

Für Version 2.3.1 wurde die Archivfunktion auf Korrektheit bei Wiederherstellungen verbessert:

- Kategorien von Teilnehmerprofilen werden im Archiv jetzt zusätzlich über Slug und Namen gespeichert, damit sie auf Zielsystemen sauberer zugeordnet werden können
- Wiederherstellungen ersetzen bestehende Profilgalerien jetzt kontrolliert statt alte Anhänge stehen zu lassen
- wiederhergestellte Galerie-Dateien landen wieder im Plugin-Uploadordner `uploads/feuer-mannschaft`
- wenn ein Profil im Archiv keine Galerie hat, wird nach dem Restore der Defaultbild-Status wieder korrekt synchronisiert
- das Manifest wird vor dem eigentlichen Restore auf unvollständige Teilnehmer-, Galerie-, Erwähnungs- und Logdaten geprüft

## Erweiterung 2.3.0

Für Version 2.3.0 wurde das Plugin technisch, sicherheitsseitig und strukturell weiter bereinigt:

- verbliebene HTML-Ausgabe aus den Kernklassen wurde in zusätzliche Templates unter `templates/admin` und `templates/frontend` ausgelagert
- die Archivfunktion prüft hochgeladene ZIP-Dateien jetzt strenger auf Größe, Dateianzahl, unsichere Pfade und eine gültige `manifest.json`
- Log- und Archivmeldungen sowie Admin-Texte wurden auf den aktuellen Markenstand `Feuer-Mannschaft` vereinheitlicht
- die Log-Tabelle wird bei fehlender Tabelle nun defensiv neu angelegt, bevor geschrieben oder gelesen wird
- weitere veraltete oder inkonsistente Texte wurden auf das neue Branding und korrekte deutsche Umlaute umgestellt

## Erweiterung 2.2.0

Für Version 2.2.0 wurde das Plugin technisch vollständig auf `Feuer-Mannschaft` umgestellt:

- Hauptdatei, Klassen, Asset-Dateien und Template-Dateien wurden auf das neue Namensschema `feuer-mannschaft` beziehungsweise `feuer_mannschaft` umbenannt
- das Plugin verlangt jetzt mindestens PHP `8.0` und blockiert die Aktivierung auf aelteren PHP-Versionen
- Einstellungen, Upload-Ordner, Tabellenname, Admin-Slugs, Hooks und interne Funktionsnamen wurden auf den neuen Präfix vereinheitlicht

## Versionsstand

- Version: 2.3.32
- Autor: Walter Faerber
- Website: https://wdmin.com
