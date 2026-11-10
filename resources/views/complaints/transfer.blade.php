@extends('layouts.app')

@section('page-title')
    {{ __('Transfer Complaint') }}
@endsection

@section('content')
    <div class="row justify-content-center mt-3">
        <div class="col-lg-7">

            {{-- Complaint Summary Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Complaint Details') }}</h5>
                    <span class="badge bg-light-secondary text-dark fs-6">
                        {{ $complaint->complaint_num }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <p class="text-muted mb-1 small">{{ __('Department') }}</p>
                            <p class="fw-semibold mb-0">{{ $complaint->department->name ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1 small">{{ __('Station') }}</p>
                            <p class="fw-semibold mb-0">{{ $complaint->station->name ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1 small">{{ __('Category') }}</p>
                            <p class="fw-semibold mb-0">{{ $complaint->amenityCategory->name ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1 small">{{ __('Current Status') }}</p>
                            <p class="mb-0">
                                @if ($complaint->status == 0)
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($complaint->status == 1)
                                    <span class="badge bg-success">Rectified</span>
                                @elseif($complaint->status == 2)
                                    <span class="badge bg-info">Reopened</span>
                                @elseif($complaint->status == 3)
                                    <span class="badge bg-secondary">Closed</span>
                                @elseif($complaint->status == 4)
                                    <span class="badge bg-primary">Assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1 small">{{ __('Complaint Date') }}</p>
                            <p class="fw-semibold mb-0">{{ $complaint->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1 small">{{ __('Safety') }}</p>
                            <p class="mb-0">
                                @if ($complaint->is_safety_critical)
                                    <span class="badge bg-danger">Critical</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transfer Form Card --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Transfer To') }}</h5>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('complaints.transfer.submit', $complaint->id) }}" method="POST">
                        @csrf

                        {{-- Department --}}
                        <div class="mb-3">
                            <label for="department_id" class="form-label fw-semibold">
                                {{ __('Select Department') }} <span class="text-danger">*</span>
                            </label>
                            <select name="department_id" id="department_id"
                                class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">- Select Department -</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Designation (loaded via AJAX) --}}
                        <div class="mb-4">
                            <label for="assigned_to" class="form-label fw-semibold">
                                {{ __('Select Assign') }} <span class="text-danger">*</span>
                            </label>
                            <select name="assigned_to" id="assigned_to"
                                class="form-select @error('assigned_to') is-invalid @enderror" disabled>
                                <option value="">- Select Assign First -</option>
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-send me-1"></i> {{ __('Transfer Complaint') }}
                            </button>
                            <a href="{{ route('all-complaints') }}" class="btn btn-outline-secondary px-4">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const deptEl = document.getElementById('department_id');
        const designationSelect = document.getElementById('assigned_to');

        if (!deptEl) {
            console.log("department_id not found ❌");
            return;
        }

        deptEl.addEventListener('change', function() {

            console.log("this is working");

            const deptId = this.value;

            designationSelect.innerHTML = '<option value="">Loading...</option>';
            designationSelect.disabled = true;

            if (!deptId) {
                designationSelect.innerHTML = '<option value="">- Select Department First -</option>';
                return;
            }
            const BASE_URL = "{{ url('/api') }}";

            fetch(`${BASE_URL}/assign-to?department_id=${deptId}`)
                .then(res => res.json())
                .then(response => {
                    console.log(response); // 🔥 debug

                    designationSelect.innerHTML =
                        '<option value="">- Select Designation -</option>';

                    if (response.status && response.data.length > 0) {
                        response.data.forEach(item => {
                            designationSelect.innerHTML +=
                                `<option value="${item.id}">${item.name}</option>`;
                        });
                        designationSelect.disabled = false;
                    } else {
                        designationSelect.innerHTML =
                            '<option value="">No designations found</option>';
                    }
                })
                .catch(error => {
                    console.log(error);
                    designationSelect.innerHTML = '<option value="">Failed to load</option>';
                });
        });

    });
</script>

