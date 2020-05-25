const mix = require('laravel-mix');

require('vuetifyjs-mix-extension');

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

mix.js('resources/js/app.js', 'public/dist/js').vuetify('vuetify-loader');
mix.sass('resources/sass/app.scss', 'public/dist/css');

mix.js('resources/js/front.js', 'public/dist/js');

mix.copy('resources/img', 'public/dist/img');

mix.disableSuccessNotifications();

if (mix.inProduction()) {
    mix.version();
}
