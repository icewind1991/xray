var redis = require("redis");
var sub = redis.createClient();

sub.subscribe("xray");

var requests = {};

// collect data until we get a SIGINT
sub.on("message", function (channel, message) {
	var data = JSON.parse(message);
	if (data.type === 'request') {
		requests[data.data.id] = data.data;
		requests[data.data.id].query = 0;
		requests[data.data.id].cache = 0;
		requests[data.data.id].storage = 0;
		requests[data.data.id].lock = 0;
	} else if(requests[data.data.request]) {
		requests[data.data.request][data.type]++;
	}
});

process.on("SIGINT", function () {
	console.log(JSON.stringify(requests));
	process.exit();
});
