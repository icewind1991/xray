import {Component} from 'react';

import LockTable from './Lock';
import StorageTable from './Storage';

import style from './Request.less';

export default class SingleRequest extends Component {

	onClick = (event) => {
		event.stopPropagation();
	};

	render () {
		const request = this.props.request;

		const locks = (request.locks.length > 0) ?
			<LockTable type="normal" filter="" items={request.locks}/> :
			<p className={style.none}>No Locks</p>;

		const storage = (request.storage.length > 0) ?
			<StorageTable type="normal" filter="" items={request.storage}/> :
			<p className={style.none}>No Storage operations</p>;

		delete request.params['v'];

		const paramRows = Object.keys(request.params).map(key => (
			<tr>
				<td>
					{key}
				</td>
				<td>
					{request.params[key]}
				</td>
			</tr>
		));

		const params = (Object.keys(request.params).length > 0) ?
			<table className={style.params}>
				<thead>
				<tr>
					<th>Key</th>
					<th>Value</th>
				</tr>
				</thead>
				<tbody>
				{paramRows}
				</tbody>
			</table> :
			<p className={style.none}>No Parameters</p>;

		return (<div onClick={this.onClick} className={style.requestDetails}>
			<button className={style.close} onClick={this.props.close}>Close
			</button>
			<h1 className={style.title}>{request.method} {request.path}</h1>
			<h2 className={style.category}>Parameters</h2>
			{params}
			<h2 className={style.category}>Locks</h2>
			{locks}
			<h2 className={style.category}>Storage</h2>
			{storage}
		</div>);
	}
}
