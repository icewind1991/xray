import {Component} from 'react';

import Timestamp from 'react-time';

import {TablePage, Row} from './TablePage';

class RequestRow extends Row {
	columns = ['Time', 'Path', 'Locks', 'Storage'];

	getDetails (item) {
		return <div>
		</div>;
	}

	getBody (item) {
		return {
			time: <Timestamp
				value={item.time * 1000}
				relative
				titleFormat="HH:mm:ss.SSS"/>,
			path: item.path,
			locks: item.locks.length,
			storage: item.storage.length,
		}
	}
}

export default class Lock extends TablePage {
	columns = ['Time', 'Path', 'Locks', 'Storage'];

	renderRow = (index, key) => {
		return (
			<RequestRow key={key} rowKey={key} locks={this.props.items}
						item={this.filteredRows[index]}/>
		);
	};
}
