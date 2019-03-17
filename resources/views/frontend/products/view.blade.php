@extends('frontend.layouts.master')

@section('title', $product->name)

@section('js')
<script src="{{asset('themes/frontend/assets/plugins/owl-carousel-2-2.1.6/owl.carousel.min.js')}}" type="text/javascript" charset="utf-8" async defer></script>
<script type="text/javascript">
  var product_id = "{{$product->id}}";
  var urlGetQuantity = "{{route('frontend.products.getMaxQuantity')}}";
  var urlAddCartDetail = "{{route('frontend.carts.addDetail')}}";
</script>
<script src="{{asset('themes/frontend/assets/js/product.js')}}" type="text/javascript" charset="utf-8" async defer></script>
<script src="{{asset('themes/frontend/assets/js/use-owl-carousel.js')}}" type="text/javascript" charset="utf-8" async defer></script>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('themes/frontend/assets/plugins/owl-carousel-2-2.1.6/owl.carousel.min.css')}}">
@endsection

@section('content')
<section id="main-content">
  <div class="container">
    <div class="row">
      <div class="col-md-6" id="product-images">
        <div class="slide-cont">
          <div class="owl-carousel">
            @foreach($product->photos as $photo)
            <div><img src="{{asset('storage/' . $photo->origin)}}"></div>
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-md-6" id="product-info">
        <h3 class="name">{{$product->name}}</h3>
        @if(Auth::check())<h5 class="price">{{$product->sell_price}}</h5>@endif
        <p class="description">{{$product->description}}</p>
        <div class="row" id="colors">
          <div class="col-3">
            <span class="attr-title">Màu</span>
          </div>
          <div class="col-9">
            @foreach($product->colorObjects as $color)
            <a href="javascript:;" id="color-{{$color->id}}" title="{{$color->name}}" onclick="selectColor({{$color->id}})"><i class="fas fa-circle" style="color: {{$color->code}}"></i></a>
            @endforeach
          </div>
        </div>
        <div class="row" id="sizes">
          <div class="col-3">
            <span class="attr-title">Size</span>
          </div>
          <div class="col-9">
            @foreach($product->sizeObjects as $size)
            <a href="javascript:;" id="size-{{$size->id}}" title="{{$size->name}}" onclick="selectSize({{$size->id}})">{{$size->name}}</a>
            @endforeach
          </div>
        </div>
        <div class="row quantity-section">
          <div class="col-3">
            <span class="attr-title">Số lượng</span>
          </div>
          <div class="col-9">
            <div class="number-input">
              <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
              <input class="quantity" min="{{$product->min_quantity_sell}}" max="{{$product->min_quantity_sell}}" name="quantity" value="{{$product->min_quantity_sell}}" type="number">
              <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
            </div>
          </div>
        </div>
        <div class="row" id="add-to-cart-button">
          <div class="col-12">
            @if(!Auth::check())
            <a href="{{ route( 'login', ['to' =>Request::url()] ) }}" class="btn btn-custom-add-to-cart">Mua</a>
            @else
            <a href="javascript:;" class="btn btn-custom-add-to-cart" onclick="addToCart();">Mua</a>
            @endif
          </div>
        </div>
        <div class="row" id="alert">
          <div class="col-12">
            <div class="alert" role="alert">
              <h4 class="alert-heading"></h4>
              <p></p>
            </div>
          </div>
        </div>
        <hr>
        <div class="row sub-info">
          <div class="col-12">SKU: <span>AD-09</span></div>
          <div class="col-12">Danh mục: <span>{{$product->category->name}}</span></div>
          <div class="col-12 shares">Chia sẻ: <a href="javascript:;"><i class="fab fa-facebook-f"></i></a><a href="javascript:;"><i class="far fa-envelope"></i></a><a href="javascript:;"><i class="fab fa-linkedin-in"></i></a></div>
        </div>
      </div>
    </div>
    <div class="row" id="product-content">
      <div class="col-12">
        <h6 class="section-title">Mô tả</h6>
        <hr>
        <div class="content">
          {{html_entity_decode($product->content)}}
        </div>
      </div>
    </div>
    <div class="row" id="related-product">
      <div class="col-12">
        <h5 class="title">Sản phẩm liên quan</h5>
      </div>
      <div class="col-md-12" id="products-wrapper">
        <div class="row" id="products-list">
          @foreach($product->relatedProducts as $relatedProduct)
          <div class="col-md-3 col-sm-4 col-xs-6 product product-grid">    
            <a href="{{ route('frontend.products.view', ['id' => $relatedProduct->id]) }}">  
              <img src="{{asset('storage/' . $relatedProduct->photo)}}" alt="" class="img-fluid">   
              <h6 class="product-name">{{$relatedProduct->name}}</h6>
              @if(Auth::check())<h6 class="product-price">{{$relatedProduct->sell_price}}</h6>@endif
            </a>    
          </div>
          @endforeach
        </div>
      </div>
    </div>    
  </section>  
  @endsection