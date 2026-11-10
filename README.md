# JSON Dashboard für Nextcloud

JSON Dashboard ist eine persönliche Nextcloud-App für bis zu acht konfigurierbare Dashboard-Widgets. Jedes Widget ruft eine HTTPS-JSON-Quelle ab, liest ein konfiguriertes Array aus dem JSON und stellt die Einträge als Karten auf dem Nextcloud-Dashboard dar.

Die App speichert die Einstellungen benutzerbezogen. Es gibt keine eigene Datenbanktabelle und keinen Build-Prozess.

## Voraussetzungen

- Nextcloud 29 oder neuer (keine künstliche obere Versionsgrenze; tatsächliche Kompatibilität wird über CI geprüft)
- PHP 8.1 oder neuer, sofern von der eingesetzten Nextcloud-Version unterstützt
- HTTPS-Zugriff vom Nextcloud-Server auf die konfigurierten JSON-Quellen
- Dashboard-App von Nextcloud

Die unterstützte App-ID ist `jsondashboard`. Der Zielpfad in einer AIO-Installation lautet normalerweise:

```
/var/www/html/custom_apps/jsondashboard
```

## Installation und Update

Das Release-Archiv mit der gewünschten Version herunterladen und die Prüfsumme prüfen:

```bash
VERSION=1.5.1
curl --fail --location \
  "https://github.com/rankerson/nextcloud_jsonDashboard/releases/download/v${VERSION}/jsondashboard-${VERSION}.tar.gz" \
  --output "jsondashboard-${VERSION}.tar.gz"
curl --fail --location \
  "https://github.com/rankerson/nextcloud_jsonDashboard/releases/download/v${VERSION}/jsondashboard-${VERSION}.tar.gz.sha256" \
  --output "jsondashboard-${VERSION}.tar.gz.sha256"
sha256sum -c "jsondashboard-${VERSION}.tar.gz.sha256"
tar -xzf "jsondashboard-${VERSION}.tar.gz"
```

Den enthaltenen Ordner `jsondashboard/` nach `custom_apps/jsondashboard` kopieren, Besitzer und Rechte setzen und die App aktivieren:

```bash
docker cp jsondashboard nextcloud-aio-nextcloud:/var/www/html/custom_apps/jsondashboard
docker exec -u root nextcloud-aio-nextcloud \
  chown -R www-data:www-data /var/www/html/custom_apps/jsondashboard
docker exec -u www-data nextcloud-aio-nextcloud \
  php occ app:enable jsondashboard
```

Container- und Pfadnamen an die eigene Nextcloud-Installation anpassen. Bei einem Update das vorhandene App-Verzeichnis vollständig durch den neuen Release-Ordner ersetzen. Die App benötigt keine Migration und keine eigene Datenbanksicherung; die benutzerbezogenen Werte liegen in Nextclouds Konfiguration.

## Konfiguration

Nach der Aktivierung die persönlichen Nextcloud-Einstellungen öffnen und den Bereich **JSON Dashboard** auswählen. Für jedes Widget können folgende Werte gesetzt werden:

- **Aktiv:** Widget in der Dashboard-Auswahl verfügbar machen.
- **Titel:** Überschrift der Widget-Karte.
- **JSON-URL:** HTTPS-Adresse der JSON-Quelle.
- **JSON-Hauptschlüssel:** Name des Feldes, dessen Wert ein Array mit Widget-Einträgen ist.
- **Icon:** Darstellung des Widgets in Nextcloud.

Nach dem Speichern das Dashboard neu laden. Es können maximal acht Widgets aktiviert werden.

Eine aktivierte Konfiguration könnte beispielsweise so aussehen:

```json
{
  "Test": [
    {
      "name": "Außentemperatur",
      "data": {
        "type": "number",
        "value": 21.4,
        "unit": "°C",
        "color": "#008000"
      }
    },
    {
      "name": "Heizung",
      "data": {
        "type": "switch",
        "value": "ON"
      }
    }
  ]
}
```

Der Hauptschlüssel `Test` muss mit der Einstellung **JSON-Hauptschlüssel** übereinstimmen. Jeder Array-Eintrag sollte `name` und `data` enthalten. Ein Eintrag ohne gefüllten `data.value` wird nicht angezeigt.

Unterstützte Darstellungen:

- `number`: Wert mit optionaler Einheit.
- `circle`: Prozentwert als Kreisdiagramm; Werte werden auf 0 bis 100 begrenzt.
- `switch`: `ON` und `OFF` als farbige Zustände.
- `switch1`: `ON` und `OFF` als Häkchen beziehungsweise Kreuz.
- `switchSF`: Symbolwerte wie `flame`, `bolt`, `drop`, `ON` und `OFF`.
- `SFSymbol`: Symbolwert mit konfigurierbarer Farbe.
- Andere Typen: Wert als Text, optional mit `color`.

Farben werden nur akzeptiert, wenn sie als drei- oder sechsstelliger hexadezimaler Farbwert angegeben werden, zum Beispiel `#00679e`.

## HTTP-Endpunkte

Die App stellt zwei interne Routen bereit:

```
GET  /index.php/apps/jsondashboard/api/data/{slot}
POST /index.php/apps/jsondashboard/settings
```

Beide Routen setzen eine angemeldete Nextcloud-Sitzung voraus. Schreibzugriffe benötigen den Nextcloud-CSRF-Token `requesttoken`; das Frontend setzt ihn automatisch.

Der Daten-Endpunkt liefert bei Erfolg:

```json
{
  "elements": [
    {
      "name": "Außentemperatur",
      "data": {
        "type": "number",
        "value": 21.4,
        "unit": "°C"
      }
    }
  ]
}
```

Bei einer ungültigen Konfiguration oder einem Fehler der Quelle wird eine passende HTTP-Fehlerantwort mit einer kurzen Meldung zurückgegeben. Der Server fordert nur HTTPS-URLs an, sendet `Accept: application/json` und verwendet ein Verbindungs-Timeout von fünf sowie ein Gesamt-Timeout von zehn Sekunden. Nicht erfolgreiche HTTP-Statuscodes und ungültiges JSON werden nicht als Widget-Daten übernommen.

## Datenschutz und Sicherheit

Die Widget-Konfiguration wird pro Nextcloud-Benutzer gespeichert. Ein Benutzer kann die URL und Daten nur für seine eigenen Widgets ändern oder abrufen. Die App gibt die konfigurierte URL nicht an andere Benutzer weiter.

Die JSON-Abfragen erfolgen serverseitig aus dem Nextcloud-Container. Deshalb sollten nur vertrauenswürdige HTTPS-Quellen eingetragen werden. Die App ersetzt keine zusätzliche Netzwerkfilterung für ausgehende Verbindungen. Zugangsdaten gehören nicht in URLs oder frei zugängliche JSON-Antworten.

Die Antwortdaten werden als Text in DOM-Knoten eingesetzt; HTML aus der Quelle wird nicht als Markup interpretiert.

## Entwicklung und Kompatibilität

Pull Requests und Commits werden durch GitHub Actions geprüft. Die CI führt PHP-Syntaxprüfungen und eine Validierung von `appinfo/info.xml` aus und testet die Installation/Aktivierung der App in mehreren Nextcloud-Versionen. Die Tests laufen zusätzlich wöchentlich, damit auch Änderungen an Nextcloud-Basisimages auffallen.

Die App-Metadaten enthalten absichtlich keine maximale Nextcloud-Version. Das verhindert eine rein administrative Installationssperre bei einem neuen Major-Release. Es ist jedoch keine Garantie für zukünftige API-Kompatibilität: Änderungen an Nextclouds öffentlichen APIs müssen weiterhin durch Tests erkannt und bei Bedarf im Code behoben werden.

### Eine neue Version veröffentlichen

1. Versionsnummer in `appinfo/info.xml` erhöhen.
2. Eintrag in `CHANGELOG.md` ergänzen.
3. Änderungen über einen Pull Request nach `main` übernehmen und auf erfolgreiche CI warten.
4. Auf `main` einen Versions-Tag pushen, zum Beispiel:

   ```bash
   git tag v1.5.2
   git push origin v1.5.2
   ```

   GitHub Actions erstellt daraus automatisch ein GitHub Release mit Installationsarchiv und SHA256-Prüfsumme.

Eine Veröffentlichung im Nextcloud App Store ist davon getrennt und benötigt gegebenenfalls zusätzliche Signierung und App-Store-Konfiguration.

## Fehlerbehebung

Wenn das Widget nicht erscheint:

1. Prüfen, ob die App aktiviert ist:
   ```bash
   docker exec -u www-data nextcloud-aio-nextcloud php occ app:list | grep -A3 -B3 jsondashboard
   ```
2. Prüfen, ob das Widget in den persönlichen Einstellungen aktiviert und gespeichert wurde.
3. Kontrollieren, ob die URL mit `https://` beginnt und vom Nextcloud-Container erreichbar ist.
4. Sicherstellen, dass der JSON-Hauptschlüssel ein Array liefert.
5. Browser vollständig neu laden und das Nextcloud-Protokoll auf HTTP- oder JSON-Fehler prüfen.

## Lizenz

AGPL-3.0-or-later.
