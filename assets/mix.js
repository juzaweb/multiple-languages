const mix = require('laravel-mix');
const path = require('path');

const baseAsset = path.resolve(__dirname, '');
const baseStyles = baseAsset + '/styles';
const basePublish = baseAsset + '/public';

mix.combine(
    [
        `${baseStyles}/js/select-language.js`,
    ],
    `${basePublish}/js/select-language.min.js`
);
