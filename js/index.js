'use strict';

import React from 'react';
import ReactDOM from 'react-dom';

import {App} from './App.js';

// Enable React devtools
window.React = React;

$(document).ready(() => {
	ReactDOM.render(<App />, document.getElementById('content'));
});
