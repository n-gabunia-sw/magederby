<?php

namespace Candles\Migration\Setup\Patch\Data;

use Magento\Catalog\Plugin\Model\Attribute\Backend\AttributeValidation;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResourceModel;
use Zend_Validate_Exception;

class AddCustomerVipAttribute implements DataPatchInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var AttributeResourceModel
     */
    private $attributeResourceModel;

    /**
     * AddCustomerVipAttribute constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param EavConfig $eavConfig
     * @param AttributeResourceModel $attributeResourceModel
     */
    public function __construct(EavSetupFactory $eavSetupFactory, EavConfig $eavConfig, AttributeResourceModel $attributeResourceModel)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResourceModel = $attributeResourceModel;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply(): void
    {
        /**
         * @var EavSetup
         */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            Customer::ENTITY,
            'is_vip',
            [
                'type' => Table::TYPE_BOOLEAN,
                'label' => 'Customer is VIP',
                'input' => 'boolean',
                'required' => true,
                'visible' => true,
                'user_defined' => false,
                'position' => 10,
                'system' => 0
            ]
        );

        $isVipAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'is_vip');
        $isVipAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $this->attributeResourceModel->save($isVipAttribute);
    }
}
