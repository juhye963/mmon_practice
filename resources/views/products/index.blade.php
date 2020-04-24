@extends('layouts.master')

@section('style')
    <style>
        div {padding: 20px}
    </style>
@endsection

@section('script')
    <script>
        
    </script>
@endsection

@section('content')
<h1>전체상품 조회 영역입니다.</h1>

<p>총 {{ $products->total() }} 개의 상품이 조회되었습니다.</p>

@include('errors.validate')

<a class="btn btn-dark" data-toggle="collapse" href="#collapseSearchForm">검색하기</a>
<div class="collapse" id="collapseSearchForm">
<form name="srch_frm" class="bg-light text-dark rounded-lg" action="{{ route('products.index') }}" method="get">
    <div class="form-group">
        <legend>검색</legend>
        <label for="searchTypeSelect">검색조건</label>
        <select class="form-control" name="search_type" id="searchTypeSelect">
            <option value="" ></option>
            @foreach($search_types as $key => $value)
                <option value="{{ $key }}" {{ $key != $parameters['search_type'] ? '' : 'selected' }}>{{ $value }}</option>
            @endforeach
        </select>
        <label for="searchWord">검색어</label>
        <input name="search_word" id="searchWord" class="form-control" type="search" placeholder="Search"
               value="{{ $parameters['search_word'] }}">
    </div>

    <div class="form-group">
        <legend>정렬조건</legend>
        <select class="form-control" name="sort">
            @foreach($sorts as $key => $value)
                <option value="{{ $key }}" {{ $key != $parameters['sort'] ? '' : 'selected' }}>{{ $value }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <legend>날짜검색</legend>
        시작일<input name="start_date" class="form-control" type="date" value="{{ $parameters['start_date'] }}">
        종료일<input name="end_date" class="form-control" type="date" value="{{ $parameters['end_date'] }}">
    </div>

    <div class="form-group">
        <legend>판매상태</legend>
        @foreach($prds_status as $display_prds_status_key => $display_prds_status_value)
            {{ $display_prds_status_value }}
            <input type="checkbox" name="prds_status[]" value="{{ $display_prds_status_key }}"
            {{ in_array($display_prds_status_key, $parameters['prds_status']) !== false ? 'checked' : '' }} />
        @endforeach
    </div>
    <button type="submit" class="btn btn-outline-success float-right">Search</button>
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
            <th>상품수정</th>
        </tr>
    </thead>
    @foreach($products as $product)
        <tr>
            <td>
                {{ $product->id }}
                <input type="checkbox" id="multiDelete{{ $product->id }}" name="productMultiDelete[]" value="{{ $product->id }}" />
            </td>
            <td><button type="button" class="btn btn-light" data-toggle="modal" data-target="#productImage{{ $product->id }}">{{ $product->name }}</button></td>
            <div class="modal fade" id="productImage{{ $product->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">{{ $product->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $product->product_image_path }}" class="img-thumbnail">
                        </div>
                    </div>
                </div>
            </div>
            <td>{{ $product->price }} 원</td>
            <td>{{ $product->discounted_price}} 원</td>
            <td>{{ $product->stock }}</td>
            <td>{{ $product->brand->name }}</td>
            <td>{{ $product->category->name }}</td>
            <td>{{ $product->seller->name }}</td>
            <td>{{ $product->created_at }}</td>
            <td>{{ $product->status }}</td>
            <td><a class="btn btn-light" role="button" onclick="deleteProduct({{ $product->id }})">삭제</a></td>
            <td><a class="btn btn-light" href="{{ route('products.edit', ['product_id' => $product->id]) }}" role="button">수정</a></td>
        </tr>
    @endforeach
</table>

<button class="btn btn-dark" role="button" id="multiDelete">일괄삭제</button>

<div class="pagination justify-content-center">
{{ $products->appends($parameters)->links()}}
</div>

@endsection
