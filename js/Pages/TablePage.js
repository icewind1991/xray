import {Component} from 'react';

import ReactList from 'react-list'
import style from './TablePage.less';

export class Row extends Component {
	detailColumn = 'path';

	state = {
		showDetail: false
	};

	onClick = () => {
		this.setState({showDetail: !this.state.showDetail})
	};

	getBody (item) {
		return {};
	}

	getDetails (item) {
		return '';
	}

	getClassName (item) {
		return (item.success === false) ? 'error' : 'req-' + item.requestCounter;
	}

	render = () => {
		const key = this.props.rowKey;
		const item = this.props.item;

		const body = this.getBody(item);

		const classTable = style[this.getClassName(item)] + ((this.props.className) ? ' ' + this.props.className : '');

		const columns = this.columns.map((column, i) => {
			const className = column.toLowerCase();
			let details = '';
			if (className === this.detailColumn && this.state.showDetail) {
				details = this.getDetails(item);
			}
			if (style[className]) {
				return <td key={i}
						   className={style[className]}>{body[className]}{details}</td>
			} else {
				return <td key={i}>{body[className]}{details}</td>
			}
		});

		return (
			<tr key={key} className={classTable}
				onClick={this.onClick}>
				{columns}
			</tr>
		)
	};
}

export class TablePage extends Component {
	filteredRows = [];

	renderRow = (index, key) => {
		return (
			<tr/>
		);
	};

	renderer = (items, ref) => {
		const columns = this.columns.map(column => {
			const className = column.toLowerCase();
			if (style[className]) {
				return <th className={style[className]}>{column}</th>
			} else {
				return <th>{column}</th>
			}
		});
		const classTable = style.lockTable + ((this.props.className) ? ' ' + this.props.className : '');
		return (<table className={classTable}>
			<thead>
			<tr>
				{columns}
			</tr>
			</thead>
			<tbody ref={ref}>
			{items}
			</tbody>
		</table>);
	};

	render () {
		if (this.props.filter) {
			this.filteredRows = this.props.items.filter(item => {
				return item.path.indexOf(this.props.filter) !== -1;
			});
		} else {
			this.filteredRows = this.props.items;
		}

		if (this.props.type === 'normal') {
			const rows = this.filteredRows.map((item, i)=>this.renderRow(i, i));
			return this.renderer(rows, this.ref);
		} else {
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
}
