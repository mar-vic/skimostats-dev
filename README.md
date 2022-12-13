# SkimoStats

## Installation

### On Ubuntu
Update Ubuntu repositories:
```
sudo apt-get update
```

Clone the site git repo:
```
git clone git@github.com:mar-vic/skimostats-dev.git
```

Install PHP and composer:
```
sudo apt-get install curl php
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/bin/composer
```

Install Ubuntu PHP packages:
```
sudo apt-get install php-curl php-xml php-mbstring php-gd php-zip php-mysql
```

Install composer dependencies:
```
composer update
```

Install and npm and its dependencies:
```
sudo apt-get install npm
npm install
```

Recompile JavaScript assets:
```
npm run dev
```
# TODOs

* Pri Pierra Menta, Altitoy-Ternua, Tour du Rutor nech do tých medailí počíta
  len GC. Lebo to ráta dokopy GC + stages. (pozn.: V kontexte TOP RESULTS atleta)
  
* Pri hover on nad medaliov pri top results zobraz info o medaile a pripadne aj
  zoznam pretekov.

* Pra ranking tabulke vekove kateogorie v ramci ismf bodovanie do dropdownu a
  pridat dorpdow s typmi pretekov.
  
  
## Statistics

    * TODO head to head na uvodnej stranke nezobrazuje vitazstva

    * TODO vyhod dropdown jednotlivych rocnkov pretekov

    * TODO Vertical meters nezobrazovat vobec v menu

    * TODO Vymen vsetky fotky na homepage

    * TODO Niekdy priradi atlettovi zlu narodnost / vlajku

    * TODO Dokoncit statistiky z grafmi: Pints per age a Nations scoing skimostats
  
    * TODO Pipravit stranku na preklady
  
    * TODO V tabulke vysledkov pretekov prepinac medzi absolutnym a relativnym casom
  
    * TODO V tabulke vysledkov pre etapy moznost zobrazenia aktualneho celkoveho poradia,
        tj scitanie vsetkych ubehnuvsich etap aj pre celovy vysledok preteku

    * TODO Mozno updatovania about stranky uzivatelom

    * TODO Responsive dizajn pre statistiky (pre zariadenia s mensimi displaymi)

    * TODO Router pre 'statistics' podstranku

## Finished
 
 * DONE Odstran 'TEST' v 'Ranking Per Season' komponente na stranke aleta
  
 * DONE Oprav 'All Right Reserved'  na 'All Right*s* reserved'

 * DONE Na stranke atleta sa nezobrazuju niektore data: 'Top Results', 'Points per
          Specialty', 'Career Wins'

 * DONE Chocolates: pridaj vysvetlujuce info

 * DONE Winners by countries: pridaj vysvetlujuce info pre metriku

 * DONE Adding new athlete fails because of link at:

    /home/marcus/Projects/skimostats-dev/resources/views/admin/athletes/edit.blade.php(:20)

 * DONE Rankings actualisation fails. The reason is the query at:

    /home/marcus/Projects/skimostats-dev/app/Http/Controllers/Admin/RankingController.php::refreshAllIsmfRankings(45:70)
