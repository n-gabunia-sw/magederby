import PropTypes from 'prop-types';
import CategoryPage from 'Route/CategoryPage/CategoryPage.component';

export default class SearchPage extends CategoryPage {
    static propTypes = {
        ...CategoryPage.propTypes,
        currentCategory: PropTypes.any,
        heading: PropTypes.string
    };

    static defaultProps = {
        heading: __('Search Results')
    };

    renderCategoryDetails() {
        const { heading } = this.props;

        return (
            <article block="CategoryDetails">
                <div block="CategoryDetails" elem="Description">
                    <h1 block="CategoryDetails" elem="Heading">{ heading }</h1>
                </div>
            </article>
        );
    }
}
