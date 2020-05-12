@extends('layouts.master')

@section('content')
    <h1>카테고리 할인 수정 페이지</h1>

    {{ $category_discount_data->category->name }}
    <input type="hidden" id="categoryIdToApplyDiscountUpdate" value="{{ $category_discount_data->category_id }}">
    <input type="hidden" id="categoryDiscountId" value="{{ $category_discount_data->id }}">

    <br><br>
    할인율 <input type="number" value="{{ $category_discount_data->discount_percentage }}" id="discountPercentage"> % <br><br>
    적용 최소금액 <input type="number" value="{{ $category_discount_data->from_price }}" id="discountTargetMinPrice"> 부터(이상)<br><br>
    시작날짜 <input type="date" value="{{ $category_discount_data->start_date }}" id="discountStartDate"><br><br>
    종료날짜 <input type="date" value="{{ $category_discount_data->end_date }}" id="discountEndDate"><br><br>

    <button role="button" class="btn btn-primary float-right" id="btnCategoryProductDiscountUpdate">저장</button>

    <span id="targetProductsIndex">대상상품 인덱스 영역</span>

@endsection

@section('script_bottom')
    <script>
        document.getElementById('btnCategoryProductDiscountUpdate').addEventListener('click', function () {
            var categoryDiscountUpdateFormData = new FormData();

            categoryDiscountUpdateFormData.append('category_discount_id', document.getElementById('categoryDiscountId').value);
            categoryDiscountUpdateFormData.append('discount_percentage', document.getElementById('discountPercentage').value);
            categoryDiscountUpdateFormData.append('discount_target_min_price', document.getElementById('discountTargetMinPrice').value);
            categoryDiscountUpdateFormData.append('discount_start_date', document.getElementById('discountStartDate').value);
            categoryDiscountUpdateFormData.append('discount_end_date', document.getElementById('discountEndDate').value);

            axios({
                method: 'post',
                url:'{{ route("category.discount.update") }}',
                data: categoryDiscountUpdateFormData
            }).then(function (response) {
                console.log(response);
                alert('할인정보 업데이트 성공');
                window.location = '{{ route("category.discount.list") }}';
            }).catch(function (error) {
                if(error.response) {
                    if (error.response.status == 422) {
                        alert(Object.values(error.response.data.errors)[0]);
                    } else {
                        alert('상품등록 실패')
                        console.log(error.response);
                    }
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log('Error', error.message);
                }
            }).finally(function () {
                console.log('great!XD');
            });
        })
    </script>
@endsection
