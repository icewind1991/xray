import React, {Component} from 'react';

export default class LockType extends Component {
	render () {
		const names = {
			1: 'Shared',
			2: 'Exclusive'
		};
		const value = (this.props.value > 0) ? '(' + this.props.value + ')' : '';
		return (
			<span>{names[this.props.type]} {value}</span>
		);
	}
}
