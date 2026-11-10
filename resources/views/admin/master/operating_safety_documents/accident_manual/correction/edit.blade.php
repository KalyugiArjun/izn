@include('admin.master.operating_safety_documents.partials.manual_correction_edit', [
    'record' => $record,
    'originals' => $originals,
    'updateRoute' => 'operating-safety-documents.accident-manual.correction.update',
])
