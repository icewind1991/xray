import React, {Component} from 'react';

import LockType from './LockType';

import style from './LockState.less';

export default class LockState extends Component {
	calculateState (entries, fromLock) {
		const reversedEntries = entries.slice().reverse();
		const filteredEntries = reversedEntries.filter(entry=>entry.time <= fromLock.time);
		return filteredEntries.reduce(this.addLockEvent, {});
	}

	addLockEvent (state, entry) {
		function initForPath (path) {
			if (!state[path]) {
				state[path] = {
					trace: [],
					state: 0
				};
			}
		}

		switch (entry.operation) {
			case 1:
				initForPath(entry.path);
				state[entry.path].trace.push(entry.key);
				if (entry.type === 1) {
					state[entry.path].state++;
				} else {
					state[entry.path].state = -1;
				}
				return state;
			case 2:
				if (state[entry.path]) {
					if (entry.type === 1) {
						state[entry.path].state--;
					} else {
						state[entry.path].state = 0;
					}
					if (state[entry.path].state === 0) {
						delete state[entry.path];
					}
				}
				return state;
			case 3:
				if (state[entry.path]) {
					state[entry.path].trace.push(entry.key);
					if (entry.type === 1) {
						state[entry.path].state = 1;
					} else {
						state[entry.path].state = -1;
					}
				}
				return state;
		}
		return state;
	}

	render () {
		const state = this.calculateState(this.props.locks, this.props.lock);
		const locks = Object.keys(state).map(path => ({
			path: path,
			trace: state[path].trace,
			state: state[path].state
		}));
		const rows = locks.map((lock, i) => {
			const type = lock.state < 0 ? 2 : 1;
			return (
				<li key={i}>
					<span className={style.path}>{lock.path}</span>: <LockType
					type={type} value={lock.state}/>
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
