<?php

namespace Candles\Migration\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\Product;

/**
 * Class CreateAttributes
 * @package Candles\Migration\Setup\Patch
 */
class CreateAttributes implements DataPatchInterface
{
    private const PRODUCT_ATTRIBUTES = [
        'scent' => [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Scent',
            'input' => 'text',
            'class' => '',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'group' => 'General',
            'visible' => true,
            'required' => true,
            'user_defined' => false,
            'default' => null,
            'searchable' => true,
            'filterable' => true,
            'comparable' => false,
            'is_visible_on_front' => true,
            'used_in_product_listing' => false,
            'unique' => false
        ],
        'size' => [
            'type'                    => 'text',
            'backend'                 => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend'                => '',
            'label'                   => 'Size',
            'input'                   => 'select',
            'class'                   => '',
            'option'                  => ['values' => ['Small', 'Medium', 'Large']],
            'global'                  => ScopedAttributeInterface::SCOPE_GLOBAL,
            'group'                   => 'General',
            'visible'                 => true,
            'required'                => true,
            'user_defined'            => true,
            'default'                 => null,
            'searchable'              => true,
            'filterable'              => true,
            'visible_on_front'        => true,
            'used_in_product_listing' => true,
            'unique'                  => false,
            'apply_to'                => ''
        ]
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * CreateAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Create specified attributes
     * @return DataPatchInterface|void
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        foreach (self::PRODUCT_ATTRIBUTES as $attributeIdentifier => $attributeContents) {
            if (!$this->isProductAttributeExists($attributeIdentifier)) {
                $eavSetup->addAttribute(
                    Product::ENTITY,
                    $attributeIdentifier,
                    $attributeContents
                );
            }
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Check if attribute already exists
     * @param $field
     * @return bool
     * @throws LocalizedException
     */
    public function isProductAttributeExists($field)
    {
        $attr = $this->eavConfig->getAttribute(Product::ENTITY, $field);
        return ($attr && $attr->getId()) ? true : false;
    }
}
