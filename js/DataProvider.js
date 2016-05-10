import EventSource from 'event-source';

export default class DataProvider {
	listening = false;
	source = null;

	lastRequest = '';
	requestCounter = 0;

	listen (lockCb, storageCb, requestCb, cacheCb, allowLiveCb) {
		if (this.listening) {
			return;
		}
		this.listening = true;

		const source = new EventSource(`http://localhost:3003`);
		source.onerror = () => {
			source.close();
			allowLiveCb(false);
			$.get(OC.generateUrl(`/apps/xray/history`)).then(items=> {
				items.forEach(item => {
					switch (item.type) {
						case 'request':
							this.onRequest(requestCb, item.data);
							break;
						case 'storage':
							this.onRequest(storageCb, item.data);
							break;
						case 'lock':
							this.onRequest(requestCb, item.data);
							break;
						case 'cache':
							this.onRequest(cacheCb, item.data);
							break;
					}
				});
			});
		};
		source.addEventListener('lock', (e) => {
			this.onRequest(lockCb, JSON.parse(e.data));
		});
		source.addEventListener('storage', (e) => {
			this.onRequest(storageCb, JSON.parse(e.data));
		});
		source.addEventListener('cache', (e) => {
			this.onRequest(cacheCb, JSON.parse(e.data));
		});
		source.addEventListener('request', (e) => {
			allowLiveCb(true);
			this.onRequest(requestCb, JSON.parse(e.data));
		});
		this.source = source;
		return source;
	}

	onRequest (cb, data) {
		if (this.listening) {
			cb(data);
		}
	}

	onLock (cb, data) {
		if (this.listening) {
			if (data.request !== this.lastRequest) {
				this.requestCounter = 1 - this.requestCounter;
				this.lastRequest = data.request;
			}
			data.requestCounter = this.requestCounter;
			cb(data);
		}
	}

	onStorage (cb, data) {
		if (this.listening) {
			if (data.request !== this.lastRequest) {
				this.requestCounter = 1 - this.requestCounter;
				this.lastRequest = data.request;
			}
			data.requestCounter = this.requestCounter;
			cb(data);
		}
	}

	onCache (cb, data) {
		if (this.listening) {
			if (data.request !== this.lastRequest) {
				this.requestCounter = 1 - this.requestCounter;
				this.lastRequest = data.request;
			}
			data.requestCounter = this.requestCounter;
			cb(data);
		}
	}

	stopListening () {
		this.listening = false;
	}
}
