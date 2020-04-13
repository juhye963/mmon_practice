<h3>상품 카테고리 셀렉 영역입니다.</h3>
<select name="category_id" size="1">
    <option value="">카테고리 선택</option>
    @foreach($categories as $category)
        <option value="{{ $category->id }}"> {{ $category->name }} </option>
    @endforeach
</select>
