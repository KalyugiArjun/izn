@extends('layouts.app')

@section('page-title', __('Staff Gradation - ') . $employee->name)

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
                        <h4 class="mb-0 text-white fw-bold" style="font-size: 1.25rem;">{{ __('Staff Gradation') }}</h4>
                        <span class="small opacity-85">{{ $employee->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <!-- Form Card -->
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">{{ __('New Gradation Entry') }}</h5>
                </div>
                <div class="card-body p-4">
                    {{ Form::open(['route' => ['report.employee.safety.gradation.store', encrypt($employee->id)], 'method' => 'post']) }}
                        
                        <!-- Gradation Date -->
                        <div class="form-group mb-3">
                            {{ Form::label('gradation_date', __('Gradation Date * (YYYY-MM-DD)'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::date('gradation_date', date('Y-m-d'), ['class' => 'form-control', 'required' => true]) }}
                        </div>

                        <!-- Total Marks -->
                        <div class="form-group mb-3">
                            {{ Form::label('total_marks', __('Total Marks *'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::number('total_marks', null, ['class' => 'form-control', 'step' => '0.01', 'placeholder' => '85.00', 'required' => true, 'min' => 0, 'max' => 100]) }}
                        </div>

                        <!-- Grade -->
                        <div class="form-group mb-4">
                            {{ Form::label('grade', __('Grade *'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::select('grade', [
                                'A' => 'Grade A',
                                'B' => 'Grade B',
                                'C' => 'Grade C',
                                'D' => 'Grade D'
                            ], null, ['class' => 'form-select', 'placeholder' => 'Select Grade', 'required' => true]) }}
                        </div>

                        <!-- Remarks (Optional but useful) -->
                        <div class="form-group mb-4">
                            {{ Form::label('remarks', __('Remarks'), ['class' => 'form-label fw-semibold text-dark']) }}
                            {{ Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Add any remarks here...']) }}
                        </div>

                        <!-- Save Button -->
                        <div class="form-group mb-0">
                            {{ Form::submit(__('Save Gradation Record'), ['class' => 'btn w-100 text-white fw-semibold', 'style' => 'background: #3ec9d6; border-radius: 10px; padding: 12px;']) }}
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
                            <p class="text-muted mb-0">{{ __('No staff gradation records found') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Gradation Date') }}</th>
                                        <th>{{ __('Marks') }}</th>
                                        <th>{{ __('Grade') }}</th>
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
                                                    {{ \Carbon\Carbon::parse($record->gradation_date)->format('d-m-Y') }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($record->total_marks, 2) }}</td>
                                            <td>
                                                <span class="badge bg-light-primary text-primary fw-bold" style="border-radius: 6px;">
                                                    {{ $record->grade }}
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
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-gradation" data-id="{{ encrypt($record->id) }}" style="border-radius: 8px; padding: 6px 10px;">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    <form action="{{ route('report.employee.safety.gradation.delete', encrypt($record->id)) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');" style="display:inline;">
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
    <div class="modal fade" id="editGradationModal" tabindex="-1" aria-labelledby="editGradationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editGradationModalLabel">{{ __('Edit Gradation Entry') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGradationForm" method="POST" action="">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="edit_gradation_date" class="form-label fw-semibold text-dark">{{ __('Gradation Date *') }}</label>
                            <input type="date" name="gradation_date" id="edit_gradation_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_total_marks" class="form-label fw-semibold text-dark">{{ __('Total Marks *') }}</label>
                            <input type="number" name="total_marks" id="edit_total_marks" class="form-control" step="0.01" min="0" max="100" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_grade" class="form-label fw-semibold text-dark">{{ __('Grade *') }}</label>
                            <select name="grade" id="edit_grade" class="form-select" required>
                                <option value="A">Grade A</option>
                                <option value="B">Grade B</option>
                                <option value="C">Grade C</option>
                                <option value="D">Grade D</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="edit_remarks" class="form-label fw-semibold text-dark">{{ __('Remarks') }}</label>
                            <textarea name="remarks" id="edit_remarks" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn w-100 text-white fw-semibold" style="background: #3ec9d6; border-radius: 10px; padding: 12px;">{{ __('Update Gradation Record') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.btn-edit-gradation', function() {
                var id = $(this).data('id');
                var url = '{{ url("employee-safety-record/gradation") }}/' + id + '/edit';
                var updateUrl = '{{ url("employee-safety-record/gradation") }}/' + id + '/update';
                
                $.get(url, function(data) {
                    $('#edit_gradation_date').val(data.gradation_date);
                    $('#edit_total_marks').val(data.total_marks);
                    $('#edit_grade').val(data.grade);
                    $('#edit_remarks').val(data.remarks);
                    $('#editGradationForm').attr('action', updateUrl);
                    var editModal = new bootstrap.Modal(document.getElementById('editGradationModal'));
                    editModal.show();
                });
            });
        });
    </script>
@endsection
