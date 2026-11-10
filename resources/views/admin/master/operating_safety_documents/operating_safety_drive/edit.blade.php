@include('admin.master.operating_safety_documents.partials.simple_document_edit', [
    'model' => $drive,
    'updateRoute' => 'operating-safety-documents.operating-safety-drive.update',
    'numberField' => 'drive_no',
    'numberLabel' => 'Drive No.',
])
