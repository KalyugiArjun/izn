@extends('layouts.app')
@section('page-title')
    {{ __('Attend Block') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Attend Block') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Attend Block List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create Type of Inspection')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.attend_block.create') }}"
                                data-title="{{ __('Create Attend Block') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Attend Block') }}
                            </a>
                        </div>
                        {{-- @endif --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Attend Block Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit Attend Block') || Gate::check('delete Attend Block')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attend_blocks as $index => $attend_block)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $attend_block->name }}</td>
                                        <td>
                                            @if ($attend_block->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        {{-- @if (Gate::check('edit Attend Block') || Gate::check('delete Attend Block')) --}}
                                        <td class="text-end">
                                            <div class="cart-action">

                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.attend_block.destroy', encrypt($attend_block->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.attend_block.edit', encrypt($attend_block->id)) }}"
                                                    data-title="{{ __('Edit Attend Block') }}"> <i
                                                        data-feather="edit"></i></a>


                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"
                                                    href="#"> <i data-feather="trash-2"></i></a>

                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                        {{-- @endif --}}

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

