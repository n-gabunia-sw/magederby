import { connect } from 'react-redux';
import { BreadcrumbsDispatcher } from 'Store/Breadcrumbs';
import {
    CategoryPageContainer,
    mapStateToProps,
    mapDispatchToProps as sourceMapDispatchToProps
} from 'Route/CategoryPage/CategoryPage.container';
import SearchPage from './SearchPage.component';

export { mapStateToProps } from 'Route/CategoryPage/CategoryPage.container';

export const mapDispatchToProps = (dispatch) => ({
    ...sourceMapDispatchToProps(dispatch),
    updateBreadcrumbs: breadcrumbs => BreadcrumbsDispatcher.update(breadcrumbs, dispatch)
});

export class SearchPageContainer extends CategoryPageContainer {
    componentDidMount() {
        this._requestSearchResultWithPageList();
        this._updateBreadcrumbs();
    }

    componentDidUpdate(prevProps) {
        const {
            match: {
                params: {
                    query: prevSearch
                }
            }
        } = prevProps;
        const currentSearch = this._getSearchParam();

        if (prevSearch !== currentSearch || this._urlHasChanged(location, prevProps)) {
            this._requestSearchResultWithPageList();
            this._onCategoryUpdate();
        }
    }

    _getProductListOptions = (currentPage) => {
        const search = this._getSearchParam();

        return {
            args: {
                filter: {
                    search
                }
            },
            currentPage
        };
    };

    _getSearchParam = () => {
        const {
            match: {
                params: {
                    query
                }
            }
        } = this.props;

        return query;
    };

    _updateBreadcrumbs = () => {
        const {
            updateBreadcrumbs,
            location: {
                pathname: url
            }
        } = this.props;
        const search = this._getSearchParam();

        updateBreadcrumbs([{ name: search, url }]);
    };

    _requestCategory = () => {
        const { categoryIds, requestCategory } = this.props;
        const categoryUrlPath = !categoryIds ? this._getCategoryUrlPath() : null;

        requestCategory({
            categoryUrlPath,
            isSearchPage: true,
            categoryIds,
            searchTerm: this._getSearchParam()
        });
    };

    _requestSearchProductInfo = () => {
        const { requestProductListInfo } = this.props;
        requestProductListInfo(this._getProductListOptions(1, false, true));
    };

    _requestSearchResultWithPageList = () => {
        this._requestCategory();
        this._requestSearchProductInfo();
    };

    _urlHasChanged(location, prevProps) {
        const { pathname, search } = location;
        const { location: { pathname: prevPathname, search: prevSearch } } = prevProps;
        const pathnameHasChanged = decodeURI(pathname) !== prevPathname;
        const searchQueryHasChanged = !this._compareQueriesWithoutPage(search, prevSearch);

        return pathnameHasChanged || searchQueryHasChanged;
    }

    _getSearchHeading = () => {
        return __('Search results for: %s', this._getSearchParam());
    };

    containerProps = () => ({
        filter: this._getFilter(),
        search: this._getSearchParam(),
        selectedSort: this._getSelectedSortFromUrl(),
        selectedFilters: this._getSelectedFiltersFromUrl(),
        selectedPriceRange: this._getPriceRangeForSlider(),
        heading: this._getSearchHeading()
    });

    render() {
        const { pageSize } = this.config;

        return (
            <SearchPage
              { ...this.props }
              pageSize={ pageSize }
              { ...this.containerFunctions }
              { ...this.containerProps() }
            />
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(SearchPageContainer);
