let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

// Config
(mix
    .setPublicPath('public/dist')
    .options({
        clearConsole: false
    })
);
if (mix.config.inProduction) {
    (mix
        .version()
        .disableNotifications()
        .webpackConfig({
            stats: {
                assets: false
            }
        })
    );
}


// Build
(mix
    .js('assets/js/main.js', 'js/main.js')
   .sass('assets/scss/main.scss', 'css/main.css')
);
