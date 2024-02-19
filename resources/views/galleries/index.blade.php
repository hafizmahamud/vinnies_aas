@extends('layouts.app')

@section('title')
    @route('galleries.index')
        Students Gallery
    @endroute

    @route('galleries.admin')
        Upload Administration for Students Gallery
    @endroute
@stop

@section('prejs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js" integrity="sha256-+oeQRyZyY2StGafEsvKyDuEGNzJWAbWqiO2L/ctxF6c=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js" integrity="sha256-P93G0oq6PBPWTP1IR8Mz/0jHHUpaWL0aBJTKauisG7Q=" crossorigin="anonymous"></script>
@stop

@section('content')
    <div class="container">
        @route('galleries.index')
            <form action="{{ route('galleries.index') }}">
        @endroute
        @route('galleries.admin')
            <form action="{{ route('galleries.admin') }}">
        @endroute
            <div class="row">
                <div class="col-sm-12 intro">
                    @route('galleries.index')
                        <h1 class="page-title text-center">Students Gallery</h1>
                    @endroute

                    @route('galleries.admin')
                        <h1 class="page-title text-center">Upload Administration for Students Gallery</h1>
                    @endroute

                    @include('flash::message')
                    @include('partials.js-alert')

                    @route('galleries.index')
                        <p><strong>Welcome to the Students Gallery where we preserve all of the pictures, letters and other materials from our Overseas Students!</strong></p>
                        <ul>
                            <li>To download multiple items in one go please select them and then click on the "Download All Selected Items (Bulk Download)" button.</li>
                            <li>To download individual documents or images simply drag your mouse over the document/image and click on the download icon that appears over it.</li>
                            <li>To view images simply drag your mouse over the image and click on the zoom lens icon that appears over it.</li>
                        </ul>
                    @endroute

                    @route('galleries.admin')
                        <p><strong>You are now into the Students Gallery Administration section!</strong></p>
                        <ul>
                            <li>To Delete multiple items in one go please select them and then click on the "Delete Selected Items (Bulk)" button.</li>
                            <li>To Add New Items please use the Button "Add New Gallery Item".</li>
                            <li>To Edit existing Gallery Items please use the "Edit" button for each individual item.</li>
                        </ul>
                    @endroute

                    <div class="section mt-1 clearfix">
                        <h2 class="section-title">Filters</h2>
                        <div class="form-inline form-table-filter pull-left">
                            <div class="form-group">
                                {{ Form::select('year', Helper::getGalleryYears(), old('year', request()->get('year')), ['class' => 'form-control', 'placeholder' => 'All Years']) }}
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                {{ Form::select('country', Helper::getGalleryCountries(), old('country', request()->get('country')), ['class' => 'form-control', 'placeholder' => 'All Countries']) }}
                            </div>
                            <div class="form-group">
                                {{ Form::select('sort', [
                                    'newest'       => 'Sort items by date (newest first)',
                                    'oldest'       => 'Sort items by date (oldest first)',
                                    'year_country' => 'Sort items by Year and Country',
                                ], old('sort', request()->get('sort')), ['class' => 'form-control']) }}
                            </div>
                            <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply">Apply</button>
                        </div>
                        <div class="pull-right">
                            @route('galleries.index')
                                <a href="{{ route('galleries.admin') }}" class="btn btn-danger">Uploads Administration</a>
                            @endroute

                            @route('galleries.admin')
                                <a href="{{ route('galleries.create') }}" class="btn btn-primary">Add New Gallery Item</a>
                            @endroute
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-inline">
                        <div class="form-group has-btn">
                            <label class="sr-only" for="galleries-search">Search donations</label>
                            <input type="text" class="form-control" id="galleries-search" placeholder="Search" name="keyword" value="{{ old('keyword', request()->get('keyword')) }}">
                            <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>

                    </div>
                </div>
                <div class="col-sm-6 text-right">
                    {{ $galleries->flatten()->count() }} {{ str_plural('item', $galleries->flatten()->count()) }}
                </div>
            </div>
        </form>

        @foreach ($galleries as $year => $items)
            <div class="gallery-container">
                <hr>
                <h2 class="stats-title mb-1">Album Year {{ $year }}</h2>

                @route('galleries.index')
                    <a href="#" class="btn btn-primary mb-2 js-bulk-download">Download All Selected Item (Bulk Download)</a>
                @endroute

                @route('galleries.admin')
                    <a href="#" class="btn btn-danger mb-2 js-bulk-delete">Delete Selected Item (Bulk)</a>
                @endroute

                @foreach ($items->chunk(3) as $i => $chunk)
                    <div class="gallery row mb-2">
                        @foreach ($chunk as $item)
                            <div class="col-sm-4" data-mh="gallery-{{ $year }}-{{ $i }}">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mt-0">
                                            @route('galleries.index')
                                                <label class="font-weight-normal"><input type="checkbox" value="{{ $item->id }}" name="id[]"> Select for Bulk Download</label>
                                            @endroute

                                            @route('galleries.admin')
                                                <label class="font-weight-normal"><input type="checkbox" value="{{ $item->id }}" name="id[]"> Select for Bulk Deletion</label>
                                            @endroute
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        @if ($item->country !== 'Generic')
                                            <p class="text-warning">{{ $item->country }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="item-preview mb-1">
                                    @switch ($item->extension)
                                        @case ('pdf')
                                            <i class="fa-img fa fa-file-pdf-o" aria-hidden="true"></i>
                                            @break

                                        @case ('ppt')
                                        @case ('pptx')
                                            <i class="fa-img fa fa-file-powerpoint-o" aria-hidden="true"></i>
                                            @break

                                        @case ('doc')
                                        @case ('docx')
                                            <i class="fa-img fa fa-file-word-o" aria-hidden="true"></i>
                                            @break

                                        @case ('zip')
                                            <i class="fa-img fa fa-file-archive-o" aria-hidden="true"></i>
                                            @break

                                        @case ('jpeg')
                                        @case ('jpg')
                                        @case ('png')
                                        @case ('bmp')
                                            <img class="img-responsive" src="{{ $item->url }}" alt="{{ $item->description }}">
                                            @break

                                    @endswitch

                                    <div class="item-preview-hover">
                                        <div class="icon">
                                            <a href="{{ route('galleries.download', $item) }}" target="_blank" rel="noopener noreferrer"><i class="fa fa-download" aria-hidden="true"></i></a>

                                            @if (in_array($item->extension, ['jpeg', 'jpg', 'png', 'bmp']))
                                                <a href="{{ $item->url }}" class="item-preview-image"><i class="fa fa-search-plus" aria-hidden="true"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="item-description text-center">
                                    <div data-mh="gallery-{{ $year }}-{{ $i }}-description">
                                        <p>{!! nl2br($item->description) !!}</p>
                                    </div>

                                    @route('galleries.admin')
                                        <a href="{{ route('galleries.edit', $item) }}" class="btn btn-warning mt-1 mb-1">Edit Gallery Item {{ $item->id }}</a>
                                    @endroute
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    @route('galleries.admin')
        <form action="{{ route('galleries.admin') }}" class="delete-galleries-form" method="post" style="display:none;">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}

            <input type="hidden" name="ids" value="">
        </form>
    @endroute
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/galleries.js') }}"></script>
@stop
