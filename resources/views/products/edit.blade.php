@extends('layouts.master')

@section('script_bottom')
    @parent
    <script>
        function updateProduct() {
            var productUpdateFormData = createFormData();

            productUpdateFormData.append('product_id', '{{ $product->id }}');

            axios({
                method: 'post',
                url: '{{ route("products.update") }}',
                data: productUpdateFormData,
                headers: { 'content-type': 'multipart/form-data' },
                // processData: false
            })
                .then(function (response) {
                    if (response.data.success_fail_status && response.data.success_fail_status == 'success') {
                        alert('상품 수정 성공');
                        history.go(-1);
                    } else if (response.data.success_fail_status && response.data.success_fail_status == 'query_fail') {
                        alert('상품 수정 실패');
                        //history.go(-1);
                    } else {
                        console.log(response);
                    }
                })
                .catch(function (error) {
                    if (error.response && error.response.status === 422) {
                        alert(Object.values(error.response.data.errors)[0]);
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log(error.message);
                    }
                })
                .finally(function () {
                    console.log('product in axios!');
                })
        }

    </script>
@endsection

@section('content')

    <h1>상품 수정 폼</h1>

    <div class="card" style="width: 18rem">
        <img src="{{ $product->product_image_path }}" class="card-img-top" />
        <div class="card-body">
            <p class="card-text">상품명 : {{ $product->name }}</p>
        </div>
    </div>

    @include('products.form', [
        'product_name' => $product->name,
        'product_price' => $product->price,
        'product_discounted_price' => $product->discounted_price,
        'product_stock' =>  $product->stock,
        'product_status' => $product_status,
        'product_status_value' =>$product->status,
        'product_parent_category_id' => $product->category->parentCategory->id,
        'product_sub_category_id' => $product->category->id
    ])

    <button type="submit" onclick="updateProduct()">상품등록</button>

@endsection()
