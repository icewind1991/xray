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
			<LockTable filter="" items={request.locks}/> :
			<p className={style.none}>No Locks</p>;

		const storage = (request.storage.length > 0) ?
			<StorageTable filter="" items={request.storage}/> :
			<p className={style.none}>No Storage operations</p>;

		return (<div onClick={this.onClick} className={style.requestDetails}>
			<button className={style.close} onClick={this.props.close}>Close</button>
			<h1 className={style.title}>{request.method} {request.path}</h1>
			<h2 className={style.category}>Locks</h2>
			{locks}
			<h2 className={style.category}>Storage</h2>
			{storage}
		</div>);
	}
}
