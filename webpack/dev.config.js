// Webpack config for creating the production bundle.

const path = require('path');
const webpack = require("webpack");
const assetsPath = path.resolve(__dirname, '../build');

module.exports = {
	devtool: 'source-map',
	entry: [
		'react-hot-loader/patch',
		'./js/index.js'
	],
	output: {
		path: assetsPath,
		filename: 'main.js',
		publicPath: '/'
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
				use: [
					'react-hot-loader/webpack',
					'babel-loader'
				]
			},
			{
				test: /\.json$/,
				loader: 'json-loader'
			},
			{
				test: /\.css$/,
				use: [
					'style-loader',
					{
						loader: 'css-loader',
						options: {
							modules: true,
							sourceMap: true
						}
					}
				]
			},
			{
				test: /\.less$/,
				use: [
					'style-loader',
					{
						loader: 'css-loader',
						options: {
							modules: true,
							localIdentName: '[name]__[local]--[hash:base64:5]',
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
	devServer: {
		contentBase: path.resolve(__dirname, './src')
	},
	plugins: [
		new webpack.NamedModulesPlugin()
	]
};
