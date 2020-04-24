@section('script')
    @parent
    <script>
        function createFormData() {
            var productFormData = new FormData();

            productFormData.append('product_name', document.getElementById('productName').value);
            productFormData.append('product_price', parseInt(document.getElementById('productPrice').value));
            productFormData.append('product_discounted_price', parseInt(document.getElementById('productDiscountedPrice').value));
            productFormData.append('product_stock', document.getElementById('productStock').value);
            productFormData.append('category_pid', document.getElementById('categoryPid').value);

            if (document.getElementById("subCategoryId")) {
                productFormData.append('sub_category_id', document.getElementById('subCategoryId').value);
            }

            var i;
            var productStatusSelect = document.getElementsByName('productStatus');
            for (i = 0; i< productStatusSelect.length; i++) {
                if (!productStatusSelect[i].checked) {
                    continue;
                }
                productFormData.append('product_status', productStatusSelect[i].value);
            }

            productFormData.append('product_image', document.getElementById('productImageFile').files[0]);

            return productFormData
        }
    </script>
@endsection

<div class="form-group">
    <label for="productName">상품명</label>
    <input type="text" id="productName" class="form-control" name="productName" placeholder="상품명" value="{{ $product_name}}" autofocus>
</div>
<div class="form-group">
    <label for="productImageFile">상품 이미지</label>
    <input type="file" id="productImageFile" class="form-control-file" name="productImage" />
</div>
<div class="form-group">
    <label for="productPrice"> 상품 가격</label>
    <input type="number" id="productPrice" class="form-control" name="productPrice" placeholder="상품가격" value="{{ $product_price }}" min="1" max="1000000">
</div>
<div class="form-group">
    <label for="productDiscountedPrice">할인가</label>
    <input type="number" id="productDiscountedPrice" class="form-control" name="discountedPrice" placeholder="할인가" value="{{ $product_discounted_price }}" min="1" max="1000000">
</div>
<div class="form-group">
    <label for="productStock">재고</label>
    <input type="number" id="productStock" class="form-control" name="productStock" placeholder="재고" value="{{ $product_stock }}" min="1" max="1000">
</div>

@include('categories.select', [
    'product_parent_category_id' => $product_parent_category_id,
    'product_sub_category_id' => $product_sub_category_id
])

<div class="form-group" id="productStatus">
    <legend>판매상태</legend>
    @foreach($product_status as $key => $status_val)
        <input type="radio" name="productStatus" value="{{ $key }}" {{ $key != $product_status_value ?  '' : 'checked'}} />
        <label> {{ $status_val }}</label>
    @endforeach
</div>

{{--<button type="submit" onclick="registerProduct()">상품등록</button>--}}
