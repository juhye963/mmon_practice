@extends('layouts.master')

@section('content')
    <h1>할인 제외상품 등록 페이지</h1>
    <div class="form-group">
        <label for="brandDiscountExceptionTargetProductIds">상품 아이디 입력</label>
        <textarea class="form-control" id="brandDiscountExceptionTargetProductIds" rows="10">할인에서 제외할 상품의 아이디를 입력해주세요. (엔터로 구분)</textarea>
    </div>
    <button class="btn btn-secondary float-right" id="btnSearchBrandDiscountExceptionTargetProductByIds">상품검색</button>
    <div id="searchedProductIndex">
        상품 검색결과 영역
    </div>
@endsection

@section('script_bottom')
    <script>
        document.getElementById('btnSearchBrandDiscountExceptionTargetProductByIds').addEventListener('click', function () {
            displayBrandDiscountExceptionTargetProducts(1);
        });

        function displayBrandDiscountExceptionTargetProducts (pageNumber) {
            var brandDiscountExceptionTargetProductsIdString = document.getElementById('brandDiscountExceptionTargetProductIds').value;
            var brandDiscountExceptionTargetProductsId = brandDiscountExceptionTargetProductsIdString.split('\n');

            axios({
                method: 'get',
                url: '{{ route("brand.discount.exceptions.target") }}',
                params: {
                    brand_discount_exception_target_product_id: brandDiscountExceptionTargetProductsId,
                    page: pageNumber
                }
            }).then(function (response) {
                var searchedProduct = response.data.searchedProduct.data;
                var searchedProductPageData = response.data.searchedProduct;
                console.log(response.data);
                var searchedProductIndexHTML = '';
                searchedProductIndexHTML += '<table border="1" class="table table-bordered text-center">';
                searchedProductIndexHTML += '<thead class="thead-light">';
                searchedProductIndexHTML += '<tr><th>선택</th><th>아이디</th><th>상품이름</th><th>가격</th></tr>';
                searchedProductIndexHTML += '</thead>';
                for(var x = 0; x < searchedProduct.length ; x++) {
                    searchedProductIndexHTML += '<tr>';
                    searchedProductIndexHTML += '<td><input type="checkbox" name="selectTargetProduct[]" value="' + searchedProduct[x].id + '" multiple></td>';
                    searchedProductIndexHTML += '<td>' + searchedProduct[x].id + '</td>';
                    searchedProductIndexHTML += '<td>' + searchedProduct[x].name + '</td>';
                    searchedProductIndexHTML += '<td>' + numberWithCommas(searchedProduct[x].price) + '원 </td>';
                    searchedProductIndexHTML += '</tr>';
                }
                searchedProductIndexHTML += '</table>';

                //페이지 url
                var currentPage = searchedProductPageData.current_page;
                if (searchedProductPageData.prev_page_url != null) {
                    searchedProductIndexHTML += '<button role="button" class="btn btn-outline-primary" value="'
                        + searchedProductPageData.prev_page_url
                        + '" onclick="displayBrandDiscountExceptionTargetProducts('
                        + (currentPage - 1)
                        + ')">prev</button>';
                }
                if (searchedProductPageData.next_page_url != null) {
                    searchedProductIndexHTML += '<button role="button" class="btn btn-outline-primary" value="'
                        + searchedProductPageData.prev_page_url
                        + '" onclick="displayBrandDiscountExceptionTargetProducts('
                        + (currentPage + 1)
                        + ')">next</button>';
                }

                searchedProductIndexHTML += '<button class="btn btn-secondary float-right" id="btnSelectedProductIdSend" onclick="sendSelectedProductIdToParent()">선택상품 제외적용</button>';
                searchedProductIndexHTML += '<button class="btn btn-secondary float-right" id="btnAllSearchedProductIdSend" onclick="sendAllSearchedProductIdToParent()">전체상품 제외적용</button>';

                document.getElementById('searchedProductIndex').innerHTML = searchedProductIndexHTML;

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
                    console.log('great!XD');
                });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function sendSelectedProductIdToParent() {

            //체크된 상품아이디 가져오기
            var productCheckbox = document.getElementsByName('selectTargetProduct[]');
            var selectedProductIds = [];
            for (var i = 0; i < productCheckbox.length; i++) {
                if (productCheckbox[i].checked) {
                    selectedProductIds.push(productCheckbox[i].value);
                }
            }
            window.opener.document.getElementById('brandDiscountExceptionsProductId').dataset.targetProductIdSet = selectedProductIds;
            window.opener.document.getElementById('brandDiscountExceptionsProductId').innerText = '할인제외상품 선택됨';
            close();
        }

        function sendAllSearchedProductIdToParent() {
            console.log('all');
            var brandDiscountExceptionTargetProductsIdString = document.getElementById('brandDiscountExceptionTargetProductIds').value;
            var brandDiscountExceptionTargetProductsId = brandDiscountExceptionTargetProductsIdString.split('\n');
            window.opener.document.getElementById('brandDiscountExceptionsProductId').dataset.targetProductIdSet = brandDiscountExceptionTargetProductsId;
            window.opener.document.getElementById('brandDiscountExceptionsProductId').innerText = '할인제외상품 선택됨';
            close();
        }
    </script>
@endsection
