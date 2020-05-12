@extends('layouts.master')

@section('content')
    <h1>브랜드 할인 등록 페이지</h1>

    {{--아이디 : selectBrandId--}}
    @include('brands.select', ['brands' => $brands])

    <br><br>
    할인율 <input type="number" value="0" id="discountPercentage"> % <br><br>
    적용 최소금액 <input type="number" value="0" id="discountTargetMinPrice"> 부터(이상)<br><br>
    시작날짜 <input type="date" id="discountStartDate"><br><br>
    종료날짜 <input type="date" id="discountEndDate"><br><br>

    <button role="button" id="btnBrandProductDiscountStore">저장</button>

    <span id="targetProductsIndex">대상상품 인덱스 영역</span>


@endsection

@section('script_bottom')
    <script>

        /*document.getElementById('btnDiscountTargetProductsDisplay').addEventListener("click", function () {
            displayDiscountTargetProducts(1);
        });*/
        document.getElementById('selectBrandId').addEventListener("change", function () {
            displayDiscountTargetProducts(1);
        });
        document.getElementById('discountTargetMinPrice').addEventListener("keyup", function () {
            displayDiscountTargetProducts(1);
        });
        document.getElementById('discountPercentage').addEventListener("keyup", function () {
            displayDiscountTargetProducts(1)
        });
        document.getElementById('discountPercentage').addEventListener("click", function () {
            displayDiscountTargetProducts(1)
        });
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
                alert('할인정보 등록 성공');
                window.location = '{{ route("brand.discount.list") }}';
            }).catch(function (error) {
                if(error.response) {
                    if (error.response.status == 422) {
                        alert(Object.values(error.response.data.errors)[0]);
                    } else {
                        alert('할인정보 등록 실패')
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

        function displayDiscountTargetProducts(pageNumber) {

            var discountTargetMinPrice = parseInt(document.getElementById('discountTargetMinPrice').value);
            if (isNaN(discountTargetMinPrice)) {
                discountTargetMinPrice = 0;
            }
            var discountTargetBrandId = parseInt(document.getElementsByName('brand_id')[0].value);
            var discountPercentage = parseInt(document.getElementById('discountPercentage').value);
            if (isNaN(discountPercentage)) {
                discountPercentage = 0;
            }
            axios({
                method: 'get',
                url: '{{ route("brand.discount.target.product") }}',
                params: {
                    discount_target_brand_id: discountTargetBrandId,
                    discount_target_min_price: discountTargetMinPrice,
                    discount_percentage: discountPercentage,
                    page: pageNumber
                }
            }).then(function (response) {
                console.log(response);
                var indexHtml = '';
                var targetProducts = response.data.targetProducts.data;
                var targetProductsPaginator = response.data.targetProducts;

                indexHtml += "<p> 총 " + targetProductsPaginator.total + " 개의 상품이 조회됨<p>"
                indexHtml += "<table border='1' class='table table-bordered text-center'>";
                indexHtml += "<thead class='thead-light'>";
                indexHtml += "<tr><th>상품아이디</th><th>상품명</th><th>가격</th><th>할인가</th>";
                indexHtml += "</thead>";
                for(var x = 0; x < targetProducts.length ; x++) {
                    indexHtml += "<tr><td>" + targetProducts[x].id + "</td>";
                    indexHtml += "<td>" + targetProducts[x].name + "</td>";
                    indexHtml += "<td>" + targetProducts[x].price + " 원 </td>";
                    if (discountPercentage != 0) {
                        var discountedPrice = (targetProducts[x].price - (targetProducts[x].price * (discountPercentage/100)));
                        discountedPrice = Math.floor(discountedPrice/100)*100;
                        indexHtml += '<td>' + discountedPrice + ' (할인' + discountPercentage +'%) </td></tr>';
                    } else {
                        indexHtml += "<td>" + targetProducts[x].discounted_price + " 원 </td></tr>";
                    }

                }
                indexHtml += "</table>";
                var currentPage = targetProductsPaginator.current_page;
                if (targetProductsPaginator.prev_page_url != null) {
                    indexHtml += "<button role='button' class='btn btn-light' value='"+targetProductsPaginator.prev_page_url+"' onclick='displayDiscountTargetProducts(" + (currentPage - 1) + ")'>이전페이지로</button>"
                }
                if (targetProductsPaginator.next_page_url != null) {
                    indexHtml += "<button role='button' class='btn btn-light' value='"+targetProductsPaginator.next_page_url+"' onclick='displayDiscountTargetProducts(" + (currentPage + 1) + ")'>다음페이지로</button>"
                }

                document.getElementById('targetProductsIndex').innerHTML = indexHtml;

            }).catch(function (error) {
                if(error.response) {
                    console.log(error.response);
                    if ( error.response.status === 422 ) {
                        alert(Object.values(error.response.data.errors)[0]);
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
    </script>
@endsection
