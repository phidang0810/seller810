<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Category;
use App\Repositories\PhotoRepository;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\BaseController;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SizeRepository;
use App\Repositories\ColorRepository;

class HomeController extends BaseController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(ProductRepository $product, PhotoRepository $photo, CategoryRepository $category)
    {

        $this->_data['show_breadcrumbs'] = false;
        
        $this->_data['title'] = 'Trang Chủ';

        $this->_data['slides'] = $photo->getList([
            'type' => PHOTO_BANNER
        ]);

        $this->_data['categories'] = $category->getListCategories([
            'is_home' => 1
        ]);

        $this->_data['newProducts'] = $product->getList();

        $this->_data['hotProducts'] = $product->getList();

        return view('frontend.home', $this->_data);
    }

    public function contact()
    {
        return view('frontend.contact', $this->_data);
    }

    public function postContact()
    {

    }

    public function listPost(PostRepository $post)
    {
        $data = $post->getList();
        return view('frontend.news_list', [
            'posts' => $data
        ]);
    }

    public function detailPost($name, $id, PostRepository $post)
    {
        $data = $post->getData($id);
        return view('frontend.news_detail', [
            'data' => $data
        ]);
    }

}
