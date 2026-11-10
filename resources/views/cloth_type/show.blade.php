@extends('layouts.app')
@section('page-title')
    {{$clothType->title}} {{__('Detail')}}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{route('dashboard')}}">{{__('Dashboard')}}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{route('cloth-type.index')}}">{{__('Cloth Type')}}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{$clothType->title}} {{__('Detail')}}
            </a>
        </li>
    </ul>
@endsection
@push('script-page')
    <script>
        $(document).on('click', '.print', function () {
            var printContents = document.getElementById('invoice-measurement').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });
    </script>
@endpush
 
@section('content')
    <div class="row" id="invoice-measurement">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>  {{$clothType->title}} {{__('Detail')}}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="detail-group">
                                <h6>{{__('Title')}}</h6>
                                <p class="mb-20">{{$clothType->title}}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="detail-group">
                                <h6>{{__('Gender')}}</h6>
                                <p class="mb-20">{{$clothType->gender}}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="detail-group">
                                <h6>{{__('Amount')}}</h6>
                                <p class="mb-20">{{priceFormat($clothType->amount) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="detail-group">
                                <h6>{{__('Tax')}}</h6>
                                <p class="mb-20">
                                    @if(!empty($clothType->taxs()))
                                        @foreach($clothType->taxs() as $tax)
                                            <span>{{$tax->tax}} ({{$tax->rate}}%)</span> ,
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="detail-group">
                                <h6>{{__('Notes')}}</h6>
                                <p class="mb-20">{{$clothType->note }} </p>
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <h5>{{__('Measurement Detail')}}</h5>
                        <div class="col-xxl-12 cdx-xxl-100 mt-20">
                            <div class="table-responsive">
                                <table class="display dataTable cell-border ">
                                    <thead>
                                    <tr>
                                        <th>{{__('Order')}}</th>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Unit')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($clothType->clothMeasureType as $clothMeasureType)
                                        <tr>
                                            <td>{{$clothMeasureType->order}}</td>
                                            <td>{{$clothMeasureType->title}}</td>
                                            <td>{{!empty($clothMeasureType->units)?$clothMeasureType->units->unit:'-'}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection

