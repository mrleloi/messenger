const mix = require('laravel-mix');
const path = require('path');
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
mix.webpackConfig({
    resolve: {
        alias: {
            'jquery': path.resolve('node_modules/jquery/src/jquery')
        }
    }
});
    mix.options({
        cssNano: { normalizePositions: false },
        processCssUrls: false
    })
    .setPublicPath('public')
    .js('resources/js/app.js', 'public/vendor/messenger')
    .js('resources/js/janus/JanusServer.js', 'public/vendor/messenger')
    .sass('resources/sass/app.scss', 'public/vendor/messenger')
    .sass('resources/sass/dark.scss', 'public/vendor/messenger')
    .version();
