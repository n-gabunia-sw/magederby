<?php

declare(strict_types=1);

namespace Candles\QuoteGraphql\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Phrase;
use ScandiPWA\QuoteGraphQl\Model\Resolver\SaveCartItem as SourceSaveCartItem;
use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask;
use Magento\Quote\Model\Webapi\ParamOverriderCartId;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\CatalogInventory\Api\StockStatusRepositoryInterface;
use Magento\Sales\Model\Order;
use \Magento\Sales\Model\Order\ItemFactory;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use \Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class SaveCartItem
 *
 * @package Candles\QuoteGraphql\Model\Resolver
 */
class SaveCartItem extends  SourceSaveCartItem
{
    private const DE_GOUGE = 'DeGouge';

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Order
     */
    private $orderModel;

    /**
     * @var ItemFactory
     */
    private $orderItem;

    /**
     * @var CollectionFactory
     */
    private $orderCollection;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * SaveCartItem constructor.
     *
     * @param QuoteIdMaskFactory             $quoteIdMaskFactory
     * @param CartRepositoryInterface        $quoteRepository
     * @param ParamOverriderCartId           $overriderCartId
     * @param ProductRepository              $productRepository
     * @param Repository                     $attributeRepository
     * @param QuoteIdMask                    $quoteIdMaskResource
     * @param Configurable                   $configurableType
     * @param StockStatusRepositoryInterface $stockStatusRepository
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $quoteRepository,
        ParamOverriderCartId $overriderCartId,
        ProductRepository $productRepository,
        Repository $attributeRepository,
        QuoteIdMask $quoteIdMaskResource,
        Configurable $configurableType,
        StockStatusRepositoryInterface $stockStatusRepository,
        Order $orderModel,
        ItemFactory $orderItem,
        CollectionFactory $orderCollection,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct(
            $quoteIdMaskFactory,
            $quoteRepository,
            $overriderCartId,
            $productRepository,
            $attributeRepository,
            $quoteIdMaskResource,
            $configurableType,
            $stockStatusRepository
        );

        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteRepository = $quoteRepository;
        $this->orderModel = $orderModel;
        $this->orderItem = $orderItem;
        $this->orderCollection = $orderCollection;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param  Field            $field
     * @param  ContextInterface $context
     * @param  ResolveInfo      $info
     * @param  array|null       $value
     * @param  array|null       $args
     * @return Value|mixed|void
     * @throws Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $requestCartItem = $args['cartItem'];
        $quoteId = isset($args['guestCartId'])
            ? $this->getGuestQuoteId($args['guestCartId'])
            : $this->overriderCartId->getOverriddenValue();
        $quote = $this->quoteRepository->getActive($quoteId);
        ['qty' => $qty] = $requestCartItem;

        $product = $this->productRepository->get($this->getSku($requestCartItem));;
        $attribute = $product->getAttributeText('brand');
        if ($attribute == self::DE_GOUGE) {
            $customerId = 9; // TODO: Get customerId from args
            $customer = $this->customerRepository->getById($customerId);
            $attr = $customer->getCustomAttributes();
            print_r($attr);
            if (!$customerId) {
                throw new GraphQlInputException(
                    new Phrase(
                        'You should log in to buy this product'
                    )
                );
            }
            if (true) { // TODO: If customer is not vip
                $ordercollection = $this->orderCollection->create()->addFieldToFilter('customer_id', $customerId);
                $productQtyOrderedByUser = 0;
                foreach ($ordercollection->getAllIds() as $id) {
                    $orderModel = $this->orderModel->load($id);
                    $orders = $orderModel->getAllItems();
                    foreach ($orders as $order) {
                        if ($order->getProductId() ==  $product->getId()
                            && !$this->itemDateIsValid($product->getCreatedAt())
                        ) {
                            $productQtyOrderedByUser += (int)$order->getQtyOrdered();
                        }
                    }
                }

                if (((int)$quote->getItemsQty() + $qty + $productQtyOrderedByUser) > 5) {
                    throw new GraphQlInputException(
                        new Phrase(
                            'You can buy only 5 pieces of this item per year. 
                            You have already bought '
                            . $productQtyOrderedByUser
                        )
                    );
                }
            }
        }
        return parent::resolve($field, $context, $info, $value, $args);
    }

    /**
     * @param $itemDate
     * @return bool
     */
    private function itemDateIsValid($itemDate):bool
    {
        $currentDate = date('Y');
        $itemDateYear = date('Y', strtotime($itemDate));
        return $itemDateYear != $currentDate;
    }
}