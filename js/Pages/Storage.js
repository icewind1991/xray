import {Component} from 'react';

import ReactList from 'react-list'
import Timestamp from 'react-time';
import LockType from '../Components/LockType';
import EventType from '../Components/LockEventType';
import LockState from '../Components/LockState';
import style from './Lock.less';

class StorageRow extends Component {
	state = {
		showState: false
	};

	render = () => {
		const key = this.props.rowKey;
		const entry = this.props.operation;
		const onClick = () => {
			this.setState({showState: !this.state.showState})
		};
		const event = entry.operation;
		let state = '';
		return (
			<tr key={key}
				onClick={onClick}>
				<td className={style.time}><Timestamp
					value={entry.time * 1000}
					relative
					titleFormat="HH:mm:ss.SSS"/>
				</td>
				<td className={style.event}>
					{event}
				</td>
				<td className={style.path}>
					{entry.path}
					{state}
				</td>
			</tr>
		)
	};
}

export default class Storage extends Component {
	renderRow = (index, key) => {
		return (
			<StorageRow rowKey={key} operation={this.props.operations[index]}/>
		);
	};

	renderer = (items, ref) => {
		return (<table className={style.lockTable}>
			<thead>
			<tr>
				<th className={style.time}>Time</th>
				<th className={style.event}>Event</th>
				<th className={style.path}>Path</th>
			</tr>
			</thead>
			<tbody ref={ref}>
			{items}
			</tbody>
		</table>);
	};

	render () {
		return (
			<ReactList
				itemRenderer={this.renderRow}
				itemsRenderer={this.renderer}
				length={this.props.operations.length}
				type='uniform'
			/>
		);
	}
}
