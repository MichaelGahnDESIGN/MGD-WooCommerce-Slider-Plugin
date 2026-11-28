<?php

declare(strict_types=1);

namespace MGD\WooSlider\Kit\Platform\WordPress;

// alle Texte von MGD Tools an einem Ort (Freigabe Michael: docs/plans/MGD-TOOLS-TEXTE-FREIGABE.md)
final class ToolsTexts
{
    public const MENU = 'MGD Tools';
    public const OVERVIEW = 'Übersicht und Updates';
    public const OVERVIEW_H1 = 'Deine MGD-Plugins';
    // Kopf (Text 51) und Abzeichen Fehler (Text 52), Freigabe offen
    public const INTRO = 'Alle Plugins von Michael Gahn DESIGN an einem Ort: Updates, Lizenz und Einstellungen.';
    public const ERROR_BADGE = 'Fehler';
    public const LICENSE = 'Lizenz';
    public const CATALOG = 'Weitere Plugins';
    public const COLS = ['Plugin', 'Installiert', 'Neueste', 'Status', 'Lizenz', 'Update-Prüfung', 'Aktionen'];
    public const CURRENT = 'Aktuell';
    public const NEWER = 'Update verfügbar';
    public const UNKNOWN = 'Unbekannt';
    public const CHECK_OK = 'Klappt, zuletzt am %s';
    public const CHECK_OK_NODATE = 'Klappt';
    public const CHECK_FAIL = 'Hat am %s nicht geklappt. WordPress versucht es später noch einmal.';
    public const CHECK_NONE = 'Noch keine Update-Datei veröffentlicht. Das ist normal bei neuen Plugins.';
    public const CHECK_OFF = 'Nicht eingerichtet';
    public const CHECK_NOW = 'Jetzt prüfen';
    public const CHECKED = 'Updates und Lizenz werden für alle MGD-Plugins neu geprüft.';
    public const SETTINGS = 'Einstellungen';
    public const DETAILS = 'Details';
    public const UPDATE_LINK = 'Zu den Plugins';
    public const INSTALL = 'Installation melden';
    public const LICENSE_INTRO = 'Trag deinen Lizenzschlüssel hier einmal ein. Er gilt für alle MGD-Plugins auf dieser Website.';
    public const LICENSE_UNLOCKS = 'Dieser Schlüssel schaltet frei:';
    public const LICENSE_OFF = 'Für deine MGD-Plugins gibt es noch keine Pro-Funktionen.';
    public const LICENSE_ELSEWHERE = 'Die Lizenz verwaltest du unter MGD Tools › Lizenz.';
    public const MOVED = 'Die MGD-Plugins findest du jetzt gesammelt im Menü „MGD Tools“.';
    // Lizenz je Plugin + an/aus (Kit 1.16.0, freigegeben von Michael 26.11.: docs/plans/MGD-TOOLS-AKTIVIEREN-LIZENZ-ENTWURF.md)
    public const LIC_FREE = 'Free';
    public const LIC_PRO = 'Pro';
    public const LIC_VALID_UNTIL = 'Gültig bis %s';
    public const LIC_VALID = 'Gültig';
    public const LIC_EXPIRED = 'Abgelaufen';
    public const LIC_INVALID = 'Ungültig';
    public const LIC_NONE = 'Nicht eingetragen';
    public const LIC_NOT_INCLUDED = 'Nicht in deiner Lizenz enthalten';
    public const LIC_GRACE = 'Gültig (letzter geprüfter Stand)';
    public const LIC_NO_PRO = 'Keine Pro-Version';
    public const LIC_CHECKED = 'Lizenz geprüft am %s. Das Ergebnis wird bis zu 24 Stunden gespeichert.';
    // gleicher Text wie auf der Karte in Weitere Plugins
    public const INACTIVE = 'Installiert · inaktiv';
    public const ACTIVATE = 'Aktivieren';
    public const DEACTIVATE = 'Deaktivieren';
    public const ACTIVATED = '%s ist jetzt aktiv.';
    public const DEACTIVATED = '%s ist jetzt deaktiviert.';
    public const ACTIVATE_FAIL = '%s konnte nicht aktiviert werden. Mehr dazu findest du unter „Plugins“.';
    // Primärfarbe (Kit 1.17, Texte C1-C11: docs/plans/MGD-TOOLS-FARBE-ENTWURF.md)
    public const SETTINGS_PAGE = 'Einstellungen';
    public const SETTINGS_INTRO = 'Hier stellst du ein, wie MGD Tools und alle MGD-Plugins in deinem WordPress-Admin aussehen.';
    public const ACCENT = 'Primärfarbe';
    public const ACCENT_HELP = 'Gilt für das Menü MGD Tools und für Knöpfe, Abzeichen und Links aller MGD-Plugins. Die Schriftfarbe darauf wählt MGD Tools automatisch, damit alles gut lesbar bleibt.';
    public const PREVIEW = 'Vorschau';
    public const SAVE = 'Speichern';
    public const ACCENT_DEFAULT = 'Auf MGD-Rot zurücksetzen';
    public const ACCENT_SAVED = 'Die Primärfarbe ist gespeichert.';
    public const ACCENT_RESET = 'Die Primärfarbe ist wieder MGD-Rot.';
    public const ACCENT_LIGHT = 'Diese Farbe ist sehr hell. Links werden deshalb etwas dunkler dargestellt, damit sie gut lesbar bleiben.';
    public const ACCENT_BAD = 'Bitte wähle eine Farbe im Format #rrggbb.';
    // ein Hinweis + ein Schalter für alle (Kit 1.18, Texte freigegeben Koordination 28.11.)
    public const NOTICE_ALL = 'Die MGD-Plugins%s melden sich einmal bei theme.michael-gahn.de an und danach höchstens einmal am Tag. Gesendet werden nur Plugin-Name, Version, die Domain deiner Website und eine zufällige Installations-ID. So bekommst du Updates, und wir wissen, wie viele Websites die Plugins nutzen. Du kannst das unter MGD Tools › Übersicht abschalten.';
    public const NOTICE_LINK = 'MGD Tools › Übersicht';
    public const INSTALL_ALL = 'Installation aller MGD-Plugins bei Michael Gahn DESIGN melden';
    public const INSTALL_ALL_HINT = 'Die MGD-Plugins melden sich einmal bei theme.michael-gahn.de an und danach höchstens einmal am Tag. Gesendet werden nur Plugin-Name, Version, die Domain deiner Website und eine zufällige Installations-ID. Die Plugins funktionieren auch ohne Meldung genauso weiter.';
    public const INSTALL_ADVANCED = 'Erweitert: je Plugin einzeln';
    public const INSTALL_MIXED = 'Einige Plugins sind abweichend eingestellt (siehe Erweitert).';
    public const INSTALL_SAVED = 'Die Einstellung gilt jetzt für alle MGD-Plugins.';
    // Lizenz-Status-Karte (Kit 1.18, Texte zur Freigabe an Koordination)
    public const LIC_CARD = 'Deine Lizenz';
    public const LIC_ART = 'Art';
    public const LIC_PACKAGE = 'Paket';
    public const LIC_STATUS = 'Status';
    public const LIC_UNTIL = 'Gültig bis';
    public const LIC_DOMAIN = 'Domain';
    public const LIC_LAST_CHECK = 'Zuletzt geprüft';
    public const LIC_PER_PLUGIN = 'Freigeschaltet je Plugin';
    public const KEY_COPY = 'Schlüssel kopieren';
    public const KEY_COPIED = 'Der Schlüssel ist in der Zwischenablage.';
    public const KEY_COPY_FAIL = 'Kopieren hat nicht geklappt. Der Schlüssel steht jetzt markiert im Feld, bitte mit Strg+C (Mac: Cmd+C) kopieren.';
    public const ERROR = 'Die Seite konnte gerade nicht geladen werden.';
    public const RETRY = 'Das hat nicht geklappt. Bitte lade die Seite neu und versuche es noch einmal.';
}
