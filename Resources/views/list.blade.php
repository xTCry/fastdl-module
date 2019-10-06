@php($title = "FastDL Manager")

@extends('layouts.main')

@section('breadclumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">GameAP</a></li>
        <li class="breadcrumb-item active">FastDL</li>
    </ol>
@endsection

@section('content')

    @include('components.grid', [
        'modelsList' => $fastdlDedicatedServers,
        'labels' => [ 'Name', 'Installed'],
        'attributes' => [
            'name',
            ['lambda', function($fastdlDsModel) {
                return $fastdlDsModel->installed
                    ? '<span class="badge badge-success">' . __('main.yes') . '</span>'
                    : '<span class="badge badge-danger">' . __('main.no') . '</span>';
            }]
        ],
        'viewRoute' => 'admin.fastdl.accounts',
        'editRoute' => 'admin.fastdl.edit',
    ])
@endsection