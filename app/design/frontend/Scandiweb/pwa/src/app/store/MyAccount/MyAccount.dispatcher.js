import { updateCustomerSignInStatus } from 'Store/MyAccount';
import { deleteAuthorizationToken } from 'Util/Auth';
import { WishlistDispatcher } from 'Store/Wishlist';
import { CartDispatcher } from 'Store/Cart';
import { history } from 'Route';
import BrowserDatabase from 'Util/BrowserDatabase';
import { ORDERS } from 'Store/Order/Order.reducer';
import { MyAccountDispatcher as SourceMyAccountDispatcher } from 'SourceStore/MyAccount/MyAccount.dispatcher';
export const CUSTOMER = 'customer';
/**
 * My account actions
 * @class MyAccount
 */
export class MyAccountDispatcher extends SourceMyAccountDispatcher {
    logout(_, dispatch) {
        dispatch(updateCustomerSignInStatus(false));
        deleteAuthorizationToken();
        CartDispatcher.updateInitialCartData(dispatch);
        WishlistDispatcher.updateInitialWishlistData(dispatch);
        BrowserDatabase.deleteItem(ORDERS);
        BrowserDatabase.deleteItem(CUSTOMER);
        history.push('/');
    }
}
export default new MyAccountDispatcher();
