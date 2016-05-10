import EventSource from 'event-source';

export default class DataProvider {
	listening = false;
	source = null;

	listen (lockCb, storageCb, requestCb, cacheCb) {
		if (this.listening) {
			return;
		}
		this.listening = true;

		// const source = new EventSource(OC.generateUrl(`/apps/xray/listen?historySize=128`));
		const source = new EventSource(`http://localhost:3003`);
		source.addEventListener('__internal__', (data) => {
			if (data === 'close') {
				console.log('closed from remote');
				source.close();
				setTimeout(this.listen.bind(this, cb), 100);
			}
		});

		let lastRequest = '';
		let requestCounter = 0;
		source.addEventListener('lock', (e) => {
			const lock = JSON.parse(e.data);
			if (this.listening) {
				if (lock.request !== lastRequest) {
					requestCounter = 1 - requestCounter;
					lastRequest = lock.request;
				}
				lock.requestCounter = requestCounter;
				lockCb(lock);
			}
		});
		source.addEventListener('storage', (e) => {
			const lock = JSON.parse(e.data);
			if (this.listening) {
				if (lock.request !== lastRequest) {
					requestCounter = 1 - requestCounter;
					lastRequest = lock.request;
				}
				lock.requestCounter = requestCounter;
				storageCb(lock);
			}
		});
		source.addEventListener('cache', (e) => {
			const lock = JSON.parse(e.data);
			if (this.listening) {
				if (lock.request !== lastRequest) {
					requestCounter = 1 - requestCounter;
					lastRequest = lock.request;
				}
				lock.requestCounter = requestCounter;
				cacheCb(lock);
			}
		});
		source.addEventListener('request', (e) => {
			const request = JSON.parse(e.data);
			if (this.listening) {
				requestCb(request);
			}
		});
		this.source = source;
		return source;
	}

	stopListening () {
		this.listening = false;
	}
}
