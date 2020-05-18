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

    <button role="button" data-url="{{ route('brand.discount.exclusions.create') }}" class="btn btn-secondary" id="btnDiscountExclusionsCreate" >
        할인 제외상품 등록
    </button>

    <div id="productIdForDiscountExclusion" data-target-product-id-set="">할인제외상품 아이디 미선택</div>

    <span id="targetProductsIndex">대상상품 인덱스 영역</span>

@endsection

@section('script_bottom')
    <script>
        document.getElementById('btnDiscountExclusionsCreate').addEventListener("click", function () {
            window.open(this.dataset.url, "", "width=800,height=800");
        });

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
                var thisCategoryDiscountId = document.getElementById('categoryDiscountId').value;
                storeCategoryDiscountExclusions(thisCategoryDiscountId);
                //window.location = '{{ route("category.discount.list") }}';
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

        document.getElementById('discountTargetMinPrice').addEventListener("keyup", function () {
            showDiscountTargetProduct(1);
        });
        document.getElementById('discountPercentage').addEventListener("keyup", function () {
            showDiscountTargetProduct(1)
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function showDiscountTargetProduct (pageNumber) {

            var discountTargetCategoryId = document.getElementById('categoryIdToApplyDiscountUpdate').value;
            var discountTargetMinPrice = document.getElementById('discountTargetMinPrice').value;
            if (discountTargetMinPrice == '') {
                discountTargetMinPrice = 0;
            }
            var categoryDiscountPercentage = document.getElementById('discountPercentage').value;
            if (categoryDiscountPercentage == '') {
                categoryDiscountPercentage = 0;
            }

            axios({
                method: 'get',
                url: '{{ route("category.discount.target.product") }}',
                params: {
                    discount_target_category_id: discountTargetCategoryId,
                    discount_target_min_price: discountTargetMinPrice,
                    discount_percentage: categoryDiscountPercentage,
                    page: pageNumber
                }
            }).then(function (response) {
                console.log(response);
                var indexHtml = '';
                var targetProducts = response.data.targetProducts.data;
                var targetProductsPaginator = response.data.targetProducts;

                indexHtml += '<p> 총 ' + targetProductsPaginator.total + ' 개의 상품이 조회됨<p>'
                indexHtml += '<table border="1" class="table table-bordered text-center">';
                indexHtml += '<thead class="thead-light">';
                indexHtml += '<tr><th>상품아이디</th><th>상품명</th><th>가격</th><th>할인율</th><th>할인가</th>';
                indexHtml += '</thead>';
                for (var x = 0; x < targetProducts.length; x++) {
                    indexHtml += '<tr><td>' + targetProducts[x].id + '</td>';
                    indexHtml += '<td>' + targetProducts[x].name + '</td>';
                    indexHtml += '<td>' + numberWithCommas(targetProducts[x].price) + ' 원 </td>';
                    var brandDiscountPercentage = 0;
                    if (targetProducts[x].brand_product_discount != null) {
                        brandDiscountPercentage = targetProducts[x].brand_product_discount.discount_percentage;
                    }
                    indexHtml += '<td> 브랜드 : ' + brandDiscountPercentage + '% <br> 카테고리 : ' + categoryDiscountPercentage + '%</td>';

                    //계산 위해 정가로 초기화
                    var discountedPrice = targetProducts[x].price;

                    /*할인가 계산*/
                    //브랜드 할인율이 0이 아닐때
                    if (brandDiscountPercentage != 0) {
                        discountedPrice = discountedPrice - (discountedPrice * (brandDiscountPercentage / 100));
                        discountedPrice = Math.round(discountedPrice / 100) * 100;
                    }

                    //카테고리 할인율이 0이 아닐때
                    if (categoryDiscountPercentage != 0) {
                        discountedPrice = discountedPrice - (discountedPrice * (categoryDiscountPercentage / 100));
                        discountedPrice = Math.round(discountedPrice / 100) * 100;
                    }

                    if (brandDiscountPercentage == 0 && categoryDiscountPercentage == 0) {
                        discountedPrice = targetProducts[x].discounted_price;
                    }
                    indexHtml += '<td>' + numberWithCommas(discountedPrice) + ' 원 </td></tr>';

                }
                indexHtml += '</table>';
                var currentPage = targetProductsPaginator.current_page;
                if (targetProductsPaginator.prev_page_url != null) {
                    indexHtml += "<button role='button' class='btn btn-light' value='" + targetProductsPaginator.prev_page_url + "' onclick='showDiscountTargetProduct(" + (currentPage - 1) + ")'>이전페이지로</button>"
                }
                if (targetProductsPaginator.next_page_url != null) {
                    indexHtml += "<button role='button' class='btn btn-light' value='" + targetProductsPaginator.next_page_url + "' onclick='showDiscountTargetProduct(" + (currentPage + 1) + ")'>다음페이지로</button>"
                }

                document.getElementById('targetProductsIndex').innerHTML = indexHtml;
            }).catch(function (error) {
                if (error.response) {
                    console.log(error.response);
                    if (error.response.status === 422) {
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

        function storeCategoryDiscountExclusions (categoryDiscountId) {
            var productIdSetString = document.getElementById('productIdForDiscountExclusion').dataset.targetProductIdSet;
            var productIdSet = productIdSetString.split(',');

            axios({
                method: 'post',
                url: '{{ route("category.discount.exclusions.store") }}',
                data: {
                    product_id_set: productIdSet,
                    category_discount_id : categoryDiscountId
                }
            })
                .then(function (response) {
                    console.log(response);
                })
                .catch(function (error) {
                    if(error.response) {
                        console.log(error.response);
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                })
                .finally(function () {
                    console.log('great!');
                });
        }
    </script>
@endsection
