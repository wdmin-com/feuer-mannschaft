# Feuer-Mannschaft 2.4.1

WordPress-Plugin fuer isolierte Teilnehmerprofile mit Medien, Taxonomien,
Archivierung, bidirektionalen Erwaehnungen und Bootstrap-kompatibler Ausgabe.

## Wichtige Shortcodes

- `[feuer_member id="123"]` - einzelnes Profil
- `[feuer_members department="einsatz" limit="8"]` - gefilterte Auswahl
- `[feuer_member_directory]` - Suche, Filter, Sortierung und URL-Zustand
- `[feuer_org_chart]` - Organigramm
- `[feuer_service_history id="123"]` - Dienstverlauf
- `[feuer_archived_members]` - ehemalige Mitglieder
- `[feuer_member_qr id="123"]` und `[feuer_member_actions id="123"]`
- `[feuer_member_breadcrumbs]` - Profil-Breadcrumbs

Die Verwaltung bietet granulare Rollen und Feldsichtbarkeit, frei sortierbare
Organisationsstrukturen, bidirektionale Publikationsbeziehungen, CSV-Vorschau
mit Feldzuordnung und Ruecknahme, ZIP-Export samt Bildern, Datenqualitaet,
getrennte Karten-/Profilbilder, lokalen QR-Code, SEO und Opt-in-Automatisierung.

Version 2.4.1 korrigiert den WordPress-Archivstatus samt automatischer
Bestandsmigration, haertet Import und Rollback, repariert Beziehungsexporte
und erzwingt die Sichtbarkeit auch in REST, Filtern und SEO. Externe
QR-Dienste werden nicht verwendet; die QR-Grafik entsteht lokal im Browser.

Systemziel: WordPress 7.1.3 oder neuer und PHP 8.1 oder neuer. Alle Releases
liegen als ZIP in `release/<version>` und als Quellstand in `Version/<version>`.
