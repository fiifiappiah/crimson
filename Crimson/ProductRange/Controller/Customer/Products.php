<?php

namespace Crimson\ProductRange\Controller\Customer;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject;

class Products extends Action
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @param int $from
     * @param int $to
     * @param string $sort
     * @return DataObject[]
     */
    public function getProductRange($from, $to, $sort = 'desc')
    {
        $collection = $this->productCollectionFactory->create();

        $collection
            ->addAttributeToSelect('name')
            ->addFieldToFilter('price', ['from' => $from, 'to' => $to])
            ->setOrder('created_at', $sort);

        return $collection->toArray();
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();

        $sort = $this->getRequest()->getParam('sort');
        $lowRange = $this->getRequest()->getParam('low-range');
        $highRange = $this->getRequest()->getParam('high-range');

        $output = $this->getProductRange($highRange, $lowRange, $sort);

        try {
            $result = $this->jsonFactory->create();
            $result->setData(
                [
                    'output' => json_encode($output),
                    'success' => true
                ]
            );
            return $result;
        } catch (\Exception $e) {
            return $this->setErrorResult(
                $result,
                __('iProduct recommendations could not be loaded.' . $e->getMessage())
            );
        }
    }

    private function setErrorResult(Json $result, $errorMessage)
    {
        $result->setData(
            [
                'output' => $errorMessage,
                'success' => false
            ]
        );
        return $result;
    }
}
