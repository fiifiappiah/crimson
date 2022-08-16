<?php

namespace Crimson\ProductRange\Controller\Customer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;

/**
 * Class RenderRelated
 *
 * @package N98\AjaxProductRecommendations\Controller\Ajax
 */
class RenderProductRange extends Action
{
    const BLOCK_NAME_PRODUCT_LIST = 'category.products.list';
    /**
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * Render constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ProductRepositoryInterface $productRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->productRepository = $productRepository;
        $this->coreRegistry = $coreRegistry;
    }
    /**
     * Execute action based on request and return result
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }
        $result = $this->jsonFactory->create();
        $sort = $this->getRequest()->getParam('sort');
        $lowRange = $this->getRequest()->getParam('low-range');
        $highRange = $this->getRequest()->getParam('high-range');

        if (!$lowRange || $highRange) {
            return $this->setErrorResult(
                $result,
                __('Product Range could not be loaded.')
            );
        }
        try {
//            $category = $this->categoryRepository->get($this->getCurrentStore()->getRootCategoryId());
//            $this->setData('current_category', $category);
//            $this->coreRegistry->register('current_category', );
//
//            $this->setData('current_category', $this->_coreRegistry->registry('current_category'));

//            $block = $this->getLayout()->getBlock('category.products.list');
//            $block = $this->_v()->getBlock('category.products.list');
//            $block = $this->_view->loadLayout('category.products.list');
//            if ($block) {
//                $productCollection = $block->getLoadedProductCollection();
//                $productTypes = [];
//                /** @var $product \Magento\Catalog\Model\Product */
//                foreach ($productCollection as $product) {
//                    $productTypes[] = $this->escapeHtml($product->getTypeId());
//                }
//                $this->productTypes = array_unique($productTypes);
//            }


            $this->_view->loadLayout(
                ['default', 'ajaxproduct_content_abstract'],
                true,
                true,
                false
            );
            $layout = $this->_view->getLayout();
            $block = $layout->getBlock(self::BLOCK_NAME_PRODUCT_LIST);
            if (!$block) {
                return $this->setErrorResult(
                    $result,
                    __('Product List could not be loaded.')
                );
            }
            $output = $block->toHtml();
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
                __('vProduct recommendations could not be loaded.')
            );
        }
    }
    /**
     * Set error result
     *
     * @param Json $result
     * @param string $errorMessage
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
