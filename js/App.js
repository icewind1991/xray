import {Component} from 'react';

import {
	SideBar,
	Entry,
	Separator,
	App as AppContainer,
	Content,
	ControlBar,
	Settings
} from 'oc-react-components';

import DataProvider from './DataProvider';

import {ToggleEntry} from './Components/ToggleEntry';

import Lock from './Pages/Lock';
import Storage from './Pages/Storage';
import Request from './Pages/Request';
import Cache from './Pages/Cache';
import Query from './Pages/Query';

import style from '../css/app.less';
import SingleRequest from "./Pages/SingleRequest";

export class App extends Component {
	loading = false;

	state = {
		filter: '',
		requests: [],
		openRequest: null
	};

	constructor () {
		super();
		this.source = new DataProvider();
	}

	initRequest (request) {
		if (!this.requests[request]) {
			this.requests[request] = {
				id: request,
				time: 0,
				path: '',
				lock: [],
				storage: [],
				cache: [],
				query: []
			};
		}
	}

	componentDidMount () {
		this.loadMore();
		document.addEventListener('paste', this.handlePaste)
	}

	handlePaste = (event) => {
		let data = event.clipboardData.getData('Text');
		if (!data) {
			data = event.clipboardData.getData('text/plain');
		}
		data = data.trim();
		if (data.indexOf('{') !== -1 && data.indexOf('}')) {
			this.loadRequestData(data);
		}
	};

	loadRequestData (rawData) {
		rawData = rawData.replace(/\n/g, '');
		const data = JSON.parse(rawData);
		if (data.id && data.path) {
			this.openRequest(data);
		}
	}

	onClick (page, e) {
		e.preventDefault();
		if (this.state.page !== page) {
			this.setState({
				page: page
			});
		}
	}

	onFilterChange (event) {
		this.setState({filter: event.target.value});
	}

	getRequests () {
		return Object.values(this.state.requests).sort((a, b) => b.time - a.time);
	}

	loadMore = () => {
		if (this.loading) {
			return;
		}
		this.loading = true;
		const lastRequest = this.state.requests.length > 0 ? this.state.requests[this.state.requests.length - 1] : '';
		this.source.getHistory(lastRequest.id).then(requests => {
			this.loading = false;
			if (requests.length > 0) {
				this.setState({requests: this.state.requests.concat(requests)});
			}
		});
	};

	openRequest = (item) => {
		this.setState({openRequest: item});
	};

	closeRequest = () => {
		this.setState({openRequest: null});
	};

	render () {
		return (
			<AppContainer appId="xray">
				<ControlBar>
					<div className={style.filterWrapper}>
						<input className={style.filter} type="text"
							   placeholder="Filter path..."
							   onChange={this.onFilterChange.bind(this)}/>
					</div>
				</ControlBar>

				<Content>
					{this.state.openRequest ?
						<SingleRequest request={this.state.openRequest}
									   close={this.closeRequest}/> :
						<Request filter={this.state.filter}
								 loadExtra={this.loadMore}
								 onOpenRequest={this.openRequest}
								 items={this.state.requests}/>
					}
				</Content>
			</AppContainer>
		);
	}
}
