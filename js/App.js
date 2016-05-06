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

import style from '../css/app.less';

export class App extends Component {
	live = true;
	locks = [];
	storage = [];

	state = {
		live: true,
		filter: '',
		page: 'lock', // lazy mans routing
		locks: [],
		storage: []
	};

	constructor () {
		super();
		this.source = new DataProvider();
	}

	componentDidMount () {
		this.source.listen((lock) => {
			this.locks.unshift(lock);
			if (this.live) {
				this.setState({locks: this.locks});
			}
		}, storageOperation => {
			this.storage.unshift(storageOperation);
			if (this.live) {
				this.setState({storage: this.storage});
			}
		});
	}

	onClick (page, e) {
		e.preventDefault();
		if (this.state.page !== page) {
			this.setState({
				page: page
			});
		}
	}

	toggleLive (live) {
		this.live = live;
		if (live) {
			this.setState({locks: this.locks, storage: this.storage});
		}
	}

	onFilterChange (event) {
		this.setState({filter: event.target.value});
	}

	render () {
		let page;
		switch (this.state.page) {
			case 'lock':
				page = <Lock filter={this.state.filter} locks={this.state.locks}/>;
				break;
			case 'storage':
				page = <Storage filter={this.state.filter} operations={this.state.storage}/>;
				break;
			default:
				page = <div>Unknown page</div>;
		}

		return (
			<AppContainer appId="xray">
				<SideBar withIcon={true}>
					<ToggleEntry onChange={this.toggleLive.bind(this)}
								 active={this.live}>Live
						Updates</ToggleEntry>
					<Separator/>
					<Entry key={1} icon="home"
						   onClick={this.onClick.bind(this,'lock')}>Locks</Entry>
					<Entry key={2} icon="link"
						   onClick={this.onClick.bind(this,'storage')}>Storage</Entry>

					<Settings>
						<h2>
							Foo...
						</h2>
					</Settings>
				</SideBar>

				<ControlBar>
					<input type="text" placeholder="Search..."
						   onChange={this.onFilterChange.bind(this)}/>
				</ControlBar>

				<Content>
					{page}
				</Content>
			</AppContainer>
		);
	}
}
