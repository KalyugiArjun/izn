<div class="modal-body">
    <div class="border-0 rounded-3">
        <div class="card-body">
            <div class="row g-4">

                <div class="col-md-6">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Expense Number') }}</label>
                        <p class="mb-0">{{ expensePrefix() . $expense->expense_id }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Date') }}</label>
                        <p class="mb-0">{{ dateFormat($expense->date) }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Expense Title') }}</label>
                        <p class="mb-0">{{ $expense->title }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Expense Category') }}</label>
                        <p class="mb-0">{{ $expense->category->name ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Expense Subcategory') }}</label>
                        <p class="mb-0">{{ $expense->subCategory->name ?? '-' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Amount') }}</label>
                        <p class="mb-0 text-success fw-bold">{{ priceFormat($expense->amount) }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Receipt') }}</label>
                        <p class="mb-0">
                            @if (!empty($expense->receipt))
                                <a href="{{ asset(Storage::url('upload/receipt')) . '/' . $expense->receipt }}"
                                   class="btn btn-sm btn-light border shadow-sm" target="_blank" download>
                                    <i data-feather="download"></i> {{ __('Download') }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-12">
                    <div class="detail-group">
                        <label class="fw-bold text-muted">{{ __('Notes') }}</label>
                        <p class="mb-0">{{ $expense->notes ?? '-' }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

