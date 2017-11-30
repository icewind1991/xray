export default class DataProvider {
	lastRequest = null;

	getHistory (before = '') {
		if (this.lastRequest === before) {
			return $.when([]);
		}
		this.lastRequest = before;
		return $.get(OC.generateUrl(`/apps/xray/history?before=${before}`)).then(this.parseResults);
	}

	parseResults (items) {
		const requests = new Map();
		items.forEach(item => {
			if (!item) {
				return;
			}
			if (item.type === 'request') {
				item.data.lock = [];
				item.data.storage = [];
				item.data.cache = [];
				item.data.query = [];
				requests.set(item.data.id, item.data);
			}
		});
		items.forEach(item => {
			if (!item) {
				return;
			}
			if (item.type !== 'request') {
				const request = requests.get(item.data.request);
				if (request) {
					request[item.type].push(item.data);
				}
			}
		});
		return Array.from(requests.values());
	}

	getSince (since) {
		return $.get(OC.generateUrl(`/apps/xray/since?since=${since}`)).then(this.parseResults);
	}
}
