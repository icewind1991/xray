import Timestamp from 'react-time';

import {TablePage, Row} from './TablePage';

import SingleRequest from './SingleRequest';

class RequestRow extends Row {
	columns = ['Time', 'Path', 'Locks', 'Storage', 'Cache'];

	closeDetails = ()=> {
		this.setState({showDetail: false});
		this.props.toggleLive(true);
		this.props.setOverlay(null);
	};

	getOverlay (item) {
		this.props.toggleLive(false);
		return (<SingleRequest close={this.closeDetails} request={item}/>);
	}

	getBody (item) {
		return {
			time: <Timestamp
				value={item.time * 1000}
				relative
				titleFormat="HH:mm:ss.SSS"/>,
			path: item.path,
			locks: item.locks.length,
			storage: item.storage.length,
			cache: item.cache.length
		}
	}
}

export default class Request extends TablePage {
	columns = ['Time', 'Path', 'Locks', 'Storage', 'Cache'];

	renderRow = (index, key) => {
		return (
			<RequestRow key={key} rowKey={key}
						locks={this.props.items}
						setOverlay={this.setOverlay}
						toggleLive={this.props.toggleLive}
						item={this.filteredRows[index]}/>
		);
	};
}
