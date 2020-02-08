<?php

declare(strict_types=1);

namespace Candles\Migration\Setup\Patch\Data;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResourceModel;
use Magento\Framework\Setup\Patch\PatchInterface;


class AddCustomerVipAttributeDefaultValues implements DataPatchInterface
{

    /**
     * @var Customer
     */
    private $customerModel;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CollectionFactory
     */
    private $customerCollection;

    /**
     * AddCustomerVipAttributeDefaultValues constructor.
     * @param Customer $customerModel
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Customer $customerModel,
        CustomerFactory $customerFactory,
        CollectionFactory $customerCollection
    ) {
        $this->customerModel = $customerModel;
        $this->customerFactory = $customerFactory;
        $this->customerCollection = $customerCollection;
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Exception
     */
    public function apply():void
    {
        $customerObj = $this->customerCollection->create()->load();
        foreach ($customerObj as $customerObjdata) {
            $customerData = $this->customerModel->getDataModel();
            $customerData->setId($customerObjdata->getData('entity_id'));
            $customerData->setCustomAttribute('is_vip', 0);
            $this->customerModel->updateData($customerData);

            $customerFactory = $this->customerFactory->create();
            $customerFactory->saveAttribute($this->customerModel, 'is_vip');
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies():array
    {
        return [
            AddCustomerVipAttribute::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAliases():array
    {
        return [];
    }
}