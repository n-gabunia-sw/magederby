<?php

declare(strict_types=1);

namespace Candles\Migration\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Class FileParser
 */
class FileParser
{
    /**
     * Path to the data files from root folder
     */
    protected const DEFAULT_PATH_TO_DATA = 'code/Candles/Migration/files';

    /**
     * @var ReadInterface
     */
    protected $rootDirectory;

    /**
     * FileParser constructor.
     *
     * @param Filesystem $fileSystem
     */
    public function __construct(
        Filesystem $fileSystem
    ) {
        $this->rootDirectory = $fileSystem->getDirectoryRead(DirectoryList::APP);
    }

    /**
     * Get content from html file
     *
     * @param string $filePath   Relative path to the html file from data/html folder
     * @param string $pathToData root folder of the file we're searching for
     *
     * @return string
     * @throws FileSystemException
     */
    public function getHtmlContent(string $filePath, string $pathToData = self::DEFAULT_PATH_TO_DATA): string
    {
        return $this->rootDirectory->readFile(
            sprintf(
                '%s/%s',
                $pathToData,
                $filePath
            )
        );
    }
}
