<<<<<<< HEAD
import SourceComponent from 'SourceComponent/SearchField/SearchField.component';
import { withRouter } from 'react-router-dom';
import { HistoryType } from 'Type/Common';

export class SearchField extends SourceComponent {
    static propTypes = {
        ...SourceComponent.propTypes,
        history: HistoryType.isRequired
    }

    handleKeyDown = (event) => {
        const {
            searchCriteria,
            onSearchOutsideClick,
            onClearSearchButtonClick,
            history
        } = this.props;
        const { keyCode } = event;

        if (keyCode === 13) {
            this.searchBarRef.current.blur();
            onSearchOutsideClick();
            onClearSearchButtonClick();
            history.push(`/search/${searchCriteria}`);
        }
    };

=======
import { PureComponent, createRef } from 'react';
import PropTypes from 'prop-types';

import ClickOutside from 'Component/ClickOutside';
import SearchOverlay from 'Component/SearchOverlay';

import './SearchField.style';

class SearchField extends PureComponent {
    static propTypes = {
        searchCriteria: PropTypes.string,
        onSearchBarClick: PropTypes.func.isRequired,
        onSearchBarChange: PropTypes.func.isRequired,
        onSearchOutsideClick: PropTypes.func.isRequired,
        onClearSearchButtonClick: PropTypes.func.isRequired,
        isVisible: PropTypes.bool,
        isActive: PropTypes.bool
    };

    static defaultProps = {
        isVisible: true,
        isActive: true,
        searchCriteria: ''
    };

    searchBarRef = createRef();

    state = {
        isPlaceholderVisible: true
    };

    static getDerivedStateFromProps(props) {
        const { isActive } = props;
        if (isActive) return null;
        return { isPlaceholderVisible: true };
    }

    onClearSearchButtonClick(isFocusOnSearchBar = true) {
        const { onClearSearchButtonClick } = this.props;
        if (isFocusOnSearchBar) this.searchBarRef.current.focus();
        onClearSearchButtonClick();
    }

    handleChange = (e) => {
        const { target: { value } } = e;
        const { onSearchBarChange } = this.props;
        onSearchBarChange(e);

        this.setState({ isPlaceholderVisible: value === '' });
    };

    clearSearch = () => {
        this.onClearSearchButtonClick(false);
    };

    renderClearSearch() {
        const { isVisible } = this.props;

        return (
            <button
              block="Header"
              elem="Button"
              onClick={ this.onClearSearchButtonClick }
              mods={ {
                  type: 'searchClear',
                  isVisible
              } }
              aria-label="Clear search"
            />
        );
    }

>>>>>>> Some of the styling added
    renderContent() {
        const {
            searchCriteria,
            onSearchBarClick,
            isActive
        } = this.props;

        const { isPlaceholderVisible } = this.state;

        return (
            <>
                <input
                  id="search-field"
                  ref={ this.searchBarRef }
                  block="SearchField"
                  elem="Input"
                  onClick={ onSearchBarClick }
                  onChange={ this.handleChange }
                  value={ searchCriteria }
                  mods={ { isActive } }
<<<<<<< HEAD
                  onKeyDown={ this.handleKeyDown }
=======
>>>>>>> Some of the styling added
                  autoComplete="off"
                />
                <div
                  block="SearchField"
                  elem="Placeholder"
                  mods={ {
                      isActive,
                      isPlaceholderVisible
                  } }
                >
                    <span>{ __('Search') }</span>
                </div>
            </>
        );
    }
<<<<<<< HEAD
}

export default withRouter(SearchField);
=======

    render() {
        const {
            onSearchOutsideClick,
            searchCriteria,
            isVisible,
            isActive
        } = this.props;

        return (
            <div block="SearchField" mods={ { isVisible, isActive } }>
                <ClickOutside onClick={ onSearchOutsideClick }>
                    <div block="SearchField" elem="Wrapper">
                        { this.renderContent() }
                        <SearchOverlay clearSearch={ this.clearSearch } searchCriteria={ searchCriteria } />
                    </div>
                </ClickOutside>
            </div>
        );
    }
}

export default SearchField;
>>>>>>> Some of the styling added
