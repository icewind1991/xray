import {Component} from 'react';

export default class EventType extends Component {
	render () {
		const names = {
			1: 'Acquire',
			2: 'Release',
			3: 'Change'
		};
		const value = (this.props.value > 0) ? '(' + this.props.value + ')' : '';
		return (
			<span>{names[this.props.type]} {value}</span>
		);
	}
}
