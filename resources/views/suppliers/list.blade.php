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
                <a href="{{ route('suppliers.getList') }}">Nhà cung cấp</a>
            </li>
            <li class="active">Danh sách</li>
        </ul><!-- /.breadcrumb -->
        <!-- /section:basics/content.searchbox -->
    </div>
    <!-- /section:basics/content.breadcrumbs -->

    <div class="page-content">
        <div class="page-header">
            <h1>
                Nhà cung cấp
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    Danh sách
                </small>
                <a class="btn btn-primary pull-right" href="{{ route('suppliers.create') }}">
                    <i class="ace-icon fa fa-plus" aria-hidden="true"></i>
                    <span class="hidden-xs">Thêm</span>
                </a>
            </h1>
        </div><!-- /.page-header -->
        <div class="row">
            <div class="col-xs-12">
                <div class="widget-box">
                    <div class="widget-header">
                        <h5 class="widget-title">Search</h5>
                    </div>

                    <div class="widget-body">
                        <div class="widget-main">
                            <form class="form-inline" id="search-form">
                                <select class="form-control" name="typeId">
                                    <option value="">--Chọn loại hóa đơn--</option>
                                    <option value="0">Thanh toán trực tiếp</option>
                                    <option value="1">Thanh toán gián tiếp</option>
                                </select>
                                <select class="form-control" name="status">
                                    <option value="">--Chọn Trạng thái--</option>
                                    <option value="active">Kích hoạt</option>
                                    <option value="inactive">Không kích hoạt</option>
                                </select>
                                <input type="text" class="form-control" name="keyword" placeholder="Từ khóa tìm kiếm" />
                                <button type="submit" class="btn btn-purple btn-sm">
                                    <span class="ace-icon fa fa-search icon-on-right bigger-110"></span> Search
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table id="dataTables-products" class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
                    <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Địa chỉ</th>
                        <th>Địa bàn cung cấp</th>
                        <th>Mã số thuế</th>
                        <th>Trạng thái</th>
                        <th>Thông tin về người đại diện của NCC</th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div><!-- /.page-content -->
@endsection

@section('scripts')
    <script src="/vendor/ace/assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="/vendor/ace/assets/js/dataTables/jquery.dataTables.bootstrap.js"></script>
@endsection

@section('inline_scripts')
    <script>
        $(function () {
            var datatable = $("#dataTables-products").DataTable({
                searching: false,
                autoWidth: false,
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: '{!! route('suppliers.suppliersDatables') !!}',
                    data: function (d) {
                        d.keyword = $('input[name=keyword]').val();
                        d.typeId = $('select[name=typeId]').val();
                        d.status = $('select[name=status]').val();
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'address', name: 'address'},
                    {data: 'province', name: 'province'},
                    {data: 'tax_number', name: 'tax_number'},
                    {data: 'status', name: 'status'},
                    {data: 'info_person', name: 'info_person'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

            $('#search-form').on('submit', function(e) {
                datatable.draw();
                e.preventDefault();
            });
        });
    </script>
@endsection
