import SourceComponent from 'SourceComponent/SearchOverlay/SearchOverlay.component';
import Link from 'Component/Link';
import Image from 'Component/Image';

export default class SearchOverlay extends SourceComponent {
    renderSearchItem(product, i) {
        const { getProductLinkTo } = this.props;
        const {
            name,
            thumbnail: { path } = {},
            attributes: {
                brand: {
                    attribute_value: brandValue,
                    attribute_options: brandOptions
                } = {}
            } = {}
        } = product;
        const { [brandValue]: { label: brandName } = {} } = brandOptions || {};

        const imageSrc = path ? media(path, PRODUCT_MEDIA) : undefined;

        return (
            <li
              block="SearchOverlay"
              elem="Item"
              key={ i }
            >
                <Link
                  block="SearchOverlay"
                  elem="Link"
                  to={ getProductLinkTo(product) }
                  onClick={ this.handleItemClick }
                >
                    <figure
                      block="SearchOverlay"
                      elem="Wrapper"
                    >
                        <Image
                          block="SearchOverlay"
                          elem="Image"
                          src={ imageSrc }
                          alt={ __('Product %s thumbnail.', name) }
                          isPlaceholder={ !imageSrc }
                        />
                        <figcaption block="SearchOverlay" elem="Content">
                            { this.renderSearchItemContent(name, brandName) }
                        </figcaption>
                    </figure>
                </Link>
            </li>
        );
    }
}
