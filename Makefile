all: build

build: node_modules
	NODE_ENV=production node_modules/.bin/webpack --verbose --colors --display-error-details --config webpack/prod.config.js

node_modules: package.json
	npm install

.PHONY: watch
watch: node_modules
	node node_modules/.bin/webpack-dev-server --hot --inline --port 3000 --public localcloud.icewind.me:444 --config webpack/dev.config.js
