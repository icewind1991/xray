import EventSource from 'event-source';

export default class DataProvider {
	listening = false;
	source = null;

	listen (lockCb, storageCb) {
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

		source.addEventListener('lock', (e) => {
			const lock = JSON.parse(e.data);
			if (this.listening) {
				lockCb(lock);
			}
		});
		source.addEventListener('storage', (e) => {
			const lock = JSON.parse(e.data);
			if (this.listening) {
				storageCb(lock);
			}
		});
		this.source = source;
		return source;
	}

	stopListening () {
		this.listening = false;
	}
}
