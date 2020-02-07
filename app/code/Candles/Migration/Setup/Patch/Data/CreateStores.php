<?php
/**
 * @category Candles
 * @package  Candles_Migration
 * @author   Tamar Tchelidze <tamar.tchelidze@scandiweb.com>.
 */
declare(strict_types=1);

namespace Candles\Migration\Setup\Patch\Data;

use Magento\Backend\Block\System\Store\Edit\Form\Store;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResourceModel;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\ResourceModel\Store as StoreResourceModel;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Model\ResourceModel\Website as WebsiteResourceModel;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;

class CreateStores implements DataPatchInterface
{
    /**
     * @var array
     */
    private $storesData = [
        [
            'code' => 'UK',
            'name' => 'UK',
            'is_active' => '1'
        ],
        [
            'code' => 'US',
            'name' => 'USA',
            'is_active' => '1'
        ]
    ];

    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;

    /**
     * @var WebsiteResourceModel
     */
    private $websiteResourceModel;

    /**
     * @var GroupFactory
     */
    private $groupFactory;

    /**
     * @var GroupResourceModel
     */
    private $groupResourceModel;

    /**
     * @var StoreFactory
     */
    private $storeFactory;

    /**
     * @var StoreResourceModel
     */
    private $storeResourceModel;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * CreateStores constructor.
     * @param WebsiteFactory $websiteFactory
     * @param WebsiteResourceModel $websiteResourceModel
     * @param GroupFactory $groupFactory
     * @param GroupResourceModel $groupResourceModel
     * @param Store $storeResourceModel
     * @param StoreFactory $storeFactory
     * @param EventManager $eventManager
     */
    public function __construct(
        WebsiteFactory $websiteFactory,
        WebsiteResourceModel $websiteResourceModel,
        GroupFactory $groupFactory,
        GroupResourceModel $groupResourceModel,
        StoreFactory $storeFactory,
        StoreResourceModel $storeResourceModel,
        EventManager $eventManager,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->websiteResourceModel = $websiteResourceModel;
        $this->groupFactory = $groupFactory;
        $this->groupResourceModel = $groupResourceModel;
        $this->storeFactory = $storeFactory;
        $this->storeResourceModel = $storeResourceModel;
        $this->eventManager = $eventManager;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $website = $this->getWebsite();
        $group = $this->getGroup($website);

        foreach ($this->storesData as $storeData) {
            $this->createStore($website, $group, $storeData);
        }
        $this->moduleDataSetup->getConnection()->endSetup();    }

    /**
     * @return Website
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
     * @param $website
     * @return Group
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
     * @param $website
     * @param $group
     * @param $storeInfo
     * @throws LocalizedException
     * @throws AlreadyExistsException
     */
    private function createStore($website, $group, $storeInfo) {
        $store = $this->storeFactory->create();
        $store->load($storeInfo['code']);
        if(!$store->getId()) {
            $store->setCode($storeInfo['code']);
            $store->setName($storeInfo['name']);
            $store->setWebsiteId($website->getId());
            $store->setGroupId($group->getId());
            $store->setIsActive($storeInfo['is_active']);
            $this->storeResourceModel->save($store);
            $this->eventManager->dispatch('store_add', ['store' => $store]);
        }
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