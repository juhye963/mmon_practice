<h1>이곳은 상품등록폼입니다.</h1>

<form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
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

    <input type="text" name="name" placeholder="상품명" value="{{ old('name') }}" autofocus><br>
    <input type="file" name="product_image"><br>
    <input type="number" name="price" placeholder="상품가격" value="{{ old('price') }}" min="1" max="1000000"> 원<br>
    <input type="number" name="discounted_price" placeholder="할인가" value="{{ old('discounted_price') }}" min="1" max="1000000"> 원<br>
    <input type="number" name="amount" placeholder="재고" value="{{ old('amount') }}" min="1" max="1000"> 개<br>

    @include('categories.select')

    <br>
    <button type="submit">상품등록</button>

</form>
