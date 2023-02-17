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

```npm run production```

# TODOs

* TODO Pipravit stranku na preklady

* TODO V tabulke vysledkov pre etapy moznost zobrazenia aktualneho celkoveho poradia,
    tj scitanie vsetkych ubehnuvsich etap aj pre celovy vysledok preteku

* TODO Moznost updatovania about stranky uzivatelom

## Statistics

* TODO Dokoncit statistiky: Points per age a Nations scoring skimostats
      points

* TODO Responsive dizajn pre statistiky (pre zariadenia s mensimi displaymi)

* TODO Router pre 'statistics' podstranku

## DONE


* DONE V tabulke vysledkov pretekov prepinac medzi absolutnym a relativnym casom

* DONE Pre ranking tabulke vekove kateogorie v ramci ismf bodovania do dropdownu a
    pridat dropdown s typmi pretekov.

* DONE 'Nations raced in' premenovat na 'Countries raced in'

* DONE Vymen vsetky fotky na homepage

* DONE Pri Pierra Menta, Altitoy-Ternua, Tour du Rutor nech do tých medailí počíta
  len GC. Lebo to ráta dokopy GC + stages. (pozn.: V kontexte TOP RESULTS atleta)

* DONE Pri hover on nad medaliou pri Top results zobraz info o jej type

* DONE Niektorým atlétom sa zobrazuje nesprávna fotka krajiny povodu

* DONE Oprav vypocet 'victories' v head2head

* DONE Odstran stvrte miesta v 'Top Results' atleta

* DONE Going to https://www.skimostats.com/athlete/cameron-smith results in
  error

* DONE Zrus debug logy v produkcii (v .env nastav app_debug=false)  [14.12]

* DONE Vertical meters nezobrazovat vobec v menu

* DONE Niekdy priradi atletovi zlu narodnost / vlajku

* DONE vyhod dropdown jednotlivych rocnkov pretekov (pre pierra menta)
 
* DONE Odstran 'TEST' v 'Ranking Per Season' komponente na stranke aleta
  
* DONE Oprav 'All Right Reserved'  na 'All Right*s* reserved'
  
* DONE Na stranke atleta sa nezobrazuju niektore data: 'Top Results', 'Points per
  Specialty', 'Career Wins'

* DONE Chocolates: pridaj vysvetlujuce info

* DONE Winners by countries: pridaj vysvetlujuce info pre metriku

* DONE Adding new athlete fails because of link at

* DONE Rankings actualisation fails. The reason is the query at

