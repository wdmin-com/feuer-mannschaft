# Mitgliederprozesse und Automatisierung

## Aufnahme, aktive Phase und Austritt

Die Mitgliedsphase wird direkt im Profil unter „Mitgliedsstatus, Einsatzberichte und Dokumente“ verwaltet.

- **Aufnahme / Prüfung** speichert das Profil als Entwurf.
- **Aktiv** gibt das Profil zur Veröffentlichung frei. Die vorhandene Prüfung auf Pflichtangaben bleibt wirksam.
- **Austritt in Bearbeitung** hält den Austritt zur Bearbeitung fest.
- **Ausgeschieden / archiviert** nimmt das Profil aus der aktiven Mannschaft und speichert das Archivdatum.

Das WordPress-Archiv bleibt über die vorhandene Archivansicht zugänglich. Ein Profil kann nach Korrektur wieder aktiviert und veröffentlicht werden.

## Lehrgänge und Nachweise

Unter „Weitere Lehrgänge“ kann pro Qualifikation ein Ablaufdatum eingetragen werden. Ein leeres Datum bedeutet **unbefristet**. Die bestehende Automatisierung „Ablaufende Qualifikationen melden“ berücksichtigt befristete Einträge und sendet Erinnerungen über den konfigurierten Empfänger.

PDF- und Bildnachweise lassen sich einem Profil und optional einer Qualifikation zuordnen. Dateien liegen außerhalb des WordPress-Webverzeichnisses. Der Download erfordert eine Anmeldung, eine gültige WordPress-Nonce und die Berechtigung zum Bearbeiten des Profils. Nachweise lassen sich im Datenarchiv separat sichern und wiederherstellen. Beim Entfernen des Plugins werden diese pluginverwalteten Dateien mit entfernt.

## Einsatzbereitschaft und Profilprüfung

Die Seite **Mannschaft → Einsatzbereitschaft** listet Status, Abteilung, Dienstgrad, Funktion, Qualifikationen, Ablaufdaten, jährlichen Prüfstatus und Archivdatum. Sie ist nur für Benutzer mit der Mannschaft-Verwaltungsberechtigung verfügbar.

Die jährliche Profilprüfung und ihre Erinnerungen verwenden die vorhandene Einstellung „Jährliche Prüfung der Profildaten erinnern“ und die Bestätigung im Profil.

## Feuer-Einsatzberichte

Das Profil enthält einen Link zur Teilnehmerverwaltung von Feuer-Einsatzberichte, falls das Plugin aktiv ist. Die dortige Synchronisierung bleibt manuell und bietet eine Vorschau sowie Prüfung möglicher Namenskonflikte. Das verhindert stilles Überschreiben von Einsatzdaten.

## Änderungsprotokoll

Änderungen an Vorname, Nachname, Dienstgrad, Funktion, Qualifikationen, Eintritt, Tätigkeit, Kategorien, Mitgliedsphase und WordPress-Status werden im vorhandenen Mannschaft-Protokoll mit Benutzer und Zeit erfasst. Protokolle sind im Bereich **Einstellungen → Protokolle** einsehbar und unterliegen der konfigurierten Aufbewahrungsdauer.
