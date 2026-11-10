@php
    $staffName = $employee ? $employee->name : 'N/A';
    $desigName = $employee && $employee->designation ? $employee->designation->name : 'N/A';
    $st = $employee && $employee->stations->isNotEmpty() ? $employee->stations->first() : null;
    $stationName = $st ? ($st->name . ' (' . $st->code . ')') : '-';
    $usmid = $employee ? $employee->usmid : 'N/A';
    $phone = $employee ? $employee->mobile : '-';
    $dept = $employee && $employee->department ? $employee->department->name : 'Operating Department';

    $typeLower = strtolower($type);
@endphp

{{-- Modal Header with Perfect Corner Alignment --}}
<div class="modal-header border-bottom px-4 py-3 d-flex align-items-center justify-content-between w-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-top-left-radius: 14px; border-top-right-radius: 14px;">
    {{-- Left: Icon + Title + Meta Info --}}
    <div class="d-flex align-items-center gap-3 flex-grow-1 overflow-hidden me-3">
        <div class="bg-white bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
            <i class="ti ti-shield-check fs-3 text-white"></i>
        </div>
        <div class="text-truncate">
            <h5 class="modal-title fw-bold text-white mb-1 d-flex align-items-center gap-2 text-truncate">
                <span>{{ __('MODULE 6: EMPLOYEE SAFETY RECORD') }} - {{ strtoupper($type) }}</span>
            </h5>
            <div class="d-flex align-items-center flex-wrap gap-2 text-white-50 small">
                <span><i class="ti ti-user me-1 text-white"></i><strong>{{ $staffName }}</strong> ({{ $desigName }})</span>
                <span>•</span>
                <span><i class="ti ti-map-pin me-1 text-white"></i>{{ $stationName }}</span>
                <span>•</span>
                <span><i class="ti ti-id me-1 text-white"></i>USMID: {{ $usmid }}</span>
            </div>
        </div>
    </div>

    {{-- Right: Type Badge + Corner Close Button --}}
    <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
        <span class="badge bg-white text-dark px-3 py-2 fw-bold shadow-sm font-12">
            {{ strtoupper($type) }}
        </span>

        {{-- Top Right Corner Close Button --}}
        <button type="button" class="btn btn-sm btn-icon rounded-circle ms-2 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width: 36px; height: 36px; min-width: 36px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            <i class="ti ti-x fs-4"></i>
        </button>
    </div>
</div>

{{-- Modal Body --}}
<div class="modal-body p-4" style="background-color: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">

    {{-- SECTION 1: EMPLOYEE PARTICULARS --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">1</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Employee Particulars') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">Profile</span>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Staff Full Name') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $staffName }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('USMID / Staff ID') }}</label>
                    <div class="fw-semibold text-dark">{{ $usmid }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Designation') }}</label>
                    <div class="fw-semibold text-dark">
                        <span class="badge bg-light text-primary border">{{ $desigName }}</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Station / Posting') }}</label>
                    <div class="fw-bold text-dark">
                        <i class="ti ti-map-pin text-danger me-1"></i>{{ $stationName }}
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Department') }}</label>
                    <div class="fw-semibold text-dark">{{ $dept }}</div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small mb-0">{{ __('Mobile Number') }}</label>
                    <div class="fw-semibold text-dark">{{ $phone }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: CURRENT RECORD DETAILS --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #2a5298; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">2</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Safety Record Details - :type', ['type' => $type]) }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">Exam / Assessment</span>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="row g-3">
                @if($typeLower === 'medical')
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Last PME Date') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->last_pme_date ? \Carbon\Carbon::parse($record->last_pme_date)->format('d M, Y') : '-' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Next Due Date') }}</label>
                        <div class="fw-bold text-primary fs-6">{{ $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date)->format('d M, Y') : '-' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Medical Certificate No') }}</label>
                        <div class="fw-bold text-dark">{{ $record->certificate_no ?: '-' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('PME Status') }}</label>
                        <div>
                            @php $due = \Carbon\Carbon::parse($record->next_due_date ?? null); @endphp
                            @if(empty($record->next_due_date))
                                <span class="badge bg-secondary">No Due Date</span>
                            @elseif($due->isPast())
                                <span class="badge bg-danger">OVERDUE</span>
                            @else
                                <span class="badge bg-success">VALID</span>
                            @endif
                        </div>
                    </div>

                @elseif($typeLower === 'refresher')
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Last Refresher Date') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->last_refresher_date ? \Carbon\Carbon::parse($record->last_refresher_date)->format('d M, Y') : '-' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Next Due Date') }}</label>
                        <div class="fw-bold text-primary fs-6">{{ $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date)->format('d M, Y') : '-' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Course Certificate No') }}</label>
                        <div class="fw-bold text-dark">{{ $record->certificate_no ?: '-' }}</div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Training Institution') }}</label>
                        <div class="fw-semibold text-dark">{{ $record->institution_name ?: '-' }}</div>
                    </div>

                @elseif($typeLower === 'gradation')
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Gradation Date') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->gradation_date ? \Carbon\Carbon::parse($record->gradation_date)->format('d M, Y') : '-' }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Total Marks (out of 100)') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->total_marks !== null ? number_format($record->total_marks, 2) : '-' }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small mb-0">{{ __('Awarded Grade') }}</label>
                        <div>
                            <span class="badge bg-primary px-3 py-2 fw-bold fs-6">Grade: {{ $record->grade ?: '-' }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SECTION 3: COMPLETE SAFETY HISTORY FOR THIS EMPLOYEE --}}
    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">3</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Complete Safety History for :name', ['name' => $staffName]) }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">Historical Track</span>
        </div>
        <div class="card-body p-3 bg-white">

            {{-- Nav Tabs --}}
            <ul class="nav nav-tabs mb-3" id="safetyHistoryTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $typeLower === 'medical' ? 'active fw-bold' : '' }}" id="pme-tab" data-bs-toggle="tab" data-bs-target="#pme-tab-pane" type="button" role="tab">
                        <i class="ti ti-stethoscope me-1 text-info"></i> Medical / PME ({{ $pmeHistory->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $typeLower === 'refresher' ? 'active fw-bold' : '' }}" id="ref-tab" data-bs-toggle="tab" data-bs-target="#ref-tab-pane" type="button" role="tab">
                        <i class="ti ti-refresh me-1 text-success"></i> Refresher Courses ({{ $refHistory->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $typeLower === 'gradation' ? 'active fw-bold' : '' }}" id="grad-tab" data-bs-toggle="tab" data-bs-target="#grad-tab-pane" type="button" role="tab">
                        <i class="ti ti-chart-bar me-1 text-warning"></i> Gradations ({{ $gradHistory->count() }})
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="safetyHistoryTabContent">
                {{-- Medical / PME History --}}
                <div class="tab-pane fade {{ $typeLower === 'medical' ? 'show active' : '' }}" id="pme-tab-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>#</th>
                                    <th>Last PME Date</th>
                                    <th>Next Due Date</th>
                                    <th>Certificate No</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pmeHistory as $pIdx => $p)
                                    @php $pDue = \Carbon\Carbon::parse($p->next_due_date ?? null); @endphp
                                    <tr>
                                        <td>{{ $pIdx + 1 }}</td>
                                        <td>{{ $p->last_pme_date ? \Carbon\Carbon::parse($p->last_pme_date)->format('d M, Y') : '-' }}</td>
                                        <td class="fw-bold">{{ $p->next_due_date ? \Carbon\Carbon::parse($p->next_due_date)->format('d M, Y') : '-' }}</td>
                                        <td>{{ $p->certificate_no ?: '-' }}</td>
                                        <td>
                                            @if(empty($p->next_due_date))
                                                <span class="badge bg-secondary">No Due Date</span>
                                            @elseif($pDue->isPast())
                                                <span class="badge bg-danger">OVERDUE</span>
                                            @else
                                                <span class="badge bg-success">VALID</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No PME records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Refresher Course History --}}
                <div class="tab-pane fade {{ $typeLower === 'refresher' ? 'show active' : '' }}" id="ref-tab-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>#</th>
                                    <th>Last Course Date</th>
                                    <th>Next Due Date</th>
                                    <th>Certificate No</th>
                                    <th>Institution</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refHistory as $rIdx => $r)
                                    @php $rDue = \Carbon\Carbon::parse($r->next_due_date ?? null); @endphp
                                    <tr>
                                        <td>{{ $rIdx + 1 }}</td>
                                        <td>{{ $r->last_refresher_date ? \Carbon\Carbon::parse($r->last_refresher_date)->format('d M, Y') : '-' }}</td>
                                        <td class="fw-bold">{{ $r->next_due_date ? \Carbon\Carbon::parse($r->next_due_date)->format('d M, Y') : '-' }}</td>
                                        <td>{{ $r->certificate_no ?: '-' }}</td>
                                        <td>{{ $r->institution_name ?: '-' }}</td>
                                        <td>
                                            @if(empty($r->next_due_date))
                                                <span class="badge bg-secondary">No Due Date</span>
                                            @elseif($rDue->isPast())
                                                <span class="badge bg-danger">OVERDUE</span>
                                            @else
                                                <span class="badge bg-success">VALID</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">No Refresher Course records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Gradation History --}}
                <div class="tab-pane fade {{ $typeLower === 'gradation' ? 'show active' : '' }}" id="grad-tab-pane" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>#</th>
                                    <th>Gradation Date</th>
                                    <th>Total Marks</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gradHistory as $gIdx => $g)
                                    <tr>
                                        <td>{{ $gIdx + 1 }}</td>
                                        <td>{{ $g->gradation_date ? \Carbon\Carbon::parse($g->gradation_date)->format('d M, Y') : '-' }}</td>
                                        <td class="fw-bold">{{ $g->total_marks !== null ? number_format($g->total_marks, 2) : '-' }} / 100</td>
                                        <td><span class="badge bg-primary px-2 py-1">Grade: {{ $g->grade ?: '-' }}</span></td>
                                        <td><span class="badge bg-success">RECORDED</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No Gradation records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Modal Footer --}}
<div class="modal-footer bg-white border-top px-4 py-3 d-flex align-items-center justify-content-between">
    <div class="small text-muted">
        <i class="ti ti-info-circle me-1"></i>{{ __('Module 6 - Saathi Safety & Operations Monitoring System') }}
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('report.employee-safety.export.single-pdf', ['type' => $typeLower, 'id' => $record->id]) }}" target="_blank" class="btn btn-outline-danger fw-semibold d-inline-flex align-items-center gap-1">
            <i class="ti ti-file-text fs-5"></i>{{ __('Download Dossier PDF') }}
        </a>

        <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">
            {{ __('Close') }}
        </button>
    </div>
</div>
