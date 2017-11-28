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

export class App extends Component {
	loading = false;

	state = {
		live: false,
		filter: '',
		page: 'request', // lazy mans routing
		requests: [],
		allowLive: false
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

	render () {
		return (
			<AppContainer appId="xray">
				<SideBar withIcon={true}>
					<input className={style.filter} type="text"
						   placeholder="Filter path..."
						   onChange={this.onFilterChange.bind(this)}/>
				</SideBar>

				<Content>
					<Request filter={this.state.filter}
							 loadExtra={this.loadMore}
							 items={this.state.requests}/>
				</Content>
			</AppContainer>
		);
	}
}
