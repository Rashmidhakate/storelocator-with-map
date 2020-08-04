<?php
namespace Brainvire\StockSourceImport\Ui\Component\Listing\StockSourceImport;
 
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
 
 
class Download extends Column
{
    
    const URL_PATH_DOWNLOAD = 'sourceimportlog/log/download';
 
     
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
                if (isset($item['csv'])) {
                    $item[$this->getData('name')] = [
                        'download' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DOWNLOAD,
                                [
                                    'csv' => $item['csv']
                                ]
                            ),
                            'label' => __('Download')
                        ]
                    ];
                }
            }
        }
 
        return $dataSource;
    }
}