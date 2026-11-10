@extends('layouts.app')
@section('page-title')
    {{ __('Order') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            {{ __('Order') }}
        </li>
    </ul>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/simplemde.min.css') }}">

    <style>
        .pc-kanban-wrapper {
            display: flex;
            gap: 10px;
            overflow-x: auto;
        }

        .pc-kanban-column {
            width: 250px;
            background: #f8f9fa;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
        }

        .pc-kanban-body {
            flex: 1;
            padding: 10px;
            max-height: 450px;
            /* Required for SimpleBar */
        }

        .pc-kanban-cards {
            min-height: 50px;
        }

        .pc-kanban-cards .card {
            cursor: pointer;
        }

        .pc-kanban-cards .card.dragging {
            opacity: 0.8;
            cursor: pointer;
        }

        .card.border {
            margin-bottom: 10px;
            transition: background 0.2s;
        }

        .card.border:hover {
            background: #f1f3f5;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Order List') }}</h5>
                </div>
                <div class="card-body">
                    <div class="pc-kanban-wrapper">
                        @foreach ($statuses as $statusKey => $statusLabel)
                            @php $ordersByStatus = $kanbanData[$statusKey]; @endphp
                            <div class="pc-kanban-column">
                                <div class="pc-kanban-header">
                                    <div class="flex-grow-1 p-2">
                                        <strong>{{ ucfirst($statusLabel) }} ({{ count($ordersByStatus) }})</strong>
                                    </div>
                                </div>

                                <div class="pc-kanban-body" data-simplebar>
                                    <div class="pc-kanban-cards" id="status-{{ $statusKey }}"
                                        data-status="{{ $statusKey }}">
                                        @foreach ($ordersByStatus as $order)
                                            <div class="card border" data-orderId="{{ $order->id }}">
                                                <div class="card-body px-3 py-3">
                                                    <div class="float-end">
                                                        <div class="dropdown">
                                                            <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none"
                                                                href="#" data-bs-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                <i class="ti ti-dots f-18"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end drop-kanban">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('order.edit', encrypt($order->id)) }}">
                                                                    <i data-feather="edit"></i>{{ __('Edit') }}
                                                                </a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('order.show', encrypt($order->id)) }}">
                                                                    <i data-feather="eye"></i>{{ __('Details') }}
                                                                </a>
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['order.destroy', encrypt($order->id)]]) !!}
                                                                <a class="dropdown-item confirm_dialog" href="#">
                                                                    <i data-feather="trash-2"></i>{{ __('Delete') }}
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h5 class="mb-1 text-truncate">
                                                        <a href="{{ route('order.show', $order->id) }}">
                                                            {{ orderPrefix() . $order->order_id }}
                                                        </a>
                                                    </h5>
                                                    <div class="text-sm mt-1">
                                                        <i
                                                            class="material-icons-two-tone text-secondary f-16 me-1">person</i>
                                                        <span>{{ __('Customer') }} :</span>
                                                        {{ !empty($order->customers) ? $order->customers->name : '-' }}
                                                    </div>
                                                    <div class="text-sm mt-1">
                                                        <i
                                                            class="material-icons-two-tone text-secondary f-16 me-1">calendar_today</i>
                                                        <span>{{ __('Order Date') }} :</span>
                                                        {{ dateFormat($order->order_date) }}
                                                    </div>
                                                    <div class="text-sm mt-1">
                                                        <i
                                                            class="material-icons-two-tone text-secondary f-16 me-1">calendar_today</i>
                                                        <span>{{ __('Deadline date') }} :</span>
                                                        {{ dateFormat($order->deadline_date) }}
                                                    </div>
                                                    <div class="text-sm mt-1">
                                                        <i class="material-icons-two-tone text-secondary f-16 me-1">wc</i>
                                                        <span>{{ __('Gender') }} :</span>
                                                        {{ $order->gender }}
                                                    </div>
                                                    <div class="text-sm mt-1">
                                                        <i
                                                            class="material-icons-two-tone text-secondary f-16 me-1">checkroom</i>
                                                        <span>{{ __('Cloth Type') }} :</span>
                                                        {{ !empty($order->clothTypes) ? $order->clothTypes->title : '-' }}
                                                    </div>
                                                    <div class="text-sm mt-1">
                                                        <i
                                                            class="material-icons-two-tone text-secondary f-16 me-1">person2</i>
                                                        <span>{{ __('Responsible') }} :</span>
                                                        {{ !empty($order->users) ? $order->users->name : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var containers = Array.from(document.querySelectorAll('.pc-kanban-cards'));

            @can(abilities: 'update order status')
            var drake = dragula(containers, {
                moves: function(el, source, handle, sibling) {
                    return !el.classList.contains('dragging');
                }
            });

                drake.on('drop', function(el, target, source, sibling) {
                    if (!target || !target.classList.contains('pc-kanban-cards')) {
                        drake.cancel();
                        toastrs("Error!", "Invalid target column.", "error");
                        return;
                    }

                    el.classList.add('dragging');

                    let orderId = el.getAttribute('data-orderId');
                    let status = target.getAttribute('data-status');
                    if (orderId && status) {
                        $.ajax({
                            url: '{{ route('order.status.update') }}',
                            type: 'POST',
                            data: {
                                orderId: orderId,
                                status: status,
                                "_token": $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                if (data.error) {
                                    drake.cancel();
                                    toastrs("Error!", data.error, "error");
                                    el.classList.remove('dragging');
                                    return;
                                }
                                toastrs("Success!", "Order updated successfully.", "success");
                            },
                            error: function(xhr) {
                                drake.cancel();
                                let message = xhr.responseJSON && xhr.responseJSON.error ?
                                    xhr.responseJSON.error :
                                    "An error occurred while updating the order.";
                                toastrs("Error!", message, "error");
                                el.classList.remove('dragging');
                            }
                        });
                    } else {
                        drake.cancel();
                        toastrs("Error!", "Invalid order or status.", "error");
                        el.classList.remove('dragging');
                    }
                });
            @endcan
        });
    </script>
@endpush

