import Timestamp from 'react-time';

import {TablePage, Row} from './TablePage';

export class RequestRow extends Row {
	columns = ['Time', 'Path', 'Locks', 'Storage', 'Cache', 'Queries'];

	getBody (item) {
		return {
			time: <Timestamp
				value={item.time * 1000}
				relative
				titleFormat="HH:mm:ss.SSS"/>,
			path: item.path,
			locks: item.lock.length,
			storage: item.storage.length,
			cache: item.cache.length,
			queries: item.query.length
		}
	}
}

export default class Request extends TablePage {
	columns = ['Time', 'Path', 'Locks', 'Storage', 'Cache', 'Queries'];

	renderRow = (index, key) => {
		if (index > this.props.items.length - 5) {
			this.props.loadExtra();
		}
		return (
			<RequestRow key={key} rowKey={key}
						onClickItem={this.props.onOpenRequest}
						locks={this.props.items}
						setOverlay={this.setOverlay}
						toggleLive={this.props.toggleLive}
						item={this.filteredRows[index]}/>
		);
	};
}
