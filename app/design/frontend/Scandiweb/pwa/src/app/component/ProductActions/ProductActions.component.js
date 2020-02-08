import SourceComponent from 'SourceComponent/ProductActions/ProductActions.component';
import TextPlaceholder from 'Component/TextPlaceholder';

export default class ProductActions extends SourceComponent {
    renderNameAndBrand() {
        const {
            product: {
                name,
                attributes: {
                    brand: {
                        attribute_value: brandValue,
                        attribute_options: brandOptions
                    } = {}
                } = {}
            },
            showOnlyIfLoaded
        } = this.props;
        const { [brandValue]: { label: brandName } = {} } = brandOptions || {};

        return (
            <section
              block="ProductActions"
              elem="Section"
              mods={ { type: 'name' } }
            >
                { showOnlyIfLoaded(
                    brandName,
                    (
                        <h4 block="ProductActions" elem="Brand" itemProp="brand">
                            <TextPlaceholder content={ brandName } />
                        </h4>
                    )
                ) }
                <p block="ProductActions" elem="Title" itemProp="name">
                    <TextPlaceholder content={ name } length="medium" />
                </p>
            </section>
        );
    }
}
