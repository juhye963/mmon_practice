@extends('layouts.master')

@section('content')
    <h1>브랜드 할인 등록 페이지</h1>

    @include('brands.select', ['brands' => $brands])

    <br><br>
    할인율 <input type="number" value="0" id="discountPercentage"> % <br><br>
    적용 최소금액 <input type="number" value="0" id="discountTargetMinPrice"> 부터(이상)<br><br>
    시작날짜 <input type="date" id="discountStartDate"><br><br>
    종료날짜 <input type="date" id="discountEndDate"><br><br>

    <button role="button" id="btnBrandProductDiscountStore">저장</button>
    <button role="button" class="btn btn-light" id="btnDiscountTargetProductsDisplay">대상상품보기</button>

    <span id="targetProductsIndex">대상상품 인덱스 영역</span>


@endsection

@section('script_bottom')
    <script>

        document.getElementById('btnDiscountTargetProductsDisplay').addEventListener("click", displayDiscountTargetProducts);
        document.getElementById('btnBrandProductDiscountStore').addEventListener("click", storeBrandDiscount);

        function storeBrandDiscount() {

            var brandDiscountFormData = new FormData();

            if(isNaN(discountTargetMinPrice)) {
                brandDiscountFormData.append('discount_target_min_price', '0');
            } else {
                brandDiscountFormData.append('discount_target_min_price', parseInt(document.getElementById('discountTargetMinPrice').value));
            }

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
                //alert('할인정보 등록 성공');
                //window.location = '{{ route("brand.discount.list") }}';
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

        function displayDiscountTargetProducts(_event) {
            _event.preventDefault();
            var discountTargetMinPrice = document.getElementById('discountTargetMinPrice').value; //string
            var discountTargetBrandId = parseInt(document.getElementsByName('brand_id')[0].value);
            var dynamicTargetProductsTableHtml = '';

            /*var url = 'http://board-test.localhost/brand-discount-create' + '?discount_target_brand_id=' + discountTargetBrandId + '&discount_target_min_price=' + discountTargetMinPrice
            console.log(url);*/

            axios({
                method: 'get',
                url: '{{ route("brand.discount.target.product") }}',
                params: {
                    discount_target_brand_id: discountTargetBrandId,
                    discount_target_min_price: discountTargetMinPrice
                }
            }).then(function (response) {
                console.log(response);
                var targetProducts = response.data.targetProducts.data;
                var targetProductsPaginator = response.data.targetProducts;

                dynamicTargetProductsTableHtml += "<p> 총 " + targetProductsPaginator.total + " 개의 상품이 조회됨<p>"
                dynamicTargetProductsTableHtml += "<table border='1' class='table table-bordered text-center'>";
                dynamicTargetProductsTableHtml += "<thead class='thead-light'>";
                dynamicTargetProductsTableHtml += "<tr><th>상품아이디</th><th>상품명</th><th>가격</th><th>할인가</th>";
                dynamicTargetProductsTableHtml += "</thead>";

                for(x in targetProducts) {
                    dynamicTargetProductsTableHtml += "<tr><td>" + JSON.stringify(targetProducts[x].id) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].name) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].price) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].discounted_price) + "</td></tr>";
                }
                dynamicTargetProductsTableHtml += "</table>";
                if (targetProductsPaginator.prev_page_url != null) {
                    dynamicTargetProductsTableHtml += "<button role='button' class='btn btn-paginate' value='"+targetProductsPaginator.prev_page_url+"' onclick='paginateTargetProducts(this.value)'>이전페이지로</button>"
                }
                if (targetProductsPaginator.next_page_url != null) {
                    dynamicTargetProductsTableHtml += "<button role='button' class='btn btn-paginate' value='"+targetProductsPaginator.next_page_url+"' onclick='paginateTargetProducts(this.value)'>다음페이지로</button>"
                }

                document.getElementById('targetProductsIndex').innerHTML = dynamicTargetProductsTableHtml;


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

        function paginateTargetProducts(url) {
            var dynamicTargetProductsTableHtml='';
            axios({
                method: 'get',
                url: url,
            }).then(function (response) {
                console.log(response);
                var targetProducts = response.data.targetProducts.data;
                var targetProductsPaginator = response.data.targetProducts;

                dynamicTargetProductsTableHtml += "<p> 총 " + targetProductsPaginator.total + " 개의 상품이 조회됨<p>"
                dynamicTargetProductsTableHtml += "<table border='1' class='table table-bordered text-center'>";
                dynamicTargetProductsTableHtml += "<thead class='thead-light'>";
                dynamicTargetProductsTableHtml += "<tr><th>상품아이디</th><th>상품명</th><th>가격</th><th>할인가</th>";
                dynamicTargetProductsTableHtml += "</thead>";

                for(x in targetProducts) {
                    dynamicTargetProductsTableHtml += "<tr><td>" + JSON.stringify(targetProducts[x].id) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].name) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].price) + "</td>";
                    dynamicTargetProductsTableHtml += "<td>" + JSON.stringify(targetProducts[x].discounted_price) + "</td></tr>";
                }
                dynamicTargetProductsTableHtml += "</table>";
                if (targetProductsPaginator.prev_page_url != null) {
                    dynamicTargetProductsTableHtml += "<button role='button' class='btn btn-paginate' value='"+targetProductsPaginator.prev_page_url+"' onclick='paginateTargetProducts(this.value)'>이전페이지로</button>"
                }
                if (targetProductsPaginator.next_page_url != null) {
                    dynamicTargetProductsTableHtml += "<button role='button' class='btn btn-paginate' value='"+targetProductsPaginator.next_page_url+"' onclick='paginateTargetProducts(this.value)'>다음페이지로</button>"
                }

                document.getElementById('targetProductsIndex').innerHTML = dynamicTargetProductsTableHtml;

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
