# Feuer-Mannschaft Plugin Historie

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

- Version: 2.3.13
- Autor: Walter Färber
- Website: https://ff-lurup.de
