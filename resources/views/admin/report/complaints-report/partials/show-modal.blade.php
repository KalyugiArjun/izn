@php
    $regDate = $complaint->created_at ? $complaint->created_at->format('d M, Y') : '-';
    $regTime = $complaint->created_at ? $complaint->created_at->format('h:i A') : '-';

    $assignDate = $complaint->assigned_employee_at ? \Carbon\Carbon::parse($complaint->assigned_employee_at)->format('d M, Y') : '-';
    $assignTime = $complaint->assigned_employee_at ? \Carbon\Carbon::parse($complaint->assigned_employee_at)->format('h:i A') : '-';

    $rectDate = $complaint->rectified_at ? \Carbon\Carbon::parse($complaint->rectified_at)->format('d M, Y') : ($complaint->rectification_date ? \Carbon\Carbon::parse($complaint->rectification_date)->format('d M, Y') : '-');
    $rectTime = $complaint->rectified_at ? \Carbon\Carbon::parse($complaint->rectified_at)->format('h:i A') : '-';

    $isClosed = ($complaint->status == 3);
    $closedDate = ($isClosed && $complaint->feedback_given_at) ? \Carbon\Carbon::parse($complaint->feedback_given_at)->format('d M, Y') : ($isClosed ? \Carbon\Carbon::parse($complaint->updated_at)->format('d M, Y') : '-');
    $closedTime = ($isClosed && $complaint->feedback_given_at) ? \Carbon\Carbon::parse($complaint->feedback_given_at)->format('h:i A') : ($isClosed ? \Carbon\Carbon::parse($complaint->updated_at)->format('h:i A') : '-');

    $isReopened = ($complaint->status == 2) || !empty($complaint->reopened_at) || !empty($complaint->reopen_reason);
    $reopenDate = $complaint->reopened_at ? \Carbon\Carbon::parse($complaint->reopened_at)->format('d M, Y') : '-';
    $reopenTime = $complaint->reopened_at ? \Carbon\Carbon::parse($complaint->reopened_at)->format('h:i A') : '-';

    $isReRectified = $complaint->reopened_at && $complaint->rectified_at && ($complaint->rectified_at > $complaint->reopened_at);
    $reRectDate = $isReRectified ? \Carbon\Carbon::parse($complaint->rectified_at)->format('d M, Y') : '-';
    $reRectTime = $isReRectified ? \Carbon\Carbon::parse($complaint->rectified_at)->format('h:i A') : '-';

    $transfer = $complaint->transferredComplaints ? $complaint->transferredComplaints->first() : null;
@endphp

{{-- Modal Header --}}
<div class="modal-header border-bottom px-4 py-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-top-left-radius: 14px; border-top-right-radius: 14px;">
    <div class="d-flex align-items-center gap-2">
        <div class="bg-white bg-opacity-25 p-2 rounded-circle me-1">
            <i class="ti ti-file-analytics fs-3 text-white"></i>
        </div>
        <div>
            <h5 class="modal-title fw-bold text-white mb-0">
                {{ __('Station Complaints Records Dossier') }} - {{ $complaint->complaint_num }}
            </h5>
            <small class="text-white-50">
                <i class="ti ti-map-pin me-1"></i>{{ $complaint->station->name ?? 'N/A' }} ({{ $complaint->station->code ?? '-' }}) | 
                <i class="ti ti-building me-1"></i>{{ $complaint->department->name ?? 'N/A' }} | 
                <i class="ti ti-calendar me-1"></i>{{ $regDate }} {{ $regTime }}
            </small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if ($complaint->is_safety_critical)
            <span class="badge bg-danger px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-alert-triangle me-1"></i>{{ __('CRITICAL') }}</span>
        @else
            <span class="badge bg-light text-dark px-3 py-2 fw-semibold">{{ __('Non-Critical') }}</span>
        @endif

        @if ($complaint->status == 0)
            <span class="badge bg-warning text-dark px-3 py-2 fw-semibold">{{ __('PENDING') }}</span>
        @elseif ($complaint->status == 1)
            <span class="badge bg-success px-3 py-2 fw-semibold">{{ __('RECTIFIED') }}</span>
        @elseif ($complaint->status == 2)
            <span class="badge bg-info text-white px-3 py-2 fw-semibold">{{ __('REOPENED') }}</span>
        @elseif ($complaint->status == 3)
            <span class="badge bg-secondary px-3 py-2 fw-semibold">{{ __('CLOSED') }}</span>
        @elseif ($complaint->status == 4)
            <span class="badge bg-primary px-3 py-2 fw-semibold">{{ __('ASSIGNED') }}</span>
        @elseif ($complaint->status == 5)
            <span class="badge bg-dark px-3 py-2 fw-semibold">{{ __('REASSIGNED') }}</span>
        @elseif ($complaint->status == 6)
            <span class="badge bg-warning text-dark px-3 py-2 fw-semibold">{{ __('TRANSFERRED') }}</span>
        @endif

        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
</div>

{{-- Modal Body --}}
<div class="modal-body p-4" style="background-color: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">

    {{-- ========================================================================= --}}
    {{-- MODULE 1: REGISTER COMPLAINT                                              --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">1</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Module 1: Register Complaint') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Initial Registration') }}</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Complaint Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Complaint Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $regDate }}</div>
                    <div class="text-primary small"><i class="ti ti-clock me-1"></i>{{ $regTime }}</div>
                </div>

                {{-- Station --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Station') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $complaint->station->name ?? 'N/A' }}</div>
                    <div class="text-muted small">{{ __('Code:') }} <span class="badge bg-light text-dark border">{{ $complaint->station->code ?? '-' }}</span></div>
                </div>

                {{-- Department --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Department') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $complaint->department->name ?? 'N/A' }}</div>
                </div>

                {{-- Complaint Type / Amenity Category --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Complaint Type') }}</label>
                    <span class="badge bg-light-primary text-primary px-2 py-1 fw-semibold">{{ $complaint->amenityCategory->name ?? 'General' }}</span>
                </div>

                {{-- Rectification Timeline --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Rectification Timeline') }}</label>
                    <div class="fw-bold text-dark">
                        @if($complaint->timeline)
                            <span class="badge bg-info text-white"><i class="ti ti-hourglass-empty me-1"></i>{{ $complaint->timeline }} {{ __('Hours') }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                {{-- Critical / Non-Critical --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Safety Classification') }}</label>
                    <div>
                        @if ($complaint->is_safety_critical)
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>{{ __('Critical (Safety Critical)') }}</span>
                        @else
                            <span class="badge bg-light text-muted border">{{ __('Non-Critical') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Forwarded To (Designation) --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Forwarded To (Designation)') }}</label>
                    <div class="fw-bold text-dark">
                        {{ $complaint->forwardedToDesignation->name ?? ($complaint->assigned_to ?? '-') }}
                    </div>
                </div>

                {{-- Transferred Department --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Transferred Department') }}</label>
                    <div>
                        @if($transfer && $transfer->toDepartment)
                            <span class="badge bg-warning text-dark"><i class="ti ti-arrow-right me-1"></i>{{ $transfer->toDepartment->name }}</span>
                            @if($transfer->reason)
                                <div class="small text-muted font-11 mt-1">{{ __('Reason:') }} {{ $transfer->reason }}</div>
                            @endif
                        @else
                            <span class="text-muted">{{ __('None') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Issue / Description --}}
                <div class="col-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Issue Description') }}</label>
                    <div class="p-3 bg-white border rounded-2 text-dark" style="min-height: 48px; line-height: 1.6;">
                        {{ $complaint->description ?? __('No description provided.') }}
                    </div>
                </div>

                {{-- Complaint Photos / Attachments --}}
                @if(!empty($complaint->images) && is_array($complaint->images) && count($complaint->images) > 0)
                    <div class="col-12">
                        <label class="text-muted small fw-semibold d-block mb-2">{{ __('Complaint Attachment Photos') }} ({{ count($complaint->images) }})</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($complaint->images as $img)
                                <a href="{{ asset(Storage::url($img)) }}" target="_blank" class="border rounded p-1 bg-white shadow-sm d-inline-block text-center" style="width: 90px; height: 90px; overflow: hidden; position: relative;">
                                    <img src="{{ asset(Storage::url($img)) }}" alt="Complaint Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;" onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODULE 2: ASSIGN, RECTIFY AND CLOSE                                       --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #0d6efd; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">2</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Module 2: Assign, Rectify and Close') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Resolution Lifecycle') }}</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Complaint Assigned Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Complaint Assigned: Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $assignDate }}</div>
                    <div class="text-primary small"><i class="ti ti-clock me-1"></i>{{ $assignTime }}</div>
                </div>

                {{-- Assigned To: Name & Designation --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Assigned To: Name') }}</label>
                    <div class="fw-bold text-dark fs-6">
                        {{ $complaint->assignedEmployee->name ?? '-' }}
                    </div>
                    <div class="text-muted small">
                        {{ __('Designation:') }} <strong>{{ $complaint->assignedEmployee->designation->name ?? '-' }}</strong>
                    </div>
                </div>

                {{-- Complaint Rectified: Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Complaint Rectified: Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $rectDate }}</div>
                    <div class="text-success small"><i class="ti ti-clock me-1"></i>{{ $rectTime }}</div>
                </div>

                {{-- Complaint Closed: Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Complaint Closed: Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $closedDate }}</div>
                    <div class="text-secondary small"><i class="ti ti-clock me-1"></i>{{ $closedTime }}</div>
                </div>

                {{-- Rectifier Details --}}
                <div class="col-md-6 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Rectifier Personnel Details') }}</label>
                    <div class="p-2 bg-light rounded border">
                        <div class="fw-bold text-dark">{{ $complaint->rectifier_name ?? '-' }}</div>
                        <div class="small text-muted">{{ __('Designation:') }} {{ $complaint->rectifier_designation ?? '-' }}</div>
                    </div>
                </div>

                {{-- Rectification Remark --}}
                <div class="col-md-6 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Rectification Remark') }}</label>
                    <div class="p-2 bg-white rounded border text-dark" style="min-height: 48px;">
                        {{ $complaint->rectification_remark ?? __('No rectification remark entered.') }}
                    </div>
                </div>

                {{-- Rectification Photos --}}
                @if(!empty($complaint->rectification_images) && is_array($complaint->rectification_images) && count($complaint->rectification_images) > 0)
                    <div class="col-12">
                        <label class="text-muted small fw-semibold d-block mb-2">{{ __('Rectification Proof Photos') }} ({{ count($complaint->rectification_images) }})</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($complaint->rectification_images as $rImg)
                                <a href="{{ asset(Storage::url($rImg)) }}" target="_blank" class="border rounded p-1 bg-white shadow-sm d-inline-block text-center" style="width: 90px; height: 90px; overflow: hidden; position: relative;">
                                    <img src="{{ asset(Storage::url($rImg)) }}" alt="Rectification Proof" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;" onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODULE 3: RE-OPEN, RE-RECTIFY AND CLOSE                                   --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-0" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #2b3a4a; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">3</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Module 3: Re-open, Re-rectify and Close') }}</h6>
            </div>
            @if($isReopened)
                <span class="badge bg-danger text-white small"><i class="ti ti-refresh me-1"></i>{{ __('Reopen Triggered') }}</span>
            @else
                <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Not Reopened') }}</span>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Complaint Re-open: Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Complaint Re-open: Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $reopenDate }}</div>
                    <div class="text-danger small"><i class="ti ti-clock me-1"></i>{{ $reopenTime }}</div>
                </div>

                {{-- Reopened By --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Reopened By') }}</label>
                    <div class="fw-bold text-dark fs-6">
                        {{ $complaint->reopenedByUser->name ?? '-' }}
                    </div>
                </div>

                {{-- Complaint Re-rectified: Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Complaint Re-rectified: Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $reRectDate }}</div>
                    <div class="text-success small"><i class="ti ti-clock me-1"></i>{{ $reRectTime }}</div>
                </div>

                {{-- Complaint Closed: Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Final Complaint Closed: Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $closedDate }}</div>
                    <div class="text-secondary small"><i class="ti ti-clock me-1"></i>{{ $closedTime }}</div>
                </div>

                {{-- Re-Remark (Reopen Reason) --}}
                <div class="col-md-6 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Re-Remark (Reopen Reason)') }}</label>
                    <div class="p-2 bg-white rounded border text-danger" style="min-height: 48px;">
                        {{ $complaint->reopen_reason ?? __('No reopen remarks recorded.') }}
                    </div>
                </div>

                {{-- Feedback / User Rating & Remarks --}}
                <div class="col-md-6 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Feedback Details & Rating') }}</label>
                    <div class="p-2 bg-white rounded border">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            @if($complaint->feedback_rating)
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $complaint->feedback_rating)
                                            <i class="ti ti-star-filled text-warning"></i>
                                        @else
                                            <i class="ti ti-star text-muted"></i>
                                        @endif
                                    @endfor
                                    <span class="fw-bold ms-1">({{ $complaint->feedback_rating }}/5)</span>
                                </div>
                            @else
                                <span class="text-muted small">{{ __('No rating given') }}</span>
                            @endif
                        </div>
                        <div class="small text-dark">{{ $complaint->feedback_description ?? '-' }}</div>
                    </div>
                </div>

                {{-- Feedback Images --}}
                @if(!empty($complaint->feedback_images) && is_array($complaint->feedback_images) && count($complaint->feedback_images) > 0)
                    <div class="col-12">
                        <label class="text-muted small fw-semibold d-block mb-2">{{ __('Feedback / Reopen Attachment Photos') }} ({{ count($complaint->feedback_images) }})</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($complaint->feedback_images as $fImg)
                                <a href="{{ asset(Storage::url($fImg)) }}" target="_blank" class="border rounded p-1 bg-white shadow-sm d-inline-block text-center" style="width: 90px; height: 90px; overflow: hidden; position: relative;">
                                    <img src="{{ asset(Storage::url($fImg)) }}" alt="Feedback Photo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;" onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Modal Footer --}}
<div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        <i class="ti ti-info-circle me-1"></i>{{ __('Generated from Station Complaints Records System') }}
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="ti ti-printer me-1"></i>{{ __('Print') }}
        </button>
        <a href="{{ route('report.complaints.export.complaint-pdf', $complaint->id) }}" class="btn btn-outline-danger" target="_blank">
            <i class="ti ti-file-text me-1"></i>{{ __('Download Dossier PDF') }}
        </a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
            {{ __('Close') }}
        </button>
    </div>
</div>

