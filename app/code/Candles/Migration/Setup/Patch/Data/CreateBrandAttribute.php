<?php
/**
 * @category Candles
 * @package  Candles_Migration
 * @author   Tamar Tchelidze <tamar.tchelidze@scandiweb.com>.
 */
declare(strict_types=1);

namespace Candles\Migration\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\Product;

class CreateBrandAttribute implements DataPatchInterface
{
    private const PRODUCT_ATTRIBUTES = [
        'brand' => [
            'type' => 'text',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend' => '',
            'label' => 'Brand',
            'input' => 'select',
            'class' => '',
            'option' => ['values' => ['DeGouge', 'DeBougies']],
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'group' => 'General',
            'visible' => true,
            'required' => true,
            'user_defined' => false,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => false,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'wysiwyg_enabled' => true,
            'unique' => false,
            'apply_to' => '',
            'is_visible_on_front' => true
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
     * @var
     */
    private $eavConfig;


    /**
     * CreateBrandAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @throws LocalizedException
     */
    public function apply():void
    {
        $this->moduleDataSetup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach (self::PRODUCT_ATTRIBUTES as $attibuteIdentifier => $attributeContents) {
            if (!$this->isProductAttributeExists($attibuteIdentifier)) {
                $eavSetup->addAttribute(
                    Product::ENTITY,
                    $attibuteIdentifier,
                    $attributeContents
                );
            }
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @param $field
     * @return bool
     * @throws LocalizedException
     */
    public function isProductAttributeExists($field):bool
    {
        $attr = $this->eavConfig->getAttribute(Product::ENTITY, $field);

        return ($attr && $attr->getId()) ? true : false;
    }


    /**
     * @return array|string[]
     */
    public static function getDependencies():array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases():array
    {
        return [];
    }
}