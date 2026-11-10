@include('admin.master.operating_safety_documents.partials.simple_document_edit', [
    'model' => $alert,
    'updateRoute' => 'operating-safety-documents.operating-safety-alert.update',
    'numberField' => 'alert_no',
    'numberLabel' => 'Alert No.'
])