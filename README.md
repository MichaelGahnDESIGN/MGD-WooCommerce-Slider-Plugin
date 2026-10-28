# MGD WooCommerce Slider Plugin

> Pre-Alpha 0.1.0. Das Plugin ist ein frühes Gerüst. Es ist noch nicht für echte Shops gedacht.

Ein "Super-Slider" für WooCommerce von Michael Gahn DESIGN. Zwei Arten:

- **Klassisch:** Du legst Slides selbst an (nur Bild oder Bild mit Text und Button).
- **Prime:** Die Slides entstehen automatisch aus deinen Shop-Artikeln. Du stellst nur ein, wie viele Slides es sein sollen. Quelle: zufällig, meistverkauft, Kategorie oder manuell (du wählst Artikel, daraus wird zufällig gezeigt).

In der kostenlosen Version kannst du bis zu 5 Slider anlegen. Mit einer Pro-Lizenz sind es unbegrenzt viele.

## Was schon geht und was nicht

- ✅ Lädt in WordPress, legt den Beitragstyp "Slider" an, Backend-Seite mit Einstellungen und Slides, Shortcode und Block (siehe Abschnitt Tests im Hauptplan).
- ✅ Limit von 5 Slidern wird serverseitig geprüft.
- ☑️ Prime-Abfrage (zufällig, meistverkauft, Kategorie, manuell) ist geschrieben, aber noch nicht mit echten WooCommerce-Artikeln geprüft.
- ☑️ Noch offen: Drag & Drop zum Sortieren, Live-Vorschau, Colorpicker, Kategorie-Auswahlliste, Plugin-Empfehlungen.

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
