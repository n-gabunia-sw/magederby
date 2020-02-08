import SourceLoader from 'SourceComponent/Loader/Loader.component';
import PropTypes from 'prop-types';

import './Loader.style.scss';

export default class Loader extends SourceLoader {
    static propTypes = {
        isLoading: PropTypes.bool.isRequired
    };

    render() {
        const { isLoading } = this.props;

        if (!isLoading) return null;

        return (
            <div block="Loader" elem="LoaderWrapper">
                <div block="Loader" elem="Main">
                    <div block="Loader" elem="Holder">
                        <div block="Loader" elem="Candle">
                            <div block="Loader" elem="BlinkingGlow" />
                            <div block="Loader" elem="Thread" />
                            <div block="Loader" elem="Glow" />
                            <div block="Loader" elem="Flame" />
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
