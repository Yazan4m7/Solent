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


mix.browserSync({
    proxy: 'http://localhost:8000',
    open: 'external',
    files: [
        'app/**/*.php',
        'resources/views/**/*.blade.php',
        'public/js/**/*.js',
        'public/css/**/*.css'
    ]
});
// mix.js('resources/js/app.js', 'public/js')
//     .vue()
//     .sass('resources/sass/app.scss', 'public/css');
