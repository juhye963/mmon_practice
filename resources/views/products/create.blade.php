
@extends('layouts.master')

@section('script')
    @parent
    {{-- 부모컨텐츠를 겹쳐 쓰지 않고 추가합니다 --}}
    {{-- include한 곳에서도 써야함 --}}
    <script type="text/javascript">
        function registerProduct () {

            var productRegisterFormData = createFormData();

            axios({
                method: 'post',
                url: '{{ route("products.store") }}',
                data: productRegisterFormData,
                headers: { 'content-type': 'multipart/form-data' },
                // processData: false
            })
            .then(function (response) {
                if (response.data.success_fail_status && response.data.success_fail_status == 'success') {
                    alert('상품 등록 성공');
                    window.location = '{{ route('products.index') }}';
                } else if (response.data.success_fail_status && response.data.success_fail_status == 'query_fail') {
                    alert('상품 DB 등록 실패');
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

<h3>상품등록</h3>

@include('products.form', [
    'product_name' => old('name'),
    'product_price' => old('price'),
    'product_discounted_price' => old('discounted_price'),
    'product_stock' => old('stock'),
    'product_status' => $product_status,
    'product_parent_category_id' => '',
    'product_sub_category_id' => '',
    'product_status_value' => ''
])

<button type="submit" onclick="registerProduct()">상품등록</button>

@endsection
