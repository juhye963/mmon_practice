@extends('layouts.master')

@section('content')

    <h1>브랜드 할인 리스트 페이지</h1>

    <table class="table table-bordered text-center">
        <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>브랜드</th>
            <th>적용 최소금액</th>
            <th>할인율(%)</th>
            <th>해당 상품 수</th>
            <th>시작일</th>
            <th>종료일</th>
            <th>관리</th>
        </tr>
        </thead>

        @foreach($brand_product_discount_lists as $discount)
            <tr>
                <td>{{ $discount->id }}</td>
                <td>{{ $discount->brand->name }}</td>
                <td>{{ $discount->from_price }}</td>
                <td>{{ $discount->discount_percentage }}</td>
                <td>해당상품수</td>
                <td>{{ $discount->start_date }}</td>
                <td>{{ $discount->end_date }}</td>
                <td>수정삭제버튼</td>
            </tr>
        @endforeach

    </table>

    <a href="{{ route('brand.discount.create') }}" role="button" class="btn btn-light">브랜드할인 등록</a>

@endsection
