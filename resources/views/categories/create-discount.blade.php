@extends('layouts.master')

@section('content')
    <h1>카테고리 할인 등록 페이지</h1>

    {{--id="categorySelect"--}}
    @include('categories.select', [
    'categories' => $categories,
    'product_parent_category_id' => '',
    'product_sub_category_id' => ''
    ])

    <br><br>
    할인율 <input type="number" value="0" id="discountPercentage"> % <br><br>
    적용 최소금액 <input type="number" value="0" id="discountTargetMinPrice"> 부터(이상)<br><br>
    시작날짜 <input type="date" id="discountStartDate"><br><br>
    종료날짜 <input type="date" id="discountEndDate"><br><br>

    <button role="button" class="btn btn-primary float-right" id="btnCategoryProductDiscountStore">저장</button>

    <span id="targetProductsIndex">대상상품 인덱스 영역</span>


@endsection

@section('script_bottom')
    <script>

        document.getElementById('btnCategoryProductDiscountStore').addEventListener('click', function () {

            //부모카테고리 선택이 안됨 = #subCategoryId 가 생기지 않음
            if (document.getElementById('subCategoryId') == null) {
                alert('부모 카테고리를 선택해주세요.');
            }

            console.log('카테고리 : ' + document.getElementById('subCategoryId').value);
            console.log('할인율 : ' + document.getElementById('discountPercentage').value);
            console.log('적용 최소금액 : ' + document.getElementById('discountTargetMinPrice').value);
            console.log('시작일 : ' + document.getElementById('discountStartDate').value);
            console.log('종료일 : ' + document.getElementById('discountEndDate').value);

            var categoryProductDiscountData = new FormData();

            categoryProductDiscountData.append('category_id', document.getElementById('subCategoryId').value);
            categoryProductDiscountData.append('discount_percentage', document.getElementById('discountPercentage').value);
            categoryProductDiscountData.append('discount_target_min_price', document.getElementById('discountTargetMinPrice').value);
            categoryProductDiscountData.append('discount_start_date', document.getElementById('discountStartDate').value);
            categoryProductDiscountData.append('discount_end_date', document.getElementById('discountEndDate').value);

            axios({
                method: 'post',
                url: '{{ route("category.discount.store") }}',
                data: categoryProductDiscountData,
            }).then(function (response) {
                console.log(response);
                alert('할인정보 업데이트 성공');
                window.location = '{{ route("category.discount.list") }}';
            }).catch(function (error) {
                if(error.response) {
                    if (error.response.status == 422) {
                        alert(Object.values(error.response.data.errors)[0]);
                    } else {
                        alert('할인정보 등록 실패')
                    }
                    console.log(error.response);
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log('Error', error.message);
                }
            }).finally(function () {
                console.log('great!XD');
            });


        });

    </script>
@endsection
