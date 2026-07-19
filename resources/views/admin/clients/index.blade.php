@extends('admin.layouts.app')

@section('title', 'Clientes (Consumidores) - Panel de Administración')
@section('header', 'Gestión de Clientes')
@section('description', 'Administra a tus consumidores finales, sus límites y accesos')

@section('content')

<livewire:admin.client-list />
@endsection


