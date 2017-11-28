import {Component} from 'react';

import {RelativeTime} from '../Components/RelativeTime';
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
			time: <RelativeTime
				time={item.time * 1000}/>,
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
