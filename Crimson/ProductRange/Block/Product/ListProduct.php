<?php

namespace Crimson\ProductRange\Block\Product;

use Magento\Catalog\Model\Layer;
use Magento\Framework\Exception\NoSuchEntityException;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{

    /**
     * ListProduct constructor.
     */
    public function __construct()
    {
    }

    private function initializeProductCollection()
    {
        $collection = $this->productCollectionFactory->create();

        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
//            ->addFieldToFilter( 'price' , array('from' => $dateFrom, 'to' => $dateTo) )
//            ->setOrder('created_at', 'desc' );
            ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);

        return $collection->getItems();

        return $collection;
    }



}
