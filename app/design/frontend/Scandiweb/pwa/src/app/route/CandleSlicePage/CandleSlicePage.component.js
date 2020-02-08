import { PureComponent } from 'react';
import CandleSlicer from 'Component/CandleSlicer';

export default class CandleSlicePage extends PureComponent {
    render() {
        return (
            <div block="CandleSlicePage">
                <CandleSlicer />
            </div>
        );
    }
}
