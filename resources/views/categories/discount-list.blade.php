@extends('layouts.master')

@section('content')

    <h1>카테고리 할인 리스트 페이지</h1>

    <table class="table table-bordered text-center">
        <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>카테고리</th>
            <th>적용 최소금액</th>
            <th>할인율(%)</th>
            <th>해당 상품 수</th>
            <th>시작일</th>
            <th>종료일</th>
            <th>관리</th>
        </tr>
        </thead>

        @foreach($category_product_discount_lists as $discount)
            <tr>
                <td>{{ $discount->id }}</td>
                <td>{{ $discount->category->name }}</td>
                <td>{{ number_format($discount->from_price) }}</td>
                <td>{{ $discount->discount_percentage }}</td>
                <td>{{ number_format($discount->products_count) }}</td>
                <td>{{ $discount->start_date }}</td>
                <td>{{ $discount->end_date }}</td>
                <td>
                    <a role="button" href="{{ route('category.discount.edit', ['category_discount_id' => $discount->id]) }}" id="categoryDiscountEdit"{{$discount->id}} class="btn btn-light">수정</a>
                </td>
            </tr>
        @endforeach

    </table>

    <a href="{{ route('category.discount.create') }}" role="button" class="btn btn-light">카테고리할인 등록</a>

@endsection
