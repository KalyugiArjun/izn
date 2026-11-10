{{ Form::open(['route' => ['authority.award-nomination.nominate.update-action', encrypt($nomination->id)], 'method' => 'POST']) }}

<div class="modal-body p-4">
    {{-- Nominee Summary Card --}}
    <div class="card bg-light border-0 mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted small">Nominee:</span>
                <h6 class="fw-bold mb-0 text-dark">{{ $nomination->name ?? ($nomination->staff->name ?? '-') }}</h6>
                <small class="text-muted">{{ $nomination->designation->name ?? '-' }} ({{ $nomination->station->name ?? '-' }})</small>
            </div>
            <div class="text-end">
                <span class="text-muted small d-block">Current Status</span>
                @if($nomination->status == 0)
                    <span class="badge bg-warning text-dark"><i class="ti ti-clock me-1"></i>Pending</span>
                @elseif($nomination->status == 1)
                    <span class="badge bg-info text-white"><i class="ti ti-clipboard-check me-1"></i>TI Recommended</span>
                @elseif($nomination->status == 2)
                    <span class="badge bg-success text-white"><i class="ti ti-check me-1"></i>Awarded</span>
                @elseif($nomination->status == 3)
                    <span class="badge bg-danger text-white"><i class="ti ti-x me-1"></i>Rejected</span>
                @endif
            </div>
        </div>

        @if(!empty($nomination->description))
            <hr class="my-2 opacity-25">
            <div class="small">
                <strong>{{ __('Meritorious Work Description:') }}</strong>
                <p class="text-muted mb-1">{{ $nomination->description }}</p>
                @if(!empty($nomination->file))
                    <a href="{{ asset('storage/' . $nomination->file) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 mt-1">
                        <i class="ti ti-paperclip me-1"></i>{{ __('View Attached Document') }}
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- ROLE-SPECIFIC ACTION PANELS --}}

    @if($isTiSafety && !$isAdmin)
        {{-- ================= TI SAFETY VIEW ================= --}}
        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center">
            <i class="ti ti-send fs-4 me-2 text-info"></i>
            <div>
                <strong class="text-info">{{ __('TI Safety Recommendation & Forward') }}</strong>
                <small class="d-block text-muted">{{ __('Review staff nomination and add your recommendation remarks to forward to Sr. DOM.') }}</small>
            </div>
        </div>

        <input type="hidden" name="status" value="1">

        <div class="mb-3">
            <label class="form-label fw-bold">{{ __('TI Safety Recommendation Comments') }} <span class="text-danger">* (Compulsory)</span></label>
            <textarea name="ti_comment" class="form-control" rows="3" placeholder="Enter your detailed remarks (e.g., Staff showed exemplary vigilance, safe train operation verified, strongly recommended for award)..." required minlength="3">{{ $nomination->ti_comment }}</textarea>
            <small class="text-muted">This recommendation comment is compulsory and will be visible to Sr. DOM upon forwarding.</small>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-semibold">{{ __('Forward To (Sr. DOM Approver)') }}</label>
            <select name="sr_dom_id" class="form-select form-select-sm">
                @foreach($srDoms as $domId => $domName)
                    <option value="{{ $domId }}" {{ ($nomination->sr_dom_id ?? 510) == $domId ? 'selected' : '' }}>{{ $domName }}</option>
                @endforeach
            </select>
        </div>

    @elseif($isSrDom && !$isAdmin)
        {{-- ================= SR. DOM VIEW ================= --}}
        <div class="alert alert-primary py-2 px-3 mb-3 d-flex align-items-center">
            <i class="ti ti-certificate fs-4 me-2 text-primary"></i>
            <div>
                <strong class="text-primary">{{ __('Sr. DOM Final Approval & Award Decision') }}</strong>
                <small class="d-block text-muted">{{ __('Review TI Safety recommendations and take final decision.') }}</small>
            </div>
        </div>

        {{-- Display TI Safety Comment --}}
        <div class="card border border-info bg-light p-3 mb-3 rounded-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-info fw-bold small"><i class="ti ti-clipboard-check me-1"></i>TI SAFETY RECOMMENDATION</span>
                <small class="text-muted">{{ $nomination->ti_forward_date ? \Carbon\Carbon::parse($nomination->ti_forward_date)->format('d M, Y') : date('d M, Y') }}</small>
            </div>
            <p class="mb-1 text-dark fw-semibold" style="font-style: italic;">"{{ $nomination->ti_comment ?: 'Recommended for Award' }}"</p>
            <small class="text-muted">Forwarded by: <strong>{{ $nomination->ti->name ?? 'TI Safety' }}</strong></small>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">{{ __('Final Decision') }} <span class="text-danger">*</span></label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" class="btn-check srdom-status-toggle" name="status" id="srdom_status_2" value="2" checked>
                    <label class="btn btn-outline-success w-100 py-2 d-flex align-items-center justify-content-center" for="srdom_status_2">
                        <i class="ti ti-certificate me-1"></i> Grant Award
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check srdom-status-toggle" name="status" id="srdom_status_3" value="3">
                    <label class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center" for="srdom_status_3">
                        <i class="ti ti-x me-1"></i> Reject
                    </label>
                </div>
            </div>
        </div>

        {{-- Award Section --}}
        <div class="srdom-award-panel border rounded p-3 mb-3 bg-light">
            <div class="mb-2">
                <label class="form-label small fw-bold">{{ __('Sr. DOM Award Remarks / Cash Order') }} <span class="text-danger">* (Compulsory)</span></label>
                <input type="text" name="dom_comment" id="srdom_award_comment" class="form-control" value="{{ $nomination->dom_comment ?: 'Merit Award Approved by Sr. DOM' }}" placeholder="e.g. Cash Award 500 / Letter of Appreciation" required minlength="3">
                <small class="text-muted">Enter award justification or sanction order details (compulsory).</small>
            </div>
            <div class="mb-2">
                <label class="form-label small fw-bold">{{ __('Award Proficiency Category') }}</label>
                <select name="cert_proficiency" class="form-select form-select-sm">
                    @foreach($proficiencies as $pId => $pName)
                        <option value="{{ $pId }}" {{ ($nomination->cert_proficiency ?: $nomination->proficiency_id) == $pId ? 'selected' : '' }}>{{ $pName }}</option>
                    @endforeach
                </select>
            </div>
            @if($nomination->certificate_no)
                <div class="alert alert-success py-1 px-2 mb-0 small font-monospace">
                    Certificate No: <strong>{{ $nomination->certificate_no }}</strong>
                </div>
            @else
                <div class="text-success small fw-semibold">
                    <i class="ti ti-info-circle me-1"></i> Official Certificate No. (format: <code>NER/IZN/OPTG/xxxx</code>) will be auto-generated upon award.
                </div>
            @endif
        </div>

        {{-- Reject Section --}}
        <div class="srdom-reject-panel border rounded p-3 mb-3 bg-light d-none">
            <label class="form-label small fw-bold">{{ __('Reason for Rejection') }} <span class="text-danger">* (Compulsory)</span></label>
            <textarea name="dom_comment_reject" id="srdom_reject_comment" class="form-control" rows="3" placeholder="Specify mandatory reason for rejecting this nomination..." disabled minlength="3"></textarea>
            <small class="text-muted">Reason for rejection is compulsory for audit trail.</small>
        </div>

    @else
        {{-- ================= ADMIN FULL OVERRIDE ================= --}}
        <div class="mb-3">
            <label class="form-label fw-bold">{{ __('Update Status To') }} <span class="text-danger">*</span></label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" class="btn-check action-status-toggle" name="status" id="status_0" value="0" {{ $nomination->status == 0 ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning w-100 py-2 d-flex align-items-center justify-content-center" for="status_0">
                        <i class="ti ti-clock me-1"></i> Pending
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check action-status-toggle" name="status" id="status_1" value="1" {{ $nomination->status == 1 ? 'checked' : '' }}>
                    <label class="btn btn-outline-info w-100 py-2 d-flex align-items-center justify-content-center" for="status_1">
                        <i class="ti ti-clipboard-check me-1"></i> Recommend (TI)
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check action-status-toggle" name="status" id="status_2" value="2" {{ $nomination->status == 2 ? 'checked' : '' }}>
                    <label class="btn btn-outline-success w-100 py-2 d-flex align-items-center justify-content-center" for="status_2">
                        <i class="ti ti-certificate me-1"></i> Award Certificate
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check action-status-toggle" name="status" id="status_3" value="3" {{ $nomination->status == 3 ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center" for="status_3">
                        <i class="ti ti-x me-1"></i> Reject
                    </label>
                </div>
            </div>
        </div>

        {{-- Fields for TI Recommendation (Status 1) --}}
        <div class="action-panel panel-1 {{ $nomination->status == 1 ? '' : 'd-none' }} border rounded p-3 mb-3 bg-light">
            <h6 class="fw-bold text-info mb-2"><i class="ti ti-clipboard-check me-1"></i>TI Recommendation Details</h6>
            <div class="mb-2">
                <label class="form-label small fw-bold">{{ __('TI Remarks / Recommendation') }} <span class="text-danger">* (Compulsory)</span></label>
                <input type="text" name="ti_comment" id="admin_ti_comment" class="form-control" value="{{ $nomination->ti_comment }}" placeholder="e.g. Good work done, recommended for award." {{ $nomination->status == 1 ? 'required' : 'disabled' }}>
            </div>
            <div class="mb-2">
                <label class="form-label small">{{ __('Forward to Sr. DOM') }}</label>
                <select name="sr_dom_id" class="form-select form-select-sm">
                    @foreach($srDoms as $domId => $domName)
                        <option value="{{ $domId }}" {{ ($nomination->sr_dom_id ?? 510) == $domId ? 'selected' : '' }}>{{ $domName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Fields for Award (Status 2) --}}
        <div class="action-panel panel-2 {{ $nomination->status == 2 ? '' : 'd-none' }} border rounded p-3 mb-3 bg-light">
            <h6 class="fw-bold text-success mb-2"><i class="ti ti-certificate me-1"></i>Sr. DOM Award Details</h6>
            <div class="mb-2">
                <label class="form-label small fw-bold">{{ __('Sr. DOM Award Comments / Cash Order') }} <span class="text-danger">* (Compulsory)</span></label>
                <input type="text" name="dom_comment" id="admin_dom_comment" class="form-control" value="{{ $nomination->dom_comment }}" placeholder="e.g. Give Cash Award 500 / Letter of Commendation" {{ $nomination->status == 2 ? 'required' : 'disabled' }}>
            </div>
            <div class="mb-2">
                <label class="form-label small">{{ __('Award Proficiency Category') }}</label>
                <select name="cert_proficiency" class="form-select form-select-sm">
                    @foreach($proficiencies as $pId => $pName)
                        <option value="{{ $pId }}" {{ ($nomination->cert_proficiency ?: $nomination->proficiency_id) == $pId ? 'selected' : '' }}>{{ $pName }}</option>
                    @endforeach
                </select>
            </div>
            @if($nomination->certificate_no)
                <div class="alert alert-success py-1 px-2 mb-0 small font-monospace">
                    Certificate No: <strong>{{ $nomination->certificate_no }}</strong>
                </div>
            @else
                <div class="text-muted small">
                    <i class="ti ti-info-circle me-1"></i> Certificate number will be auto-generated in format <code>NER/IZN/OPTG/xxxx</code> upon saving.
                </div>
            @endif
        </div>

        {{-- Fields for Rejection (Status 3) --}}
        <div class="action-panel panel-3 {{ $nomination->status == 3 ? '' : 'd-none' }} border rounded p-3 mb-3 bg-light">
            <h6 class="fw-bold text-danger mb-2"><i class="ti ti-x me-1"></i>Rejection Reason</h6>
            <div class="mb-2">
                <label class="form-label small fw-bold">{{ __('Reason for Rejection') }} <span class="text-danger">* (Compulsory)</span></label>
                <textarea name="dom_comment_reject" id="admin_reject_comment" class="form-control" rows="2" placeholder="Specify why this nomination was rejected..." {{ $nomination->status == 3 ? 'required' : 'disabled' }}>{{ $nomination->dom_comment }}</textarea>
            </div>
        </div>
    @endif
</div>

<div class="modal-footer bg-light px-4 py-3">
    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary rounded-pill px-4">
        @if($isTiSafety && !$isAdmin)
            <i class="ti ti-send me-1"></i> {{ __('Forward to Sr. DOM') }}
        @elseif($isSrDom && !$isAdmin)
            <i class="ti ti-check me-1"></i> {{ __('Submit Final Decision') }}
        @else
            <i class="ti ti-device-floppy me-1"></i> {{ __('Save & Update Status') }}
        @endif
    </button>
</div>

{{ Form::close() }}

<script>
(function() {
    // Admin toggle
    $('.action-status-toggle').on('change', function() {
        const status = $(this).val();
        $('.action-panel').addClass('d-none');
        $('#admin_ti_comment').prop('disabled', true).prop('required', false);
        $('#admin_dom_comment').prop('disabled', true).prop('required', false);
        $('#admin_reject_comment').prop('disabled', true).prop('required', false);

        if (status === '1') {
            $('.panel-1').removeClass('d-none');
            $('#admin_ti_comment').prop('disabled', false).prop('required', true);
        } else if (status === '2') {
            $('.panel-2').removeClass('d-none');
            $('#admin_dom_comment').prop('disabled', false).prop('required', true);
        } else if (status === '3') {
            $('.panel-3').removeClass('d-none');
            $('#admin_reject_comment').prop('disabled', false).prop('required', true);
        }
    });

    // Sr. DOM toggle
    $('.srdom-status-toggle').on('change', function() {
        const status = $(this).val();
        if (status === '2') {
            $('.srdom-award-panel').removeClass('d-none');
            $('#srdom_award_comment').prop('disabled', false).prop('required', true);
            $('.srdom-reject-panel').addClass('d-none');
            $('#srdom_reject_comment').prop('disabled', true).prop('required', false);
        } else {
            $('.srdom-award-panel').addClass('d-none');
            $('#srdom_award_comment').prop('disabled', true).prop('required', false);
            $('.srdom-reject-panel').removeClass('d-none');
            $('#srdom_reject_comment').prop('disabled', false).prop('required', true);
        }
    });
})();
</script>

