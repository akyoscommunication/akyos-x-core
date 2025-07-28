@extends('akyos-modules::layouts.admin')

@section('title', 'Modules Akyos')

@section('content')
<div class="wrap">
    <h1>Modules Akyos</h1>
    <p>Gérez les modules de votre site WordPress</p>

    <div class="akyos-modules-grid">
        @foreach($modules as $moduleName => $moduleClass)
        @php
        $isActive = $moduleManager::isModuleActive($moduleName);
        @endphp
        <div class="akyos-module-card {{ $isActive ? 'active' : 'inactive' }}">
            <div class="module-header">
                <h3>{{ $moduleClass::getName() }}</h3>
                <div class="module-status">
                    <span class="status-indicator {{ $isActive ? 'active' : 'inactive' }}"></span>
                    <span class="status-text">{{ $isActive ? 'Actif' : 'Inactif' }}</span>
                </div>
            </div>

            <div class="module-content">
                <p class="module-description">{{ $moduleClass::getDescription() }}</p>
            </div>

            <div class="module-actions">
                <button
                    class="button {{ $isActive ? 'button-secondary' : 'button-primary' }} toggle-module"
                    data-module="{{ esc_attr($moduleName) }}"
                    data-action="{{ $isActive ? 'deactivate' : 'activate' }}">
                    {{ $isActive ? 'Désactiver' : 'Activer' }}
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Le JavaScript sera chargé via le ModuleManager
</script>
@endpush