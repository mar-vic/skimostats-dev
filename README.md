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
