<h1>이곳은 로그인폼입니다.</h1>

<form action="{{'login'}}" method="post">
    {!! csrf_field() !!}
    <input type="text" name="email" placeholder="아이디(이메일형태)" value="{{ old('email') }}" autofocus>
    {!! $errors->first('email','<span class="form-error">:message</span>') !!}
    <input type="password" name="password" placeholder="비밀번호" >
    {!! $errors->first('password','<span class="form-error">:message</span>') !!}
    <button type="submit">로그인</button>
</form>
