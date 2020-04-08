<h1>홈입니다.</h1>
{{auth()->user()->name}} 님 환영합니다.
<ul>
    <li>로그인한 상태에만 올 수 있도록 만들기(컨트롤러 연결?)</li>
    <li>나중에 판매자의 회사명, 등록한 판매상품 등 볼 수 있게하기 (엘로퀀트로 유저테이블과 has many 연결)</li>
</ul>
<button><a href="{{ action('SessionsController@destroy') }}">로그아웃</a></button>
