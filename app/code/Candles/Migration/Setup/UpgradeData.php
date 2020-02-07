<?php
/**
 * @category EDU267
 * @package Edu_Task4
 * @author Tornike Bakuradze <tornike.bakuradze@scandiweb.com>
 * @copyright Copyright (c) 2019 Scandiweb, Ltd (http://scandiweb.com).
 */

namespace Candles\Migration\Setup;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Model\ResourceModel\Website;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\ResourceModel\Store;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use Magento\Theme\Model\Theme;

/**
 * Class InstallData
 * @package Scandiweb\StoreMigration\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var WebsiteFactory $websiteFactory
     */
    private $websiteFactory;

    /**
     * @var Website $websiteResourceModel
     */
    private $websiteResourceModel;

    /**
     * @var GroupFactory $groupFactory
     */
    private $groupFactory;

    /**
     * @var Group $groupResourceModel
     */
    private $groupResourceModel;

    /**
     * @var StoreFactory $storeFactory
     */
    private $storeFactory;

    /**
     * @var Store
     */
    private $storeResourceModel;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Data used to create store views.
     * @var array $storesData
     */
    private $storesData = [
        [
            'code' => 'default',
            'name' => 'UK',
            'is_active' => '1'
        ],
        [
            'code' => 'us',
            'name' => 'US',
            'is_active' => '1'
        ]
    ];

    /**
     * @var WriterInterface $coreConfigWriter
     */
    private $coreConfigWriter;

    /**
     * @var StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * @var CollectionFactory $themeCollectionFactory
     */
    private $themeCollectionFactory;

    /**
     * InstallData constructor.
     * @param WebsiteFactory $websiteFactory
     * @param Website $websiteResourceModel
     * @param GroupFactory $groupFactory
     * @param Group $groupResourceModel
     * @param Store $storeResourceModel
     * @param StoreFactory $storeFactory
     * @param ManagerInterface $eventManager
     * @param WriterInterface $coreConfigWriter
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $themeCollectionFactory
     */
    public function __construct(
        WebsiteFactory $websiteFactory,
        Website $websiteResourceModel,
        GroupFactory $groupFactory,
        Group $groupResourceModel,
        Store $storeResourceModel,
        StoreFactory $storeFactory,
        ManagerInterface $eventManager,
        WriterInterface $coreConfigWriter,
        StoreManagerInterface $storeManager,
        CollectionFactory $themeCollectionFactory
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->websiteResourceModel = $websiteResourceModel;
        $this->groupFactory = $groupFactory;
        $this->groupResourceModel = $groupResourceModel;
        $this->storeFactory = $storeFactory;
        $this->storeResourceModel = $storeResourceModel;
        $this->eventManager = $eventManager;
        $this->coreConfigWriter = $coreConfigWriter;
        $this->storeManager = $storeManager;
        $this->themeCollectionFactory = $themeCollectionFactory;
    }

    /**
     * Creates store views based on data set in $this->storesData.
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        switch ($context->getVersion()) {
        case '0.0.1':
            $website = $this->getWebsite();
            $group = $this->getGroup($website);

            foreach ($this->storesData as $storeData) {
                $this->createStore($website, $group, $storeData);
            }

            $this->setDefaultProperties();
            $this->setStoreProperties();
            $this->removeHtmlSuffix();
        case '0.0.2':
            $this->addDefaultCountriesAndCurrenciesToStores();
        }

        $setup->endSetup();
    }

    private function addDefaultCountriesAndCurrenciesToStores()
    {
        $this->coreConfigWriter->save('currency/options/base', 'USD', 'default', 0);
        $this->coreConfigWriter->save('currency/options/default', 'USD', 'default', 0);
        $this->coreConfigWriter->save('currency/options/allow', 'USD,GBP', 'default', 0);
    }

    /**
     * Gets existing website with id 'base' or creates new one.
     * @return \Magento\Store\Model\Website
     * @throws AlreadyExistsException
     */
    private function getWebsite() {
        $website = $this->websiteFactory->create();
        $website->load('base');
        if(!$website->getId()) {
            $website->setCode('base');
            $website->setName('Main Website');
            $this->websiteResourceModel->save($website);
        }

        return $website;
    }

    /**
     * Gets group with modelId 'main_website_store', if not found creates new one.
     * @param \Magento\Store\Model\Website $website
     * @return \Magento\Store\Model\Group
     * @throws AlreadyExistsException
     */
    private function getGroup($website) {
        $group = $this->groupFactory->create();
        $this->groupResourceModel->load($group, 'main_website_store', 'code');
        if(!$group->getCode()) {
            $group->setWebsiteId($website->getWebsiteId());
            $group->setName('Main Website Store');
            $this->groupResourceModel->save($group);
        }

        return $group;
    }

    /**
     * Creates new store
     * @param \Magento\Store\Model\Website $website
     * @param \Magento\Store\Model\Group $group
     * @param $storeInfo
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    private function createStore($website, $group, $storeInfo) {
        $store = $this->storeFactory->create();
        $store->load($storeInfo['code']);
        $store->setCode($storeInfo['code']);
        $store->setName($storeInfo['name']);
        $store->setWebsite($website);
        $store->setGroupId($group->getId());
        $store->setData('is_active', $storeInfo['is_active']);
        $this->storeResourceModel->save($store);
        if(!$store->getId()) {
            $this->eventManager->dispatch('store_add', ['store' => $store]);
        } else {
            $this->eventManager->dispatch('store_update', ['store' => $store]);
        }
    }

    /**
     * Sets default currencies
     */
    private function setDefaultProperties() {
        $ukStore = $this->storeFactory->create();
        $this->storeResourceModel->load($ukStore, 'default', 'code');

        $usStore = $this->storeFactory->create();
        $this->storeResourceModel->load($usStore, 'us', 'code');

        $this->coreConfigWriter->save('general/country/default', 'GB', 'stores', $ukStore->getId());
        $this->coreConfigWriter->save('currency/options/default', 'GBP', 'stores', $ukStore->getId());

        $this->coreConfigWriter->save('general/country/default', 'US', 'stores', $usStore->getId());
        $this->coreConfigWriter->save('currency/options/default', 'USD', 'stores', $ukStore->getId());
    }

    /**
     * Set store properties
     */
    private function setStoreProperties() {
        $stores = $this->storeManager->getStores(true, false);
        $scope = ScopeInterface::SCOPE_STORES;
        foreach ($stores as $store) {
            $id = $store->getId();

            switch ($store->getCode()) {
                case 'german':
                    $this->coreConfigWriter->save('currency/options/default', 'GBP', $scope, $id);
                    $this->coreConfigWriter->save('currency/options/allow', 'GBP', $scope, $id);
                    $this->coreConfigWriter->save('general/country/default', 'UK', $scope, $id);
                    $this->coreConfigWriter->save('general/country/destination', 'UK', $scope, $id);
                    $this->coreConfigWriter->save('general/locale/code', 'en_GB', $scope, $id);
                    $this->coreConfigWriter->save('design/theme/theme_id', $this->loadTheme('Scandipwa/pwa'), $scope, $id);
                    break;
                case 'english':
                    $this->coreConfigWriter->save('currency/options/default', 'USD', $scope, $id);
                    $this->coreConfigWriter->save('currency/options/allow', 'USD', $scope, $id);
                    $this->coreConfigWriter->save('general/country/default', 'US', $scope, $id);
                    $this->coreConfigWriter->save('general/country/destination', 'US', $scope, $id);
                    $this->coreConfigWriter->save('general/locale/code', 'en_US', $scope, $id);
                    $this->coreConfigWriter->save('design/theme/theme_id', $this->loadTheme('Scandipwa/pwa'), $scope, $id);
                    break;
            }
        }
    }

    /**
     * Get theme by its code
     * @param string $code
     * @return string|null
     */
    private function loadTheme($code) {
        $themes = $this->themeCollectionFactory->create()->loadRegisteredThemes();
        foreach ($themes as /** @var Theme $theme */ $theme) {
            if($theme->getCode() == $code) {
                return $theme->getId();
            }
        }

        return null;
    }

    /**
     * Removes .html suffix from urls
     */
    private function removeHtmlSuffix() {
        $paths = [
            'catalog/seo/product_url_suffix',
            'catalog/seo/category_url_suffix'
        ];
        foreach ($paths as $path) {
            $this->coreConfigWriter->save($path, null, 'default', '0');
        }
    }
}
