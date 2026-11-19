# Deployment und Update-Ablauf

## Zielbild

Die empfohlene Trennung ist:

- GitHub: Quellcode, Dokumentation, Historie, Skripte
- `wdmin.com`: veroeffentlichtes `update.json` und das auslieferbare ZIP
- WordPress-Zielseite: installiert das Plugin normal ueber das ZIP oder ueber vorhandene Updates

## Serverstruktur fuer Updates

Der Update-Endpunkt ist aktuell fest auf diese Basis-URL vorbereitet:

- `https://wdmin.com/plugins/feuer-mannschaft/`

Auf dem Webserver sollten dort mindestens diese Dateien liegen:

```text
/plugins/feuer-mannschaft/
  index.php
  update.json
  release/2.3.62/feuer-mannschaft.zip
  release/latest/feuer-mannschaft.zip
  legacy/update.json
  legacy/feuer-mannschaft.zip
```

Empfehlung fuer die Ablage auf dem Server:

- eigener Ordner ausserhalb des WordPress-Plugin-Verzeichnisses der Zielseite
- direkter HTTPS-Zugriff ohne Login oder Cookie
- stabiles `update.json` im Hauptordner und versionsbezogene ZIP-Pfade fuer Releases

## Was fuer Updates zwingend noetig ist

1. `update.json` muss eine hoehere Version als das installierte Plugin melden.
2. `download_url` in `update.json` muss auf das aktuelle ZIP zeigen.
3. Das ZIP muss im Inneren den Plugin-Hauptordner `feuer-mannschaft/` enthalten.
4. In `feuer-mannschaft.php` muss dieselbe Version stehen wie im Manifest.

`update.json` ist ein Artefakt des Update-Servers und wird bewusst nicht in
das Plugin-ZIP aufgenommen. So kann die SHA-256-Pruefsumme des ZIPs nicht
durch eine im ZIP enthaltene, nachtraeglich geaenderte Manifestdatei
unbrauchbar werden.

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
powershell.exe -ExecutionPolicy Bypass -File .\tools\build-release-package.ps1 -Version 2.3.62
```

7. Diese Dateien auf `wdmin.com/plugins/feuer-mannschaft/` hochladen:
   - `index.php`
   - `release/2.3.62/feuer-mannschaft.zip`
   - `release/latest/feuer-mannschaft.zip`
   - `update.json`
   - optional fuer alte Installationen: `legacy/update.json` und `legacy/feuer-mannschaft.zip`

8. Fuer Installationen bis 2.3.30 muss der alte Endpunkt ebenfalls erreichbar sein:
   - `https://walterfaerber.eu/plugins/feuer-mannschaft/update.json`
   - `https://walterfaerber.eu/plugins/feuer-mannschaft/feuer-mannschaft.zip`

   Dafuer entweder die Dateien aus `release/legacy/` dort ablegen oder einen Redirect auf `https://wdmin.com/plugins/feuer-mannschaft/update.json` und `https://wdmin.com/plugins/feuer-mannschaft/release/latest/feuer-mannschaft.zip` setzen.

9. Danach in einer WordPress-Testinstallation `Dashboard -> Aktualisierungen` oder `Plugins` aufrufen und pruefen, ob das Update angeboten wird.

## Wie der Code im Repository liegen sollte

Empfohlene Struktur im GitHub-Repository:

- `assets/`, `includes/`, `templates/`: eigentlicher Plugin-Code
- `tools/`: lokale Hilfsskripte fuer Dokumentation, Snapshots und Release-Pakete
- `Version/`: lokale Archivstaende, wenn du diese weiterhin im Repository behalten willst
- `README.md`, `ANLEITUNG.md`, `DEPLOYMENT.md`, `DOKUMENTATION.md`: technische und fachliche Dokumentation

Empfehlung fuer saubere Wartung:

- keine produktiven Uploads oder Backup-Dateien im Repository speichern
- das Release-ZIP nicht manuell im Plugin-Ordner pflegen, sondern aus dem Quellstand erzeugen
- Releases in versionsbezogenen Unterordnern wie `release/2.3.62/` ablegen
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
- die Entwickler-Landing-Page `index.php` wird separat im Serverordner abgelegt
  und ist nicht Bestandteil des WordPress-Update-ZIP
