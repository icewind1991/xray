import Timestamp from 'react-time';

import {TablePage, Row} from './TablePage';

import SingleRequest from './SingleRequest';

class RequestRow extends Row {
	columns = ['Time', 'Path', 'Locks', 'Storage'];

	closeDetails = ()=> {
		this.setState({showDetail: false});
		this.props.toggleLive(true);
		this.props.setHidden(false);
	};

	onClick = () => {
		const showDetail = !this.state.showDetail;
		if (showDetail) {
			this.props.toggleLive(false);
			this.props.setHidden(false);
		}
		this.setState({showDetail})
	};

	getDetails (item) {
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
			storage: item.storage.length
		}
	}
}

export default class Request extends TablePage {
	state = {
		hidden: false
	};

	setHidden = (hidden) => {
		this.setState({hidden});
	};

	columns = ['Time', 'Path', 'Locks', 'Storage'];

	renderRow = (index, key) => {
		const className = (this.state.hidden) ? 'hidden' : '';
		return (
			<RequestRow className={className} key={key} rowKey={key}
						locks={this.props.items}
						setHidden={this.setHidden}
						toggleLive={this.props.toggleLive}
						item={this.filteredRows[index]}/>
		);
	};
}
