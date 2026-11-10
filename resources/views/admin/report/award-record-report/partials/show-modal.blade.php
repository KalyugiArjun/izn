@php
    $formattedDate = $record->date ? \Carbon\Carbon::parse($record->date)->format('d M, Y') : ($record->created_at ? $record->created_at->format('d M, Y') : '-');
    $formattedTime = $record->time ? \Carbon\Carbon::parse($record->time)->format('h:i A') : ($record->created_at ? $record->created_at->format('h:i A') : '-');

    $nomineeName = $record->name ?: ($record->staff->name ?? 'N/A');
    $designationName = $record->designation->name ?? ($record->staff->designation->name ?? 'N/A');
    $departmentName = $record->staff->department->name ?? 'Operating Department';
    $stationName = $record->station->name ?? ($record->station->code ?? '-');

    $fileExt = strtolower(pathinfo($record->file ?? '', PATHINFO_EXTENSION));
    $isPdf = ($fileExt === 'pdf' || strtolower($record->file_type ?? '') === 'pdf');
    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);

    $statusMeta = match ((int)$record->status) {
        0 => ['text' => 'PENDING TI REVIEW', 'class' => 'bg-warning text-dark', 'icon' => 'ti-clock-hour-4'],
        1 => ['text' => 'RECOMMENDED BY TI', 'class' => 'bg-info text-white', 'icon' => 'ti-thumb-up'],
        2 => ['text' => 'AWARDED & APPROVED', 'class' => 'bg-success text-white', 'icon' => 'ti-award'],
        3 => ['text' => 'REJECTED', 'class' => 'bg-danger text-white', 'icon' => 'ti-circle-x'],
        default => ['text' => 'UNKNOWN', 'class' => 'bg-secondary text-white', 'icon' => 'ti-help']
    };
@endphp

{{-- Modal Header with Perfect Corner Alignment --}}
<div class="modal-header border-bottom px-4 py-3 d-flex align-items-center justify-content-between w-100" style="background: linear-gradient(135deg, #0b2545 0%, #134074 100%); color: #fff; border-top-left-radius: 14px; border-top-right-radius: 14px;">
    {{-- Left: Icon + Title + Nominee Meta Info --}}
    <div class="d-flex align-items-center gap-3 flex-grow-1 overflow-hidden me-3">
        <div class="bg-white bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
            <i class="ti ti-trophy fs-3 text-white"></i>
        </div>
        <div class="text-truncate">
            <h5 class="modal-title fw-bold text-white mb-1 d-flex align-items-center gap-2 text-truncate">
                <span>{{ __('MODULE 5: AWARD RECORD DOSSIER') }} - #{{ $record->id }}</span>
            </h5>
            <div class="d-flex align-items-center flex-wrap gap-2 text-white-50 small">
                <span><i class="ti ti-user me-1 text-white"></i><strong>{{ $nomineeName }}</strong> ({{ $designationName }})</span>
                <span>•</span>
                <span><i class="ti ti-building me-1 text-white"></i>{{ $stationName }}</span>
                <span>•</span>
                <span><i class="ti ti-calendar me-1 text-white"></i>{{ $formattedDate }} at {{ $formattedTime }}</span>
            </div>
        </div>
    </div>

    {{-- Right: Status Badge + Corner Close Button --}}
    <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
        <span class="badge {{ $statusMeta['class'] }} px-3 py-2 fw-semibold shadow-sm">
            <i class="ti {{ $statusMeta['icon'] }} me-1"></i>{{ $statusMeta['text'] }}
        </span>

        @if(!empty($record->certificate_no))
            <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 fw-semibold shadow-sm font-12">
                <i class="ti ti-certificate me-1"></i>{{ $record->certificate_no }}
            </span>
        @endif

        {{-- Top Right Corner Close Button --}}
        <button type="button" class="btn btn-sm btn-icon rounded-circle ms-2 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width: 36px; height: 36px; min-width: 36px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            <i class="ti ti-x fs-4"></i>
        </button>
    </div>
</div>

{{-- Modal Body --}}
<div class="modal-body p-4" style="background-color: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">

    {{-- SECTION 1: NOMINEE INFORMATION --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #0b2545; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">1</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Nominee & Station Details') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Official Particulars') }}</span>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Nominee Full Name') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $nomineeName }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('USMID / Employee ID') }}</label>
                    <div class="fw-semibold text-dark">{{ $record->staff->usmid ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Designation') }}</label>
                    <div class="fw-semibold text-dark">
                        <span class="badge bg-light text-primary border">{{ $designationName }}</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Department') }}</label>
                    <div class="fw-semibold text-dark">{{ $departmentName }}</div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Station / Posting') }}</label>
                    <div class="fw-bold text-dark">
                        <i class="ti ti-map-pin text-danger me-1"></i>{{ $stationName }}
                        @if($record->station && $record->station->code)
                            <span class="text-muted small">({{ $record->station->code }})</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Nomination Date & Time') }}</label>
                    <div class="fw-semibold text-dark">
                        <i class="ti ti-calendar me-1 text-primary"></i>{{ $formattedDate }}
                        <span class="text-muted ms-1"><i class="ti ti-clock me-1"></i>{{ $formattedTime }}</span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Area of Proficiency') }}</label>
                    <div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold fs-6">
                            <i class="ti ti-star me-1"></i>{{ $record->proficiency->name ?? 'General' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: MERIT / PROFICIENCY DESCRIPTION & ATTACHMENT --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #134074; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">2</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Nomination Justification & Uploaded Media') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Evidence') }}</span>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="row g-3">
                <div class="col-12">
                    <label class="text-muted small mb-1 fw-semibold">{{ __('Description / Reason for Nomination') }}</label>
                    <div class="p-3 bg-light rounded border text-dark" style="white-space: pre-wrap; line-height: 1.6; font-size: 14px;">{{ $record->description ?: 'No detailed description provided.' }}</div>
                </div>

                {{-- Uploaded Document / Image Preview --}}
                <div class="col-12 mt-3">
                    <label class="text-muted small mb-1 fw-semibold">{{ __('Uploaded PDF / JPG Attachment') }}</label>
                    @if($record->file)
                        <div class="d-flex flex-wrap align-items-center gap-3 p-3 border rounded bg-light">
                            @if($isImage)
                                <div class="border rounded p-1 bg-white shadow-sm" style="max-width: 200px;">
                                    <a href="{{ asset('storage/' . $record->file) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $record->file) }}" alt="Evidence Image" class="img-fluid rounded" style="max-height: 150px; object-fit: contain;">
                                    </a>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ __('Attachment Image (JPG/PNG)') }}</h6>
                                    <p class="text-muted small mb-2">{{ basename($record->file) }}</p>
                                    <a href="{{ asset('storage/' . $record->file) }}" target="_blank" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                        <i class="ti ti-external-link"></i> {{ __('View Full Image') }}
                                    </a>
                                    <a href="{{ asset('storage/' . $record->file) }}" download class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 ms-1">
                                        <i class="ti ti-download"></i> {{ __('Download') }}
                                    </a>
                                </div>
                            @elseif($isPdf)
                                <div class="p-3 bg-white rounded border d-flex align-items-center gap-3 shadow-sm">
                                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded">
                                        <i class="ti ti-file-text fs-2"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('Attached Nomination PDF') }}</h6>
                                        <p class="text-muted small mb-2">{{ basename($record->file) }}</p>
                                        <a href="{{ asset('storage/' . $record->file) }}" target="_blank" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1">
                                            <i class="ti ti-eye"></i> {{ __('Open PDF') }}
                                        </a>
                                        <a href="{{ asset('storage/' . $record->file) }}" download class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 ms-1">
                                            <i class="ti ti-download"></i> {{ __('Download PDF') }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="p-2">
                                    <a href="{{ asset('storage/' . $record->file) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                        <i class="ti ti-file me-1"></i>{{ __('Download Attachment (:file)', ['file' => basename($record->file)]) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-3 bg-light rounded text-center text-muted">
                            <i class="ti ti-file-off fs-3 mb-1 d-block text-secondary"></i>
                            <span class="small">{{ __('No file uploaded with this nomination.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: DECISION & APPROVAL WORKFLOW --}}
    <div class="card border-0 shadow-sm mb-2" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #0b2545; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">3</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('TI Recommendation & Sr. DOM Decision') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Audit Trail') }}</span>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="row g-3">

                {{-- TI Recommendation --}}
                <div class="col-md-6 border-end-md">
                    <div class="p-3 rounded bg-light border h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1">
                                <i class="ti ti-user-check text-info"></i> {{ __('Traffic Inspector (TI) Review') }}
                            </h6>
                            @if($record->ti_forward_date)
                                <span class="badge bg-info-subtle text-info border border-info-subtle font-11">
                                    {{ \Carbon\Carbon::parse($record->ti_forward_date)->format('d M, Y') }} {{ $record->ti_forward_time ? \Carbon\Carbon::parse($record->ti_forward_time)->format('h:i A') : '' }}
                                </span>
                            @endif
                        </div>
                        <div class="small mb-2">
                            <strong>{{ __('Forwarded By:') }}</strong>
                            <span class="text-dark">{{ $record->ti->name ?? ($record->ti_id ? 'TI #' . $record->ti_id : 'Pending') }}</span>
                        </div>
                        <div class="small">
                            <strong>{{ __('TI Remarks:') }}</strong>
                            <div class="mt-1 p-2 bg-white rounded border text-muted">
                                {{ $record->ti_comment ?: 'No remarks recorded.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sr. DOM Decision --}}
                <div class="col-md-6">
                    <div class="p-3 rounded bg-light border h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1">
                                <i class="ti ti-shield-check text-primary"></i> {{ __('Sr. DOM Final Decision') }}
                            </h6>
                            @if($record->checked_date)
                                <span class="badge {{ $record->status == 2 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} font-11">
                                    {{ \Carbon\Carbon::parse($record->checked_date)->format('d M, Y') }} {{ $record->checked_time ? \Carbon\Carbon::parse($record->checked_time)->format('h:i A') : '' }}
                                </span>
                            @endif
                        </div>
                        <div class="small mb-2">
                            <strong>{{ __('Decided By:') }}</strong>
                            <span class="text-dark">{{ $record->srdom->name ?? ($record->sr_dom_id ? 'Sr. DOM #' . $record->sr_dom_id : 'Pending') }}</span>
                        </div>
                        <div class="small mb-2">
                            <strong>{{ __('Sr. DOM Remarks / Cash Award:') }}</strong>
                            <div class="mt-1 p-2 bg-white rounded border fw-semibold {{ $record->status == 2 ? 'text-success' : 'text-danger' }}">
                                {{ $record->dom_comment ?: 'No remarks recorded.' }}
                            </div>
                        </div>

                        {{-- Certificate Link inside Decision box --}}
                        @if($record->status == 2 && !empty($record->certificate_no))
                            <div class="mt-3 p-2 bg-success bg-opacity-10 rounded border border-success d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-certificate fs-3 text-success"></i>
                                    <div>
                                        <div class="fw-bold text-success font-12">{{ __('Certificate Generated') }}</div>
                                        <div class="small text-muted">{{ $record->certificate_no }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('report.award-record.certificate', $record->id) }}" target="_blank" class="btn btn-sm btn-success fw-bold">
                                    <i class="ti ti-printer me-1"></i>{{ __('View / Print') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Modal Footer --}}
<div class="modal-footer bg-white border-top px-4 py-3 d-flex align-items-center justify-content-between">
    <div class="small text-muted">
        <i class="ti ti-info-circle me-1"></i>{{ __('Module 5 - Saathi Safety & Operations Monitoring System') }}
    </div>

    <div class="d-flex align-items-center gap-2">
        @if($record->status == 2 && !empty($record->certificate_no))
            <a href="{{ route('report.award-record.certificate', $record->id) }}" target="_blank" class="btn btn-success fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                <i class="ti ti-certificate fs-5"></i>{{ __('View Certificate') }}
            </a>
        @endif

        <a href="{{ route('report.award-record.export.nomination-pdf', $record->id) }}" target="_blank" class="btn btn-outline-danger fw-semibold d-inline-flex align-items-center gap-1">
            <i class="ti ti-file-text fs-5"></i>{{ __('Download Dossier PDF') }}
        </a>

        <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">
            {{ __('Close') }}
        </button>
    </div>
</div>
