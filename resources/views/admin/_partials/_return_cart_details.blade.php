<div class="ibox-content m-b">
    <style>
    .table-borderless > tbody > tr > td,
    .table-borderless > tbody > tr > th,
    .table-borderless > tfoot > tr > td,
    .table-borderless > tfoot > tr > th,
    .table-borderless > thead > tr > td,
    .table-borderless > thead > tr > th {
        border: none;
    }

    .table-borderless > tbody > tr:last-child{
        border-bottom: 1px solid #ccc;
    }
</style>
<table class="table table-borderless">
    <thead>
        <tr class="table-body-head">
            <th>Mã sản phẩm</th>
            <th>Kích thước</th>
            <th>Màu sắc</th>
            <th>Số lượng</th>
        </tr>
    </thead>
    <tfoot>
    </tfoot>
    <tbody class="cart-detail-wrapper">
        @foreach ($result['cart']->returnDetails as $detail)
        <tr>
            <td colspan="1">{{$detail->product->barcode_text}}</td>
            <td class="" colspan="1">{{$detail->productDetail->size->name}}</td>
            <td class="" colspan="1">{{$detail->productDetail->color->name}}</td>
            <td class="" colspan="1">{{$detail->quantity}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
