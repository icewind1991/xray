import {Component} from 'react';

import Timestamp from 'react-time';
import LockType from '../Components/LockType';
import EventType from '../Components/LockEventType';
import LockState from '../Components/LockState';
import StackTrace from '../Components/StackTrace';

import {TablePage, Row} from './TablePage';

export class LockRow extends Row {
	columns = ['Time', 'Event', 'Path', 'Type'];

	getDetails (item) {
		return <div>
			<LockState locks={this.props.locks} lock={item}/>
			<StackTrace trace={item.stack}/>
		</div>;
	}

	getBody(item) {
		return {
			time: <Timestamp
				value={item.time * 1000}
				relative
				titleFormat="HH:mm:ss.SSS"/>,
			event: <EventType type={item.operation}/>,
			path: item.path,
			type: <LockType type={item.type}/>
		}
	}
}

export default class Lock extends TablePage {
	columns = ['Time', 'Event', 'Path', 'Type'];

	renderRow = (index, key) => {
		return (
			<LockRow key={key} rowKey={key} locks={this.props.items}
					 item={this.filteredRows[index]}/>
		);
	};
}
