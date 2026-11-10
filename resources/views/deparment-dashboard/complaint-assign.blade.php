@extends('layouts.app')
@section('page-title')
    {{ __('Assign Complaint') }}
@endsection

@section('content')
<div class="row g-3 mt-1">

    {{-- Back --}}
    <div class="col-12">
        <a href="{{ route('all-complaints') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>{{ __('Back to All Complaints') }}
        </a>
    </div>

    {{-- Complaint Summary Banner --}}
    <div class="col-12">
        <div class="card border-start border-4 border-primary mb-0">
            <div class="card-body py-3">
                <div class="row align-items-center g-2">
                    <div class="col-auto">
                        <span class="badge bg-light-secondary text-dark fs-6 px-3 py-2">
                            {{ $complaint->complaint_num }}
                        </span>
                    </div>
                    <div class="col">
                        <div class="d-flex flex-wrap gap-3 text-muted small">
                            <span><i class="ti ti-building me-1"></i>{{ $complaint->station->name ?? '-' }}</span>
                            <span><i class="ti ti-tag me-1"></i>{{ $complaint->amenityCategory->name ?? '-' }}</span>
                            <span><i class="ti ti-calendar me-1"></i>{{ $complaint->created_at->format('d M Y') }}</span>
                        </div>
                        {{-- <p class="mb-0 mt-1 text-dark">{{ Str::limit($complaint->description, 120) }}</p> --}}
                    </div>
                    <div class="col-auto d-flex flex-column gap-1 align-items-end">
                        @if ($complaint->is_safety_critical)
                            <span class="badge bg-danger px-3 py-2">
                                <i class="ti ti-alert-triangle me-1"></i>{{ __('Safety Critical') }}
                            </span>
                        @endif
                        @if ($complaint->status == 0)
                            <span class="badge bg-warning text-dark px-3 py-2">{{ __('Pending') }}</span>
                        @elseif($complaint->status == 1)
                            <span class="badge bg-success px-3 py-2">{{ __('Rectified') }}</span>
                        @elseif($complaint->status == 2)
                            <span class="badge bg-info px-3 py-2">{{ __('Reopened') }}</span>
                        @elseif($complaint->status == 3)
                            <span class="badge bg-secondary px-3 py-2">{{ __('Closed') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    {{-- @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif --}}

    {{-- Main Card --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-0">{{ __('Select Employee to Assign') }}</h5>
                    <p class="text-muted small mb-0 mt-1">
                        <i class="ti ti-info-circle me-1"></i>
                        {{ __('Only employees from your department are listed. Click a card to select, then click Assign.') }}
                    </p>
                </div>
                <div class="position-relative" style="min-width:260px;">
                    <i class="ti ti-search position-absolute top-50 translate-middle-y ms-3 text-muted" style="z-index:1;"></i>
                    <input type="text" id="employeeSearch" class="form-control ps-5"
                           placeholder="{{ __('Search name or designation...') }}">
                </div>
            </div>

            <div class="card-body">

                <form action="{{ route('complaints.assign.store', $complaint->id) }}" method="POST" id="assignForm">
                    @csrf
                    <input type="hidden" name="employee_id" id="selectedEmployeeId" value="">

                    @if($employees->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="ti ti-users-off f-48 d-block mb-3 opacity-50"></i>
                            <p class="mb-0">{{ __('No active employees found in your department.') }}</p>
                        </div>
                    @else

                        <div class="row g-3" id="employeeList">
                            @foreach($employees as $employee)
                                <div class="col-xl-3 col-lg-4 col-md-6 col-12 employee-item"
                                     data-name="{{ strtolower($employee->name) }}"
                                     data-designation="{{ strtolower($employee->designation->name ?? '') }}">

                                    <div class="emp-card rounded-3 p-3 h-100 position-relative
                                            {{ $complaint->assigned_employee_id == $employee->id ? 'emp-selected' : '' }}"
                                         data-id="{{ $employee->id }}"
                                         data-name="{{ $employee->name }}"
                                         data-designation="{{ $employee->designation->name ?? '' }}">

                                        {{-- Check mark top-right --}}
                                        <span class="emp-check-icon position-absolute top-0 end-0 mt-2 me-2
                                            {{ $complaint->assigned_employee_id == $employee->id ? '' : 'd-none' }}">
                                            <i class="ti ti-circle-check-filled text-success f-22"></i>
                                        </span>

                                        <div class="d-flex align-items-start gap-3">
                                            <div class="emp-avatar rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-user f-20 text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="mb-0 fw-semibold text-truncate">{{ $employee->name }}</p>
                                                <small class="text-muted d-block text-truncate">
                                                    {{ $employee->designation->name ?? '-' }}
                                                </small>
                                                @if($employee->mobile)
                                                    <small class="text-muted d-flex align-items-center gap-1 mt-1">
                                                        <i class="ti ti-phone f-11"></i> {{ $employee->mobile }}
                                                    </small>
                                                @endif
                                                @if($employee->email)
                                                    <small class="text-muted d-flex align-items-center gap-1 text-truncate">
                                                        <i class="ti ti-mail f-11"></i> {{ $employee->email }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>

                                        @if($complaint->assigned_employee_id == $employee->id)
                                            <div class="mt-2 pt-2 border-top border-success-subtle">
                                                <span class="small text-success fw-semibold">
                                                    <i class="ti ti-check me-1"></i>{{ __('Currently Assigned') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <div id="noResults" class="text-center text-muted py-4 d-none">
                            <i class="ti ti-search-off f-36 d-block mb-2 opacity-50"></i>
                            {{ __('No employees match your search.') }}
                        </div>

                        @error('employee_id')
                            <p class="text-danger small mt-3 mb-0">
                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                            </p>
                        @enderror

                    @endif
                </form>

            </div>

            {{-- Sticky footer --}}
            @if(!$employees->isEmpty())
            <div class="card-footer bg-light border-top">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                    {{-- Selected employee preview --}}
                    <div id="selectedPreview" class="d-flex align-items-center gap-2">
                        <div id="previewAvatar" class="emp-avatar-sm rounded-circle d-flex align-items-center justify-content-center bg-light border d-none">
                            <i class="ti ti-user f-14 text-muted"></i>
                        </div>
                        <span id="previewText" class="text-muted small">
                            <i class="ti ti-hand-click me-1"></i>{{ __('Click an employee card to select') }}
                        </span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('all-complaints') }}" class="btn btn-outline-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" form="assignForm" id="assignBtn"
                                class="btn btn-success px-4" disabled>
                            <i class="ti ti-user-check me-2"></i>{{ __('Assign Complaint') }}
                        </button>
                    </div>

                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection


<style>
    /* Employee card base */
    .emp-card {
        border: 2px solid #e9ecef;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }
    .emp-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 14px rgba(13,110,253,0.12);
        transform: translateY(-2px);
    }
    /* Selected state */
    .emp-card.emp-selected {
        border-color: #198754 !important;
        background: #f0fff6;
        box-shadow: 0 0 0 3px rgba(25,135,84,0.18);
    }
    /* Avatar circle */
    .emp-avatar {
        width: 44px;
        height: 44px;
        background: #e8f0fe;
        flex-shrink: 0;
    }
    .emp-avatar-sm {
        width: 32px;
        height: 32px;
    }
    /* Pulsing assign button when active */
    #assignBtn:not(:disabled) {
        animation: pulse-success 1.8s ease-in-out infinite;
    }
    @keyframes pulse-success {
        0%, 100% { box-shadow: 0 0 0 0 rgba(25,135,84,0.45); }
        60%       { box-shadow: 0 0 0 7px rgba(25,135,84,0); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const cards         = document.querySelectorAll('.emp-card');
    const hiddenInput   = document.getElementById('selectedEmployeeId');
    const assignBtn     = document.getElementById('assignBtn');
    const previewText   = document.getElementById('previewText');
    const previewAvatar = document.getElementById('previewAvatar');
    const searchInput   = document.getElementById('employeeSearch');

    // Pre-select if already assigned
    const preSelected = document.querySelector('.emp-card.emp-selected');
    if (preSelected) {
        applySelection(preSelected);
    }

    // Click selection
    cards.forEach(function (card) {
        card.addEventListener('click', function () {
            // Clear all
            cards.forEach(function (c) {
                c.classList.remove('emp-selected');
                const icon = c.querySelector('.emp-check-icon');
                if (icon) icon.classList.add('d-none');
            });
            // Apply to clicked
            applySelection(this);
        });
    });

    function applySelection(card) {
        card.classList.add('emp-selected');

        const icon = card.querySelector('.emp-check-icon');
        if (icon) icon.classList.remove('d-none');

        const id    = card.dataset.id;
        const name  = card.dataset.name;
        const desig = card.dataset.designation;

        hiddenInput.value  = id;
        assignBtn.disabled = false;

        // Footer preview
        previewAvatar.classList.remove('d-none');
        previewText.innerHTML =
            `<span class="fw-semibold text-dark">${name}</span>` +
            (desig ? ` <span class="text-muted">&mdash; ${desig}</span>` : '');
    }

    // Live search
    searchInput.addEventListener('input', function () {
        const q     = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('.employee-item');
        let visible = 0;

        items.forEach(function (item) {
            const match =
                item.dataset.name.includes(q) ||
                item.dataset.designation.includes(q);

            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('noResults').classList.toggle('d-none', visible > 0);
    });

});
</script>


