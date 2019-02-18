<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\BaseController;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SizeRepository;
use App\Repositories\ColorRepository;

class ProductController extends BaseController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh Sách sản phẩm', route('frontend.products.index'));
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

}
