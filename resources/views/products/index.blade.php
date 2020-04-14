@extends('layouts.master')

@section('content')
<h1>상품 조회 영역입니다.</h1>

<p>{{auth()->user()->name}} 님이 업로드한 상품은 총 {{ $products->total() }} 개 입니다.</p>

<table border="1" cellpadding="10" class="table table-borderless table-sm">
    <thead class="thead-light">
        <tr>
            <th>상품번호</th>
            <th>상품명</th>
            <th>첨부파일명</th>
            <th>가격</th>
            <th>할인가</th>
            <th>재고</th>
            <th>브랜드</th>
            <th>카테고리</th>
        </tr>
    </thead>
    @foreach($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->filename }}</td>
            <td>{{ $product->price }} 원</td>
            <td>{{ $product->discount}} 원</td>
            <td>{{ $product->amount }}</td>
            <td>{{ $product->brand->name }}</td>
            <td>{{ $product->category->name }}</td>
        </tr>
    @endforeach
</table>

{{ $products->links() }}

    @endsection
