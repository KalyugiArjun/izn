@extends('layouts.app')

@section('page-title', __('Ambush Check (BA Test) Visibility'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Staff Visibility') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Ambush Check (BA Test)') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Ambush Check (BA Test) Visibility List') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="md"
                                data-url="{{ route('staff-visibility.ambush-ba-test.create') }}"
                                data-title="{{ __('Create Ambush Check (BA Test) Visibility') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Ambush Check (BA Test)') }}
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
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->department->name ?? '-' }}</td>
                                        <td>{{ $item->designation->name ?? '-' }}</td>
                                        <td class="text-end">
                                            <div class="cart-action">
                                                {{-- Form for delete --}}
                                                {{ Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['staff-visibility.ambush-ba-test.destroy', encrypt($item->id)],
                                                ]) }}

                                                {{-- Edit --}}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('staff-visibility.ambush-ba-test.edit', encrypt($item->id)) }}"
                                                    data-title="{{ __('Edit Ambush Check (BA Test) Visibility') }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                {{-- Delete --}}
                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"
                                                    href="#">
                                                    <i data-feather="trash-2"></i>
                                                </a>

                                                {{ Form::close() }}
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
