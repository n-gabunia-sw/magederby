<?php
/**
 * @category Candles
 * @package Candles/CatalogGraphQl
 */

namespace Candles\CatalogGraphQl\Plugin\Model\ResourceModel\Product;

use Closure;
use Magento\Catalog\Model\ResourceModel\Product\Collection as CoreCollection;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Collection
 * @package Candles\CatalogGraphQl\Model\Plugin\ResourceModel\Product
 */
class Collection
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var array
     */
    protected $multiselectAttributeCodes;


    /**
     * Collection constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param CoreCollection $coreCollection
     * @param Closure $proceed
     * @param string $attribute
     * @param string $dir
     * @return CoreCollection|mixed
     */
    public function aroundAddAttributeToSort(
        CoreCollection $coreCollection,
        Closure $proceed,
        $attribute,
        $dir = CoreCollection::SORT_ORDER_ASC
    ) {
        $storeId = $coreCollection->getStoreId();

        if ((int)$storeId === 0) {
            return $proceed($attribute, $dir);
        }

        if ($attribute === 'created_at') {
            $coreCollection->addAttributeToSelect('news_from_date', 'left');
            $newsFrom = 'IF(at_news_from_date.value_id > 0, at_news_from_date.value, at_news_from_date_default.value)';

            $createdAtSort = $coreCollection->getConnection()->getCheckSql(
                $newsFrom . ' IS NOT NULL',
                $newsFrom,
                'created_at'
            );

            $coreCollection->getSelect()->columns(
                ['created_at_sort' => $createdAtSort]
            );

            $coreCollection->getSelect()->order('created_at_sort ' . $dir);
            return $coreCollection;
        }

        return $proceed($attribute, $dir);
    }

    /**
     * @param CoreCollection $subject
     * @param $attribute
     * @param null $condition
     * @param string $joinType
     * @return array
     */
    public function beforeAddAttributeToFilter(
        CoreCollection $subject,
        $attribute,
        $condition = null,
        $joinType = 'inner'
    ) {
        if (is_array($attribute)) {
            $newAttribute = [];
            foreach ($attribute as $key => $item) {
                if (array_key_exists('in', $item)
                    && array_key_exists($item['attribute'], $this->getSelectAttributes())) {
                    foreach ($item['in'] as $value) {
                        $newAttribute[] = [
                            'attribute' => $item['attribute'],
                            'finset' => $value
                        ];
                    }
                } else {
                    $newAttribute[] = $item;
                }
            }
            $attribute = $newAttribute;
        }

        return [
            $attribute,
            $condition,
            $joinType
        ];
    }

    /**
     * @return array
     */
    protected function getSelectAttributes()
    {
        if ($this->multiselectAttributeCodes === null) {
            $connection = $this->resourceConnection->getConnection();
            $eavTable = $connection->getTableName('eav_attribute');
            $select = $connection->select()
                ->from($eavTable, ['attribute_code'])
                ->where('frontend_input = ?', 'select')
                ->where('entity_type_id = ?', CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);

            $values = $connection->fetchCol($select);
            $this->multiselectAttributeCodes = array_combine($values, $values);
        }

        return $this->multiselectAttributeCodes;
    }
}
