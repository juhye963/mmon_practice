@extends('layouts.master')

@section('content')
<h1>이곳은 회원가입폼입니다.</h1>

<form action={{ route('sellers.store') }} method="post" class="form__auth">
    {!! csrf_field() !!}
    {{--
    CSRF공격 막기 위해 _token 키 가진 숨은 필드 만드는 도우미 함수
    csrf_token은 토큰의 값만 문자열 형태로 가지는데 field는 전체 입력 필드를 토큰값과 함께 작성해줌
    이 토큰값은 인증된 사용자가 application에 request할 수 있는 고유한 사용자임을 확인하는데 사용
    이 함수는 (엄청 긴)HTML태그를 출력하는데, 이걸 이스케이프(보간=그냥 문자열로 인식)하지 않으려고 !!를 씀
    --}}

    @include('errors.validate')

    <input type="text" name="name" placeholder="이름" value="{{ old('name') }}" autofocus>
    <input type="text" name="email" placeholder="이메일" value="{{ old('email') }}">

    <input type="password" name="password" placeholder="비밀번호" >
    <input type="password" name="password_confirmation" placeholder="비밀번호 확인" >

    @include('brands.select')

    <button type="submit">회원가입</button>
</form>
@endsection
