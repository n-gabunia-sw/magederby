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
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class SaveCartItem
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
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * SaveCartItem constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param ParamOverriderCartId $overriderCartId
     * @param ProductRepository $productRepository
     * @param Repository $attributeRepository
     * @param QuoteIdMask $quoteIdMaskResource
     * @param Configurable $configurableType
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
        CustomerSession $customerSession
    )
    {
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
        $this->customerSession = $customerSession;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed|void
     * @throws Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        $requestCartItem = $args['cartItem'];
        $product = $this->productRepository->get($this->getSku($requestCartItem));
        $attribute = $product->getAttributeText('brand');
        if ($attribute == self::DE_GOUGE) {
            $customer = $this->customerSession->getCustomer();
            if (!$customer->getId()) {
                throw new GraphQlInputException(new Phrase('You should log in to buy this product'));
            }
        }
        parent::resolve($field, $context, $info, $value, $args);
    }
}