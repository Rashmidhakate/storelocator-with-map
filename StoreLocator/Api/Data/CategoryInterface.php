<?php
namespace Brainvire\StoreLocator\Api\Data;

interface CategoryInterface
{
    const NAME = 'name';
    const CREATION_TIME = 'created_at';
    const UPDATE_TIME = 'updated_ad';
    const IS_ACTIVE = 'is_active';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Brainvire\StoreLocator\Api\Data\CategoryInterface
     */
    public function setId($id);

    /**
     * Set Name
     *
     * @param string $name
     * @return \Brainvire\StoreLocator\Api\Data\CategoryInterface
     */
    public function setName($name);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Brainvire\StoreLocator\Api\Data\CategoryInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \Brainvire\StoreLocator\Api\Data\CategoryInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Brainvire\StoreLocator\Api\Data\CategoryInterface
     */
    public function setIsActive($isActive);
}
