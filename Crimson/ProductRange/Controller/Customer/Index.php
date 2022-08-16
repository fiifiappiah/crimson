<?php

namespace Crimson\ProductRange\Controller\Customer;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
//        die('bit');
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
