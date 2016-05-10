# XRay

See what is happening inside the ownCloud instance

![](https://i.imgur.com/Ifzh8uM.png)

## Installation

XRay requires Redis to be installed and configured for ownCloud

## Live Updates


XRay can optionally use a NodeJS EventSource server to allow live updating the XRay data, to enable this make sure NodeJS is installed and run `npm run source` this will start the EventSource server on port 3003
