var webpack = require('webpack');
var path = require('path');

var config = {};

config.entry = {
    app: './src/main/js/index.js'
};

config.module = {
    rules: []
};

config.module.rules.push({
    test: /\.jsx?$/,
    exclude: /node_modules/,
    loader: 'babel-loader',
    query: {
        presets: ['es2015', 'react']
    }
});

config.module.rules.push({
    test: /\.(jpg|jpeg|gif)$/,
    loaders: ['file?name=assets/images/[hash].[ext]']
});

config.module.rules.push({
    test: /\.png$/,
    loaders: ['url-loader?limit=1000000&name=assets/images/[hash].[ext]']
});

config.module.rules.push({
    test: /\.(ttf|eot|woff|woff2|svg)(\?\S*)?$/,
    loaders: ['url-loader?limit=1000000&name=assets/fonts/[hash].[ext]']
});

config.module.rules.push({
    test: /index\.html$/,
    loaders: [
        {loader: 'file-loader', options: {name: '[name].[ext]'}},
        {loader: 'extract-loader'}, {loader: 'html-loader'}
    ]
});

config.plugins = [];

module.exports = config;