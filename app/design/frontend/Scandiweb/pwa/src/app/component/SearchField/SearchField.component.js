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
                  onKeyDown={ this.handleKeyDown }
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
}

export default withRouter(SearchField);
