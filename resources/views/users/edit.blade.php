@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="/vendor/ace/assets/css/chosen.css" />
@endsection

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
            <a href="{{ route('users.index') }}">Users</a>
        </li>
        <li class="active">Edit</li>
    </ul><!-- /.breadcrumb -->
    <!-- /section:basics/content.searchbox -->
</div>
<!-- /section:basics/content.breadcrumbs -->

<div class="page-content">
    <div class="page-header">
        <h1>
            Users
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                Edit
            </small>
            <a class="btn btn-primary pull-right" href="{{ route('users.index') }}">
                <i class="ace-icon fa fa-list" aria-hidden="true"></i>
                <span class="hidden-xs">List</span>
            </a>
        </h1>
    </div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            @include('common.errors')
            
            <form class="form-horizontal" role="form" method="POST" action="{{ route('users.update', $user->id) }}">
                {!! method_field('PUT') !!}
                
                @include('users._form')
            </form>
        </div>
    </div>
</div><!-- /.page-content -->
@endsection

@section('scripts')
<script src="/vendor/ace/assets/js/chosen.jquery.js"></script>
@endsection

@section('inline_scripts')
<script>
$(function () {
    $(".chosen-select").chosen({
        allow_single_deselect: true,
        width: '100%'
    });
});
</script>
@endsection