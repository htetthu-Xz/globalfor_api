@extends('layouts.studio')
@section('title', 'Edit news')
@section('content')
    <livewire:studio.news-composer :news-item="$newsItem" />
@endsection
