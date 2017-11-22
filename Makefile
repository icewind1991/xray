all: build

build: node_modules
	NODE_ENV=production node_modules/.bin/webpack --verbose --colors --display-error-details --config webpack/prod.config.js

node_modules: package.json
	npm install
