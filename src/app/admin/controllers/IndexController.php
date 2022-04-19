<?php

/**
* NOTE:
*
* $this->response->redirect(admin/:controller/:action) is adding a random '/public' to the url, rendering the paths wrong.
* Same thing is happening with Phalcon tag->linkTo();
*/

namespace Admin\Controllers;

use Admin\Models\User;
use Admin\Models\Product;

/**
 * Index Controller
 * Product CRUD
 */
class IndexController extends ControllerMaster
{
    public function initialize()
    {
        //Strings for translation
        $this->view->strings = [
            "login" => "Login",
            "register" => "Signup",
            "add_product" => "Add Product",
            "update" => "Update",
        ];
        $this->view->message = null;
    }
    public function indexAction()
    {
        if ($this->request->isPost()) {
            $user = new User();
            $post = $this->request->getPost();
            if ($post['action'] == 'register') {
                $user->assign($post, ["username", "password"]);
                $user->save();
            } elseif ($post['action'] == 'login') {
                $result = $user->find([
                    "username" => $post['username'],
                    "password" => $post['password'],
                ]);
                if (count($result) == 1) {
                    header("location: /admin/index/products");
                } else {
                    $this->view->message = "Login fail, multiple users or invalid credentials";
                }
            }
        }
    }
    public function productsAction()
    {
        $this->view->update = [];
        $id = $this->request->getQuery("id");
        $prod = null;
        if ($id != "") {
            $prod = new Product();
            $prod->findByID($id);
            $this->view->update = $prod->vals;
        }
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $prod = new Product();
            if ($post['_id']) {
                $prod->findByID($post['_id']);
            }
            $prod->assign($post, ["name", "price"]);
            print_r($prod->save());
        }
        $this->view->products = (new Product)->find();
    }
    public function deleteAction()
    {
        $id = $this->request->getQuery("id");
        $prod = new Product();
        $prod->findByID($id);
        print_r($prod->vals);
        $prod->delete();
        header("location: /admin/index/products");
    }
}
