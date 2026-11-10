<div class="modal-body p-4">
    {{-- Header Banner --}}
    <div class="card border-0 mb-4 bg-light-primary rounded-3">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <span class="text-uppercase text-muted small fw-bold d-block">Award Nomination #{{ $nomination->id }}</span>
                    <h5 class="fw-bold mb-0 text-primary">{{ $nomination->name ?? ($nomination->staff->name ?? 'Staff') }}</h5>
                    <small class="text-muted">USMID: {{ $nomination->staff->usmid ?? 'N/A' }} | Mobile: {{ $nomination->staff->mobile ?? 'N/A' }}</small>
                </div>
                <div class="text-end">
                    @if($nomination->status == 0)
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="ti ti-clock me-1"></i>Pending</span>
                    @elseif($nomination->status == 1)
                        <span class="badge bg-info text-white fs-6 px-3 py-2"><i class="ti ti-clipboard-check me-1"></i>TI Recommended</span>
                    @elseif($nomination->status == 2)
                        <span class="badge bg-success text-white fs-6 px-3 py-2"><i class="ti ti-certificate me-1"></i>Awarded</span>
                        @if($nomination->certificate_no)
                            <div class="fw-bold font-monospace text-success mt-1 small">{{ $nomination->certificate_no }}</div>
                        @endif
                    @elseif($nomination->status == 3)
                        <span class="badge bg-danger text-white fs-6 px-3 py-2"><i class="ti ti-x me-1"></i>Rejected</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Step 1: Staff Submission --}}
    <div class="card border mb-3 shadow-none">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center">
            <div class="avtar avtar-xs bg-light-primary text-primary rounded-circle me-2">1</div>
            <strong class="text-dark">Staff Submission & Merit Details</strong>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-sm-4">
                    <small class="text-muted d-block">Station / Location</small>
                    <strong class="text-dark">{{ $nomination->station->name ?? '-' }}</strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">Designation</small>
                    <strong class="text-dark">{{ $nomination->designation->name ?? '-' }}</strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">Date & Time</small>
                    <strong class="text-dark">
                        {{ $nomination->date ? \Carbon\Carbon::parse($nomination->date)->format('d M, Y') : '-' }}
                        {{ $nomination->time ? \Carbon\Carbon::parse($nomination->time)->format('h:i A') : '' }}
                    </strong>
                </div>

                <div class="col-sm-6">
                    <small class="text-muted d-block">Area of Proficiency</small>
                    <span class="badge bg-primary text-white mt-1">{{ $nomination->proficiency->name ?? '-' }}</span>
                </div>

                <div class="col-sm-6">
                    <small class="text-muted d-block">Supporting Evidence</small>
                    @if(!empty($nomination->file))
                        <a href="{{ asset('storage/' . $nomination->file) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-1">
                            <i class="ti ti-download me-1"></i> Download / View Attachment ({{ strtoupper($nomination->file_type ?? 'File') }})
                        </a>
                    @else
                        <span class="text-muted small">No file uploaded</span>
                    @endif
                </div>

                <div class="col-12">
                    <small class="text-muted d-block mb-1">Reason / Description</small>
                    <div class="p-3 bg-light rounded-2 border text-dark font-monospace small">
                        {{ $nomination->description }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Traffic Inspector (TI) Review --}}
    <div class="card border mb-3 shadow-none">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center">
            <div class="avtar avtar-xs bg-light-info text-info rounded-circle me-2">2</div>
            <strong class="text-dark">Traffic Inspector (TI) Review</strong>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Assigned TI</small>
                    <strong class="text-dark">{{ $nomination->ti->name ?? ($nomination->ti_id ? 'TI ID: ' . $nomination->ti_id : 'Not Assigned') }}</strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Forwarded Date & Time</small>
                    <strong class="text-dark">
                        {{ $nomination->ti_forward_date ? \Carbon\Carbon::parse($nomination->ti_forward_date)->format('d M, Y') : '-' }}
                        {{ $nomination->ti_forward_time ? \Carbon\Carbon::parse($nomination->ti_forward_time)->format('h:i A') : '' }}
                    </strong>
                </div>
                <div class="col-12">
                    <small class="text-muted d-block mb-1">TI Remarks</small>
                    <div class="p-2 bg-light rounded-2 border text-dark">
                        {{ $nomination->ti_comment ?: 'No remarks recorded yet.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 3: Sr. DOM Approval & Award --}}
    <div class="card border mb-0 shadow-none">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center">
            <div class="avtar avtar-xs bg-light-success text-success rounded-circle me-2">3</div>
            <strong class="text-dark">Sr. Divisional Operations Manager (Sr. DOM) Decision</strong>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-sm-4">
                    <small class="text-muted d-block">Sr. DOM Official</small>
                    <strong class="text-dark">{{ $nomination->srdom->name ?? ($nomination->sr_dom_id ? 'Sr. DOM ID: ' . $nomination->sr_dom_id : '-') }}</strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">Decision Date & Time</small>
                    <strong class="text-dark">
                        {{ $nomination->checked_date ? \Carbon\Carbon::parse($nomination->checked_date)->format('d M, Y') : '-' }}
                        {{ $nomination->checked_time ? \Carbon\Carbon::parse($nomination->checked_time)->format('h:i A') : '' }}
                    </strong>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">Certificate Number</small>
                    <strong class="text-success font-monospace">{{ $nomination->certificate_no ?? 'Pending Award' }}</strong>
                </div>
                <div class="col-12">
                    <small class="text-muted d-block mb-1">Sr. DOM Comments / Award Order</small>
                    <div class="p-2 bg-light rounded-2 border text-dark">
                        {{ $nomination->dom_comment ?: 'No comments recorded yet.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer bg-light px-4 py-3">
    @if($nomination->status == 2)
        <a href="{{ route('authority.award-nomination.nominate.certificate', encrypt($nomination->id)) }}" target="_blank" class="btn btn-success rounded-pill px-3">
            <i class="ti ti-printer me-1"></i> {{ __('Print Certificate') }}
        </a>
    @endif
    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>

