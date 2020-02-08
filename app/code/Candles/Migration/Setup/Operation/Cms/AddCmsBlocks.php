<?php

declare(strict_types=1);

namespace Candles\Migration\Setup\Operation\Cms;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\ResourceModel\Block as BlockResource;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Candles\Migration\Helper\FileParser;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AddCmsBlocks
 *
 * @package Candles\Migration\Migration\Setup\Operation\Cms
 */
class AddCmsBlocks
{
    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var BlockResource
     */
    private $blockResource;

    /**
     * @var array
     */
    private $storeMap = [];

    /**
     * AddCmsBlocks constructor.
     *
     * @param FileParser $fileParser
     * @param BlockFactory $blockFactory
     * @param StoreManagerInterface $storeManager
     * @param BlockRepositoryInterface $blockRepository
     * @param BlockResource $blockResource
     */
    public function __construct(
        FileParser $fileParser,
        BlockFactory $blockFactory,
        StoreManagerInterface $storeManager,
        BlockRepositoryInterface $blockRepository,
        BlockResource $blockResource
    ) {
        $this->fileParser = $fileParser;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->blockResource = $blockResource;

        foreach ($storeManager->getStores() as $store) {
            $this->storeMap[$store->getCode()] = $store->getId();
        }
    }

    /**
     * @param array $cmsBlocks
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function addCmsBlocks(array $cmsBlocks): void
    {
        foreach ($cmsBlocks as $blockIdentifier => $blockContents) {
            foreach ($blockContents as $blockContent) {
                if (isset($blockContent['store_codes'])) {
                    foreach ($blockContent['store_codes'] as $storeCode) {
                        if (isset($this->storeMap[$storeCode])
                            && !$this->isBlockExists($blockIdentifier, $this->storeMap[$storeCode])) {
                            $blockContent['stores'][] = $this->storeMap[$storeCode];
                        }
                    }
                }
                if (isset($blockContent['stores'])) {
                    $block = $this->blockFactory->create();
                    $block->setStoreId($blockContent['stores']);
                    $block->setIdentifier($blockIdentifier);
                    $block->setContent($this->fileParser->getHtmlContent($blockContent['content']));
                    $block->setTitle($blockContent['title']);
                    $this->blockRepository->save($block);
                }
            }
        }
    }

    /**
     * @param array $cmsBlocks
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteCmsBlocks(array $cmsBlocks): void
    {
        foreach ($cmsBlocks as $blockIdentifier) {
            $block = $this->blockFactory->create();
            $this->blockResource->load($block, $blockIdentifier, BlockInterface::IDENTIFIER);

            if ($blockId = $block->getId()) {
                $this->blockRepository->deleteById($blockId);
            }
        }
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return bool
     */
    private function isBlockExists($identifier, $storeId): bool
    {
        $block = $this->blockFactory->create();
        $block->setStoreId($storeId);
        $this->blockResource->load($block, $identifier, BlockInterface::IDENTIFIER);

        return (bool)$block->getId();
    }
}
