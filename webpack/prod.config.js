// Webpack config for creating the production bundle.

var path = require('path');
var webpack = require('webpack');
var MiniCssExtractPlugin = require('mini-css-extract-plugin');
var strip = require('strip-loader');

var relativeAssetsPath = '../build';
var assetsPath = path.join(__dirname, relativeAssetsPath);

module.exports = {
	devtool: 'source-map',
	mode: 'production',
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
		rules: [
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
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
						options: {
							hmr: process.env.NODE_ENV === 'development',
						},
					},
					'css-loader'
				]
			},
			{
				test: /\.less$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
						options: {
							hmr: process.env.NODE_ENV === 'development',
						},
					},
					{
						loader: 'css-loader',
						options: {
							modules: true,
							sourceMap: true
						}
					},
					'less-loader'
				]
			}
		]
	},
	resolve: {
		extensions: ['.json', '.js']
	},
	plugins: [
		new MiniCssExtractPlugin("[name].css"),
		new webpack.DefinePlugin({
			__CLIENT__: true,
			__SERVER__: false,
			__DEVELOPMENT__: false,
			__DEVTOOLS__: false
		}),

		// ignore dev config
		new webpack.IgnorePlugin(/\.\/dev/, /\/config$/),
	]
};
