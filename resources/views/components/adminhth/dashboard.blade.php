@extends('layouts.main')

@section('content')
<div class="admin-dashboard-container">
    @include('admin.components.admin-header')
    
    <div class="dashboard-content">
        @include('admin.components.admin-navigation')
        
        <div class="dashboard-main">
            @include('admin.components.admin-statistics-overview')
            
            <div class="dashboard-sections">
                <div class="left-column">
                    @include('admin.components.admin-recent-cases')
                    @include('admin.components.admin-user-activity')
                </div>
                
                <div class="right-column">
                    @include('admin.components.admin-machine-status')
                    @include('admin.components.admin-notifications')
                    @include('admin.components.admin-quick-actions')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection