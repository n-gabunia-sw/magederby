<?php
/**
 * @category Candles
 * @package Candles\Migration
 */

namespace Candles\Migration\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleReader;

/**
 * Class MediaMigration
 * @package Candles\Migration\Helper
 */
class MediaMigration
{
    /**
     * @var ModuleReader
     */
    protected $moduleReader;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @param ModuleReader $moduleReader
     * @param Filesystem $fileSystem
     */
    public function __construct(
        ModuleReader $moduleReader,
        Filesystem $fileSystem
    ) {
        $this->fileSystem = $fileSystem;
        $this->moduleReader = $moduleReader;
    }

    /**
     * Copies an array of files from a source to a destination media directory.
     *
     * @param $files
     * @param $sourceModule
     * @param null $folderPath
     * @throws FileSystemException
     */
    public function copyMediaFiles($files, $sourceModule, $folderPath = null)
    {
        $sourcePath = $this->getSourceMediaDirectory($sourceModule);
        $destinationPath = $this->getDestinationMediaDirectory($folderPath);

        $rootDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::ROOT);

        $relativeSourcePath = str_replace($rootDirectory->getAbsolutePath(), '', $sourcePath);
        $relativeDestinationPath = str_replace($rootDirectory->getAbsolutePath(), '', $destinationPath);

        foreach ($files as $file) {
            if ($rootDirectory->isFile($relativeSourcePath . $file)) {
                $rootDirectory->copyFile($relativeSourcePath . $file, $relativeDestinationPath . $file);
            }
        }
    }

    /**
     * Delete media files
     *
     * @param $files
     * @param null $folderPath
     * @throws FileSystemException
     */
    public function deleteMediaFiles($files, $folderPath = null)
    {
        $sourcePath = $this->getDestinationMediaDirectory($folderPath);
        $rootDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::ROOT);
        $relativeSourcePath = str_replace($rootDirectory->getAbsolutePath(), '', $sourcePath);

        foreach ($files as $file) {
            if ($rootDirectory->isFile($relativeSourcePath . $file)) {
                $rootDirectory->delete($relativeSourcePath . $file);
            }
        }
    }

    /**
     * Gets the directory from which media files are copied.
     *
     * @param $sourceModule
     * @return string
     */
    protected function getSourceMediaDirectory($sourceModule)
    {
        return $this->moduleReader->getModuleDir(Dir::MODULE_VIEW_DIR, $sourceModule)
            . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
    }

    /**
     * Gets the directory in which media files are copied to.
     *
     * @param string $folderPath
     * @return string
     * @throws FileSystemException
     */
    protected function getDestinationMediaDirectory($folderPath = 'wysiwyg')
    {
        if (!$folderPath) {
            return $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        }

        return $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)
                ->getAbsolutePath() . $folderPath . DIRECTORY_SEPARATOR;
    }
}
