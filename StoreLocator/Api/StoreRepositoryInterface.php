<?php
namespace Brainvire\StoreLocator\Api;

use \Brainvire\StoreLocator\Api\Data\StoreInterface;

/**
 * Interface StoreRepositoryInterface
 * @package Brainvire\StoreLocator\Api
 */
interface StoreRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \Brainvire\StoreLocator\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Brainvire\StoreLocator\Api\Data\StoreInterface $model
     *
     * @return \Brainvire\StoreLocator\Api\Data\StoreInterface
     * @throws \Exception
     */
    public function save(StoreInterface $model);

    /**
     * @param \Brainvire\StoreLocator\Api\Data\StoreInterface $model
     *
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(StoreInterface $model);

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);
}
