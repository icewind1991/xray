import {Component} from 'react';

import Timestamp from 'react-time';
import StackTrace from '../Components/StackTrace';

import {TablePage, Row} from './TablePage';

export class QueryRow extends Row {
	detailColumn = 'query';
	columns = ['Time', 'Query', 'Duration'];

	getDetails (item) {
		return <div>
			{JSON.stringify(item.parameters)}
			<StackTrace trace={item.stack}/>
		</div>;
	}

	getBody (item) {
		const duration = (item.duration * 1000).toFixed(2);
		return {
			time: <Timestamp
				value={item.time * 1000}
				relative
				titleFormat="HH:mm:ss.SSS"/>,
			query: item.sql,
			duration: duration + 'ms'
		}
	}
}

export default class Query extends TablePage {
	columns = ['Time', 'Query', 'Duration'];

	renderRow = (index, key) => {
		return (
			<QueryRow key={key} rowKey={key}
					 item={this.filteredRows[index]}/>
		);
	};
}
