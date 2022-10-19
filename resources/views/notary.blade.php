@extends('app')
@section('content')
    <notary-entry-component :document_types="{{ $documentTypes }}" :notary_records="{{ !empty($notaryRecords) ? $notaryRecords : "[]" }}"></notary-entry-component>
@endsection