# Feuer-Mannschaft 2.7.12

WordPress-Plugin für Teilnehmerprofile mit Medien, Taxonomien, Archivierung,
Organisationsstruktur, Erwähnungen, Import und Export.

## Wichtige Shortcodes

- `[feuer_member id="123"]` - einzelnes Profil
- `[feuer_members department="einsatz" limit="8"]` - gefilterte Auswahl
- `[feuer_member_directory]` - Suche, Filter, Sortierung und URL-Zustand
- `[feuer_org_chart]` - Organigramm
- `[feuer_service_history id="123"]` - Dienstverlauf
- `[feuer_archived_members]` - ehemalige Mitglieder
- `[feuer_mannschaft_funktion funktion="Wehrführer"]` - aktive Person mit einer Funktion
- `[feuer_mannschaft_verantwortliche haupt="Wehrführer" stellvertreter="Wehrführer-Vertreter"]` - Hauptverantwortliche und Stellvertretung; bei unbesetzter Hauptfunktion wird nur die Stellvertretung gezeigt
- `[feuer_member_qr id="123"]` und `[feuer_member_actions id="123"]`
- `[feuer_member_breadcrumbs]` - Profil-Breadcrumbs

Die Verwaltung bietet granulare Rollen und Feldsichtbarkeit, frei sortierbare
Organisationsstrukturen, bidirektionale Publikationsbeziehungen, CSV-Vorschau
mit Feldzuordnung und Rücknahme, ZIP-Export samt Bildern, Datenqualität,
getrennte Karten-/Profilbilder, lokalen QR-Code, SEO und Opt-in-Automatisierung.

Für Teamübersichten stehen drei neue Kartenstile bereit: die helle Porträt-Karte,
die kompakte Horizontal-Karte und die Editorial-Karte. Alle drei zeigen farbige
Fotos ohne dunklen Textgradienten und verwenden eine zurückhaltende Typografie.
Die Standardvorlage für Teilnehmerlisten ist `Team Raster`.

Die Projektseite `index.php` zeigt Release-Daten, Architektur, Funktionen,
Shortcodes und Versionshistorie. Externe QR-Dienste werden nicht verwendet;
die QR-Grafik entsteht lokal im Browser.

Systemziel: WordPress 7.1.3 oder neuer und PHP 8.1 oder neuer. Alle Releases
liegen als ZIP in `release/<version>` und als Quellstand in `Version/<version>`.

## Lizenz

Feuer-Mannschaft ist proprietäre Software und steht nicht unter einer Open-Source-Lizenz. Die Veröffentlichung des Quellcodes auf GitHub erteilt keine Nutzungserlaubnis. Die Nutzung ist nur mit ausdrücklicher persönlicher Genehmigung des Entwicklers gestattet. Es gelten die vollständigen Bedingungen in [LICENSE.md](LICENSE.md).
