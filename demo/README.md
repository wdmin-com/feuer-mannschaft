# Demo-Sicherung

`feuer-mannschaft-demo-backup.zip` ist eine vorbereitete Test-Sicherung für den Import über **Mannschaft → Sicherungen → Sicherung wiederherstellen**.

Das Archiv enthält 25 fiktive Profile mit Funktionen, Dienstgraden, Ausbildungen, Porträtfotos und einem eingerichteten Kartenraster. Es enthält keine Kontaktdaten oder Bilder realer Personen. Nach dem Import wird das erste Galeriebild automatisch als Kartenbild verwendet.

Um das Archiv nach Änderungen an den Demo-Daten zu aktualisieren, führen Sie im Plugin-Stammverzeichnis Folgendes aus:

```powershell
powershell -ExecutionPolicy Bypass -File .\tools\create-demo-backup.ps1
```

Die Sicherung ist für eine Testinstallation oder eine leere Website gedacht. Erstellen Sie vor der Wiederherstellung auf einer produktiven Website eine reguläre Sicherung. Beim Import werden Plugin-Einstellungen aktualisiert und Profile anhand ihres Slugs hinzugefügt oder aktualisiert.
