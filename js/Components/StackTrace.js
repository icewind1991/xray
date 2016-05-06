import {Component} from 'react';

export default class StackTrace extends Component {
	render () {

		const rows = this.props.trace.filter(trace => {
			return trace.file;
		}).map((trace)=> {
			const line = (trace.file.indexOf('runtime-created') > 0) ? trace.file : `${trace.file}: ${trace.line}`;
			const method = (trace.type === '->') ? trace.class + '->' + trace.function : trace.function;
			return (
				<tr>
					<td>{line}</td>
					<td>{method}</td>
				</tr>
			);
		});

		return (
			<table>
				<thead>
				<tr>
					<th>Line</th>
					<th>Method</th>
				</tr>
				</thead>
				<tbody>
				{rows}
				</tbody>
			</table>
		);
	}
}
