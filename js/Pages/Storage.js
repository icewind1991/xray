import Timestamp from 'react-time';
import StackTrace from '../Components/StackTrace';
import {TablePage, Row} from './TablePage';

class StorageRow extends Row {
	columns = ['Time', 'Event', 'Path', 'Duration'];

	getBody (item) {
		const duration = (item.duration * 1000).toFixed(2);
		return {
			time: <Timestamp
				value={item.time * 1000}
				relative
				titleFormat="HH:mm:ss.SSS"/>,
			event: item.operation,
			path: item.path,
			duration: duration + 'ms'
		}
	}

	getDetails (item) {
		return <StackTrace trace={item.stack}/>;
	}
}

export default class Storage extends TablePage {
	columns = ['Time', 'Event', 'Path', 'Duration'];

	renderRow = (index, key) => {
		return (
			<StorageRow key={key} rowKey={key}
						item={this.filteredRows[index]}/>
		);
	};
}
