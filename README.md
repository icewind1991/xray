# XRay

See what is happening inside the Nextcloud instance.

Note: this app is **not** for production use and should not be enabled without good reason. 

![xray](https://github.com/icewind1991/xray/raw/master/screenshots/xray.png)

This app enables developers to monitor the usage of external resources include the
full stack trace of the code leading up to the resource usage to make it easier to
reduce the usage of external resources.

## Warning

This app involves logging a significant amount of potentially personal information,
this means that having the app enabled will incur a performance penalty and increase
the database load and sharing any information logged by the app can lead to leaking of
personal information.

### Usage

For correct use [debug mode](https://docs.nextcloud.com/server/12/developer_manual/general/debugging.html)
should be enabled to ensure that xray can access the info it requires.

## Developing

XRay uses webpack to compile it's javascript for the browser, you can build the
project by running `make`
