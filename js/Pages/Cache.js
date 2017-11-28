import {Component} from 'react';

import {RelativeTime} from '../Components/RelativeTime';
import StackTrace from '../Components/StackTrace';

import {TablePage, Row} from './TablePage';

export class CacheRow extends Row {
	columns = ['Time', 'Event', 'Path'];

	getDetails (item) {
		return <div>
			<StackTrace trace={item.stack}/>
		</div>;
	}

	getBody (item) {
		return {
			time: <RelativeTime
				time={item.time * 1000}/>,
			event: item.operation,
			path: item.path,
		}
	}
}

export default class Cache extends TablePage {
	columns = ['Time', 'Event', 'Path'];

	renderRow = (index, key) => {
		return (
			<CacheRow key={key} rowKey={key}
					 item={this.filteredRows[index]}/>
		);
	};
}
