@include('admin.master.operating_safety_documents.partials.simple_document_edit', [
    'model' => $episode,
    'updateRoute' => 'operating-safety-documents.operating-safety-episode.update',
    'numberField' => 'episode_no',
    'numberLabel' => 'Episode No.'
])