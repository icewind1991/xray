// Webpack config for creating the production bundle.

var path = require('path');
var webpack = require('webpack');
var CleanPlugin = require('clean-webpack-plugin');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var strip = require('strip-loader');

var relativeAssetsPath = '../build';
var assetsPath = path.join(__dirname, relativeAssetsPath);

module.exports = {
	devtool: 'source-map',
	context: path.resolve(__dirname, '..'),
	entry: {
		'main': './js/index.js'
	},
	output: {
		path: assetsPath,
		filename: '[name].js',
		chunkFilename: '[name]-[chunkhash].js',
		publicPath: '/dist/'
	},
	module: {
		loaders: [
			{
				test: /\.(jpe?g|png|gif|svg)$/,
				loader: 'url',
				query: {limit: 10240}
			},
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: [strip.loader('debug'), 'babel-loader']
			},
			{test: /\.json$/, loader: 'json-loader'},
			{
				test: /\.css$/,
				use: ExtractTextPlugin.extract({
					fallback: "style-loader",
					use: [{
						loader: 'css-loader',
						options: {
							modules: true,
							sourceMap: true
						}
					}]
				})
			},
			{
				test: /\.less$/,
				use: ExtractTextPlugin.extract({
					fallback: "style-loader",
					use: [
						{
							loader: 'css-loader',
							options: {
								modules: true,
								sourceMap: true
							}
						},
						'less-loader'
					]
				})
			}
		]
	},
	resolve: {
		extensions: ['.json', '.js']
	},
	plugins: [
		new CleanPlugin([relativeAssetsPath]),
		new ExtractTextPlugin("[name].css"),
		new webpack.DefinePlugin({
			__CLIENT__: true,
			__SERVER__: false,
			__DEVELOPMENT__: false,
			__DEVTOOLS__: false
		}),

		// ignore dev config
		new webpack.IgnorePlugin(/\.\/dev/, /\/config$/),

		// set global vars
		new webpack.DefinePlugin({
			'process.env': {
				// Useful to reduce the size of client-side libraries, e.g. react
				NODE_ENV: JSON.stringify('production')
			}
		}),
		new webpack.optimize.UglifyJsPlugin({
			compress: {
				warnings: false
			}
		})
	]
};
