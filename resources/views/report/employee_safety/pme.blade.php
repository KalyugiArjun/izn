@extends('layouts.app')

@section('page-title', __('PME Record - ') . $employee->name)

@section('content')
    <div class="row">
        <!-- Header with Back Button -->
        <div class="col-12 mb-3">
            <div class="d-flex align-items-center justify-content-between p-3" 
                 style="background: #3ec9d6; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(62, 201, 214, 0.2);">
                <div class="d-flex align-items-center">
                    <a href="{{ route('report.employee.safety') }}" class="text-white me-3" style="font-size: 20px;">
                        <i class="ti ti-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="mb-0 text-white fw-bold" style="font-size: 1.25rem;">{{ __('PME Record') }}</h4>
                        <span class="small opacity-85">{{ $employee->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <!-- Form Card -->
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">{{ __('New PME Entry') }}</h5>
                </div>
                <div class="card-body p-4">
                    {{ Form::open(['route' => ['report.employee.safety.pme.store', encrypt($employee->id)], 'method' => 'post']) }}
                        
                        <!-- Last PME Date -->
                        <div class="form-group mb-3">
                            {{ Form::label('last_pme_date', __('Last PME Date * (YYYY-MM-DD)'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::date('last_pme_date', date('Y-m-d'), ['class' => 'form-control', 'required' => true]) }}
                        </div>

                        <!-- Certificate No -->
                        <div class="form-group mb-3">
                            {{ Form::label('certificate_no', __('Certificate No. *'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::text('certificate_no', null, ['class' => 'form-control', 'placeholder' => 'PME00T54', 'required' => true]) }}
                        </div>

                        <!-- Next Due Date -->
                        <div class="form-group mb-4">
                            {{ Form::label('next_due_date', __('Next Due Date * (YYYY-MM-DD)'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::date('next_due_date', date('Y-m-d', strtotime('+3 years')), ['class' => 'form-control', 'required' => true]) }}
                        </div>

                        <!-- Remarks (Optional but useful) -->
                        <div class="form-group mb-4">
                            {{ Form::label('remarks', __('Remarks'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Add any remarks here...']) }}
                        </div>

                        <!-- Save Button -->
                        <div class="form-group mb-0">
                            {{ Form::submit(__('Save PME Record'), ['class' => 'btn w-100 text-white fw-semibold', 'style' => 'background: #3ec9d6; border-radius: 10px; padding: 12px;']) }}
                        </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <!-- History Card -->
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">{{ __('History') }}</h5>
                </div>
                <div class="card-body p-4">
                    @if ($history->isEmpty())
                        <div class="text-center py-5">
                            <img src="{{ asset('assets/images/no-data.png') }}" alt="" style="width: 80px; opacity: 0.15;" class="mb-3">
                            <p class="text-muted mb-0">{{ __('No PME records found') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Last PME Date') }}</th>
                                        <th>{{ __('Certificate No.') }}</th>
                                        <th>{{ __('Next Due Date') }}</th>
                                        <th>{{ __('Remarks') }}</th>
                                        <th>{{ __('Added On') }}</th>
                                        <th class="text-end">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($history as $record)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">
                                                    {{ \Carbon\Carbon::parse($record->last_pme_date)->format('d-m-Y') }}
                                                </span>
                                            </td>
                                            <td>{{ $record->certificate_no }}</td>
                                            <td>
                                                <span class="badge bg-light-danger text-danger fw-bold" style="border-radius: 6px;">
                                                    {{ \Carbon\Carbon::parse($record->next_due_date)->format('d-m-Y') }}
                                                </span>
                                            </td>
                                            <td class="text-muted" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $record->remarks ?? '-' }}
                                            </td>
                                            <td class="small text-muted">
                                                {{ $record->created_at->format('d-m-Y H:i') }}
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-pme" data-id="{{ encrypt($record->id) }}" style="border-radius: 8px; padding: 6px 10px;">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    <form action="{{ route('report.employee.safety.pme.delete', encrypt($record->id)) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; padding: 6px 10px;">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editPmeModal" tabindex="-1" aria-labelledby="editPmeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editPmeModalLabel">{{ __('Edit PME Entry') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPmeForm" method="POST" action="">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="edit_last_pme_date" class="form-label fw-semibold text-dark">{{ __('Last PME Date *') }}</label>
                            <input type="date" name="last_pme_date" id="edit_last_pme_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_certificate_no" class="form-label fw-semibold text-dark">{{ __('Certificate No. *') }}</label>
                            <input type="text" name="certificate_no" id="edit_certificate_no" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_next_due_date" class="form-label fw-semibold text-dark">{{ __('Next Due Date *') }}</label>
                            <input type="date" name="next_due_date" id="edit_next_due_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="edit_pme_remarks" class="form-label fw-semibold text-dark">{{ __('Remarks') }}</label>
                            <textarea name="remarks" id="edit_pme_remarks" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn w-100 text-white fw-semibold" style="background: #3ec9d6; border-radius: 10px; padding: 12px;">{{ __('Update PME Record') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.btn-edit-pme', function() {
                var id = $(this).data('id');
                var url = '{{ url("employee-safety-record/pme") }}/' + id + '/edit';
                var updateUrl = '{{ url("employee-safety-record/pme") }}/' + id + '/update';
                
                $.get(url, function(data) {
                    $('#edit_last_pme_date').val(data.last_pme_date);
                    $('#edit_certificate_no').val(data.certificate_no);
                    $('#edit_next_due_date').val(data.next_due_date);
                    $('#edit_pme_remarks').val(data.remarks);
                    $('#editPmeForm').attr('action', updateUrl);
                    var editModal = new bootstrap.Modal(document.getElementById('editPmeModal'));
                    editModal.show();
                });
            });
        });
    </script>
@endsection
