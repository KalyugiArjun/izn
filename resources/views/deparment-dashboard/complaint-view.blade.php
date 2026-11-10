@extends('layouts.app')
@section('page-title')
    {{ __('Complaint Details') }}
@endsection

@section('content')
    <div class="row g-3 mt-1">

        {{-- Back Button --}}
        <div class="col-12">
            <a href="{{ route('all-complaints') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>{{ __('Back to All Complaints') }}
            </a>
        </div>

        {{-- Complaint Info Card --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        {{ __('Complaint') }}:
                        <span class="badge bg-light-secondary text-dark ms-1">{{ $complaint->complaint_num }}</span>
                    </h5>
                    <div class="d-flex gap-2">
                        @if ($complaint->is_safety_critical)
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>{{ __('Safety Critical') }}</span>
                        @endif
                        @if ($complaint->status == 0)
                            <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                        @elseif($complaint->status == 1)
                            <span class="badge bg-success">{{ __('Rectified') }}</span>
                        @elseif($complaint->status == 2)
                            <span class="badge bg-info">{{ __('Reopened') }}</span>
                        @elseif($complaint->status == 3)
                            <span class="badge bg-secondary">{{ __('Closed') }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">{{ __('Department') }}</label>
                            <p class="fw-semibold mb-0">{{ $complaint->department->name ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">{{ __('Station') }}</label>
                            <p class="fw-semibold mb-0">{{ $complaint->station->name ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">{{ __('Category') }}</label>
                            <p class="fw-semibold mb-0">{{ $complaint->amenityCategory->name ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">{{ __('Submitted On') }}</label>
                            <p class="fw-semibold mb-0">{{ $complaint->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        @if($complaint->timeline)
                        {{-- <div class="col-sm-6">
                            <label class="form-label text-muted small">{{ __('Timeline / Due Date') }}</label>
                            <p class="fw-semibold mb-0 {{ $complaint->timeline < now() && !in_array($complaint->status,[1,3]) ? 'text-danger' : '' }}">
                                {{ \Carbon\Carbon::parse($complaint->timeline)->format('d M Y') }}
                                @if($complaint->timeline < now() && !in_array($complaint->status,[1,3]))
                                    <span class="badge bg-danger ms-1">{{ __('Overdue') }}</span>
                                @endif
                            </p>
                        </div> --}}
                        @endif
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">{{ __('Assigned To') }}</label>
                            <p class="fw-semibold mb-0">{{ $complaint->assignedEmployee->name ?? '-' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">{{ __('Description') }}</label>
                            <p class="mb-0">{{ $complaint->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side Info --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Assignment Info') }}</h6>
                </div>
                <div class="card-body">
                    @if($complaint->assignedEmployee)
                        <div class="d-flex align-items-center gap-3">
                            <div class="avtar avtar-s bg-light-primary rounded-circle">
                                <i class="ti ti-user f-18 text-primary"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">{{ $complaint->assignedEmployee->name }}</p>
                                <small class="text-muted">{{ $complaint->assignedEmployee->designation->name ?? '' }}</small>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('Not yet assigned.') }}</p>
                        <a href="{{ route('complaints.assign', $complaint->id) }}"
                           class="btn btn-sm btn-success mt-3 w-100">
                            <i class="ti ti-user-plus me-1"></i>{{ __('Assign Now') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection

