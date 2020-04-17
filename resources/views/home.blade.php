<h1>홈입니다.</h1>
<p>{{auth()->user()->name}} 님 환영합니다.</p>
<p>소속 브랜드 : {{auth()->user()->brand->name}}</p>
<button><a href="{{ route('seller.brand.edit') }}">브랜드수정</a></button>
<button><a href="{{ route('sessions.destroy') }}">로그아웃</a></button>
<button><a href="{{ route('products.create') }}">상품등록</a></button>
<button><a href="{{ route('products.index') }}">상품조회</a></button>

<!--직접 컨트롤러와 연결x 라우트 이름과 연결하기 = 확장성 좋아짐-->
