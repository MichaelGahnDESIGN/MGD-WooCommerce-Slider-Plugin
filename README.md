# MGD WooCommerce Slider Plugin

> Version 2.1.3 (frühes Plugin). Das Plugin ist ein frühes Gerüst. Es ist noch nicht für echte Shops gedacht.

Ein "Super-Slider" für WooCommerce von Michael Gahn DESIGN. Zwei Arten:

- **Klassisch:** Du legst Slides selbst an (nur Bild oder Bild mit Text und Button).
- **Prime:** Die Slides entstehen automatisch aus deinen Shop-Artikeln. Du stellst nur ein, wie viele Slides es sein sollen. Quelle: zufällig, meistverkauft, Kategorie oder manuell (du wählst Artikel, daraus wird zufällig gezeigt).

In der kostenlosen Version kannst du bis zu 5 Slider anlegen. Mit einer Pro-Lizenz sind es unbegrenzt viele.

## Was schon geht und was nicht

- ✅ Lädt in WordPress, legt den Beitragstyp "Slider" an, Backend-Seite mit Einstellungen und Slides, Shortcode und Block (siehe Abschnitt Tests im Hauptplan).
- ✅ Limit von 5 Slidern wird serverseitig geprüft.
- ✅ Prime "meistverkauft" in der MGD-Demo (WooCommerce) mit echten Artikeln geprüft: 4 Slides mit Bild, Titel und Button.
- ☑️ Prime zufällig, Kategorie und manuell sind noch nicht mit echten Artikeln geprüft.
- ☑️ Noch offen: Drag & Drop zum Sortieren, Live-Vorschau, Colorpicker, Kategorie-Auswahlliste, Plugin-Empfehlungen.

## Für WordPress, WooCommerce und Divi

- **Ohne WooCommerce:** Bei Art „Prime“ wählst du unter „Inhalte für Prime“ **WordPress-Inhalte**. Die Slides entstehen dann aus Beiträgen oder Seiten mit Beitragsbild (neueste zuerst, zufällig, nach Kategorie oder per ID).
- **Mit WooCommerce:** Du kannst weiter **Shop-Artikel** wählen (meistverkauft, zufällig, Kategorie, manuell).
- **Divi:** Im Divi-Builder gibt es das Modul **MGD Slider**. Dort wählst du einen Slider aus. Der Shortcode `[mgd_slider id="..."]` geht in Divi auch im Text- oder Code-Modul.

## MGD Tools: an- und ausschalten, Lizenz

Unter **MGD Tools** siehst du alle MGD-Plugins auf einen Blick. Beim Slider gibt es dort:

- **Aktivieren / Deaktivieren:** Du kannst das Plugin direkt in MGD Tools an- oder ausschalten. Deine Slider bleiben dabei erhalten.
- **Lizenz:** Hier steht, ob du die kostenlose Version (Free) oder eine Pro-Lizenz nutzt und ob die Lizenz gültig ist. Eine Pro-Lizenz für den Slider gibt es noch nicht.

## Info, Updates und Weitere Plugins

Im Menü **MGD Slider** gibt es die Seite **MGD Slider** (Info und Updates). Dort stehen:

- die Version und ob es ein Update gibt (das Update kommt wie bei jedem WordPress-Plugin, es wird nichts ohne dein Zutun installiert),
- **Weitere Plugins**: andere MGD-Plugins zum Ansehen, kostenlose kannst du mit einem Klick installieren (sie werden nie von selbst aktiviert),
- der Schalter **Installation bei Michael Gahn DESIGN melden** (ab Werk an). Das Plugin meldet sich höchstens einmal am Tag bei theme.michael-gahn.de (Plugin, Version, Shopsystem, Domain, zufällige ID). Abgeschaltet läuft der Slider genauso weiter.

## Installation, Schritt für Schritt

1. Repo als ZIP herunterladen (grüner Knopf "Code", dann "Download ZIP") oder die ZIP aus dem Release nehmen.
2. In WordPress: Plugins, Installieren, Plugin hochladen, ZIP wählen, aktivieren.
3. Im Menü "MGD Slider" einen neuen Slider anlegen und speichern.
4. Den Shortcode (z. B. `[mgd_slider id="12"]`) in eine Seite einfügen, oder den Block "MGD Slider" wählen.

## Slider-Regeln für Barrierefreiheit

- Es gibt einen Pausenknopf, sobald sich der Slider von selbst bewegt.
- Bedienung mit Tastatur: Pfeiltasten links und rechts, Pos1 und Ende.
- Wer "Bewegung reduzieren" im System eingestellt hat, bekommt keinen automatischen Wechsel.
- Keine externen Schriften und kein CDN: Alles liegt im Plugin (Datenschutz).

## Aufbau (Kurzfassung)

Der Kern (Slider-Skript, Stil, Auswahl-Logik) ist auf allen Plattformen gleich und wird aus einem gemeinsamen Quellordner erzeugt. Nur die Anbindung an WooCommerce ist je Plattform anders. Den ganzen Plan gibt es im privaten Hauptprojekt (docs/SLIDER-PLUGINS.md).

## Hilfe und Fehler melden

Bitte als Issue in diesem Repository melden. Das ist keine Rechtsberatung: Die Lizenzfrage wird noch geprüft (siehe LICENSE).
