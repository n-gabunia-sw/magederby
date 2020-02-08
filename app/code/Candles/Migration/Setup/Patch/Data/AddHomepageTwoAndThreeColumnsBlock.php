<?php

declare(strict_types=1);

namespace Candles\Migration\Setup\Patch\Data;

use Candles\Migration\Helper\MediaMigration;
use Candles\Migration\Setup\Operation\Cms\AddCmsBlocks;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CreateFooterBlocks
 *
 * @package Candles\Migration\Setup\Patch\Data
 */
class AddHomepageTwoAndThreeColumnsBlock implements DataPatchInterface
{
    /**
     * CMS blocks content mapping
     */
    private const CMS_BLOCKS = [
        'homepage-two-columns-block' => [
            [
                'title' => 'Homepage Two Columns Block',
                'stores' => ['default'],
                'content' => 'cms/block/homepage-two-columns-block.html'
            ]
        ],
        'homepage-two-columns-block-us' => [
            [
                'title' => 'Homepage Two Columns Block',
                'stores' => ['us'],
                'content' => 'cms/block/homepage-two-columns-block.html'
            ]
        ],
        'homepage-three-column-block' => [
            [
                'title' => 'Homepage thee columns block',
                'stores' => ['default'],
                'content' => 'cms/block/homepage-three-columns-block.html'
            ]
        ],
        'homepage-three-column-block-us' => [
            [
                'title' => 'Homepage thee columns block',
                'stores' => ['us'],
                'content' => 'cms/block/homepage-three-columns-block.html'
            ]
        ]
    ];

    private const TWO_COLUMN_BLOCK_IMAGES = [
        'white.png',
        'orange.png'
    ];

    private const THREE_COLUMN_BLOCK_IMAGES = [
        'amaranth.png',
        'baby-blue.png',
        'desert-sand.png',
        'orange.png',
        'orchid.png',
        'pear.png',
        'red.png',
    ];

    /**
     * @var AddCmsBlocks
     */
    private $cmsBlocksCreator;

    /**
     * @var MediaMigration;
     */
    private $mediaMigration;

    /**
     * AddAboutUsCmsBlock constructor.
     *
     * @param AddCmsBlocks $cmsBlocks
     * @param MediaMigration $mediaMigration
     */
    public function __construct(AddCmsBlocks $cmsBlocks, MediaMigration $mediaMigration)
    {
        $this->cmsBlocksCreator = $cmsBlocks;
        $this->mediaMigration = $mediaMigration;
    }

    /**
     * @return DataPatchInterface|void
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->cmsBlocksCreator->addCmsBlocks(self::CMS_BLOCKS);
        $this->mediaMigration->copyMediaFiles(self::TWO_COLUMN_BLOCK_IMAGES, 'Candles_Migration', 'wysiwyg/cms/homepage-2-column');
        $this->mediaMigration->copyMediaFiles(self::THREE_COLUMN_BLOCK_IMAGES, 'Candles_Migration', 'wysiwyg/cms/homepage-3-column');
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
