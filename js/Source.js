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

var port = process.env.PORT || 3003;

http.createServer(function (req, res) {
	res.setHeader('Access-Control-Allow-Origin', '*');
	res.setHeader('Access-Control-Request-Method', '*');
	litesocket(req, res, function () {
		getHistory((history) => {
			history.reverse().forEach(sendItem.bind(null, res));
			sub.on("message", function (channel, message) {
				sendItem(res, JSON.parse(message));
			});
		});
	})
}).listen(port);

console.log('listening on port ' + port);
