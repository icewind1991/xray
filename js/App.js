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

import {LockType} from './Components/LockType';
import {LockState} from './Components/LockState'
import {ToggleEntry} from './Components/ToggleEntry';

import Lock from './Pages/Lock';
import Storage from './Pages/Storage';

import style from '../css/app.less';

export class App extends Component {
	state = {
		page: 'lock', // lazy mans routing
		locks: [],
		storage: []
	};

	constructor () {
		super();
		this.source = new DataProvider();
	}

	componentDidMount () {
		let locks = [];
		let storage = [];
		this.source.listen((lock) => {
			locks.unshift(lock);
			this.setState({locks});
		}, storageOperation => {
			storage.unshift(storageOperation);
			this.setState({storage});
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

	render () {
		let page;
		switch (this.state.page) {
			case 'lock':
				page = <Lock locks={this.state.locks}/>;
				break;
			case 'storage':
				page = <Storage operations={this.state.storage}/>;
				break;
			default:
				page = <div>Unknown page</div>;
		}

		return (
			<AppContainer appId="xray">
				<SideBar withIcon={true}>
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
					<input type="text" placeholder={this.state.page}/>
				</ControlBar>

				<Content>
					{page}
				</Content>
			</AppContainer>
		);
	}
}
