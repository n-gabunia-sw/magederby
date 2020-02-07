/**
 * ScandiPWA - Progressive Web App for Magento
 *
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See LICENSE for license details.
 *
 * @license OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * @package scandipwa/base-theme
 * @link https://github.com/scandipwa/base-theme
 */

/* eslint-disable import/no-extraneous-dependencies */
// Disabled due webpack plugins being dev dependencies

// TODO: merge Webpack config files

const path = require('path');
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const WebpackPwaManifest = require('webpack-pwa-manifest');
const autoprefixer = require('autoprefixer');

const webmanifestConfig = require('./webmanifest.config');
const BabelConfig = require('./babel.config');
const FallbackPlugin = require('./FallbackPlugin');

const projectRoot = path.resolve(__dirname, '..', '..');
const magentoRoot = path.resolve(projectRoot, '..', '..', '..', '..', '..');
const fallbackRoot = path.resolve(magentoRoot, 'vendor', 'scandipwa', 'source');

module.exports = {
    resolve: {
        extensions: [
            '.js',
            '.jsx',
            '.scss',
            '*'
        ],
        plugins: [
            new FallbackPlugin({
                fallbackRoot, projectRoot
            })
        ]
    },

    mode: 'development',

    devtool: 'source-map',

    stats: {
        warnings: false
    },

    entry: {
        bundle: path.resolve(projectRoot, 'src', 'app', 'index.js'),
        sw: path.resolve(projectRoot, 'src', 'sw', 'index.js')
    },

    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /(node_modules)/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: BabelConfig
                    }
                ]
            },
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    'css-hot-loader',
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            sourceMap: true,
                            plugins: () => [autoprefixer]
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true
                        }
                    },
                    {
                        loader: 'sass-resources-loader',
                        options: {
                            resources: path.resolve(projectRoot, 'src', 'app', 'style', 'abstract', '_abstract.scss')
                        }
                    }
                ]
            },
            {
                test: /\.(jpe?g|png|gif|svg)$/,
                use: [
                    {
                        loader: 'url-loader'
                    }
                ]
            }
        ]
    },

    output: {
        filename: '[name].js',
        publicPath: '/',
        pathinfo: true,
        globalObject: 'this', // fix for https://github.com/webpack/webpack/issues/6642
        path: path.resolve(projectRoot, 'Magento_Theme', 'web')
    },

    devServer: {
        watchContentBase: true,
        publicPath: '/',
        historyApiFallback: true,
        port: 3003,
        https: false,
        overlay: true,
        compress: true,
        inline: true,
        hot: true,
        host: '0.0.0.0',
        public: 'scandipwa.local',
        allowedHosts: [
            '.local'
        ]
    },

    watchOptions: {
        aggregateTimeout: 300,
        poll: 1000
    },

    plugins: [
        new webpack.HotModuleReplacementPlugin(),

        new webpack.DefinePlugin({
            'process.env': {
                REBEM_MOD_DELIM: JSON.stringify('_'),
                REBEM_ELEM_DELIM: JSON.stringify('-'),
                MAGENTO_VERSION: JSON.stringify('2.3.1')
            }
        }),

        new webpack.ProvidePlugin({
            __: path.resolve(path.join(__dirname, 'TranslationFunction')),
            React: 'react'
        }),

        new HtmlWebpackPlugin({
            template: path.resolve(projectRoot, 'src', 'public', 'index.development.html'),
            filename: 'index.html',
            inject: false,
            publicPath: '/'
        }),

        new WebpackPwaManifest(webmanifestConfig(projectRoot)),

        new CopyWebpackPlugin([
            { from: path.resolve(projectRoot, 'src', 'public', 'assets'), to: './assets' }
        ]),

        new MiniCssExtractPlugin()
    ]
};
