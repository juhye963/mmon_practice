
{{ auth()->user()->name }}님의 현재 브랜드는 {{ $brand_name }} 입니다.
{!! csrf_field() !!}
<form action="{{ route('brands.update') }}" method="post">
    @csrf
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @include('brands.select')
    <button type="submit">브랜드 변경</button>
</form>

