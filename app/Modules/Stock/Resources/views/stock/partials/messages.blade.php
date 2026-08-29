@if(session('success'))
    <div class="stock-alert stock-alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="stock-alert stock-alert-danger">
        <strong>Please fix the following:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
