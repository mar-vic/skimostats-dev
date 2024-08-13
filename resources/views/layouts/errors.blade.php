@if (count($errors) > 0)
<div class='alert alert-danger mt-2'>
    <ul style='margin-bottom:0;'>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
