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
  
* Odstran TEST v Ranking Per Season pre na stranke atleta

* Pri hover on nad medaliov pri top results zobraz info o medaile a pripadne aj
  zoznam pretekov.
  
* Pra ranking tabulke vekove kateogorie v ramci ismf bodovanie do dropdownu a
  pridat dorpdow s typmi pretekov.
  
# Statistics

* Chocolates: pridaj vysvetlujuce info

* Niekdy priradi atlettovi zlu narodnost / vlajku

* Vertical meters nezobrazovat vobec v menu

* Dokoncit statistiky z grafmi: Pints per age a Nations scoing skimostats

* Na stranke atleta sa nezobrazuju niektore data: 'Top Results', 'Points per
  Specialty', 'Career Wins'

* Pipravit stranku na preklady
  
* head to head na uvodnej stranke nezobrazuje vitazstva
  
* V tabulke vysledkov pretekov prepinac medzi absolutny a relativnym casom
  
* V tabulke vysledkov pre etapy moznost zobrazenia aktualneho celkoveho poradia,
  tj scitanie vsetkych ubehnuvsich etap aj pre celovy vysledok preteku

* vyhod dropdawn jednotlivych rocnkiv pretekov

* Vymen vsetky fotky na homepage

* Mozno updatovania about stranky uzivatelom
  
* All Right Reserved -> All Rights reserved

* Dizajn statisk pre mobily

* Routing pre statistics

* DONE Winners by countries: pridaj vysvetlujuce info pre metriku

* DONE Adding new athlete fails because of link at:

  /home/marcus/Projects/skimostats-dev/resources/views/admin/athletes/edit.blade.php(:20)

* DONE Rankings actualisation fails. The reason is the query at:

  /home/marcus/Projects/skimostats-dev/app/Http/Controllers/Admin/RankingController.php::refreshAllIsmfRankings(45:70)
