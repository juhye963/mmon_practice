@extends('layouts.master')

@section('content')
<h1>전체상품 조회 영역입니다.</h1>

<p>총 {{ $products->total() }} 개의 상품이 조회되었습니다.</p>

오늘 날짜 : {{ date('Y-m-d') }}

@include('errors.validate')

<div class="form-group">
    <form name="srch_frm" class="form-inline" action="{{ route('products.index') }}" method="get">
        <select class="form-control" name="search_type">
            @foreach($search_types as $key => $value)
                <option value="{{ $key }}" {{ $key != $parameters['search_type'] ? '' : 'selected' }}>{{ $value }}</option>
            @endforeach
        </select>

        <input name="search_word" class="form-control mr-sm-2" type="search" placeholder="Search" value="{{ $parameters['search_word'] }}">

        <select class="form-control" name="sort">
             @foreach($sorts as $key => $value)
                <option value="{{ $key }}" {{ $key != $parameters['sort'] ? '' : 'selected' }}>{{ $value }}</option>
             @endforeach
        </select>

        <fieldset class="form-group">
            시작일<input name="start_date" class="form-control" type="date" value="{{ $parameters['start_date'] }}">
            종료일<input name="end_date" class="form-control" type="date" value="{{ $parameters['end_date'] }}">
        </fieldset>

        <fieldset>
            <legend>판매상태</legend>
            @foreach($prds_status as $display_prds_status_key => $display_prds_status_value)
                {{ $display_prds_status_value }}
                <input type="checkbox" name="prds_status[]" value="{{ $display_prds_status_key }}"
                @foreach($parameters['prds_status'] as $input_prds_status_value)
                    {{ $display_prds_status_key != $input_prds_status_value ? '' : 'checked'}}
                    @endforeach
                >
            @endforeach
        </fieldset>

        <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Search</button>
    </form>
</div>

<table cellpadding="10" class="table table-bordered text-center">
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
            <th>등록일</th>
            <th>상태</th>
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
            <td>{{ $product->created_at }}</td>
            <td>{{ $product->status }}</td>
            <td><a class="btn btn-light" href="{{ route('products.destroy',['product_id' => $product->id]) }}" role="button">삭제</a></td>
        </tr>
    @endforeach
</table>

<div class="pagination justify-content-center">
{{ $products->appends($parameters)->links()}}
</div>

@endsection
