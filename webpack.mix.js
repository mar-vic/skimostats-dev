const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
   .js('resources/js/app.js', 'public/js')
   // Front
   .js('resources/js/front/landing-page/landing.js', 'public/js')
   .js('resources/js/front/races/races.js', 'public/js')
   .js('resources/js/front/athlete/athlete.js', 'public/js')
   .js('resources/js/front/race-event/race-event.js', 'public/js')
   .js('resources/js/main.js', 'public/js')
   .sass('resources/sass/front/main.scss', 'public/css')

   // Admin
   .js('resources/js/admin/rankings-manager/rankings-manager.js', 'public/js/admin')

   .version();
