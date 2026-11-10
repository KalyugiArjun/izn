<div class="modal-header bg-light border-bottom py-3">
    <div class="d-flex align-items-center">
        <div class="bg-success-subtle text-success p-2 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="ti ti-edit fs-4"></i>
        </div>
        <div>
            <h5 class="modal-title fw-bold text-dark mb-0">{{ __('Edit Movement Record') }} #{{ $record->id }}</h5>
            <div class="small text-muted">{{ __('Update movement and inspection parameters') }}</div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="editMovementForm" method="POST" action="{{ route('report.my-movement.update', $record->id) }}">
    @csrf
    <div class="modal-body p-4">
        <div id="editMovementAlert" class="alert alert-danger d-none mb-3 py-2"></div>

        <div class="row g-3">
            {{-- 1. Officer Name --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Officer Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="officer_name" class="form-control" value="{{ $record->officer_name ?: ($record->employee->name ?? '') }}" required>
            </div>

            {{-- 2. Designation --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Designation') }}</label>
                <input type="text" name="designation" class="form-control" value="{{ $record->designation ?: ($record->employee->designation->name ?? '') }}">
            </div>

            {{-- 3. Inspection Date --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Date of Movement') }} <span class="text-danger">*</span></label>
                <input type="date" name="inspection_date" class="form-control" value="{{ $record->inspection_date ? \Carbon\Carbon::parse($record->inspection_date)->format('Y-m-d') : ($record->created_at ? $record->created_at->format('Y-m-d') : '') }}" required>
            </div>

            {{-- 4. Type of Movement --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Type of Movement') }}</label>
                <select name="movement_id" class="form-select">
                    <option value="">{{ __('Select Movement Type') }}</option>
                    @foreach($movements as $mId => $mName)
                        <option value="{{ $mId }}" {{ $record->movement_id == $mId ? 'selected' : '' }}>
                            {{ $mName }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 5. Station / Location --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Station') }}</label>
                <select name="station_id" class="form-select">
                    <option value="">{{ __('None / Select Station') }}</option>
                    @foreach($stations as $st)
                        @php
                            $currStationId = $record->station_id 
                                ?: ($record->regularInspection->station_id ?? ($record->ambushInspection->station_id ?? null));
                        @endphp
                        <option value="{{ $st->id }}" {{ $currStationId == $st->id ? 'selected' : '' }}>
                            {{ $st->name }} ({{ $st->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 6. Block Section --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Block Section') }}</label>
                <input type="text" name="block_section" class="form-control" value="{{ $record->block_section ?: ($record->ambushInspection->block_section ?? '') }}" placeholder="e.g. BC-STP">
            </div>

            {{-- 7. Train No --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Train No') }}</label>
                <input type="text" name="train_no" class="form-control" value="{{ $record->train_no ?: ($record->ambushInspection->train_no ?? '') }}" placeholder="e.g. 15014">
            </div>

            {{-- 8. Goods / Passenger --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Service Type') }}</label>
                <select name="goods_passenger" class="form-select">
                    <option value="">{{ __('Select...') }}</option>
                    <option value="Goods" {{ strtolower($record->goods_passenger ?? '') === 'goods' ? 'selected' : '' }}>Goods</option>
                    <option value="Passenger" {{ strtolower($record->goods_passenger ?? '') === 'passenger' ? 'selected' : '' }}>Passenger</option>
                </select>
            </div>

            {{-- 9. Major Deficiency --}}
            @php
                $isDef = false;
                $defNote = '';
                if ($record->regularInspection) {
                    $isDef = strtoupper($record->regularInspection->major_deficiency ?? '') === 'YES';
                    $defNote = $record->regularInspection->deficiency_note ?? '';
                } elseif ($record->ambushInspection) {
                    $isDef = strtoupper($record->ambushInspection->major_deficiency ?? '') === 'YES';
                }
            @endphp
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('Major Deficiency Noted?') }}</label>
                <select name="major_deficiency" class="form-select">
                    <option value="NO" {{ !$isDef ? 'selected' : '' }}>NO (Compliant)</option>
                    <option value="YES" {{ $isDef ? 'selected' : '' }}>YES (Deficiency Found)</option>
                </select>
            </div>

            {{-- 10. GPS Location --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-dark">{{ __('GPS Coordinates') }}</label>
                <input type="text" name="gps_location" class="form-control" value="{{ $record->gps_location }}" placeholder="e.g. 28.3670, 79.4304">
            </div>

            {{-- 11. Deficiency Note --}}
            <div class="col-12">
                <label class="form-label small fw-semibold text-dark">{{ __('Deficiency Details / Note') }}</label>
                <textarea name="deficiency_note" class="form-control" rows="2" placeholder="{{ __('Details of any irregularity or deficiency found...') }}">{{ $defNote }}</textarea>
            </div>

            {{-- 12. Reason / Description --}}
            <div class="col-12">
                <label class="form-label small fw-semibold text-dark">{{ __('Observations / Movement Notes') }}</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('Enter observations, instructions or reasons...') }}">{{ $record->reason }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light border-top py-2 px-3">
        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
            <i class="ti ti-x me-1"></i>{{ __('Cancel') }}
        </button>
        <button type="submit" class="btn btn-success px-4" id="saveMovementBtn">
            <i class="ti ti-check me-1"></i>{{ __('Save Changes') }}
        </button>
    </div>
</form>

