@extends('layouts.app')
@section('page-title')
    {{ __('Department Dashboard') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Department Dashboard') }}
            </a>
        </li>
        
    </ul>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Department Name : ') }}  <span class="badge bg-success p-2">{{ __($department_name) }}</span> 
                            <br><br> 
                            {{ __('Status : ') }} 
                                @if ($status_view == 'Pending')
                                    <span class="badge bg-warning p-2">{{ __('Pending') }}</span>
                                @elseif ($status_view == 'Resolved')
                                    <span class="badge bg-primary p-2">{{ __('Resolved') }}</span>
                                @elseif ($status_view == 'Re-raised')
                                    <span class="badge bg-info p-2">{{ __('Re-raised') }}</span>
                                @elseif ($status_view == 'Closed')
                                    <span class="badge bg-danger p-2">{{ __('Closed') }}</span>
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ __('Complaint ID') }}</th>
                                    <th class="text-center">{{ __('Images') }}</th>
                                    <th class="text-center">{{ __('Station Name') }}</th>
                                    <th class="text-center">{{ __('Amenity Category') }}</th>
                                    <th class="text-center">{{ __('Complaint Description') }}</th>
                                    <th class="text-center">{{ __('Passenger Safety Critical') }}</th>
                                    <th class="text-center">{{ __('Timeline') }}</th>
                                    <th class="text-center">{{ __('Address') }}</th>
                                    <th class="text-center">{{ __('Latitude') }}</th>
                                    <th class="text-center">{{ __('Longitude') }}</th>
                                    <th class="text-center">{{ __('Map') }}</th>
                                    <th class="text-center">{{ __('Location Accuracy') }}</th>
                                    <th class="text-center">{{ __('Have You Reported This Before ?') }}</th>
                                    <th class="text-center">{{ __('Assigned To') }}</th>
                                    <th class="text-center">{{ __('Status') }}</th>


                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->complaint_num }} </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($item->images ?? [] as $image)
                                                    <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $image) }}"
                                                            class="img-thumbnail"
                                                            style="width:80px;height:80px;object-fit:cover;">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $item->station_name }} </td>
                                        <td>{{ $item->amenity_category_name }} </td>
                                        <td>{{ $item->description }} </td>
                                        <td>
                                            @if($item->is_safety_critical == 1)
                                                <span class="badge bg-success p-2">{{ __('Yes') }}</span>
                                            @else
                                                <span class="badge bg-danger p-2">{{ __('No') }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $item->timeline ?? 0 }} Days </td>
                                        <td>{{ $item->location_address }} </td>
                                        <td>{{ $item->latitude }} </td>
                                        <td>{{ $item->longitude }} </td>
                                        <td>
                                            @if($item->latitude && $item->longitude)
                                                <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                                target="_blank"
                                                class="btn btn-sm btn-primary">
                                                    View Location
                                                </a>
                                            @else
                                                <span class="text-muted"></span>
                                            @endif
                                        </td>
                                        <td>{{ $item->location_accuracy }} </td>
                                        <td>
                                            @if($item->reported_before == 1)
                                                <span class="badge bg-success p-2">{{ __('Yes') }}</span>
                                            @else
                                                <span class="badge bg-danger p-2">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->assigned_to_name ?? '' }} </td>
                                        <td>
                                            @if ($item->status == 0)
                                                <span class="badge bg-warning p-2">{{ __('Pending') }}</span>
                                            @elseif ($item->status == 1)
                                                <span class="badge bg-success p-2">{{ __('Resolved') }}</span>
                                            @elseif ($item->status == 2)
                                                <span class="badge bg-info p-2">{{ __('Re-raised') }}</span>
                                            @elseif ($item->status == 3)
                                                <span class="badge bg-secondary p-2">{{ __('Closed') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

