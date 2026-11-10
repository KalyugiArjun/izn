@extends('layouts.app')

@section('page-title', __('Slider'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Slider') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Slider List') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.slider.create') }}" data-title="{{ __('Create Slider') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Slider') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Subtitle') }}</th>
                                    <th>{{ __('Button Text') }}</th>
                                    <th>{{ __('Button URL') }}</th>
                                    <th>{{ __('Sort Order') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sliders as $index => $slider)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}"
                                                class="rounded" style="width: 80px; height: 50px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='{{ asset('storage/app/public/' . $slider->image) }}';">
                                        </td>
                                        <td>
                                            <span class="fw-semibold">
                                                {{ $slider->title ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $slider->subtitle ? Str::limit($slider->subtitle, 50) : '-' }}
                                        </td>
                                        <td>
                                            @if ($slider->button_text)
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    {{ $slider->button_text }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($slider->button_url)
                                                <a href="{{ $slider->button_url }}" target="_blank"
                                                    class="text-primary small">
                                                    <i class="ti ti-external-link"></i>
                                                    {{ Str::limit($slider->button_url, 30) }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ $slider->sort_order }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($slider->status == 1)
                                                <span class="badge bg-success-subtle text-success">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{-- <div class="dropdown">
                                            <a class="dropdown-toggle text-primary opacity-50 arrow-none"
                                               href="#"
                                               data-bs-toggle="dropdown"
                                               aria-haspopup="true"
                                               aria-expanded="false">
                                                <i class="ti ti-dots f-16"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item customModal" href="#"
                                                   data-size="lg"
                                                   data-url="{{ route('master.slider.edit', encrypt($slider->id)) }}"
                                                   data-title="{{ __('Edit Slider') }}">
                                                    <i class="ti ti-edit"></i> {{ __('Edit') }}
                                                </a>
                                                <a href="{{ route('master.slider.destroy', encrypt($slider->id)) }}"
                                                   class="dropdown-item"
                                                   onclick="return confirm('Are you sure you want to delete this?')">
                                                    <i class="ti ti-trash"></i> {{ __('Delete') }}
                                                </a>
                                            </div>
                                        </div> --}}
                                            <div class="cart-action">
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['master.slider.destroy', encrypt($slider->id)],
                                                ]) !!}

                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg" title="{{ __('Edit') }}"
                                                    href="#"
                                                    data-url="{{ route('master.slider.edit', encrypt($slider->id)) }}"
                                                    data-title="{{ __('Edit Scope of Work') }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}" href="#">
                                                    <i data-feather="trash-2"></i>
                                                </a>

                                                {!! Form::close() !!}
                                            </div>
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

