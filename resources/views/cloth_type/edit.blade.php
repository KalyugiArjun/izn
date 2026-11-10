@extends('layouts.app')
@section('page-title')
    {{ __('Edit Cloth Type') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('cloth-type.index') }}">{{ __('Cloth Type') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Edit') }}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row wrapper">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::model($clothType, ['route' => ['cloth-type.update', encrypt($clothType->id)], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="form-group col-md-4">
                            {{ Form::label('title', __('Title') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter cloth type title'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4">
                            {{ Form::label('gender', __('Gender') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
                            {!! Form::select('gender', $gender, null, ['class' => 'form-control select2 select2']) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {{ Form::label('amount', __('Amount') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
                            {{ Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('Enter amount'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4">
                            {{ Form::label('taxes', __('Tax'), ['class' => 'form-label']) }}
                            {!! Form::select('taxes[]', $taxes, !empty($clothType->taxes) ? explode(',', $clothType->taxes) : [], [
                                'class' => 'form-control select2',
                                'multiple',
                            ]) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}
                            {{ Form::text('note', null, ['class' => 'form-control', 'placeholder' => __('Enter note')]) }}
                        </div>
                    </div>
                    <div class="row mt-5">
                        <h5 class="mb-20 ">
                            {{ __('Measurement Detail') }}
                            <a href="#" class="btn btn-secondary btn-xs cloth_type_clone float-end"><i
                                    class="ti ti-plus"></i></a>
                        </h5>
                    </div>
                    @if (empty($clothType->clothMeasureType))
                        <div class="row cloth_type">
                            <div class="form-group col-md-4">
                                {{ Form::label('measurement_title', __('Measurement Title'), ['class' => 'form-label']) }}
                                {{ Form::text('measurement_title[]', null, ['class' => 'form-control', 'placeholder' => __('Enter measurement title'), 'required' => 'required']) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ Form::label('measurement_unit', __('Measurement Unit'), ['class' => 'form-label']) }}
                                {!! Form::select('measurement_unit[]', $unit, null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col align-self-center">
                                {{ Form::label('order', __('Sequence Order'), ['class' => 'form-label']) }}
                                {{ Form::number('order[]', null, ['class' => 'form-control sequence-number', 'placeholder' => __('Enter sequence number'), 'required' => 'required']) }}
                            </div>
                            <div class="col-auto verticle_middle">
                                <a href="#" class="f-20 text-danger cloth_type_remove btn-sm"><i
                                        class="ti ti-trash"></i></a>
                            </div>
                        </div>
                    @endif
                    @if (!empty($clothType->clothMeasureType))
                        @foreach ($clothType->clothMeasureType as $clothMeasureType)
                            <div class="row cloth_type">
                                <input type="hidden" name="id[]" value="{{ $clothMeasureType->id }}">
                                <div class="form-group col-md-4">
                                    {{ Form::label('measurement_title', __('Measurement Title'), ['class' => 'form-label']) }}
                                    {{ Form::text('measurement_title[]', $clothMeasureType->title, ['class' => 'form-control', 'placeholder' => __('Enter measurement title'), 'required' => 'required']) }}
                                </div>
                                <div class="form-group col-md-4">
                                    {{ Form::label('measurement_unit', __('Measurement Unit'), ['class' => 'form-label']) }}
                                    {!! Form::select('measurement_unit[]', $unit, $clothMeasureType->unit, ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col align-self-center">
                                    {{ Form::label('order', __('Sequence Order'), ['class' => 'form-label']) }}
                                    {{ Form::number('order[]', $clothMeasureType->order, ['class' => 'form-control sequence-number', 'placeholder' => __('Enter sequence number'), 'required' => 'required']) }}
                                </div>
                                <div class="col-auto verticle_middle">
                                    <a href="#" class="f-20 text-danger cloth_type_remove btn-sm"
                                        data-val="{{ $clothMeasureType->id }}"><i class="ti ti-trash"></i></a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="cloth_type_results"> </div>
                    <div class="row">
                        <div class="form-group text-end">
                            {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary ml-10']) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            function updateSequenceNumbers() {
                document.querySelectorAll('.cloth_type').forEach((item, index) => {
                    item.querySelector('.sequence-number').value = index + 1;
                });
            }
            $('.wrapper').on('click', '.cloth_type_remove', function() {
                $(this).closest('.wrapper').find('.cloth_type').not(':first').last().remove();
                updateSequenceNumbers();
            });

            $('.wrapper').on('click', '.cloth_type_clone', function() {
                let $clone = $(this).closest('.wrapper').find('.cloth_type').first().clone();
                $clone.find('input').val('');
                $clone.appendTo('.cloth_type_results');
                updateSequenceNumbers();
            });
        });

        $('.wrapper').on('click', '.cloth_type_remove', function() {
            var id = $(this).data('val');
            if (id != '') {
                if (confirm('Are you sure you want to delete this element?')) {
                    $.ajax({
                        url: '{{ route('cloth-type.measure.destroy') }}',
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            'id': id
                        },
                        cache: false,
                        success: function(data) {

                        },
                    });
                }
            }
            $('.schedule_remove').closest('.wrapper').find('.schedule').not(':first').last().remove();
        });
    </script>
@endpush

