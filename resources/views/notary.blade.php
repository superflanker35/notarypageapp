@extends('app')
@section('content')
    <notary-entry-component :document_types="{{ $documentTypes }}"></notary-entry-component>
@endsection