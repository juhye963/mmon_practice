<h1> 여기는 브랜드 셀렉 영역 </h1>
<select  name="brand_id" size="1">
    <option value="">브랜드 선택</option>
@foreach($brands as $brand)
    <option value="{{ $brand->id }}"> {{ $brand->name }} </option>
@endforeach
{{--{{ $brands }}--}}
</select>
