import SourceComponent, { AMOUNT_OF_PLACEHOLDERS } from 'SourceComponent/SearchOverlay/SearchOverlay.component';
import Link from 'Component/Link';
import Image from 'Component/Image';
import Overlay from 'Component/Overlay';
import media, { PRODUCT_MEDIA } from 'Util/Media';
import './SearchOverlay.override.style.scss';

export { AMOUNT_OF_PLACEHOLDERS } from 'SourceComponent/SearchOverlay/SearchOverlay.component';
export const OVERLAY_SEARCH_ITEM_LIMIT = 7;

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

    renderMoreButton = () => {
        const { searchCriteria, searchResults, isLoading } = this.props;

        if (!searchCriteria || !searchResults.length || isLoading || this.timeout) return null;

        return (
            <p
              block="SearchOverlay"
              elem="MoreLink"
              mods={ { isVisible: !!searchCriteria } }
            >
                <Link to={ `/search/${searchCriteria}`} onClick={ this.handleItemClick }>
                    { __('See more results for: ') }
                    <strong>{ searchCriteria }</strong>
                </Link>
            </p>
        );
    };

    renderSearchResults() {
        const { searchCriteria, searchResults, isLoading } = this.props;

        if (!searchCriteria) return this.renderNoSearchCriteria();
        if (!searchResults.length && !isLoading && !this.timeout) return this.renderNoResults();
        const resultsToRender = (isLoading || this.timeout) ? Array(AMOUNT_OF_PLACEHOLDERS).fill({}) : searchResults;

        return (
            <ul>
                { resultsToRender.slice(0, OVERLAY_SEARCH_ITEM_LIMIT).map((item, i) => this.renderSearchItem(item, i)) }
            </ul>
        );
    }

    render() {
        return (
            <Overlay
              id="search"
              mix={ { block: 'SearchOverlay' } }
            >
                { this.renderSearchCriteria() }
                <article
                  block="SearchOverlay"
                  elem="Results"
                  aria-label="Search results"
                >
                    { this.renderSearchResults() }
                </article>
                { this.renderMoreButton() }
            </Overlay>
        );
    }
}
