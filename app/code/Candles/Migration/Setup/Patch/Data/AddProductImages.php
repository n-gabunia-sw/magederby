<?php
/**
 * @category Candles
 * @package Candles\Migration
 */
declare(strict_types=1);

namespace Candles\Migration\Setup\Patch\Data;

use Candles\Migration\Helper\MediaMigration;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddProductImages
 * @package Candles\Migration\Setup\Patch\Data
 */
class AddProductImages implements DataPatchInterface
{
    /**
     * @var MediaMigration;
     */
    private $mediaMigration;

    /**
     * AddProductImages constructor.
     * @param MediaMigration $mediaMigration
     */
    public function __construct(MediaMigration $mediaMigration)
    {
        $this->mediaMigration = $mediaMigration;
    }

    /**
     * @return DataPatchInterface|void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->mediaMigration->copyMediaFiles(
            [
                'amaranth.png',
                'baby-blue.png',
                'desert-sand.png',
                'orange.png',
                'orchid.png',
                'pear.png',
                'red.png',
                'salmon.png',
                'viridian.png',
                'white.png'
            ],
            'Candles_Migration',
            '/product-images'
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
