<?php

namespace Frontend\Controllers;
use Frontend\Models\Product;

class IndexController extends ControllerMaster
{
    public function indexAction()
    {
        $this->view->products=(new Product())->find();
    }
    public function viewAction()
    {
        $id=$this->request->getQuery("id");
        $product=new Product();
        $product->findByID($id);
        $this->view->product=$product->vals;
    }
}
