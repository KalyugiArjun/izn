@php
    $ti = $work->ph16Ti;
    $tiName = !empty($work->concern_traffic_inspector) ? $work->concern_traffic_inspector : ($ti && $ti->designation ? $ti->designation->name : 'N/A');
    $deptName = $ti && $ti->department ? $ti->department->name : '-';
    $isCompleted = ($work->executionends && $work->executionends->count() > 0) || $work->status === 1;
@endphp

<div>
    {{-- Header Work Banner --}}
    <div class="p-3 mb-4 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary fs-6">#2026-{{ str_pad($work->id, 2, '0', STR_PAD_LEFT) }}</span>
                    @if ($isCompleted)
                        <span class="badge bg-success">{{ __('Completed') }}</span>
                    @else
                        <span class="badge bg-warning text-dark">{{ __('In Progress') }}</span>
                    @endif
                    <span class="badge bg-secondary">{{ $work->current_stage }}</span>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $work->work_description_identification }}</h4>
                <div class="text-muted small">
                    <span class="me-3"><i class="ti ti-calendar me-1"></i><strong>{{ __('Identified Date:') }}</strong> {{ $work->date_of_identification_of_work ? $work->date_of_identification_of_work->format('d M, Y') : 'N/A' }}</span>
                    <span class="me-3"><i class="ti ti-user-check me-1"></i><strong>{{ __('Concerned Inspector:') }}</strong> {{ $tiName }}</span>
                    <span><i class="ti ti-building me-1"></i><strong>{{ __('Department:') }}</strong> {{ $deptName }}</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('report.ph16.export.work-pdf', $work->id) }}" class="btn btn-outline-danger btn-sm shadow-sm" target="_blank">
                    <i class="ti ti-download me-1"></i> {{ __('Download Work Dossier PDF') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <ul class="nav nav-pills mb-3 border-bottom pb-2" id="workDetailTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active btn-sm" id="tab-timeline" data-bs-toggle="pill" data-bs-target="#content-timeline" type="button" role="tab">
                <i class="ti ti-chart-dots me-1"></i> {{ __('Milestone Timeline') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm" id="tab-estimates" data-bs-toggle="pill" data-bs-target="#content-estimates" type="button" role="tab">
                <i class="ti ti-calculator me-1"></i> {{ __('Estimates') }} ({{ $work->estimates->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm" id="tab-approvals" data-bs-toggle="pill" data-bs-target="#content-approvals" type="button" role="tab">
                <i class="ti ti-checks me-1"></i> {{ __('Approvals & Vetting') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm" id="tab-plans" data-bs-toggle="pill" data-bs-target="#content-plans" type="button" role="tab">
                <i class="ti ti-map-pin me-1"></i> {{ __('ESP & SIP Plans') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm" id="tab-execution" data-bs-toggle="pill" data-bs-target="#content-execution" type="button" role="tab">
                <i class="ti ti-tools me-1"></i> {{ __('Execution') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm" id="tab-variations" data-bs-toggle="pill" data-bs-target="#content-variations" type="button" role="tab">
                <i class="ti ti-git-fork me-1"></i> {{ __('Variations') }} ({{ $work->variations->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm" id="tab-inspections" data-bs-toggle="pill" data-bs-target="#content-inspections" type="button" role="tab">
                <i class="ti ti-clipboard-check me-1"></i> {{ __('Inspection Logs') }} ({{ $work->inspectionLogs->count() }})
            </button>
        </li>
    </ul>

    {{-- Tabs Content --}}
    <div class="tab-content" id="workDetailTabContent">

        {{-- 1. Timeline & Overview --}}
        <div class="tab-pane fade show active" id="content-timeline" role="tabpanel">
            <div class="row g-3">
                @php
                    $stages = [
                        [
                            'name' => __('1. Work Identification'),
                            'done' => true,
                            'date' => $work->date_of_identification_of_work ? $work->date_of_identification_of_work->format('d/m/Y') : 'Done',
                            'detail' => $tiName . ' (' . $deptName . ')'
                        ],
                        [
                            'name' => __('2. Joint Survey'),
                            'done' => $work->jointSurveys->count() > 0,
                            'date' => $work->jointSurveys->first()?->date ?? 'Pending',
                            'detail' => $work->jointSurveys->first()?->milestone ?? '-'
                        ],
                        [
                            'name' => __('3. Estimates Called & Recv'),
                            'done' => $work->estimates->count() > 0,
                            'date' => $work->estimates->count() . ' recorded',
                            'detail' => $work->estimates->pluck('department')->implode(', ') ?: '-'
                        ],
                        [
                            'name' => __('4. Review Estimates'),
                            'done' => $work->reviewEstimates->count() > 0,
                            'date' => $work->reviewEstimates->first()?->review_estimate_date ?? 'Pending',
                            'detail' => $work->reviewEstimates->first()?->milestone ?? '-'
                        ],
                        [
                            'name' => __('5. Approval & Listing'),
                            'done' => $work->approvalListings->count() > 0,
                            'date' => $work->approvalListings->first()?->date ?? 'Pending',
                            'detail' => $work->approvalListings->first()?->milestone ?? '-'
                        ],
                        [
                            'name' => __('6. Finance Vetting & Sanction'),
                            'done' => $work->financeVettingSanctions->count() > 0,
                            'date' => $work->financeVettingSanctions->first()?->date ?? 'Pending',
                            'detail' => $work->financeVettingSanctions->first()?->milestone ?? '-'
                        ],
                        [
                            'name' => __('7. Tendering'),
                            'done' => $work->tenderings->count() > 0,
                            'date' => $work->tenderings->first()?->date ?? 'Pending',
                            'detail' => $work->tenderings->first()?->department ?? '-'
                        ],
                        [
                            'name' => __('8. ESP & SIP'),
                            'done' => $work->esps->count() > 0 || $work->sips->count() > 0,
                            'date' => ($work->esps->count() > 0 ? 'ESP Done' : '') . ($work->sips->count() > 0 ? ' SIP Done' : ''),
                            'detail' => ($work->esps->count() + $work->sips->count()) . ' plan records'
                        ],
                        [
                            'name' => __('9. Execution Start'),
                            'done' => $work->executions->count() > 0,
                            'date' => $work->executions->first()?->date ?? 'Pending',
                            'detail' => 'Target: ' . ($work->executions->first()?->work_completion_date ?? '-')
                        ],
                        [
                            'name' => __('10. Execution End / Handover'),
                            'done' => $work->executionends->count() > 0,
                            'date' => $work->executionends->first()?->execution_completion_date ?? 'Pending',
                            'detail' => $work->executionends->first()?->remarks ?? '-'
                        ],
                    ];
                @endphp

                @foreach ($stages as $stage)
                    <div class="col-md-6">
                        <div class="card border p-3 mb-0 h-100 {{ $stage['done'] ? 'border-success bg-success bg-opacity-10' : 'border-light bg-light' }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark">{{ $stage['name'] }}</span>
                                @if ($stage['done'])
                                    <span class="badge bg-success"><i class="ti ti-check"></i> {{ __('Done') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Pending') }}</span>
                                @endif
                            </div>
                            <div class="small text-muted mt-2">
                                <div><strong>{{ __('Date / Status:') }}</strong> {{ $stage['date'] }}</div>
                                <div><strong>{{ __('Detail:') }}</strong> {{ $stage['detail'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 2. Estimates --}}
        <div class="tab-pane fade" id="content-estimates" role="tabpanel">
            <h6 class="fw-bold mb-3">{{ __('Estimates Called & Received from Departments') }}</h6>
            @if ($work->estimates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Department') }}</th>
                                <th>{{ __('Estimate Called Date') }}</th>
                                <th>{{ __('Call Letter') }}</th>
                                <th>{{ __('Estimate Received Date') }}</th>
                                <th>{{ __('Received Document') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($work->estimates as $i => $est)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-bold">{{ $est->department }}</td>
                                    <td>{{ $est->estimate_called ?? '-' }}</td>
                                    <td>
                                        @if ($est->call_letter)
                                            <a href="{{ asset(Storage::url($est->call_letter)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                                <i class="ti ti-file"></i> {{ __('View Letter') }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $est->estimate_received ?? '-' }}</td>
                                    <td>
                                        @if ($est->received_estimate)
                                            <a href="{{ asset(Storage::url($est->received_estimate)) }}" target="_blank" class="btn btn-xs btn-outline-success">
                                                <i class="ti ti-file-check"></i> {{ __('View Doc') }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-secondary mb-0">{{ __('No estimates recorded for this work yet.') }}</div>
            @endif
        </div>

        {{-- 3. Approvals & Vetting --}}
        <div class="tab-pane fade" id="content-approvals" role="tabpanel">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border p-3">
                        <h6 class="fw-bold text-primary">{{ __('Approval & Listing') }}</h6>
                        @forelse ($work->approvalListings as $app)
                            <div class="mb-2 pb-2 border-bottom">
                                <div><strong>{{ __('Milestone:') }}</strong> {{ $app->milestone }}</div>
                                <div><strong>{{ __('Date:') }}</strong> {{ $app->date ?? '-' }}</div>
                                <div><strong>{{ __('Days Taken:') }}</strong> {{ $app->days_taken ?? '-' }}</div>
                                @if ($app->supporting_document)
                                    <div class="mt-1">
                                        <a href="{{ asset(Storage::url($app->supporting_document)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="ti ti-file"></i> {{ __('Supporting Doc') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small">{{ __('Pending') }}</span>
                        @endforelse
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border p-3">
                        <h6 class="fw-bold text-primary">{{ __('Finance Vetting & Sanction') }}</h6>
                        @forelse ($work->financeVettingSanctions as $fvs)
                            <div class="mb-2 pb-2 border-bottom">
                                <div><strong>{{ __('Milestone:') }}</strong> {{ $fvs->milestone }}</div>
                                <div><strong>{{ __('Date:') }}</strong> {{ $fvs->date ?? '-' }}</div>
                                <div><strong>{{ __('Days Taken:') }}</strong> {{ $fvs->days_taken ?? '-' }}</div>
                                @if ($fvs->supporting_document)
                                    <div class="mt-1">
                                        <a href="{{ asset(Storage::url($fvs->supporting_document)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="ti ti-file"></i> {{ __('Supporting Doc') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small">{{ __('Pending') }}</span>
                        @endforelse
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card border p-3">
                        <h6 class="fw-bold text-primary">{{ __('Tendering Details') }}</h6>
                        @forelse ($work->tenderings as $tnd)
                            <div class="mb-2 pb-2 border-bottom">
                                <div><strong>{{ __('Department:') }}</strong> {{ $tnd->department }}</div>
                                <div><strong>{{ __('Milestone:') }}</strong> {{ $tnd->milestone }}</div>
                                <div><strong>{{ __('Date:') }}</strong> {{ $tnd->date ?? '-' }}</div>
                                @if ($tnd->supporting_document)
                                    <div class="mt-1">
                                        <a href="{{ asset(Storage::url($tnd->supporting_document)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="ti ti-file"></i> {{ __('Supporting Doc') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small">{{ __('Pending') }}</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. ESP & SIP Plans --}}
        <div class="tab-pane fade" id="content-plans" role="tabpanel">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border p-3">
                        <h6 class="fw-bold text-primary">{{ __('ESP (Engineering Scale Plan)') }}</h6>
                        @forelse ($work->esps as $esp)
                            <div class="mb-2 pb-2 border-bottom">
                                <div><strong>{{ __('Milestone:') }}</strong> {{ $esp->milestone }}</div>
                                <div><strong>{{ __('Date:') }}</strong> {{ $esp->date ?? '-' }}</div>
                                <div><strong>{{ __('Days Taken:') }}</strong> {{ $esp->days_taken ?? '-' }}</div>
                                @if ($esp->supporting_document)
                                    <div class="mt-1">
                                        <a href="{{ asset(Storage::url($esp->supporting_document)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="ti ti-file"></i> {{ __('View Plan Doc') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small">{{ __('No ESP records') }}</span>
                        @endforelse
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border p-3">
                        <h6 class="fw-bold text-primary">{{ __('SIP (Signaling Interlocking Plan)') }}</h6>
                        @forelse ($work->sips as $sip)
                            <div class="mb-2 pb-2 border-bottom">
                                <div><strong>{{ __('Milestone:') }}</strong> {{ $sip->milestone }}</div>
                                <div><strong>{{ __('Date:') }}</strong> {{ $sip->date ?? '-' }}</div>
                                <div><strong>{{ __('Days Taken:') }}</strong> {{ $sip->days_taken ?? '-' }}</div>
                                @if ($sip->supporting_document)
                                    <div class="mt-1">
                                        <a href="{{ asset(Storage::url($sip->supporting_document)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="ti ti-file"></i> {{ __('View Plan Doc') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small">{{ __('No SIP records') }}</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Execution --}}
        <div class="tab-pane fade" id="content-execution" role="tabpanel">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border p-3">
                        <h6 class="fw-bold text-primary">{{ __('Execution Start') }}</h6>
                        @forelse ($work->executions as $ex)
                            <div class="mb-2">
                                <div><strong>{{ __('Start Date:') }}</strong> {{ $ex->date ?? '-' }}</div>
                                <div><strong>{{ __('Target Completion Date:') }}</strong> {{ $ex->work_completion_date ?? '-' }}</div>
                                <div><strong>{{ __('Days Taken:') }}</strong> {{ $ex->days_taken ?? '-' }}</div>
                                @if ($ex->supporting_document)
                                    <div class="mt-2">
                                        <a href="{{ asset(Storage::url($ex->supporting_document)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="ti ti-file"></i> {{ __('Execution Order / Doc') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small">{{ __('Execution not yet started') }}</span>
                        @endforelse
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border p-3">
                        <h6 class="fw-bold text-primary">{{ __('Execution End & Handover') }}</h6>
                        @forelse ($work->executionends as $exe)
                            <div class="mb-2">
                                <div><strong>{{ __('Execution Start Date:') }}</strong> {{ $exe->execution_start_date ?? '-' }}</div>
                                <div><strong>{{ __('Actual Completion Date:') }}</strong> {{ $exe->execution_completion_date ?? '-' }}</div>
                                <div><strong>{{ __('Days Taken:') }}</strong> {{ $exe->days_taken ?? '-' }}</div>
                                <div><strong>{{ __('Remarks:') }}</strong> {{ $exe->remarks ?? '-' }}</div>
                                @if ($exe->supporting_document)
                                    <div class="mt-2">
                                        <a href="{{ asset(Storage::url($exe->supporting_document)) }}" target="_blank" class="btn btn-xs btn-outline-success">
                                            <i class="ti ti-file-check"></i> {{ __('Handover Certificate') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small">{{ __('Execution end pending') }}</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Variations --}}
        <div class="tab-pane fade" id="content-variations" role="tabpanel">
            <h6 class="fw-bold mb-3">{{ __('Work Variations & Revised TDC') }}</h6>
            @if ($work->variations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Date of Variation') }}</th>
                                <th>{{ __('Nature of Variation') }}</th>
                                <th>{{ __('Revised TDC') }}</th>
                                <th>{{ __('Supporting File') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($work->variations as $i => $var)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $var->date_of_variation ?? '-' }}</td>
                                    <td>{{ $var->nature_of_variation ?? '-' }}</td>
                                    <td class="fw-bold text-danger">{{ $var->revised_tdc ?? '-' }}</td>
                                    <td>
                                        @if ($var->file)
                                            <a href="{{ asset(Storage::url($var->file)) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                                <i class="ti ti-file"></i> {{ __('View File') }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-secondary mb-0">{{ __('No variations recorded for this work.') }}</div>
            @endif
        </div>

        {{-- 7. Inspection Logs --}}
        <div class="tab-pane fade" id="content-inspections" role="tabpanel">
            <h6 class="fw-bold mb-3">{{ __('Inspection Logs for this Work') }}</h6>
            @if ($work->inspectionLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Inspecting Authority') }}</th>
                                <th>{{ __('Inspecting Official') }}</th>
                                <th>{{ __('Department') }}</th>
                                <th>{{ __('Work Done Detail') }}</th>
                                <th>{{ __('Photos') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($work->inspectionLogs as $i => $log)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $log->date_of_inspection ? date('d/m/Y', strtotime($log->date_of_inspection)) : '-' }}</td>
                                    <td>{{ $log->inspecting_authority ?? '-' }}</td>
                                    <td class="fw-bold">{{ $log->name_of_inspecting_official ?? '-' }}</td>
                                    <td>{{ $log->department_inspected ?? '-' }}</td>
                                    <td>{{ $log->work_done_detail ?? '-' }}</td>
                                    <td>
                                        @if ($log->photos && $log->photos->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($log->photos as $p)
                                                    <a href="{{ asset(Storage::url($p->photo)) }}" target="_blank">
                                                        <img src="{{ asset(Storage::url($p->photo)) }}" alt="Photo" class="rounded border" style="width: 36px; height: 36px; object-fit: cover;">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">{{ __('No photos') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-secondary mb-0">{{ __('No inspection logs recorded for this work yet.') }}</div>
            @endif
        </div>

    </div>
</div>

