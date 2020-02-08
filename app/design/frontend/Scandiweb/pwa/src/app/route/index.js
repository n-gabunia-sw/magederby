import { Route } from 'react-router-dom';
import SourceRouter, { SWITCH_ITEMS_TYPE } from 'SourceRoute/';
import CandleSlicePage from 'Route/CandleSlicePage';

export { BEFORE_ITEMS_TYPE } from 'SourceRoute/';
export { SWITCH_ITEMS_TYPE } from 'SourceRoute/';
export { AFTER_ITEMS_TYPE } from 'SourceRoute/';

export { history } from 'SourceRoute/';

class AppRouter extends SourceRouter {
    [SWITCH_ITEMS_TYPE] = [
        {
            component: <Route path="/candle-slice" component={ CandleSlicePage } />,
            position: 90
        },
        ...this[SWITCH_ITEMS_TYPE]
    ]
}

export default AppRouter
