import {Component} from 'react';

import ReactList from 'react-list'
import Timestamp from 'react-time';
import StackTrace from '../Components/StackTrace';
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
		let trace = '';
		if (this.state.showState) {
			trace = (<StackTrace trace={entry.stack}/>);
		}
		const className = 'req-' + entry.requestCounter;
		return (
			<tr key={key} className={style[className]}
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
					{trace}
				</td>
			</tr>
		)
	};
}

export default class Storage extends Component {
	filteredRows = [];

	renderRow = (index, key) => {
		return (
			<StorageRow rowKey={key}
						operation={this.filteredRows[index]}/>
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
		if (this.props.filter) {
			this.filteredRows = this.props.operations.filter(lock => {
				return lock.path.indexOf(this.props.filter) !== -1;
			});
		} else {
			this.filteredRows = this.props.operations;
		}

		return (
			<ReactList
				itemRenderer={this.renderRow}
				itemsRenderer={this.renderer}
				length={this.filteredRows.length}
				type='uniform'
			/>
		);
	}
}
