<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\BaseController;
use App\Repositories\Frontend\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SizeRepository;
use App\Repositories\ColorRepository;

class ProductController extends BaseController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Sản phẩm', route('frontend.products.index'));
    }

    /**
     * Show the products list page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductRepository $product, CategoryRepository $category, SizeRepository $size, ColorRepository $color){
        if ($this->_request->ajax()){
            return $product->getProductsByFilters($this->_request);
        }
        $this->_data['show_breadcrumbs'] = true;
        
        $this->_data['title'] = 'Danh sách sản phẩm';
        $this->_data['categories'] = $category->getCategories();
        $this->_data['sizes'] = $size->getSizes();
        $this->_data['colors'] = $color->getColors();
        $this->_data['product_prices'] = $product->getProductPriceRanges();

        return view('frontend.products.index', $this->_data);
    }

    /**
     * Show the product detail page.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($id, $slug = null, ProductRepository $product, CategoryRepository $category, SizeRepository $size, ColorRepository $color){
        $this->_data['product'] = $product->getProductByID($id);

        $this->_pushBreadCrumbs($this->_data['product']->name);
        $this->_data['show_breadcrumbs'] = true;

        return view('frontend.products.view', $this->_data);
    }

    public function getMaxQuantity(ProductRepository $product) {
        $result = $product->getMaxQuantity($this->_request);

        return response()->json($result);
    }

    /**
     * Show the category detail page.
     *
     * @return \Illuminate\Http\Response
     */
    public function category($id, $slug = null, ProductRepository $product, CategoryRepository $category, SizeRepository $size, ColorRepository $color){
        if ($this->_request->ajax()){
            return $product->getProductsByFilters($this->_request);
        }
        $this->_data['show_breadcrumbs'] = true;

        $cat = $category->getCategoryByID($id);
        $this->_data['category'] = $cat; 
        $this->_data['title'] = 'Danh sách sản phẩm';
        $this->_data['categories'] = $category->getCategories($cat->id, $cat->parent_id);
        $this->_data['sizes'] = $size->getSizes();
        $this->_data['colors'] = $color->getColors();
        $this->_data['product_prices'] = $product->getProductPriceRanges();

        return view('frontend.products.index', $this->_data);
    }

}
