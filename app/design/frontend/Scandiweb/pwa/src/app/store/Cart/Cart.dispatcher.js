import { fetchMutation } from 'Util/Request';
import { isSignedIn } from 'Util/Auth';
import { CartQuery } from 'Query';
import { showNotification } from 'Store/Notification';
import { getExtensionAttributes } from 'Util/Product';
import { CartDispatcher as SourceCartDispatcher } from 'SourceStore/Cart/Cart.dispatcher';

export const GUEST_QUOTE_ID = 'guest_quote_id';

/**
 * Product Cart Dispatcher
 * @class CartDispatcher
 */
export class CartDispatcher extends SourceCartDispatcher {
    addProductToCart(dispatch, options) {
        const { product, quantity, userId } = options;
        const { sku, type_id: product_type } = product;

        const productToAdd = {
            sku,
            product_type,
            qty: parseInt(quantity, 10),
            product_option: { extension_attributes: getExtensionAttributes(product) },
            userId
        };

        if (this._canBeAdded(options)) {
            return fetchMutation(CartQuery.getSaveCartItemMutation(
                productToAdd, !isSignedIn() && this._getGuestQuoteId()
            )).then(
                ({ saveCartItem: { cartData } }) => this._updateCartData(cartData, dispatch),
                ([{ message }]) => {
                    dispatch(showNotification('error', message));
                    return Promise.reject();
                }
            );
        }

        return Promise.reject();
    }

    changeItemQty(dispatch, options) {
        const { item_id, quantity, sku, userId } = options;

        return fetchMutation(CartQuery.getSaveCartItemMutation(
            { sku, item_id, qty: quantity, userId },
            !isSignedIn() && this._getGuestQuoteId()
        )).then(
            ({ saveCartItem: { cartData } }) => this._updateCartData(cartData, dispatch),
            error => dispatch(showNotification('error', error[0].message))
        );
    }
}

export default new CartDispatcher();
