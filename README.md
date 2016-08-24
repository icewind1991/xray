# XRay

See what is happening inside the Nextcloud instance

![](https://i.imgur.com/Ifzh8uM.png)

## Installation

XRay requires Redis to be installed and configured for Nextcloud

## Live Updates

XRay can optionally use a NodeJS EventSource server to allow live updating the XRay data, to enable this make sure NodeJS is installed and run `npm run source` this will start the EventSource server on port 3003

When running nextcloud over https you'll need to add ssl termination yourself to the EventSource server, you can do this by changing the port the node server listens to by setting the `PORT` enviroment variable and use your favorite reverse proxy to proxy https from port 3003 to http on your custom port.  

## Developing

XRay uses webpack to compile it's javascript for the browser, you can start the webpack development server by running `npm run dev`. This will start the webpack dev server, the EventSource server for live updates and a proxy server to let Nextcloud use the webpack dev server.

To use the dev server open `http://localhost:3000/path/to/nextcloud` in the browser
