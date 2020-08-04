<?php
namespace Brainvire\StoreLocator\Model\Category;

use \Magento\Framework\UrlInterface;
use \Magento\Framework\Filesystem;
use \Magento\Framework\App\Filesystem\DirectoryList;

class Icon
{
    /**
     * media sub folder
     * @var string
     */
    private $subDir = 'Brainvire/storelocator/category';

    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $fileSystem;

    /**
     * @param UrlInterface $urlBuilder
     * @param Filesystem $fileSystem
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Filesystem $fileSystem
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $this->subDir . '/icon';
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($this->subDir . '/icon');
    }
}
