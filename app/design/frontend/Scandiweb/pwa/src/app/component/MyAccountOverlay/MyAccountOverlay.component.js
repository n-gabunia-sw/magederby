import SourceComponent, { CUSTOMER_ACCOUNT_OVERLAY_KEY } from 'SourceComponent/MyAccountOverlay/MyAccountOverlay.component';
import { withRouter } from 'react-router-dom';
import Overlay from 'Component/Overlay';
import isMobile from 'Util/Mobile';
import Loader from 'Component/Loader/Loader.component';

import './MyAccountOverlay.override.style.scss';

export * from 'SourceComponent/MyAccountOverlay/MyAccountOverlay.component';

export class MyAccountOverlay extends SourceComponent.WrappedComponent {
    renderMyAccount() {
        const { state, isLoading } = this.props;
        const { render, title } = this.renderMap[state];

        console.log({ state, isLoading });
        return (
            <div block="MyAccountOverlay" elem="Action" mods={ { state, isLoading } }>
                <p block="MyAccountOverlay" elem="Heading">{ title }</p>
                { render() }
            </div>
        );
    }

    render() {
        const { isLoading, onVisible } = this.props;

        return (
            <Overlay
                id={ CUSTOMER_ACCOUNT_OVERLAY_KEY }
                mix={ { block: 'MyAccountOverlay', mods: { isLoading } } }
                onVisible={ onVisible }
                isStatic={ !!isMobile.any() }
            >
                <Loader isLoading={ isLoading } />
                { this.renderMyAccount() }
            </Overlay>
        );
    }
}

export default withRouter(MyAccountOverlay);