# RocketChat
Beschreibung des Moduls.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Ermöglicht das versenden einer Nachricht an einen Rocket Chat Server in einem Kanal.
* Es können Bild URLs mit übergeben werden die auch eingeklappt dargestellt werden können.

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.5

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen
    `https://github.com/Housemann/RocketChat`


### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'RocketChat'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

### 5. Konfiguration der Instanz

Es mus in Rocket.Chat im Administrationsbereich ein eingehender WebHook unter "</> Integration" hinzugefügt werden. 
Kanal und User (Muss vorhanden sein) muss angegeben sein. 
Nach dem Speichern erhält man seine Webhook-URL Beispiel: https://DeineDomain.com/hooks/PJfhjauiesv7tprdghhA/S7zkedvhiiQiZwkdsoslwölkapxdsdklslkdynTer
Diese muss man im Modul hinterlegen.


### 6. PHP-Befehlsreferenz

`string ROCK_BeispielFunktion(integer $InstanzID);`
Erklärung der Funktion.

Beispiel:
`ROCK_BeispielFunktion(12345);`

Funktion nur zum Senden einer Nachricht
```php
ROCKET_SendRocket_Msg(
       $InstanzID,
       $channel = '#Webhook',
       $message = 'Hallo this is a Message',
       $alias = 'Housemann',
       $avatar_url = 'https://static.wikia.nocookie.net/jamescameronsavatar/images/0/08/Neytiri_Profilbild.jpg/revision/latest?cb=20100107164021&path-prefix=de',
);
```

Funktion zum Senden mit allen Feldern.
Mit der Variable $collapsed (true/false), kann man die Werte $image und $fields auf bzw. zuklappen. Das klappt leider nur mit der Browser/Desktop Version, nicht jedoch mit der Handy App.
```php
ROCKET_SendRocket(    
       $InstanzID,
       $channel = '#Webhook',
       $message = 'Hallo this is a Message',
       $alias = 'Housemann',
       $avatar_url = 'https://static.wikia.nocookie.net/jamescameronsavatar/images/0/08/Neytiri_Profilbild.jpg/revision/latest?cb=20100107164021&path-prefix=de',
       $color = '#ff0000',
       $author_name = 'James Cameron',
       $author_icon = 'https://mar.prod.image.rndtech.de/var/storage/images/haz/nachrichten/kultur/kino/james-cameron-will-mission-munroe-verfilmen/26639883-1-ger-DE/James-Cameron-will-Mission-Munroe-verfilmen_reference_4_3.jpg',
       $author_link = 'https://de.wikipedia.org/wiki/James_Cameron',       
       $title = 'Alien',
       $title_link = 'https://de.wikipedia.org/wiki/Alien_%E2%80%93_Das_unheimliche_Wesen_aus_einer_fremden_Welt',
       $collapsed = 'true',
       $image = 'https://n-cdn.serienjunkies.de/hq/108641.jpg',
       $fields = '{"short": true,"title": "Verbrauch Tag","value": "10"},{"short": true,"title": "Verbrauch Vortag","value": "20"}'
);
```

Bei den $fields muss ein JSON Codierter String mit den folgenden Inhalten für short / title / value übergeben werden.
Bei RocketChat können zwei Werte nebeneinander dargestellt werden, dafür ist das Feld "short" auf "true" zu setzten. Wenn "false", dann werden die Werte untereinander dargestellt.
```php
Array
(
    [0] => Array
        (
            [short] => true
            [title] => Verbrauch Tag
            [value] => 10
        )

    [1] => Array
        (
            [short] => true
            [title] => Verbrauch Vortag
            [value] => 20
        )

)
```

Beispielübergabe für Funktion ROCKET_SendRocket()
```php
$array = [];
array_push($array,array("short"=>"true","title"=>"Verbrauch Tag","value"=>"10"));
array_push($array,array("short"=>"true","title"=>"Verbrauch Vortag","value"=>"20"));

ROCKET_SendRocket(    
       $InstanzID,
       $channel = '#Webhook',
       $message = 'Hallo this is a Message',
       $alias = 'Housemann',
       $avatar_url = 'https://static.wikia.nocookie.net/jamescameronsavatar/images/0/08/Neytiri_Profilbild.jpg/revision/latest?cb=20100107164021&path-prefix=de',
       $color = '#ff0000',
       $author_name = 'James Cameron',
       $author_icon = 'https://mar.prod.image.rndtech.de/var/storage/images/haz/nachrichten/kultur/kino/james-cameron-will-mission-munroe-verfilmen/26639883-1-ger-DE/James-Cameron-will-Mission-Munroe-verfilmen_reference_4_3.jpg',
       $author_link = 'https://de.wikipedia.org/wiki/James_Cameron',       
       $title = 'Alien',
       $title_link = 'https://de.wikipedia.org/wiki/Alien_%E2%80%93_Das_unheimliche_Wesen_aus_einer_fremden_Welt',
       $collapsed = 'true',
       $image = 'https://n-cdn.serienjunkies.de/hq/108641.jpg',
       $fields = json_encode($array);
);
```
