import {
    CartItemContainer as SourceCartItemContainer,
    mapDispatchToProps as sourceMapDispatchToProps
} from 'SourceComponent/CartItem/CartItem.container';
import { connect } from 'react-redux';
import BrowserDatabase from 'Util/BrowserDatabase';

export const mapDispatchToProps = dispatch => ({
    ...sourceMapDispatchToProps(dispatch)
});

export const CUSTOMER = 'customer';

export class CartItemContainer extends SourceCartItemContainer {
    /**
     * Handle item quantity change. Check that value is <1
     * @param {Number} value new quantity
     * @return {void}
     */
    handleChangeQuantity(quantity) {
        const customer = BrowserDatabase.getItem(CUSTOMER) || {};
        const userId = customer.id ? customer.id : null;

        this.setState({ isLoading: true }, () => {
            const { changeItemQty, item: { item_id, sku } } = this.props;
            this.hideLoaderAfterPromise(changeItemQty({ item_id, quantity, sku, userId }));
        });
    }
}

export default connect(null, mapDispatchToProps)(CartItemContainer);
