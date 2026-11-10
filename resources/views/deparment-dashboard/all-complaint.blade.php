@extends('layouts.app')
@section('page-title')
    {{ __('All Complaints') }}
@endsection


@section('content')
    <div class="row">
        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5>{{ __('All Complaints') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="dt-responsive table-responsive">
                            <table class="table table-hover advance-datatable mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Complaint No.') }}</th>
                                        <th>{{ __('Department') }}</th>
                                        <th>{{ __('Station') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        {{-- <th>{{ __('Description') }}</th> --}}
                                        <th>{{ __('Safety') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Complaint Date') }}</th>
                                        <th>{{ __('Assigned To') }}</th>
                                        <th>{{ __('Assigned At') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result['recentComplaints'] as $i => $complaint)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <span class="badge bg-light-secondary text-dark">
                                                    {{ $complaint->complaint_num }}
                                                </span>
                                            </td>
                                            <td>{{ $complaint->department->name ?? '-' }}</td>
                                            <td>{{ $complaint->station->name ?? '-' }}</td>
                                            <td>{{ $complaint->amenityCategory->name ?? '-' }}</td>
                                            {{-- <td>
                                                <span title="{{ $complaint->description }}">
                                                    {{ Str::limit($complaint->description, 40) }}
                                                </span>
                                            </td> --}}
                                            <td>
                                                @if ($complaint->is_safety_critical)
                                                    <span class="badge bg-danger">{{ __('Critical') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($complaint->status == 0)
                                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                                @elseif($complaint->status == 1)
                                                    <span class="badge bg-success">{{ __('Rectified') }}</span>
                                                @elseif($complaint->status == 2)
                                                    <span class="badge bg-info">{{ __('Reopened') }}</span>
                                                @elseif($complaint->status == 3)
                                                    <span class="badge bg-secondary">{{ __('Closed') }}</span>
                                                @elseif($complaint->status == 4)
                                                    <span class="badge bg-primary">{{ __('Assigned') }}</span>
                                                @elseif($complaint->status == 5)
                                                    <span class="badge bg-dark">{{ __('Reassigned') }}</span>
                                                @elseif($complaint->status == 6)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="ti-exchange-horizontal"></i> {{ __('Transferred') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $complaint->created_at->format('d M Y') }}</td>
                                            <td>
                                                @if ($complaint->assignedEmployee)
                                                    <span class="badge bg-success">
                                                        {{ $complaint->assignedEmployee->name }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark">
                                                        Not Assigned
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $complaint->assigned_employee_at
                                                    ? \Carbon\Carbon::parse($complaint->assigned_employee_at)->format('d M Y, h:i A')
                                                    : '-' }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    {{-- View --}}
                                                    <a href="{{ route('complaints.view', $complaint->id) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="{{ __('View Complaint') }}">
                                                        <i class="ti ti-eye"></i>
                                                    </a>

                                                    {{-- Assign --}}
                                                    <a href="{{ route('complaints.assign', $complaint->id) }}"
                                                        class="btn btn-sm btn-outline-success"
                                                        title="{{ __('Assign Complaint') }}">
                                                        <i class="ti ti-user-plus"></i>

                                                    </a>

                                                    {{-- Transfer --}}
                                                    @if ($complaint->status != 6)
                                                        <a href="{{ route('complaints.transfer', $complaint->id) }}"
                                                            class="btn btn-sm btn-outline-warning"
                                                            title="{{ __('Transfer Complaint') }}">
                                                            {{-- ✅ FIXED --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                class="size-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                                                            </svg>

                                                        </a>
                                                    @endif
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
    </div>
@endsection

