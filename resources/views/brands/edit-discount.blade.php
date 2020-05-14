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

    <button role="button" class="btn btn-secondary" id="btnBrandProductDiscountUpdate">저장</button>
    <button role="button" data-url="{{ route('brand.discount.exceptions.create') }}" class="btn btn-secondary" id="btnBrandDiscountExceptionsCreate" >
        할인 제외상품 등록
    </button>

    <p id="targetProductsIndex">대상상품 인덱스 영역</p>


@endsection

@section('script_bottom')
    <script>

        document.getElementById('btnBrandDiscountExceptionsCreate').addEventListener("click", function () {
            window.open(this.dataset.url, "", "width=800,height=800");
        })

        document.getElementById('discountTargetMinPrice').addEventListener("keyup", function () {
            displayDiscountTargetProducts(1);
        });
        document.getElementById('discountPercentage').addEventListener("keyup", function () {
            displayDiscountTargetProducts(1)
        });
        document.getElementById('discountPercentage').addEventListener("click", function () {
            displayDiscountTargetProducts(1)
        });

        document.getElementById('btnBrandProductDiscountUpdate').addEventListener("click", updateBrandDiscount);

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

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

        function displayDiscountTargetProducts(pageNumber) {

            var discountTargetMinPrice = parseInt(document.getElementById('discountTargetMinPrice').value);
            if (isNaN(discountTargetMinPrice)) {
                discountTargetMinPrice = 0;
            }
            var discountTargetBrandId = parseInt(document.getElementById('brandIdToApplyDiscountUpdate').value);
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
                    indexHtml += "<td>" + numberWithCommas(targetProducts[x].price) + " 원 </td>";
                    //할인가 계산 위해 정가로 초기화
                    var discountedPrice = targetProducts[x].price;

                    /*할인가 계산*/
                    //브랜드할인율이 0%가 아닐때
                    if (discountPercentage != 0) {
                        discountedPrice = discountedPrice - (discountedPrice * (discountPercentage/100));
                        discountedPrice = Math.floor(discountedPrice/100)*100;
                    }

                    //카테고리할인이 존재할 때
                    var categoryDiscountOfProduct = targetProducts[x].category_product_discount;
                    if (categoryDiscountOfProduct != null) {
                        discountedPrice = discountedPrice - (discountedPrice * (categoryDiscountOfProduct.discount_percentage/100));
                        discountedPrice = Math.floor(discountedPrice/100)*100 + '(카테고리 할인' + categoryDiscountOfProduct.discount_percentage + '%)';
                    }

                    //할인이 아무것도 없으면 기존 할인가로
                    if (discountPercentage == 0 && categoryDiscountOfProduct == null) {
                        discountedPrice = targetProducts[x].discounted_price;
                    }

                    indexHtml += "<td>" + numberWithCommas(discountedPrice) + '(' + discountPercentage + "%) 원 </td>";

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
