<?php
namespace Brainvire\StoreLocator\Model;

use \Brainvire\StoreLocator\Api\CategoryRepositoryInterface;
use \Brainvire\StoreLocator\Model\ResourceModel\Category as ResourceModel;
use \Brainvire\StoreLocator\Api\Data\CategoryInterface;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\StateException;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var \Brainvire\StoreLocator\Model\ResourceModel\Category
     */
    private $resourceModel;

    /**
     * @var \Brainvire\StoreLocator\Model\CategoryFactory
     */
    private $modelFactory;

    /**
     * @var Category[]
     */
    private $instances = [];

    /**
     * CategoryRepository constructor.
     * @param ResourceModel $resourceModel
     * @param CategoryFactory $modelFactory
     */
    public function __construct(
        ResourceModel $resourceModel,
        CategoryFactory $modelFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->modelFactory = $modelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!isset($this->instances[$id])) {
            $model = $this->modelFactory->create();

            $model->load($id);

            if (!$model->getId()) {
                throw NoSuchEntityException::singleField('category_id', $id);
            }

            $this->instances[$id] = $model;
        }

        return $this->instances[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function save(CategoryInterface $model)
    {
        try {
            $existingModel = $this->get($model->getId());
        } catch (NoSuchEntityException $e) {
            $existingModel = null;
        }

        if ($existingModel !== null) {
            foreach ($existingModel->getData() as $key => $value) {
                if (!$model->hasData($key)) {
                    $model->setData($key, $value);
                }
            }
        }

        $this->resourceModel->save($model);
        unset($this->instances[$model->getId()]);

        return $this->get($model->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CategoryInterface $model)
    {
        $name = $model->getName();
        try {
            unset($this->instances[$model->getId()]);
            $this->resourceModel->delete($model);
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove category %1', $name)
            );
        }
        unset($this->instances[$model->getId()]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        $model = $this->get($id);

        return $this->delete($model);
    }
}
