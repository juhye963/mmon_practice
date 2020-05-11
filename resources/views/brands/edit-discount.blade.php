@extends('layouts.master')

@section('content')
    <h1>브랜드 할인 수정 페이지</h1>

    {{ $brand_discount_data->brand->name }}
    <input type="hidden" id="brandIdToApplyDiscountUpdate" value="{{ $brand_discount_data->brand_id }}">
    <input type="hidden" id="brandDiscountId" value="{{ $brand_discount_data->id }}">

    <br><br>
    할인율 <input type="number" id="discountPercentage" value="{{ $brand_discount_data->discount_percentage }}"> % <br><br>
    적용 최소금액 <input type="number" id="discountTargetMinPrice" value="{{ $brand_discount_data->from_price }}"> 부터(이상)<br><br>
    시작날짜 <input type="date" id="discountStartDate" value="{{ $brand_discount_data->start_date }}"><br><br>
    종료날짜 <input type="date" id="discountEndDate" value="{{ $brand_discount_data->end_date }}"><br><br>

    <button role="button" id="btnBrandProductDiscountUpdate">저장</button>
    <button role="button" id="btnNewDiscountTargetProductsDisplay">대상상품보기</button>

    <p id="targetProductsIndex">대상상품 인덱스 영역</p>


@endsection

@section('script_bottom')
    <script>
        document.getElementById('btnNewDiscountTargetProductsDisplay').addEventListener("click", displayNewDiscountTargetProducts);
        document.getElementById('btnBrandProductDiscountUpdate').addEventListener("click", updateBrandDiscount);

        function updateBrandDiscount() {

            var brandDiscountFormData = new FormData();

            brandDiscountFormData.append('brand_discount_id', parseInt(document.getElementById('brandDiscountId').value));
            brandDiscountFormData.append('discount_percentage', parseInt(document.getElementById('discountPercentage').value));
            brandDiscountFormData.append('discount_target_min_price', parseInt(document.getElementById('discountTargetMinPrice').value));
            brandDiscountFormData.append('discount_start_date', document.getElementById('discountStartDate').value);
            brandDiscountFormData.append('discount_end_date', document.getElementById('discountEndDate').value);

            axios({
                method: 'post',
                url:'{{ route("brand.discount.update") }}',
                data: brandDiscountFormData
            }).then(function (response) {
                console.log(response);
                alert('할인정보 업데이트 성공');
                window.location = '{{ route("brand.discount.list") }}';
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

        function displayNewDiscountTargetProducts() {

            var discountTargetMinPrice = document.getElementById('discountTargetMinPrice').value; //string
            var discountTargetBrandId = parseInt(document.getElementById('brandIdToApplyDiscountUpdate').value);
            var dynamicTargetProductsTableHtml = '';
            console.log(discountTargetBrandId);

            axios({
                method: 'get',
                url: '{{ route('brand.discount.target.product') }}',
                params: {
                    discount_target_brand_id: discountTargetBrandId,
                    discount_target_min_price: discountTargetMinPrice
                }
            }).then(function (response) {
                var targetProducts = response.data.targetProducts;
                console.log(typeof (targetProducts));

                dynamicTargetProductsTableHtml += "<p> 총 " + Object.keys(targetProducts).length + " 개의 상품이 조회됨<p>"
                dynamicTargetProductsTableHtml += "<table border='1' class='table table-bordered text-center'>";
                dynamicTargetProductsTableHtml += "<thead class='thead-light'>";
                dynamicTargetProductsTableHtml += "<tr><th>상품명</th><th>가격</th><th>할인가</th>";
                dynamicTargetProductsTableHtml += "</thead>";

                for(x in targetProducts) {
                    dynamicTargetProductsTableHtml += "<tr><td>" + JSON.stringify(targetProducts[x].name) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].price) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].discounted_price) + "</td></tr>";
                }
                dynamicTargetProductsTableHtml += "</table>";
                document.getElementById('targetProductsIndex').innerHTML = dynamicTargetProductsTableHtml;

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
