var redis = require("redis");
var sub = redis.createClient();
var history = redis.createClient();

sub.subscribe("xray");

function getHistory (cb) {
	history.lrange('xray_history', 0, 512, (err, res) => cb(
		res.map(JSON.parse)
	));
}

var litesocket = require("litesocket");
var http = require('http');

function sendItem (res, item) {
	res.send(JSON.stringify(item.data), {
		event: item.type
	});
}

http.createServer(function (req, res) {
		litesocket(req, res, function () {
			getHistory((history) => {
				history.reverse().forEach(sendItem.bind(null, res));
				sub.on("message", function (channel, message) {
					sendItem(res, JSON.parse(message));
				});
			});
		})
	})
	.listen(3003);
