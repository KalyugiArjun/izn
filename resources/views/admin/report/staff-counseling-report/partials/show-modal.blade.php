@php
    $formattedDate = $record->counseling_date
        ? \Carbon\Carbon::parse($record->counseling_date)->format('d M, Y')
        : ($record->created_at ? $record->created_at->format('d M, Y') : '-');
    $formattedTime = $record->counseling_time ?: ($record->created_at ? $record->created_at->format('h:i A') : '-');

    $mode = strtolower($record->counseling_mode ?? '');
    $isOnline = str_contains($mode, 'online');
    $loc = strtolower($record->counseling_location ?? '');
    $staffList = $record->resolved_staff_list;
@endphp

{{-- Modal Header with Perfect Corner Alignment --}}
<div class="modal-header border-bottom px-4 py-3 d-flex align-items-center justify-content-between w-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-top-left-radius: 14px; border-top-right-radius: 14px;">
    {{-- Left: Icon + Title + Counselor Meta Info --}}
    <div class="d-flex align-items-center gap-3 flex-grow-1 overflow-hidden me-3">
        <div class="bg-white bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
            <i class="ti ti-users fs-3 text-white"></i>
        </div>
        <div class="text-truncate">
            <h5 class="modal-title fw-bold text-white mb-1 d-flex align-items-center gap-2 text-truncate">
                <span>{{ __('MODULE 4: STAFF COUNSELING RECORD') }} - #{{ $record->id }}</span>
            </h5>
            <div class="d-flex align-items-center flex-wrap gap-2 text-white-50 small">
                <span><i class="ti ti-user me-1 text-white"></i><strong>{{ $record->counselor_name ?: '-' }}</strong> ({{ $record->counselor_designation_name }})</span>
                <span>•</span>
                <span><i class="ti ti-calendar me-1 text-white"></i>{{ $formattedDate }} at {{ $formattedTime }}</span>
                <span>•</span>
                <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded">{{ strtoupper($record->counseling_mode ?: 'OFFLINE') }}</span>
            </div>
        </div>
    </div>

    {{-- Right: Status Badges + Corner Close Button --}}
    <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
        @if(strcasecmp($record->counseling_type, 'Group') === 0)
            <span class="badge bg-warning text-dark px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-users me-1"></i>GROUP</span>
        @else
            <span class="badge bg-info text-white px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-user me-1"></i>INDIVIDUAL</span>
        @endif

        @if($isOnline)
            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 fw-semibold shadow-sm">ONLINE</span>
        @else
            <span class="badge bg-light text-dark px-3 py-2 fw-semibold shadow-sm">{{ ucfirst($record->counseling_location ?: 'Station') }}</span>
        @endif

        {{-- Corner Close Button --}}
        <button type="button" class="btn btn-sm btn-icon rounded-circle ms-2 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width: 36px; height: 36px; min-width: 36px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            <i class="ti ti-x fs-4"></i>
        </button>
    </div>
</div>

{{-- Modal Body --}}
<div class="modal-body p-4" style="background-color: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">

    {{-- SECTION 1: COUNSELOR & BASIC OVERVIEW --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">1</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Counselor & Session Details') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">Overview</span>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Counselor Name') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $record->counselor_name ?: '-' }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Counselor Designation') }}</label>
                    <div class="fw-semibold text-dark">{{ $record->counselor_designation_name }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Date of Counseling') }}</label>
                    <div class="fw-semibold text-dark">{{ $formattedDate }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Time of Counseling') }}</label>
                    <div class="fw-semibold text-dark">{{ $formattedTime }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Mode of Counseling') }}</label>
                    <div>
                        <span class="badge {{ $isOnline ? 'bg-info text-white' : 'bg-primary text-white' }} px-2 py-1">
                            {{ strtoupper($record->counseling_mode ?: '-') }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Counseling Type') }}</label>
                    <div>
                        <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">
                            {{ $record->counseling_type ?: 'Individual' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Location Type') }}</label>
                    <div class="fw-semibold text-dark">{{ ucfirst($record->counseling_location ?: ($isOnline ? 'Online / Remote' : '-')) }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('GPS Location') }}</label>
                    <div>
                        @if($record->gps_location)
                            <a href="https://maps.google.com/?q={{ $record->gps_location }}" target="_blank" class="text-primary text-decoration-none small fw-semibold">
                                <i class="ti ti-map-pin me-1"></i>{{ $record->gps_location }}
                            </a>
                        @else
                            <span class="text-muted small">{{ __('Not recorded') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: COUNSELING STAFF (TABLE OF COUNSELED EMPLOYEES) --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #0f766e; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">2</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Counseled Staff Details') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ count($staffList) }} {{ __('Staff Member(s)') }}</span>
        </div>
        <div class="card-body p-0 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Staff Name') }}</th>
                            <th>{{ __('Designation') }}</th>
                            <th>{{ __('Station / Posting') }}</th>
                            <th>{{ __('Employee Code / ID') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffList as $idx => $st)
                            <tr>
                                <td class="fw-semibold text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $st['staff_name'] }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        {{ $st['designation_name'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark">{{ $st['station_name'] }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $st['employee_code'] ?: ('#' . $st['staff_id']) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="ti ti-user-x fs-2 d-block mb-1 text-muted"></i>
                                    {{ __('No staff members mapped to this record.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SECTION 3: SPECIFIC DETAILS CAPTURED (ONLINE / OFFLINE LOGIC) --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #0369a1; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">3</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Location & Specific Parameters Captured') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">Module 4 Specification</span>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="row g-3">
                @if($isOnline)
                    {{-- Online Mode Details --}}
                    <div class="col-md-6">
                        <label class="text-muted small mb-0">{{ __('Mode of Counseling') }}</label>
                        <div class="fw-bold text-info"><i class="ti ti-device-laptop me-1"></i>ONLINE</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-0">{{ __('Calling Channel / Mode') }}</label>
                        <div class="fw-semibold text-dark">{{ $record->calling_mode ?: '-' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small mb-0">{{ __('Location') }}</label>
                        <div class="text-muted">— (Online Remote Session)</div>
                    </div>
                @else
                    {{-- Offline Mode Details (Station / Diesel Lobby / Running Train) --}}
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Offline Venue') }}</label>
                        <div class="fw-bold text-primary">{{ ucfirst($record->counseling_location ?: 'Station') }}</div>
                    </div>

                    @if($loc === 'station')
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small mb-0">{{ __('Station Name') }}</label>
                            <div class="fw-bold text-dark">{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small mb-0">{{ __('LC Gate No') }}</label>
                            <div class="fw-semibold text-dark">{{ $record->lc_gate ?: '-' }}</div>
                        </div>
                    @elseif($loc === 'diesel lobby')
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small mb-0">{{ __('Diesel Lobby Name') }}</label>
                            <div class="fw-bold text-dark">{{ $record->dieselLobby ? $record->dieselLobby->name : '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small mb-0">{{ __('Counseling Type') }}</label>
                            <div class="fw-semibold text-dark">{{ $record->counseling_type ?: 'Individual' }}</div>
                        </div>
                    @elseif($loc === 'running train')
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small mb-0">{{ __('Train No') }}</label>
                            <div class="fw-bold text-dark">{{ $record->train_no ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small mb-0">{{ __('Lobby Name / Base') }}</label>
                            <div class="fw-semibold text-dark">{{ $record->dieselLobby ? $record->dieselLobby->name : '-' }}</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- SECTION 4: SUBJECT OF COUNSELING --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #334155; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">4</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Subject of Counseling & Instructions') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">Content</span>
        </div>
        <div class="card-body p-4 bg-white">
            <div class="p-3 bg-light rounded border border-light-subtle">
                <p class="mb-0 text-dark fs-6" style="white-space: pre-line; line-height: 1.6;">
                    {{ $record->subject_of_counseling ?: __('No subject details recorded.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- SECTION 5: ATTACHED PHOTO / EVIDENCE (IF ANY) --}}
    @if($record->image)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between bg-dark text-white">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">5</span>
                    <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Attached Photo / Evidence') }}</h6>
                </div>
            </div>
            <div class="card-body p-3 bg-white text-center">
                <a href="{{ asset('storage/' . $record->image) }}" target="_blank">
                    <img src="{{ asset('storage/' . $record->image) }}" class="img-fluid rounded border shadow-sm" style="max-height: 280px;" alt="Counseling Evidence">
                </a>
                <div class="small text-muted mt-2">{{ __('Click to view full image') }}</div>
            </div>
        </div>
    @endif

    {{-- SECTION 6: SYSTEM AUDIT & TIMESTAMPS --}}
    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <div class="card-body p-3 bg-light">
            <div class="row g-3 small text-muted">
                <div class="col-md-4 col-sm-6">
                    <div><i class="ti ti-calendar me-1"></i>{{ __('Record Created') }}: <strong>{{ $record->created_at ? $record->created_at->format('d M, Y h:i:s A') : '-' }}</strong></div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div><i class="ti ti-clock me-1"></i>{{ __('Last Updated') }}: <strong>{{ $record->updated_at ? $record->updated_at->format('d M, Y h:i:s A') : '-' }}</strong></div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div><i class="ti ti-fingerprint me-1"></i>{{ __('System ID') }}: <strong>#{{ $record->id }}</strong></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Footer --}}
<div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        <i class="ti ti-info-circle me-1"></i>{{ __('Generated from Saathi Staff Counseling System (Module 4)') }}
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="ti ti-printer me-1"></i>{{ __('Print') }}
        </button>
        <a href="{{ route('report.staff-counseling.export.counseling-pdf', $record->id) }}" class="btn btn-outline-danger" target="_blank">
            <i class="ti ti-file-text me-1"></i>{{ __('Download Dossier PDF') }}
        </a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
            {{ __('Close') }}
        </button>
    </div>
</div>

