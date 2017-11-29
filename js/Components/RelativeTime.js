import {Component} from 'react';

export class RelativeTime extends Component {
	state = {
		diff: 0
	};

	scheduleRedraw (diff) {
		let timeout = 0;

		if (diff < 60) {
			timeout = 10;
		} else if (diff < 3600) {
			timeout = 600;
		}

		setTimeout(() => {
			this.setState({diff});
		}, timeout * 1000)
	}

	relTime (delta) {
		const minute = 60,
			hour = minute * 60,
			day = hour * 24,
			week = day * 7;

		let fuzzy;

		if (delta < 5) {
			fuzzy = 'seconds ago';
		} else if (delta < minute) {
			fuzzy = delta + ' seconds ago';
		} else if (delta < 2 * minute) {
			fuzzy = 'a minute ago'
		} else if (delta < hour) {
			fuzzy = Math.floor(delta / minute) + ' minutes ago';
		} else if (Math.floor(delta / hour) == 1) {
			fuzzy = '1 hour ago.'
		} else if (delta < day) {
			fuzzy = Math.floor(delta / hour) + ' hours ago';
		} else {
			fuzzy = Math.floor(delta / day) + ' days ago';
		}
		return fuzzy;
	}

	render () {
		const time = this.props.time;
		const now = Date.now();
		const diff = Math.round((now - time) / 1000);
		const relTime = this.relTime(diff);
		const formattedDate = OC.Util.formatDate(time);

		this.scheduleRedraw(diff);

		return <span title={formattedDate}>
			{relTime}
		</span>
	}
}
