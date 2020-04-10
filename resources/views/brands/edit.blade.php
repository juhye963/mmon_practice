
{{ auth()->user()->name }}님의 현재 브랜드는 {{ $brand_name }} 입니다.

<form action="{{ route('brands.update') }}">
    @include('brands.select')
</form>
