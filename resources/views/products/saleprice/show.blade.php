@extends('layouts.app')

@section('content')
<!-- #section:basics/content.breadcrumbs -->
<div class="breadcrumbs" id="breadcrumbs">
    <script type="text/javascript">
        try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
    </script>

    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ url('/dashboard') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('products.index') }}">Sản phẩm</a>
        </li>
        <li>
            <a href="{{ route('products.edit', $product->id) }}">{{ $product->name }}</a>
        </li>
        <li class="active">Đặt giá bán</li>
    </ul><!-- /.breadcrumb -->
    <!-- /section:basics/content.searchbox -->
</div>
<!-- /section:basics/content.breadcrumbs -->

<div class="page-content" ng-controller="ProductSalepriceController">
    <div class="page-header">
        <h1>
            Sản phẩm {{ $product->name }}
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Đặt giá bán
            </small>
            <a class="btn btn-primary pull-right" href="{{ route('products.index') }}">
                <i class="ace-icon fa fa-list" aria-hidden="true"></i>
                <span class="hidden-xs">Danh sách</span>
            </a>
        </h1>
    </div><!-- /.page-header -->
    <div class="row" ng-if="productIsLoaded">
        <div class="col-xs-6">
            <div class="alert alert-success" ng-show="productSalepriceForm.successful">
                Đặt giá bán thành công.
            </div>

            <div class="alert alert-danger" ng-show="productSalepriceForm.errors.length > 0">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    <li ng-repeat="error in productSalepriceForm.errors">@{{ error }}</li>
                </ul>
            </div>

            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right">Giá bán</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="price" placeholder="Giá bán" ng-model="productSalepriceForm.price" ng-change="updateMargin()">
                        <label class="col-sm-6 control-label no-padding-right"><strong ng-bind="productMargin"></strong></label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-3 no-padding-right">Áp dụng cho</label>

                    <div class="col-xs-12 col-sm-9">
                        @foreach (config('teko.stores') as $k => $v)
                        <div class="checkbox">
                            <label>
                                <input name="form-field-checkbox" type="checkbox" class="ace" ng-model="productSalepriceForm.stores[{{ $k }}]" value="{{ $k }}" />
                                <span class="lbl"> {{ $v }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-3 no-padding-right">Áp dụng cho</label>

                    <div class="col-xs-12 col-sm-9">
                        @foreach (config('teko.regions') as $k => $v)
                            <div class="checkbox">
                                <label>
                                    <input name="form-field-checkbox" type="checkbox" class="ace" ng-model="productSalepriceForm.regions[{{ $k }}]" value="{{ $k }}" />
                                    <span class="lbl"> {{ $v }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-success" ng-click="update()" ng-disabled="productSalepriceForm.disabled">
                            <i class="ace-icon fa fa-save bigger-110"></i>Cập nhật
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xs-6">
                <h3>Nhà cung cấp</h3>

                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Nhà cung cấp</th>
                            <th>Số lượng</th>
                            <th>Giá nhập</th>
                            <th>Giá bán khuyến nghị</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($productSuppliers as $k => $v)
                            <tr>
                                <td>{{ $k + 1 }}</td>
                                <td>{{ $v->supplier->name }}</td>
                                <td>{{ $v->quantity }}</td>
                                <td>{{  number_format (  $v->import_price , 0 , "." , "," )}}</td>
                                <td>{{  number_format (  $v->price_recommend , 0 , "." , "," )}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
        <h3>Giá bán hiện tại</h3>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Giá bán</th>
                    <th>Online</th>
                    <th>Offline</th>
                    <th>Phòng máy</th>
                </tr>
                </thead>
                <tbody>
                    <?php $regions = config('teko.regions'); ?>
                    @foreach($nowSalePrices as $key => $nowSalePrice)
                        <tr>
                            <td>{{ $regions[$key] }}</td>
                            @foreach([1,2,3] as $k)
                            <td>{{ $nowSalePrice->where('store_id', $k)->isEmpty() ?
                            "N/A" :
                            $nowSalePrice->where('store_id', $k)->first()->price }}
                            </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-xs-6">
            <h3>Giá bán thấp nhất trên thị trường</h3>
            @if($productMarket)
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Giá bán</th>
                        <th>Url</th>
                        <th>Retailer Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $productMarket->min_retailer_price }}</td>
                        <td>{{ $productMarket->crawled_url }}</td>
                        <td>{{ $productMarket->retailer_name }}</td>
                    </tr>
                    </tbody>
                </table>
            @else
                <p> Không có dữ liệu </p>
            @endif
        </div>
    </div>

    <div class="row">
        <h3>Lịch sử cập nhật giá</h3>
        <div class="col-xs-12">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Store</th>
                    <th>Miền</th>
                    <th>Price</th>
                    <th>Cập nhật</th>
                </tr>
                </thead>
                <tbody>
                @foreach($product->saleprices as $k => $v)
                    <tr>
                        <td>{{ $k + 1 }}</td>
                        <td>{{ config('teko.stores')[$v->store_id] }}</td>
                        <td>{{ config('teko.regions')[$v->region_id] }}</td>
                        <td>{{  number_format (  $v->price , 0 , "." , "," )}}</td>
                        <td>{{ convert_time($v->created_at) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div><!-- /.page-content -->
@endsection

@section('inline_scripts')
<script>
var PRODUCT_ID = {{ $product->id }};
var BEST_PRICE = {{ isset($productSuppliers[0]) ? $productSuppliers[0]->import_price : 0}};
</script>
@endsection