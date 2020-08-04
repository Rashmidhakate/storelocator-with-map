<?php
namespace Brainvire\StoreLocator\Api;

use \Brainvire\StoreLocator\Api\Data\CategoryInterface;

/**
 * Interface CategoryRepositoryInterface
 * @package Brainvire\StoreLocator\Api
 */
interface CategoryRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \Brainvire\StoreLocator\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Brainvire\StoreLocator\Api\Data\CategoryInterface $model
     *
     * @return \Brainvire\StoreLocator\Api\Data\CategoryInterface
     * @throws \Exception
     */
    public function save(CategoryInterface $model);

    /**
     * @param \Brainvire\StoreLocator\Api\Data\CategoryInterface $model
     *
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(CategoryInterface $model);

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);
}
