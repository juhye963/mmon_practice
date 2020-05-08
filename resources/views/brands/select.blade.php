
<h4> 브랜드 선택 </h4>
<select id = "selectBrandId" name="brand_id" size="1">
    <option value="">브랜드 선택</option>
@foreach($brands as $brand)
    <option value="{{ $brand->id }}"> {{ $brand->name }} </option>
@endforeach
{{--{{ $brands }}--}}
</select>
