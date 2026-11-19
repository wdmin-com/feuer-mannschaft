# Feuer-Mannschaft

WordPress-Plugin zur Verwaltung von Mannschaftsprofilen mit:

- Profilfeldern fuer Mitglieder
- Galerie und Einzelansichten
- Archivierung und Backup/Restore
- flexiblen Listenfeldern
- mehreren Frontend-Templates
- benutzerdefinierter Update-Pruefung ueber `wdmin.com`
- zentraler Site-Registry fuer den Ueberblick, auf welchen Websites das Plugin eingesetzt wird

## Projektdateien

- [ANLEITUNG.md](./ANLEITUNG.md): Bedienung und Nutzung im WordPress-Backend
- [DOKUMENTATION.md](./DOKUMENTATION.md): Dateikarte und PHP-Funktionsindex
- [DEPLOYMENT.md](./DEPLOYMENT.md): Release-, Update- und Server-Ablauf
- [HISTORIE.md](./HISTORIE.md): Versionshistorie

## Entwicklung

Empfohlener Arbeitsablauf:

1. Quellcode im GitHub-Repository pflegen.
2. Plugin-Version in `feuer-mannschaft.php` anheben.
3. `HISTORIE.md` und `update.json` fuer die neue Version aktualisieren.
4. `powershell.exe -ExecutionPolicy Bypass -File .\tools\build-plugin-docs.ps1` ausfuehren.
5. Bei Bedarf einen Versions-Snapshot mit `tools/create-version-snapshot.ps1` erzeugen.
6. Release-ZIP mit `tools/build-release-package.ps1` bauen.
7. `feuer-mannschaft.zip` und `update.json` auf den Update-Server hochladen.

## Updates

Das Plugin fragt Updates aktuell unter dieser URL ab:

- `https://wdmin.com/plugins/feuer-mannschaft/update.json`

Die ZIP-Datei fuer WordPress-Updates wird hier erwartet:

- `https://wdmin.com/plugins/feuer-mannschaft/feuer-mannschaft.zip`

GitHub dient damit als Quellcode- und Release-Repository, waehrend
`wdmin.com` der veroeffentlichte Update-Endpunkt fuer produktive
WordPress-Installationen bleibt.

## Site-Registry

Fuer die zentrale Uebersicht aller Installationen stehen im Projekt zusaetzlich bereit:

- `usage-registry.php`: zentraler Endpoint fuer Heartbeats und Listenabrufe
- `usage-registry-config.sample.php`: Vorlage fuer Write Key, Read Key und Datendatei

Die eigentlichen WordPress-Seiten senden mit dem Plugin regelmaessig ihre
Site-URL, Version und den letzten Heartbeat an diesen Endpoint. Eine
Installation mit gesetztem Read Key kann die komplette Liste anschliessend im
Tab `Verwendung` anzeigen.

Der Endpoint selbst ist kein Bestandteil des WordPress-Release-ZIP und wird
separat auf `wdmin.com` bereitgestellt.
