# XRay

See what is happening inside the Nextcloud instance

![](https://i.imgur.com/Ifzh8uM.png)

## Developing

XRay uses webpack to compile it's javascript for the browser, you can start the webpack development server by running `npm run dev`. This will start the webpack dev server, the EventSource server for live updates and a proxy server to let Nextcloud use the webpack dev server.

To use the dev server open `http://localhost:3000/path/to/nextcloud` in the browser
