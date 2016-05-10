import {Component} from 'react';

import Timestamp from 'react-time';
import StackTrace from '../Components/StackTrace';

import {TablePage, Row} from './TablePage';

class CacheRow extends Row {
	columns = ['Time', 'Event', 'Path'];

	getDetails (item) {
		return <div>
			<StackTrace trace={item.stack}/>
		</div>;
	}

	getBody (item) {
		return {
			time: <Timestamp
				value={item.time * 1000}
				relative
				titleFormat="HH:mm:ss.SSS"/>,
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
