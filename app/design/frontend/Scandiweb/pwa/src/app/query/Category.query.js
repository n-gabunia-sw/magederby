import { CategoryQuery as SourceQuery } from 'SourceQuery/Category.query';

export class CategoryQuery extends SourceQuery {
    _getConditionalArguments() {
        const { categoryUrlPath, categoryIds, isSearchPage } = this.options;

        if (categoryUrlPath) return ['url_path', 'String!', categoryUrlPath];
        if (categoryIds) return ['id', 'Int!', categoryIds];
        if (isSearchPage) return ['id', 'Int!', 2];

        throw new Error(
            isSearchPage ? __('There was an error requesting serach results')
                : __('There was an error requesting the category')
        );
    }
}

export default new CategoryQuery();
