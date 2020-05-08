@extends('layouts.master')

@section('content')
    <h1>브랜드 할인 등록 페이지</h1>

    @include('brands.select', ['brands' => $brands])

    <br><br>
    할인율 <input type="number" id="discountPercentage"> % <br><br>
    적용 최소금액 <input type="number" id="discountTargetMinPrice"> 부터(이상)<br><br>
    시작날짜 <input type="date" id="discountStartDate"><br><br>
    종료날짜 <input type="date" id="discountEndDate"><br><br>

    <button role="button" id="btnBrandProductDiscountStore">저장</button>
    <button role="button" id="btnDiscountTargetProductsDisplay">대상상품보기</button>

@endsection

@section('script_bottom')
    <script>
        document.getElementById('selectBrandId').addEventListener("change", displayDiscountTargetProducts)
        document.getElementById('discountTargetMinPrice').addEventListener("keyup", displayDiscountTargetProducts);
        document.getElementById('btnBrandProductDiscountStore').addEventListener("click", storeBrandDiscount);

        function storeBrandDiscount() {

            var brandDiscountFormData = new FormData();

            brandDiscountFormData.append('discount_target_brand_id', document.getElementsByName('brand_id')[0].value);
            brandDiscountFormData.append('discount_percentage', parseInt(document.getElementById('discountPercentage').value));
            brandDiscountFormData.append('discount_target_min_price', parseInt(document.getElementById('discountTargetMinPrice').value));
            brandDiscountFormData.append('discount_start_date', document.getElementById('discountStartDate').value);
            brandDiscountFormData.append('discount_end_date', document.getElementById('discountEndDate').value);

            axios({
                method: 'post',
                url:'{{ route("brand.discount.store") }}',
                data: brandDiscountFormData
            }).then(function (response) {
                console.log(response);
            }).catch(function (error) {
                if(error.response) {
                    if (error.response.status == 422) {
                        alert(Object.values(error.response.data.errors)[0]);
                    } else {
                        alert('상품등록 실패')
                    }
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log('Error', error.message);
                }
            }).finally(function () {
                console.log('great!XD');
            });
        }

        function displayDiscountTargetProducts() {

            var discountTargetMinPrice = document.getElementById('discountTargetMinPrice').value; //string
            var discountTargetBrandId = parseInt(document.getElementsByName('brand_id')[0].value);

            axios({
                method: 'get',
                url: '{{ route('brand.discount.target.product') }}',
                params: {
                    discount_target_brand_id: discountTargetBrandId,
                    discount_target_min_price: discountTargetMinPrice
                }
            }).then(function (response) {
                console.log(response);
            }).catch(function (error) {
                if(error.response) {
                    console.log(error.response);
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log('Error', error.message);
                }
            }).finally(function () {
                console.log('great!XD');
            });
        }
    </script>
@endsection
