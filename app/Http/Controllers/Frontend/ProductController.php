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
    public function view($slug, ProductRepository $product, CategoryRepository $category, SizeRepository $size, ColorRepository $color){
        $this->_data['product'] = $product->getProductBySlug($slug);

        $this->_pushBreadCrumbs($this->_data['product']->name);

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
    public function category($slug, ProductRepository $product, CategoryRepository $category, SizeRepository $size, ColorRepository $color){
        if ($this->_request->ajax()){
            return $product->getProductsByFilters($this->_request);
        }

        $cat = $category->getCategoryBySlug($slug);
        $this->_data['category'] = $cat; 
        $this->_data['title'] = 'Danh sách sản phẩm';
        $this->_data['categories'] = $category->getCategories($cat->id);
        $this->_data['sizes'] = $size->getSizes();
        $this->_data['colors'] = $color->getColors();
        $this->_data['product_prices'] = $product->getProductPriceRanges();

        return view('frontend.products.index', $this->_data);
    }

}
