<form action="{{'login.store'}}" method="post">
    {!! csrf_field() !!}
    <input type="text" name="id" placeholder="아이디(이메일형태)" value="{{ old('id') }}" autofocus>
    {!! $errors->first('id','<span class="form-error">:message</span>') !!}
    <input type="text" name="password" placeholder="비밀번호" >
    {!! $errors->first('password','<span class="form-error">:message</span>') !!}
    <button type="submit">로그인하기</button>
</form>
