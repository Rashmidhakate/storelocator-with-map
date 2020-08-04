<?php
namespace Brainvire\StockSourceImport\Ui\Component\Listing\StockSourceImport;
 
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
 
 
class Actions extends Column
{
    
    const URL_PATH_DELETE = 'sourceimportlog/log/delete';
 
     
    protected $urlBuilder;
 
     
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
 
 
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Event "${ $.$data.id }"'),
                                'message' => __('Are you sure you wan\'t to delete a event "${ $.$data.id }" record?')
                            ]
                        ]
                    ];
                }
            }
        }
 
        return $dataSource;
    }
}