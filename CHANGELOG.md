# Änderungen

## [2.1.4] - 2026-11-26

- MGD Tools: inaktive MGD-Plugins in der Übersicht, alle Karten in Weitere Plugins (Baukasten 1.16.1).

## [2.1.3] - 2026-11-26

- MGD Tools: Plugins an- und abschalten, Lizenz je Plugin.

## [2.1.1] - 2026-11-25

- Gemeinsame Verwaltung unter MGD Tools, einheitliche Darstellung, Icons.
- Der Slider läuft jetzt auch ohne WooCommerce: Prime holt die Slides wahlweise aus **WordPress-Inhalten** (Beiträge, Seiten, andere öffentliche Inhaltstypen mit Beitragsbild) oder aus **Shop-Artikeln** (nur mit WooCommerce). Auswahl im Slider unter „Inhalte für Prime“. Bestehende Slider bleiben bei Shop-Artikeln.
- Neues **Divi-Modul „MGD Slider“** (Divi 5, nutzt dieselben Slider). Ohne Divi passiert nichts.
- Name im Plugin-Kopf: „MGD Slider (WordPress, WooCommerce, Divi)“. Ordner und Plugin-Datei bleiben gleich, damit Updates weiter gehen.
- Baukasten 1.15: alles hängt jetzt unter dem Menü **MGD Tools** (Übersicht und Updates, Lizenz, Weitere Plugins, Seite „Slider“ mit der Slider-Liste). Das eigene Hauptmenü „MGD Slider“ und die eigene Lizenzseite sind weg. Der Baukasten liegt jetzt in `src/Kit`, die alte eigene Meldung (`inc/Install`) ist entfernt.
- Icons (`assets/icon-128x128.png`, `icon-256x256.png`), genormter Plugin-Kopf „MGD Slider“ mit einem Satz Beschreibung.
- Akzentfarbe im Slider nutzt den MGD Farbwähler (`data-mgd-color`).
- `check-plugin-ready` ist grün. In der Woo-Demo geprüft: MGD Tools mit Unterseite „Slider“, kein eigenes Menü, Wartungsmodus bleibt an.
- Tests: `tests/divi-smoke.php` (Divi-Attrappe). In der Woo-Demo geprüft: WordPress-Inhalte neueste/manuell/zufällig, Shop-Quelle unverändert. Divi-Modul noch nicht in einem echten Divi gesehen.

## 1.6.0

- Brückenversion: Das Plugin nutzt jetzt den gemeinsamen Baukasten von theme.michael-gahn.de (Ordner `inc/Kit`).
- Neue Seite „MGD Slider“ (Info und Updates) im Menü MGD Slider: Version, Updates, Weitere Plugins (mit Installieren-Knopf für kostenlose Plugins) und der Schalter „Installation melden“.
- Update-Anzeige in WordPress über theme.michael-gahn.de (Prüfsumme geprüft, kein automatisches Update).
- Die alte eigene Meldung (`inc/Install`) wird nicht mehr angemeldet. Die Dateien bleiben noch liegen und fliegen in der Endversion raus. Die alte Installations-Kennung bleibt.
- Beim Löschen des Plugins meldet es sich ab. Die Slider bleiben wie bisher erhalten.
- Der Slider selbst ist unverändert.

## 1.5.0

- Neu: Install-Meldung (Free-Lizenz). Meldet sich höchstens einmal am Tag bei theme.michael-gahn.de (Plugin, Version, Shopsystem, Domain, zufällige ID). Abschaltbar in den Einstellungen.
- Gemeldet wird höchstens einmal am Tag im WordPress-Admin. Nach dem Aktivieren erscheint einmal ein Hinweis mit Link zum Schalter.
- Schalter unter „MGD Slider“ › „Lizenzen“, ab Werk an. Abgeschaltet wird der Eintrag auf der Plattform gelöscht.
- Der Slider läuft immer voll weiter, auch ohne Plattform.

## 1.4.0

- Neue Versionsnummer 1.4.0: Alle MGD-Produkte haben jetzt die gleiche Version. Das Plugin ist trotzdem noch früh (siehe README).
- Slider-Kern abgeglichen: Text auf dem Bild besser lesbar, auf dem Handy eine feste Höhe, Button-Höhe korrigiert, Beschriftungen der Knöpfe lassen sich übersetzen (sonst Deutsch).
- Pro-Lizenz und Update-Kanal: gibt es noch nicht.

## 0.1.1 (Pre-Alpha)

- In der MGD-Demo (WooCommerce) installiert und aktiviert. Shortcode und Block sind da.
- Prime "meistverkauft" mit echten Artikeln geprüft: 4 Slides. Dafür liegt dort jetzt ein Slider "Prime Demo".
- Versionsnummer überall auf 0.1.1 (Plugin-Kopf, Konstante, Block).
- Weiter offen: Prime zufällig/Kategorie/manuell mit echten Artikeln, Drag & Drop, Live-Vorschau, Pro-Lizenz, Update-Kanal.

## 0.1.0 (Pre-Alpha)

- Erstes Gerüst: Slider-Skript mit Tastatur, Wischen, Pausenknopf, reduzierte Bewegung.
- Datenmodell mit Klassisch und Prime, Limit von 5 Slidern (Free).
- Anbindung an WooCommerce als Platzhalter (siehe README, Abschnitt "Was schon geht").
