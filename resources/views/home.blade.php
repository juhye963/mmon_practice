<h1>홈입니다.</h1>
<p>{{auth()->user()->name}} 님 환영합니다.</p>
<p>소속 브랜드 : {{auth()->user()->brand->name}}</p>
<button><a href="{{ route('seller.brand.edit') }}">브랜드수정</a></button>
<button><a href="{{ route('sessions.destroy') }}">로그아웃</a></button>
<button><a href="{{ route('products.create') }}">상품등록</a></button>
<button><a href="{{ route('products.index') }}">상품조회</a></button>
<button><a href="{{ route('brand.discount.create') }}">브랜드할인 등록</a></button>
<button><a href="{{ route('brand.discount.list') }}">브랜드할인 목록 보기</a></button>
<button><a href="{{ route('category.discount.create') }}">카테고리 할인 등록</a></button>
<button><a href="{{ route('category.discount.list') }}">카테고리 할인 목록 보기</a></button>
<button><a href="{{ route('products.statistics') }}">상품통계보기</a></button>

