
{{ auth()->user()->name }}님의 현재 브랜드는 {{ $brand_name }} 입니다.
{!! csrf_field() !!}
<form action="{{ route('brands.update') }}" method="post">
    @csrf
    @include('brands.select')
    <button type="submit">브랜드 변경</button>
</form>

