import {
    AddToCartContainer as SourceAddToCartContainer,
    mapDispatchToProps as sourceMapDispatchToProps,
    mapStateToProps as sourceMapStateToProps
} from 'SourceComponent/AddToCart/AddToCart.container';
import { connect } from 'react-redux';
import { customerType } from 'Type/Account';
import BrowserDatabase from 'Util/BrowserDatabase';

export const mapStateToProps = state => ({
    ...sourceMapStateToProps(state),
    customer: state.MyAccountReducer.customer
});

export const mapDispatchToProps = dispatch => ({
    ...sourceMapDispatchToProps(dispatch)
});

export const CUSTOMER = 'customer';

export class AddToCartContainer extends SourceAddToCartContainer {
    static propTypes = {
        ...this.propTypes,
        customer: customerType.isRequired
    }

    buttonClick() {
        const {
            product,
            onProductValidationError,
            configurableVariantIndex,
            groupedProductQuantity,
            quantity,
            addProduct
        } = this.props;

        const customer = BrowserDatabase.getItem(CUSTOMER) || {};
        const userId = customer.id ? customer.id : null;

        console.log(userId);
        const { variants, type_id } = product;

        if (!this._validateAddToCart()) {
            onProductValidationError(type_id);
            return;
        }

        this.setState({ isLoading: true });

        if (type_id === 'grouped') {
            const { items } = product;

            Promise.all(items.map((item) => {
                const { product: groupedProductItem } = item;

                groupedProductItem.parent = product;
                const quantity = groupedProductQuantity[groupedProductItem.id];
                if (!quantity) return Promise.resolve();

                return addProduct({
                    product: groupedProductItem,
                    quantity,
                    userId
                });
            })).then(() => this._afterAdded());

            return;
        }

        const productToAdd = variants
            ? {
                ...product,
                configurableVariantIndex
            }
            : product;

        addProduct({
            product: productToAdd,
            quantity,
            userId
        }).then(
            () => this._afterAdded()
        ).catch(
            () => this.resetLoading()
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(AddToCartContainer);
