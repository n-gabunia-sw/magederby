<?php

namespace Candles\Migration\Setup\Patch\Data;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResourceModel;

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
                'type' => 'int',
                'label' => 'Customer is VIP',
                'input' => 'boolean',
                'required' => false,
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
