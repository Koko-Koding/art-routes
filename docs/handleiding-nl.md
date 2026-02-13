# Art Routes - Gebruikershandleiding

*Versie 2.2.3 | Februari 2026*

---

## Inhoudsopgave

1. [Inleiding](#1-inleiding)
2. [Installatie en activering](#2-installatie-en-activering)
3. [Het menu begrijpen](#3-het-menu-begrijpen)
4. [Edities aanmaken en beheren](#4-edities-aanmaken-en-beheren)
5. [Routes aanmaken](#5-routes-aanmaken)
6. [Locaties aanmaken](#6-locaties-aanmaken)
7. [Informatiepunten aanmaken](#7-informatiepunten-aanmaken)
8. [Het Editie Dashboard](#8-het-editie-dashboard)
9. [Importeren en exporteren](#9-importeren-en-exporteren)
10. [Kaarten weergeven op je website](#10-kaarten-weergeven-op-je-website)
11. [Iconen beheren](#11-iconen-beheren)
12. [Instellingen aanpassen](#12-instellingen-aanpassen)
13. [De kaart voor bezoekers](#13-de-kaart-voor-bezoekers)
14. [Tips en veelgestelde vragen](#14-tips-en-veelgestelde-vragen)

---

## 1. Inleiding

**Art Routes** is een WordPress-plugin waarmee je interactieve kaarten maakt voor culturele evenementen zoals kunstroutes, theaterwandelingen, muziekfestivals en erfgoedwandelingen. De plugin maakt gebruik van OpenStreetMap om kaarten weer te geven met routes, locaties (zoals kunstwerken) en informatiepunten.

### Wat kun je ermee?

- **Routes** uittekenen op een kaart (wandelroutes, fietsroutes, etc.)
- **Locaties** met GPS-coordinaten toevoegen (kunstwerken, optredens, venues)
- **Informatiepunten** plaatsen (parkeren, horeca, toiletten, etc.)
- Alles organiseren in **Edities** (bijv. "Kunstroute 2026", "Gluren bij de Buren 2025")
- Gegevens **importeren** vanuit CSV- of GPX-bestanden
- Gegevens **exporteren** naar CSV of GPX (voor GPS-apparaten)
- Interactieve kaarten tonen op je website met **shortcodes** of **Gutenberg-blokken**
- Bezoekers hun **locatie laten volgen** op de kaart
- Alle benamingen **aanpassen** aan jouw evenement

---

## 2. Installatie en activering

### De plugin installeren

1. Ga naar **Plugins > Nieuwe plugin** in het WordPress-beheergedeelte
2. Klik op **Plugin uploaden** en selecteer het `art-routes.zip`-bestand
3. Klik op **Nu installeren**
4. Klik daarna op **Plugin activeren**

### Na activering

Na het activeren verschijnt er een nieuw menu-item **Art Routes** (of **Kunstroutes** in het Nederlands) in de linkerzijbalk van WordPress.

---

## 3. Het menu begrijpen

Na activering vind je het volgende menu in de WordPress-zijbalk:

```
Art Routes
  ├── Edities           - Overzicht van al je edities
  ├── Routes            - Alle routes beheren
  ├── Locaties          - Alle locaties/kunstwerken beheren
  ├── Informatiepunten  - Alle informatiepunten beheren
  ├── Dashboard         - Editie Dashboard voor bulkbeheer
  ├── Import/Export     - Gegevens importeren en exporteren
  └── Instellingen      - Plugin-instellingen aanpassen
```

> **Tip:** De benamingen in het menu kunnen afwijken als je de terminologie hebt aangepast in de instellingen. Bijvoorbeeld: "Locaties" kan "Kunstwerken" worden en "Informatiepunten" kan "Bezienswaardigheden" worden.

---

## 4. Edities aanmaken en beheren

### Wat is een Editie?

Een **Editie** is een container die routes, locaties en informatiepunten groepeert voor een specifiek evenement of tijdsperiode. Denk aan:

- "Kunstroute Amersfoort 2026"
- "Gluren bij de Buren 2025"
- "Erfgoedwandeling Voorjaar"

Door edities te gebruiken kun je elk jaar of elk evenement apart beheren, terwijl alles netjes georganiseerd blijft.

### Een nieuwe Editie aanmaken

1. Ga naar **Art Routes > Edities**
2. Klik op **Nieuwe toevoegen** bovenaan de pagina
3. Vul de volgende gegevens in:
   - **Titel**: De naam van je editie (bijv. "Kunstroute 2026")
   - **Beschrijving**: Een tekst die op de editiepagina wordt getoond
   - **Uitgelichte afbeelding**: Een afbeelding voor de editie (optioneel)
4. In het **Editie-instellingen** vak rechts kun je instellen:
   - **Startdatum**: Wanneer het evenement begint
   - **Einddatum**: Wanneer het evenement eindigt
   - **Standaard locatie-icoon**: Een standaardicoon voor locaties in deze editie
5. Optioneel: Pas de **Terminologie** aan (zie hoofdstuk 12)
6. Klik op **Publiceren**

### Een Editie bewerken

1. Ga naar **Art Routes > Edities**
2. Klik op de naam van de editie die je wilt bewerken
3. Pas de gewenste gegevens aan
4. Klik op **Bijwerken**

---

## 5. Routes aanmaken

Een **Route** is een pad op de kaart dat bezoekers kunnen volgen, zoals een wandelroute of fietsroute.

### Een nieuwe Route aanmaken

1. Ga naar **Art Routes > Routes**
2. Klik op **Nieuwe toevoegen**
3. Vul de basisgegevens in:
   - **Titel**: De naam van de route (bijv. "Wandelroute Binnenstad")
   - **Beschrijving**: Een omschrijving van de route
   - **Uitgelichte afbeelding**: Een foto van de route (optioneel)

### Het routepad tekenen

Onder de beschrijving vind je de **Route Editor** - een interactieve kaart waarmee je het pad uittekent:

1. **Zoeken**: Typ een adres of plaatsnaam in het zoekveld en klik op "Zoeken" om de kaart naar die locatie te verplaatsen
2. **Mijn locatie**: Klik op deze knop om de kaart te centreren op je huidige locatie
3. **Punten toevoegen**: Klik op de kaart om routepunten toe te voegen. De punten worden verbonden tot een lijn
4. **Punten verslepen**: Sleep een bestaand punt naar een nieuwe positie om het pad aan te passen
5. **Punt invoegen**: Klik op de groene **+** knop bij een routepunt om een nieuw punt tussenvoegen
6. **Punt verwijderen**: Klik op de rode **x** knop om een punt te verwijderen (minimaal 2 punten vereist)
7. **Punt bewerken**: Klik op het potloodicoon om extra informatie bij een punt in te vullen (start/eindmarkering, richtingpijlen)
8. **Route passend maken**: Klik op "Route passend maken" om de kaart automatisch in te zoomen op de hele route

### Route-eigenschappen instellen

Naast het pad kun je ook de volgende eigenschappen invullen:

- **Routetype**: Wandeling, fietstocht, rolstoelvriendelijk of kindvriendelijk
- **Lengte**: De afstand van de route (bijv. "3,5 km")
- **Duur**: Hoe lang de route duurt. Dit wordt automatisch berekend op basis van het routetype en de afstand, maar je kunt het ook handmatig invullen
- **Editie**: Koppel de route aan een editie

Klik op **Publiceren** wanneer de route klaar is, of sla op als **Concept** om later verder te werken.

---

## 6. Locaties aanmaken

Een **Locatie** is een punt op de kaart, zoals een kunstwerk, een optreden of een venue.

### Een nieuwe Locatie aanmaken

1. Ga naar **Art Routes > Locaties**
2. Klik op **Nieuwe toevoegen**
3. Vul de basisgegevens in:
   - **Titel**: De naam van de locatie (bijv. "Schilderij 'Zonsondergang'")
   - **Beschrijving**: Een uitgebreide omschrijving
   - **Uitgelichte afbeelding**: Een foto van het kunstwerk of de locatie

### De locatie op de kaart plaatsen

Onder de beschrijving vind je de **Locatie Picker**:

1. **Zoeken**: Typ een adres in het zoekveld om de kaart te verplaatsen
2. **Op de kaart klikken**: Klik op de gewenste plek op de kaart om de GPS-coordinaten automatisch in te vullen
3. **Handmatig invullen**: Je kunt ook direct de breedtegraad (Latitude) en lengtegraad (Longitude) invoeren

### Aanvullende gegevens

- **Nummer**: Een nummer of code voor de locatie (bijv. "A1", "001"). Dit nummer wordt op de kaartmarkering getoond
- **Locatiebeschrijving**: Een korte plaatsaanduiding (bijv. "Bij de kerk op het plein")
- **Icoon**: Kies een icoon voor de kaartmarkering
- **Kunstenaar/Maker**: Koppel de locatie aan een of meer kunstenaars
- **Rolstoeltoegankelijk**: Geef aan of de locatie toegankelijk is voor rolstoelen
- **Kinderwagentoegang**: Geef aan of de locatie toegankelijk is met een kinderwagen
- **Editie**: Koppel de locatie aan een editie

Klik op **Publiceren** of sla op als **Concept**.

---

## 7. Informatiepunten aanmaken

Een **Informatiepunt** is een nuttig punt op de kaart dat geen kunstwerk is, maar wel handig voor bezoekers, zoals parkeergelegenheid, een toilet of een horecagelegenheid.

### Een nieuw Informatiepunt aanmaken

1. Ga naar **Art Routes > Informatiepunten**
2. Klik op **Nieuwe toevoegen**
3. Vul in:
   - **Titel**: De naam (bijv. "Parkeerplaats Centrum")
   - **Beschrijving**: Extra informatie
4. Gebruik de **Locatie Picker** om het punt op de kaart te plaatsen
5. Kies een passend **Icoon** (bijv. parkeren, toilet, cafe, bus, trein)
6. Koppel het aan een **Editie**
7. Klik op **Publiceren**

---

## 8. Het Editie Dashboard

Het **Editie Dashboard** is het krachtigste beheerscherm van de plugin. Hier kun je alle inhoud van een editie in een overzicht beheren.

### Het Dashboard openen

1. Ga naar **Art Routes > Dashboard**
2. Selecteer een editie uit het dropdown-menu bovenaan

### Wat zie je op het Dashboard?

#### Overzichtskaart

Bovenaan zie je een interactieve kaart met:
- **Routes** als gekleurde lijnen
- **Locaties** als markeringen
- **Informatiepunten** als markeringen
- Concepten worden met 50% transparantie getoond

#### Secties voor Routes, Locaties en Informatiepunten

Onder de kaart vind je inklapbare secties met tabellen voor elk type inhoud. Per item zie je:

- **Selectievakje**: Om items te selecteren voor bulkacties
- **Titel**: Klik om te bewerken (direct op het Dashboard)
- **Nummer**: Alleen bij locaties, klik om te bewerken
- **Coordinaten**: Breedtegraad en lengtegraad, klik om te bewerken
- **Icoon**: Kies een icoon uit het dropdown-menu
- **Status**: Een badge die "Gepubliceerd" of "Concept" toont. Klik op de badge om de status te wisselen
- **Acties**: Knoppen om te publiceren/verbergen, bewerken (opent de bewerkpagina) of bekijken (opent de voorpagina)

#### Bulkacties

Selecteer meerdere items met de selectievakjes en gebruik de knoppen bovenaan elke sectie:

- **Alles selecteren**: Selecteert alle items in de sectie
- **Niets selecteren**: Deselecteert alle items
- **Concepten selecteren**: Selecteert alleen concepten
- **Publiceren**: Publiceert alle geselecteerde items
- **Concept maken**: Maakt alle geselecteerde items tot concept
- **Verwijderen**: Verwijdert alle geselecteerde items (let op: dit is permanent!)

#### Editie-instellingen

Onderin het Dashboard vind je de sectie **Editie-instellingen** waar je kunt aanpassen:

- **Evenementdatums**: Start- en einddatum
- **Standaard locatie-icoon**: Het standaardicoon voor locaties zonder eigen icoon
- **Terminologie**: Pas de benamingen aan per editie (Route, Locatie, Informatiepunt, Maker - enkelvoud en meervoud)

Klik op **Instellingen opslaan** om de wijzigingen op te slaan (zonder de pagina te verlaten).

---

## 9. Importeren en exporteren

Met de Import/Export-functie kun je snel grote hoeveelheden gegevens toevoegen of een back-up maken.

### De Import/Export-pagina openen

Ga naar **Art Routes > Import/Export**

### CSV Importeren (Locaties en Informatiepunten)

Met een CSV-bestand (spreadsheet) kun je in een keer veel locaties en informatiepunten toevoegen.

#### Stap voor stap:

1. Ga naar het tabblad **Import**
2. Selecteer een **Editie** uit het dropdown-menu, of kies **"+ Nieuwe editie aanmaken"** om er direct een aan te maken
3. Klik op **Bestand kiezen** en selecteer je CSV-bestand
4. Klik op **CSV importeren**

#### CSV-bestandsformaat

Je CSV-bestand moet de volgende kolommen bevatten:

| Kolom | Verplicht | Beschrijving |
|-------|-----------|-------------|
| Type | Ja | `location` of `info_point` |
| Name | Ja | De naam van het item |
| Description | Nee | Een beschrijving |
| Latitude | Ja | Breedtegraad (bijv. 52.0907) |
| Longitude | Ja | Lengtegraad (bijv. 5.1214) |
| Number | Nee | Nummer (alleen locaties, bijv. "A1") |
| Icon | Nee | Bestandsnaam van een icoon (bijv. "art.svg") |
| Creator | Nee | Naam van de kunstenaar (alleen locaties) |

> **Tip:** Download het **sjabloonbestand** via de knop "Download Template CSV" op de importpagina. Dit geeft je een voorbeeldbestand in het juiste formaat.

#### Na het importeren

- Alle items worden aangemaakt als **Concept** (draft), zodat je ze eerst kunt controleren
- Je krijgt een samenvatting te zien van hoeveel items zijn aangemaakt
- Duplicaten worden automatisch overgeslagen (op basis van naam of locatie)
- Er verschijnt een link naar het **Editie Dashboard** waar je de items kunt bekijken en publiceren

### GPX Importeren (Routes en/of Locaties)

Met een GPX-bestand (van een GPS-app of -apparaat) kun je routes en waypoints importeren.

#### Stap voor stap:

1. Ga naar het tabblad **Import**
2. Scroll naar het gedeelte **Importeren van GPX**
3. Selecteer een **Editie**
4. Kies je **GPX-bestand**
5. Kies een **Importmodus**:
   - **Alleen routepad**: Importeert tracks als routes (waypoints worden genegeerd)
   - **Routepad + waypoints als Locaties**: Importeert zowel routes als waypoints
   - **Alleen waypoints als Locaties**: Negeert tracks, importeert alleen waypoints
6. Klik op **GPX importeren**

### Exporteren

1. Ga naar het tabblad **Export**
2. Selecteer een **Editie**
3. Kies het **formaat**:
   - **CSV (Spreadsheet)**: Geschikt voor bewerking in Excel of Google Sheets
   - **GPX (GPS Exchange Format)**: Geschikt voor GPS-apparaten en kaart-apps
4. Klik op **Exporteren**

Het bestand wordt automatisch gedownload.

---

## 10. Kaarten weergeven op je website

Er zijn verschillende manieren om kaarten op je website te tonen.

### Automatische editiepagina

Elke gepubliceerde editie krijgt automatisch een eigen pagina op je website met:
- De titel en beschrijving van de editie
- Een interactieve kaart met alle routes, locaties en informatiepunten
- Een overzicht van routes (als cards met afbeeldingen)
- Een overzicht van locaties (als cards met afbeeldingen)
- Een lijst van informatiepunten

De link naar de editiepagina vind je in het **Editie Dashboard** via de knop **Frontend bekijken**.

### Shortcodes

Shortcodes zijn korte codes die je in een pagina of bericht plakt om een kaart weer te geven.

#### Editie-kaart

```
[edition_map edition_id="123" height="500px"]
```

Opties:
- `edition_id`: Het ID van de editie (optioneel op een editiepagina)
- `routes`: `all` (alle routes), `none` (geen routes), of specifieke ID's gescheiden door komma's
- `show_locations`: `true` of `false`
- `show_info_points`: `true` of `false`
- `show_legend`: `true` of `false` (schakelknoppen tonen)
- `height`: Hoogte van de kaart (bijv. "500px")

#### Enkele route-kaart

```
[art_route_map route="123" height="500px"]
```

Opties:
- `route`: Het ID van de route (verplicht)
- `height`: Hoogte van de kaart
- `zoom`: Zoomniveau (standaard 13)

#### Meerdere routes op een kaart

```
[art_routes_map height="600px" edition_id="123"]
```

Opties:
- `height`: Hoogte van de kaart
- `edition_id`: Filter op een specifieke editie (optioneel)
- `zoom`: Zoomniveau (standaard 12)

#### Route-iconen weergeven

```
[art_route_icons]
```

Toont een raster van route-iconen die linken naar de bijbehorende routepagina's.

### Gutenberg-blok: Editie-kaart

Als je de WordPress blok-editor (Gutenberg) gebruikt:

1. Klik op **+** om een nieuw blok toe te voegen
2. Zoek naar **"Edition Map"** of **"Editie Kaart"**
3. Voeg het blok toe aan je pagina
4. In de blokinstelling (rechts) kun je kiezen:
   - **Editie**: Selecteer welke editie je wilt tonen (of laat op "automatisch detecteren")
   - **Hoogte**: De hoogte van de kaart
   - **Routes tonen**: Aan/uit
   - **Locaties tonen**: Aan/uit
   - **Informatiepunten tonen**: Aan/uit
   - **Legenda tonen**: Aan/uit

---

## 11. Iconen beheren

De plugin bevat meer dan 40 ingebouwde iconen voor kaartmarkeringen, en je kunt ook je eigen iconen uploaden.

### Ingebouwde iconen

De plugin bevat onder andere:

- **Genummerd** (1 t/m 20): Nummers in een cirkel
- **Categorie-iconen**: Kunst, museum, muziek, kerk, sculptuur, poëzie, foto
- **Voorzieningen**: Cafe, restaurant, parkeren, bus, trein, fiets, toilet
- **Natuur**: Park, uitzichtpunt
- **Navigatie**: Start, einde, markering, informatie, huis, festival

### Eigen iconen uploaden

1. Ga naar **Art Routes > Instellingen > Eigen iconen** (tabblad "Custom Icons")
2. Klik op **Bestand kiezen** en selecteer een bestand:
   - **SVG** (aanbevolen): Schaalbare vectorafbeelding, altijd scherp
   - **PNG, JPG of WebP**: Ook ondersteund
3. Klik op **Icoon uploaden**
4. Het icoon verschijnt in het overzicht en is nu beschikbaar in alle icoonkeuze-menu's

### Iconen verwijderen

Klik op de rode **Verwijderen**-knop onder een geupload icoon. Let op: dit kan niet ongedaan worden gemaakt, en ingebouwde iconen kunnen niet verwijderd worden.

### Standaardicoon instellen

Je kunt een standaardicoon instellen voor locaties die geen eigen icoon hebben:

- **Globaal**: Ga naar **Instellingen > Algemeen** en kies een "Standaard locatie-icoon"
- **Per editie**: In het Editie Dashboard onder "Editie-instellingen" kun je per editie een standaardicoon kiezen

De volgorde van iconen is: eigen icoon van de locatie > standaardicoon van de editie > globaal standaardicoon > grijs bolletje (geen icoon).

---

## 12. Instellingen aanpassen

Ga naar **Art Routes > Instellingen** voor de plugin-instellingen. Er zijn drie tabbladen.

### Tabblad: Algemeen

- **Standaardroute**: Kies welke route wordt gebruikt als er geen specifieke route is geselecteerd
- **Locatietracking**: Schakel in/uit of bezoekers hun locatie kunnen delen om hun positie op de kaart te zien
- **Standaard locatie-icoon**: Het icoon dat wordt gebruikt voor locaties zonder eigen icoon

### Tabblad: Terminologie

Hier pas je alle benamingen aan die de plugin gebruikt. Dit is handig als je de plugin niet voor een kunstroute gebruikt, maar bijvoorbeeld voor een theaterfestival of erfgoedwandeling.

Voor elk type kun je instellen:
- **Enkelvoud**: Bijv. "Kunstwerk" in plaats van "Locatie"
- **Meervoud**: Bijv. "Kunstwerken" in plaats van "Locaties"
- **URL-slug**: Het deel van de URL (bijv. "kunstwerk" in plaats van "artwork")

De vier types die je kunt aanpassen:

| Type | Standaard | Voorbeeld aanpassing |
|------|-----------|---------------------|
| **Route** | Route / Routes | Wandeling / Wandelingen |
| **Locatie** | Location / Locations | Kunstwerk / Kunstwerken |
| **Informatiepunt** | Info Point / Info Points | Bezienswaardigheid / Bezienswaardigheden |
| **Maker** | Artist / Artists | Kunstenaar / Kunstenaars |

> **Let op:** Na het wijzigen van de URL-slug moet je naar **Instellingen > Permalinks** gaan en op **Wijzigingen opslaan** klikken om de URL-regels te verversen.

### Tabblad: Eigen iconen

Zie hoofdstuk 11 voor uitleg over het beheren van eigen iconen.

### Terminologie per Editie

Naast de globale terminologie kun je ook **per editie** de benamingen aanpassen. Dit doe je in het Editie Dashboard onder "Editie-instellingen". Als je per editie niets invult, worden de globale instellingen gebruikt.

---

## 13. De kaart voor bezoekers

### Wat zien bezoekers op de kaart?

Bezoekers van je website zien een interactieve kaart met:

- **Routes** als gekleurde lijnen op de kaart
- **Locaties** als ronde markeringen met een icoon, foto of nummer
- **Informatiepunten** als kleinere markeringen met een icoon
- **Start- en eindpunten** van routes
- **Richtingpijlen** langs de route

### Interactie met de kaart

Bezoekers kunnen:

- **Inzoomen en uitzoomen** met de +/- knoppen of door te scrollen
- **De kaart verschuiven** door te slepen
- **Op een markering klikken** om een popup te zien met details, een foto en een link naar de detailpagina
- **Lagen aan/uit zetten** via de schakelknoppen (als de legenda is ingeschakeld): routes, locaties, informatiepunten en eigen locatie

### Locatietracking

Als locatietracking is ingeschakeld (zie Instellingen):

- Bezoekers worden gevraagd om hun locatie te delen
- Hun positie wordt als blauwe stip op de kaart getoond
- Ze krijgen een melding wanneer ze **in de buurt** van een locatie zijn
- Dit werkt het beste op mobiele apparaten (telefoons)

### GPX-export voor bezoekers

Op routepagina's kunnen bezoekers de route downloaden als GPX-bestand via de **"Exporteren naar GPX"**-knop. Dit bestand kunnen ze openen in GPS-apps zoals:

- Google Maps
- Komoot
- OsmAnd
- Garmin-apparaten

---

## 14. Tips en veelgestelde vragen

### Hoe vind ik het ID van een route of editie?

Het ID staat in de URL wanneer je een item bewerkt. Bijvoorbeeld:
`wp-admin/post.php?post=123&action=edit` - hier is **123** het ID.

### Mijn kaart toont niets

- Controleer of de routes/locaties **gepubliceerd** zijn (niet "Concept")
- Controleer of de items aan de juiste **editie** zijn gekoppeld
- Controleer of je het juiste **editie-ID** of **route-ID** in de shortcode gebruikt

### De kaart zoomt niet goed in

De kaart zoomt automatisch in op alle zichtbare items. Als er maar een item is, kan het zijn dat de kaart erg ver is ingezoomd. Voeg een `zoom`-parameter toe aan de shortcode om dit aan te passen.

### Hoe maak ik een back-up van mijn gegevens?

Gebruik de **Export**-functie om een CSV- of GPX-bestand te downloaden van een editie. Dit bevat alle locaties, informatiepunten en routes.

### Kan ik de plugin in het Nederlands gebruiken?

Ja! De plugin heeft een volledige Nederlandse vertaling. Als je WordPress in het Nederlands hebt ingesteld, worden alle teksten automatisch in het Nederlands getoond.

### Hoe voeg ik een kunstenaar/maker toe?

Kunstenaars zijn gewone WordPress-berichten of pagina's. Als je een locatie bewerkt, kun je in het veld "Kunstenaar" zoeken naar bestaande berichten/pagina's en deze koppelen.

### Hoe wissel ik snel de status van items?

In het **Editie Dashboard** kun je:
- Op de **statusbadge** klikken om een enkel item te publiceren of terug te zetten naar concept
- Meerdere items selecteren en de **bulkacties** gebruiken

### Kan ik het uiterlijk van de kaart aanpassen?

De plugin gebruikt standaard OpenStreetMap-tegels. Het uiterlijk van de popups, markeringen en kaart-layout kan worden aangepast via CSS in je thema. De plugin biedt templates in de map `templates/` die je kunt overschrijven door ze te kopieren naar `art-routes/` in je thema.

### Dubbele items bij het importeren

De plugin detecteert automatisch duplicaten bij het importeren:
- **Locaties en informatiepunten**: Worden overgeslagen als er al een item met dezelfde naam OF dezelfde coordinaten (binnen ~2 meter) bestaat in de editie
- **Routes**: Worden overgeslagen als er al een route met dezelfde naam bestaat in de editie

---

## Hulp nodig?

Heb je een probleem of wil je een suggestie doen? Neem contact op met de pluginontwikkelaar via de [GitHub-pagina](https://github.com/Koko-Koding/art-routes).

---

*Art Routes - Interactieve kaarten voor culturele evenementen*
*Versie 2.2.3 | GPL v2 of later*
