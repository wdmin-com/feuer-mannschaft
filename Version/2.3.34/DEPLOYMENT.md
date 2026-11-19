# Deployment und Update-Ablauf

## Zielbild

Die empfohlene Trennung ist:

- GitHub: Quellcode, Dokumentation, Historie, Skripte
- `wdmin.com`: veroeffentlichtes `update.json`, das auslieferbare ZIP und den zentralen Registry-Endpoint
- WordPress-Zielseite: installiert das Plugin normal ueber das ZIP oder ueber vorhandene Updates

## Serverstruktur fuer Updates

Der Update-Endpunkt ist aktuell fest auf diese Basis-URL vorbereitet:

- `https://wdmin.com/plugins/feuer-mannschaft/`

Auf dem Webserver sollten dort mindestens diese Dateien liegen:

```text
/plugins/feuer-mannschaft/
  update.json
  feuer-mannschaft.zip
  usage-registry.php
  usage-registry-config.php
```

Empfehlung fuer die Ablage auf dem Server:

- eigener Ordner ausserhalb des WordPress-Plugin-Verzeichnisses der Zielseite
- direkter HTTPS-Zugriff ohne Login oder Cookie
- stabile Dateinamen, damit installierte Seiten immer dieselbe URL abfragen

## Was fuer Updates zwingend noetig ist

1. `update.json` muss eine hoehere Version als das installierte Plugin melden.
2. `download_url` in `update.json` muss auf das aktuelle ZIP zeigen.
3. Das ZIP muss im Inneren den Plugin-Hauptordner `feuer-mannschaft/` enthalten.
4. In `feuer-mannschaft.php` muss dieselbe Version stehen wie im Manifest.

## Release-Schritte

1. Quellcode aendern.
2. Version in `feuer-mannschaft.php` anheben.
3. `HISTORIE.md` aktualisieren.
4. `update.json` auf dieselbe Version anheben.
5. Dokumentation neu erzeugen:

```powershell
powershell.exe -ExecutionPolicy Bypass -File .\tools\build-plugin-docs.ps1
```

6. Release-ZIP bauen:

```powershell
powershell.exe -ExecutionPolicy Bypass -File .\tools\build-release-package.ps1 -Version 2.3.34
```

7. Diese Dateien auf `wdmin.com/plugins/feuer-mannschaft/` hochladen:
   - `release/feuer-mannschaft.zip`
   - `update.json`
   - `usage-registry.php`
   - `usage-registry-config.php` auf Basis von `usage-registry-config.sample.php`

8. Danach in einer WordPress-Testinstallation `Dashboard -> Aktualisierungen` oder `Plugins` aufrufen und pruefen, ob das Update angeboten wird.

## Wie der Code im Repository liegen sollte

Empfohlene Struktur im GitHub-Repository:

- `assets/`, `includes/`, `templates/`: eigentlicher Plugin-Code
- `tools/`: lokale Hilfsskripte fuer Dokumentation, Snapshots und Release-Pakete
- `Version/`: lokale Archivstaende, wenn du diese weiterhin im Repository behalten willst
- `README.md`, `ANLEITUNG.md`, `DEPLOYMENT.md`, `DOKUMENTATION.md`: technische und fachliche Dokumentation

Empfehlung fuer saubere Wartung:

- keine produktiven Uploads oder Backup-Dateien im Repository speichern
- das Release-ZIP nicht manuell im Plugin-Ordner pflegen, sondern aus dem Quellstand erzeugen
- immer erst in GitHub committen oder taggen, dann ZIP bauen und auf den Update-Server laden

## GitHub-Nutzung

Empfohlener Ablauf:

1. Entwicklung im Repository
2. optional Tag wie `v2.3.30` setzen
3. Release-ZIP erzeugen
4. ZIP als GitHub-Release-Asset ablegen
5. dieselbe ZIP-Datei auf `wdmin.com` hochladen

Wichtig:

- der eingebaute Updater verwendet aktuell `wdmin.com` als Quelle
- GitHub ist damit zunaechst die Quellverwaltung, nicht direkt die WordPress-Update-Quelle
- spaeter kann der Updater auch auf GitHub Releases oder eine API umgestellt werden, falls du die Update-Quelle wechseln willst

## Site-Registry einrichten

Fuer die zentrale Liste der verwendeten Websites:

1. `usage-registry-config.sample.php` in `usage-registry-config.php` kopieren.
2. Einen langen `WRITE_KEY` und `READ_KEY` setzen.
3. `usage-registry.php` und `usage-registry-config.php` nach `wdmin.com/plugins/feuer-mannschaft/` hochladen.
4. Auf allen WordPress-Installationen im Plugin-Tab `Verwendung` denselben Write Key eintragen.
5. Nur auf der zentralen Verwaltungsseite zusaetzlich den Read Key eintragen.

Danach meldet jede Installation taeglich einen Heartbeat, und die zentrale
Verwaltungsseite zeigt die komplette Liste der registrierten Websites an.

Wichtig:

- `usage-registry.php` und `usage-registry-config.php` werden bewusst separat
  auf `wdmin.com` abgelegt und nicht in das WordPress-Plugin-ZIP gepackt
- produktive Client-Sites erhalten damit nur den eigentlichen Plugin-Code und
  keine zusaetzliche oeffentliche Registry-Datei
