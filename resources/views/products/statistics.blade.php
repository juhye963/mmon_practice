@extends('layouts.master')

@section('content')
    <h1>브랜드별 통계</h1>
    <table cellpadding="10" class="table table-bordered text-center">
        <thead class="thead-light">
        <tr>
            <th>브랜드명</th>
            <th>카테고리별</th>
            <th>총 상품수</th>
            <th>판매중</th>
            <th>판매중지</th>
            <th>품절</th>
        </tr>
        </thead>
        @foreach($brands as $brand)
            <tr>
                <td rowspan="{{ count($brand['categories'])+1 }}">{{ $brand['brand_id'] }}</td>
                <td rowspan="{{ count($brand['categories'])+1 }}">{{ $brand['brand_name'] }}</td>
                <td>0</td>
                <td>총 상품수</td>
                <td>{{ $brand['total'] }}</td>
                <td>{{ $brand['selling'] }}</td>
                <td>{{ $brand['stop_selling'] }}</td>
                <td>{{ $brand['sold_out'] }}</td>
            </tr>
            @foreach($brand['categories'] as $brand_category)
                <tr>
                    <td>{{ $brand_category['category_id'] }}</td>
                    <td>{{ $brand_category['category_name'] }}</td>
                    <td>{{ $brand_category['total'] }}</td>
                    <td>{{ $brand_category['selling'] }}</td>
                    <td>{{ $brand_category['sold_out'] }}</td>
                    <td>{{ $brand_category['stop_selling'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>
    <hr>
    <h1>카테고리별 통계</h1>
    <table cellpadding="10" class="table table-bordered text-center">
        <thead class="thead-light">
        <tr>
            <th>카테고리 번호</th>
            <th>카테고리명</th>
            <th>브랜드번호</th>
            <th>브랜드별</th>
            <th>총 상품수</th>
            <th>판매중</th>
            <th>판매중지</th>
            <th>품절</th>
        </tr>
        </thead>
        @foreach($categories as $category)
            <tr>
                <td rowspan="{{ count($category['brands'])+1 }}">{{ $category['category_id'] }}</td>
                <td rowspan="{{ count($category['brands'])+1 }}">{{ $category['category_name'] }}</td>
                <td>0</td>
                <td>총 상품수</td>
                <td>{{ $category['total'] }}</td>
                <td>{{ $category['selling'] }}</td>
                <td>{{ $category['stop_selling'] }}</td>
                <td>{{ $category['sold_out'] }}</td>
            </tr>
            @foreach($category['brands'] as $category_brand)
                <tr>
                    <td>{{ $category_brand['brand_id'] }}</td>
                    <td>{{ $category_brand['brand_name'] }}</td>
                    <td>{{ $category_brand['total'] }}</td>
                    <td>{{ $category_brand['selling'] }}</td>
                    <td>{{ $category_brand['sold_out'] }}</td>
                    <td>{{ $category_brand['stop_selling'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>
    <hr>
    <h1>셀러별 통계</h1>
    <table cellpadding="10" class="table table-bordered text-center">
        <thead class="thead-light">
        <tr>
            <th>셀러 번호</th>
            <th>셀러이름</th>
            <th>총 상품수</th>
            <th>판매중</th>
            <th>판매중지</th>
            <th>품절</th>
        </tr>
        </thead>
        @foreach($sellers as $seller)
            <tr>
                <td>{{ $seller['seller_id'] }}</td>
                <td>{{ $seller['seller_name'] }}</td>
                <td>{{ $seller['total'] }}</td>
                <td>{{ $seller['selling'] }}</td>
                <td>{{ $seller['stop_selling'] }}</td>
                <td>{{ $seller['sold_out'] }}</td>
            </tr>
        @endforeach
    </table>
@endsection
