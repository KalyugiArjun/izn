@include('admin.master.operating_safety_documents.partials.manual_correction_edit', [
    'record' => $record,
    'originals' => $originals,
    'updateRoute' => 'operating-safety-documents.operating-manual.correction.update',
])
