@extends('layouts.studio')
@section('title', 'Edit activity')
@section('content')
    <livewire:studio.activity-composer :activity="$activity" />
@endsection
