@extends('layouts.master')

@section('content')
<h1>전체상품 조회 영역입니다.</h1>

<p>총 {{ $products->total() }} 개의 상품이 조회되었습니다.</p>
<b>검색키워드 : </b> {{ $srch_key_org }}
<b>정렬기준 : </b>{{ $sort }}

<div class="form-group float-right">
    <form name="srch_frm" class="form-inline" action="{{ route('products.index') }}" method="get">
        <input name="prds_nm" class="form-control mr-sm-2" type="search" placeholder="상품명으로 찾기"
               value="{{ isset($_GET['prds_nm']) ? $_GET['prds_nm'] : '' }}">
        <input name="seller_nm" class="form-control mr-sm-2" type="search" placeholder="판매자 이름으로 찾기"
               value="{{ isset($_GET['seller_nm']) ? $_GET['seller_nm'] : '' }}">
        <select name="sort">
            <option value="recent" selected>최근등록순</option>
            <option value="price_asc">낮은 가격순</option>
            <option value="price_desc">높은 가격순</option>
            <option value="prds_name">상품명 순</option>
        </select>
        <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Search</button>
    </form>
</div>

<table cellpadding="10" class="table table-borderless table-sm">
    <thead class="thead-light">
        <tr>
            <th>상품번호</th>
            <th>상품명</th>
            <th>가격</th>
            <th>할인가</th>
            <th>재고</th>
            <th>브랜드</th>
            <th>카테고리</th>
            <th>등록자</th>
            <th>상품삭제</th>
        </tr>
    </thead>
    @foreach($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->price }} 원</td>
            <td>{{ $product->discounted_price}} 원</td>
            <td>{{ $product->amount }}</td>
            <td>{{ $product->brand->name }}</td>
            <td>{{ $product->category->name }}</td>
            <td>{{ $product->seller->name }}</td>
            <td><a class="btn btn-light" href="{{ route('products.destroy',['product_id' => $product->id]) }}" role="button">삭제</a></td>
        </tr>
    @endforeach
</table>

{{ $products->links() }}

@endsection
