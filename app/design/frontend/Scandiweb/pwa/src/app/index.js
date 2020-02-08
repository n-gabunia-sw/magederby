import { PureComponent } from 'react';
import { Provider } from 'react-redux';
import AppRouter from 'Route';
import store from 'Store';
import ReactDOM from 'react-dom';
import 'Style/main';

// Disable react dev tools in production
if (process.env.NODE_ENV === 'production'
    && window.__REACT_DEVTOOLS_GLOBAL_HOOK__
) window.__REACT_DEVTOOLS_GLOBAL_HOOK__.inject = function () {};

// Enable React hot reload in development
if (process.env.NODE_ENV === 'development') {
    module.hot.accept('./index.js', () => {
        // eslint-disable-next-line import/no-self-import, global-require
        const NextRootContainer = require('./index.js').default;
        ReactDOM.render(<NextRootContainer />, document.getElementById('root'));
    });
}

class App extends PureComponent {
    render() {
        return (
            <Provider store={ store }>
                <AppRouter />
            </Provider>
        );
    }
}

ReactDOM.render(<App />, document.getElementById('root'));
