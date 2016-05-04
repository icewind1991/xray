import React, {Component} from 'react';
import Timestamp from 'react-time';

import {LockType} from './LockType';

import style from './LockState.less';

export class LockState extends Component {
	render () {
		const locks = Object.keys(this.props.state).map(path => ({
			path: path,
			trace: this.props.state[path].trace,
			state: this.props.state[path].state
		}));
		const rows = locks.map(lock => {
			const type = lock.state < 0 ? 2 : 1;
			return (
				<li>
					<span className={style.path}>{lock.path}</span>: <LockType type={type} value={lock.state}/>
				</li>
			)
		});
		return (
			<div className={style.state}>
				<p>State after operation:</p>
				<ul>
					{rows}
				</ul>
			</div>
		);
	}
}
