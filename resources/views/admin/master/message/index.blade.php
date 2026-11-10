@extends('layouts.app')

@section('page-title', __('Messages'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Messages') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Message List') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.message.create') }}" data-title="{{ __('Create Message') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Message') }}
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
                                    <th>{{ __('Name / Title') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Message Preview') }}</th>
                                    <th>{{ __('Sort') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($messages as $index => $msg)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($msg->image_url)
                                                <img src="{{ $msg->image_url }}" alt="{{ $msg->title }}"
                                                    class="rounded-circle border"
                                                    style="width:52px;height:52px;object-fit:cover;">
                                            @else
                                                <div class="avatar-letter bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                    style="width:52px;height:52px;font-size:1.3rem;">
                                                    {{ strtoupper(substr($msg->title ?? 'M', 0, 1)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $msg->title ?? '-' }}</span>
                                        </td>
                                        <td>{{ $msg->designation ?? '-' }}</td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ Str::limit(strip_tags($msg->message), 80) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ $msg->sort_order }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($msg->status == 1)
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
                                                   data-url="{{ route('master.message.edit', encrypt($msg->id)) }}"
                                                   data-title="{{ __('Edit Message') }}">
                                                    <i class="ti ti-edit"></i> {{ __('Edit') }}
                                                </a>
                                                <a href="{{ route('master.message.destroy', encrypt($msg->id)) }}"
                                                   class="dropdown-item"
                                                   onclick="return confirm('Are you sure you want to delete this?')">
                                                    <i class="ti ti-trash"></i> {{ __('Delete') }}
                                                </a>
                                            </div>
                                        </div> --}}
                                            <div class="cart-action">
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['master.message.destroy', encrypt($msg->id)],
                                                ]) !!}

                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg" title="{{ __('Edit') }}"
                                                    href="#"
                                                     data-url="{{ route('master.message.edit', encrypt($msg->id)) }}"
                                                    data-title="{{ __('Edit Message') }}">
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

