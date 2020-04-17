@extends('layouts.master')

@section('content')
<h1>전체상품 조회 영역입니다.</h1>

<p>총 {{ $products->total() }} 개의 상품이 조회되었습니다.</p>

@include('errors.validate')

<div class="form-group float-right">
    <form name="srch_frm" class="form-inline" action="{{ route('products.index') }}" method="get">
        <select class="form-control" name="search_type">
            @foreach($search_types as $key => $value)
                <option value="{{ $value }}" {{ $value != $parms['search_type'] ? '' : 'selected' }}>{{ $key }}</option>
            @endforeach
        </select>

        <input name="search_word" class="form-control mr-sm-2" type="search" placeholder="Search" value="{{ $parms['search_word'] }}">

        <select class="form-control" name="sort">
             @foreach($sorts as $key => $value)
                <option value="{{ $value }}" {{ $value != $parms['sort'] ? '' : 'selected' }}>{{ $key }}</option>
             @endforeach
        </select>
        <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Search</button>
    </form>
</div>

<table cellpadding="10" class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
            @foreach($prds_theads as $head)
                <th>{{ $head }}</th>
            @endforeach
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

<div class="pagination justify-content-center">
{{ $products->appends([
    'search_type' => $parms['search_type'],
    'search_word' => $parms['search_word'],
    'sort' => $parms['sort']
    ])->links()}}
</div>

@endsection
