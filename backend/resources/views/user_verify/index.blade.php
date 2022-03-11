@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{route('regist_verify')}}" method="post">
@csrf
<div>
<p>
<p>email</p>
<input type="text" name="email" value="{{isset($email) ? $email : ''}}">
</div>
<div>
<p>pin code</p>
<input type="text" name="code" value="">
</div>
<div>
<input type="submit">
</div>
</form>