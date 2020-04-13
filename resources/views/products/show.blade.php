<h1>상품 조회 영역입니다.</h1>

<p>{{auth()->user()->name}} 님이 업로드한 상품은 총 {{ $product_cnt }} 개 입니다.</p>

<table>
    <tr>
        <th>상품번호</th>
        <th>상품명</th>
        <th>첨부파일명</th>
        <th>가격</th>
        <th>할인율</th>
        <th>할인가</th>
        <th>재고</th>
        <th>브랜드</th>
        <th>카테고리</th>
    </tr>
    @foreach($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->filename }}</td>
            <td>{{ $product->price }}</td>
            <td>{{ ($product->discount)*100 }}</td>
            <td>할인가 계산</td>
            <td>{{ $product->amount }}</td>
            <td>{{ $product->brand_id }}</td>
            <td>{{ $product->category_id }}</td>
        </tr>
    @endforeach
</table>
