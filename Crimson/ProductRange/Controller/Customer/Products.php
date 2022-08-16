<?php

namespace Crimson\ProductRange\Controller\Customer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Products
 * @package Crimson\ProductRange\Controller\Customer
 */
class Products extends Action
{
    const SORT_TYPE = [SortOrder::SORT_ASC, SortOrder::SORT_DESC];

    const DEFAULT_PAGE_SIZE = 10;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Products constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param JsonFactory $jsonFactory
     */
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
     * @param $from
     * @param $to
     * @param $sort
     * @return Collection
     */
    public function getProductRange(int $from, int $to, string $sort = 'asc')
    {
        $collection = $this->productCollectionFactory->create();

        $collection
            ->addAttributeToSelect('*')
            ->addFieldToFilter('price', [ ['gt' => $from, 'lt' => $to]])
            ->setPageSize(self::DEFAULT_PAGE_SIZE)
            ->setOrder('price', $sort);

        return $collection;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
        $result = $this->jsonFactory->create();

        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }

        try {
            $sort = strtolower($this->getRequest()->getParam('sort'));
            $lowRange = intval($this->getRequest()->getParam('low_range'));
            $highRange = intval($this->getRequest()->getParam('high_range'));

            if ((!$lowRange || !$highRange)) {
                return $this->setErrorResult(
                    $result,
                    __('Product Range could not be loaded.')
                );
            }

            if (!in_array($sort, self::SORT_TYPE)) {
                $sort = SortOrder::SORT_ASC;
            }

            $collection = $this->getProductRange($highRange, $lowRange, $sort);

            $layout = $this->_view->getLayout();
            $output = $layout->createBlock('Magento\Catalog\Block\Product\ListProduct')
                ->setCollection($collection)
                ->setTemplate('Magento_Catalog::product/list.phtml')
                ->toHtml();

            $result->setData(
                [
                    'output' => $output,
                    'success' => true
                ]
            );
            return $result;
        } catch (\Exception $e) {
            return $this->setErrorResult(
                $result,
                __('Product Range could not be loaded.' . $e->getMessage())
            );
        }
    }

    /**
     * @param Json $result
     * @param $errorMessage
     * @return Json
     */
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
