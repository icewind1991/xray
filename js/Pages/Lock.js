import {Component} from 'react';

import ReactList from 'react-list'
import Timestamp from 'react-time';
import LockType from '../Components/LockType';
import style from '../../css/app.less';

export default class Lock extends Component {
	state = {
		showState: 0
	};

	toggleShowState (showState) {
		this.setState({showState});
	}

	renderRow = (index, key) => {
		const entry = this.props.locks[index];
		const onClick = (entry.event === 'error') ? function () {
		} : this.toggleShowState.bind(this, key);
		const className = (!entry.success) ? style.error : '';
		const event = (!entry.success) ? 'Error on ' + entry.operation : entry.operation;
		return (
			<tr key={key} className={className}
				onClick={onClick}>
				<td className={style.time}><Timestamp
					value={entry.time * 1000}
					relative
					titleFormat="HH:mm:ss.SSS"/>
				</td>
				<td className={style.event}>{event}</td>
				<td className={style.path}>{entry.path}</td>
				<td className={style.type}>
					<LockType type={entry.type}/>
				</td>
			</tr>
		)
	};

	renderer = (items, ref) => {
		return (<table className={style.locklog}>
			<thead>
			<tr>
				<th className={style.time}>Time</th>
				<th className={style.event}>Event</th>
				<th className={style.path}>Path</th>
				<th className={style.type}>Type</th>
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
				length={this.props.locks.length}
				type='uniform'
			/>
		);
	}
}
