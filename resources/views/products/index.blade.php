@extends('layouts.master')

@section('style')
    <style>
        div {padding: 20px}
    </style>
@endsection

@section('script_bottom')
    <script>
        var selectAllButton = document.querySelectorAll('.btn.btn-light.btn-remove');
        //console.log(selectAllButton.length);
        for (var i = 0; i < selectAllButton.length; i++) {
            selectAllButton[i].addEventListener('click', deleteThisProduct);
        }

        document.getElementById('productMultiDelete').addEventListener('click', deleteCheckedProducts);
        document.getElementById('selectedProductCategoryChange').addEventListener('click', function () {
            this.dataset.checkedOrNot = true;
            openCategorySelectPage(this.dataset.categorySelectUrl)
        });
        document.getElementById('searchedProductCategoryChange').addEventListener('click', function () {
            this.dataset.checkedOrNot = true;
            openCategorySelectPage(this.dataset.categorySelectUrl)
        });

        function deleteThisProduct(_event) {
            //_event.preventDefault(); a 태그에서 button 태그로 바꿨으니까 필요 없음
            console.log('삭제주소 : '+this.dataset.deleteUrl);
            var productDeleteUrl = this.dataset.deleteUrl;

            axios.delete(productDeleteUrl)
                .then(function (response) {
                    console.log(response);
                    alert('상품이 성공적으로 삭제되었습니다.');
                    window.location.reload();
                })
                .catch(function (error) {
                    if(error.response) {
                        if (error.response.status == 403 || error.response.status == 500) {
                            alert(error.response.data.message);
                        } else {
                            console.log(response);
                        }
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                })
                .finally(function () {
                    console.log('done');
                })
        }

        function deleteCheckedProducts() {
            //alert('dd');
            var productCheckboxForDeletion = document.getElementsByName('productsSelect[]');
            var checkedProductIdsForDeletion = [];
            for (var i = 0; i < productCheckboxForDeletion.length; i++) {
                if (productCheckboxForDeletion[i].checked) {
                    checkedProductIdsForDeletion.push(productCheckboxForDeletion[i].value);
                }
            }

            axios.delete('{{ route("products.destroy-many") }}', {
                data: {
                    product_ids_for_deletion: checkedProductIdsForDeletion
                }
            })
                .then(function (response) {
                    console.log(response);
                    alert('상품이 성공적으로 삭제되었습니다.');
                    window.location.reload();
                })
                .catch(function (error) {
                    if(error.response) {
                        if (error.response.status == 403 || error.response.status == 500) {
                            alert(error.response.data.message);
                        } else {
                            console.log(response);
                        }
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                })
                .finally(function () {
                    console.log('done');
                })
        }

        function changeCheckedProductsCategory () {
            var selectedProductCheckbox = document.getElementsByName('productsSelect[]');
            //var checkedProductIds = [];
            var updateCategoryOfCheckedProductFormData = new FormData();

            for (var i = 0; i < selectedProductCheckbox.length; i++) {
                if (selectedProductCheckbox[i].checked) {
                    //checkedProductIds.push(selectedProductCheckbox[i].value);
                    updateCategoryOfCheckedProductFormData.append('selected_products_to_change_category[]', selectedProductCheckbox[i].value);
                }
            }

            updateCategoryOfCheckedProductFormData.append('selected_category_to_update_checked_products',
                document.getElementById("selectedCategoryForMultiProductUpdate").value);
            console.log('선택된 카테고리 : '+updateCategoryOfCheckedProductFormData.getAll('selected_category_to_update_checked_products'));
            console.log('선택된 상품 : '+updateCategoryOfCheckedProductFormData.getAll('selected_products_to_change_category[]'));

            axios({
                method: 'post',
                url: '{{ route("update.category.selected.products") }}',
                data: updateCategoryOfCheckedProductFormData,
                headers: { 'content-type': 'multipart/form-data' },
                // processData: false
            })
                .then(function (response) {
                    console.log(response);
                    alert('상품의 카테고리가 성공적으로 변경되었습니다.');
                    window.location.reload();
                })
                .catch(function (error) {
                    if(error.response) {
                        if (error.response.status == 403) {
                            alert(error.response.data.message);
                            //유효하지 않은 값
                        } else {
                            console.log(response);
                        }
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                })
                .then(function () {
                });

        }

        function openCategorySelectPage (categorySelectUrl) {
            var categorySelectWindow = window.open(categorySelectUrl, '카테고리 선택', "resizable,scrollbars,status");
        }

        function changeSearchedProductsCategory () {
            var url_string = window.location.href;
            var searchParams = new URLSearchParams(url_string);
            console.log(document.getElementById("selectedCategoryForMultiProductUpdate").value);

            var changeSearchedProductsCategoryFormData = new FormData();

            for (var p of searchParams) {
                if (p[0].indexOf('?') != -1) {
                    var urlSplit = p[0].split('?', 2);
                    p[0] = urlSplit[1];
                }
                //console.log(p[0] + ' : ' + p[1]);
                changeSearchedProductsCategoryFormData.append(p[0], p[1]);
            }

            changeSearchedProductsCategoryFormData.append('selected_category_to_update_searched_products',
                document.getElementById("selectedCategoryForMultiProductUpdate").value);

            axios({
                method: 'post',
                url: '{{ route("update.category.searched.products") }}',
                data: changeSearchedProductsCategoryFormData,
                headers: { 'content-type': 'multipart/form-data' },
            })
                .then(function (response) {
                    console.log(response);
                    alert('상품의 카테고리가 성공적으로 변경되었습니다.');
                    window.location.reload();
                })
                .catch(function (error) {
                    if(error.response) {
                        if (error.response.status == 403) {
                            alert(error.response.data.message);
                            //유효하지 않은 값
                        } else {
                            console.log(response);
                        }
                    } else if (error.request) {
                        console.log(error.request);
                    } else {
                        console.log('Error', error.message);
                    }
                })
                .then(function () {

                });
        }
    </script>
@endsection


<h1>전체상품 조회 영역입니다.</h1>

<p>총 {{ $products->total() }} 개의 상품이 조회되었습니다.</p>

@include('errors.validate')

{{--@if (\Session::has('success'))
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('success') !!}</li>
        </ul>
    </div>
@endif--}}


<a class="btn btn-dark" data-toggle="collapse" href="#collapseSearchForm">검색하기</a>
<div class="collapse" id="collapseSearchForm">
<form name="srch_frm" class="bg-light text-dark rounded-lg" action="{{ route('products.index') }}" method="get">
    <div class="form-group">
        <legend>검색</legend>
        <label for="searchTypeSelect">검색조건</label>
        <select class="form-control" name="search_type" id="searchTypeSelect">
            <option value="" ></option>
            @foreach($search_types as $key => $value)
                <option value="{{ $key }}" {{ $key != $parameters['search_type'] ? '' : 'selected' }}>{{ $value }}</option>
            @endforeach
        </select>
        <label for="searchWord">검색어</label>
        <input name="search_word" id="searchWord" class="form-control" type="search" placeholder="Search"
               value="{{ $parameters['search_word'] }}">
    </div>

    <div class="form-group">
        <legend>정렬조건</legend>
        <select class="form-control" name="sort">
            @foreach($sorts as $key => $value)
                <option value="{{ $key }}" {{ $key != $parameters['sort'] ? '' : 'selected' }}>{{ $value }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <legend>날짜검색</legend>
        시작일<input name="start_date" class="form-control" type="date" value="{{ $parameters['start_date'] }}">
        종료일<input name="end_date" class="form-control" type="date" value="{{ $parameters['end_date'] }}">
    </div>

    <div class="form-group">
        <legend>판매상태</legend>
        @foreach($prds_status as $display_prds_status_key => $display_prds_status_value)
            {{ $display_prds_status_value }}
            <input type="checkbox" name="prds_status[]" value="{{ $display_prds_status_key }}"
            {{ in_array($display_prds_status_key, $parameters['prds_status']) !== false ? 'checked' : '' }} />
        @endforeach
    </div>
    <button type="submit" class="btn btn-outline-success float-right">Search</button>
</form>
</div>



<div id = "selectedCategoryForMultiProductUpdateArea">
    <input type="hidden" value="default" id="selectedCategoryForMultiProductUpdate">
</div>

<table cellpadding="10" class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
            <th>
                상품번호
                {{--<input type="checkbox" id="selectAllSearchedProduct" name="selectAllSearchedProduct" value="default" />--}}
            </th>
            <th>상품명</th>
            <th>가격</th>
            <th>할인가</th>
            <th>재고</th>
            <th>브랜드</th>
            <th>카테고리</th>
            <th>등록자</th>
            <th>등록일</th>
            <th>상태</th>
            <th>상품수정이력</th>
            <th>상품삭제</th>
            <th>상품수정</th>
        </tr>
    </thead>
    @foreach($products as $product)
        <tr>
            <td>
                {{ $product->id }}
                <input type="checkbox" id="multiSelect{{ $product->id }}" name="productsSelect[]" value="{{ $product->id }}" multiple />
            </td>
            <td><button type="button" class="btn btn-light" data-toggle="modal" data-target="#productImage{{ $product->id }}">{{ $product->name }}</button></td>
            <div class="modal fade" id="productImage{{ $product->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">{{ $product->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ $product->product_image_path }}" class="img-thumbnail">
                        </div>
                    </div>
                </div>
            </div>
            <td>{{ $product->price }} 원</td>
            <td>{{ $product->discounted_price }} 원</td>
            <td>{{ $product->stock }}</td>
            <td>{{ $product->brand->name }}</td>
            <td>{{ $product->category->name }}</td>
            <td>{{ $product->seller->name }}</td>
            <td>{{ $product->created_at }}</td>
            <td>{{ $product->status }}</td>
            <td>

                @for ($i = 0; $i < 3; $i++)
                    @if ($product->updateLogs->count() <= 0 || empty($product->updateLogs[$i]))
                        @break
                    @endif
                    <li>
                        <b>{{ $product->updateLogs[$i]->updated_at }}</b>
                        {{ $product->updateLogs[$i]->log_description }}
                    </li>
                @endfor

                @if ($product->updateLogs->count() > 0)
                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseMoreUpdateLogs{{ $product->id }}" role="button" aria-expanded="false" aria-controls="collapseMoreUpdateLogs">
                        더보기
                    </a>
                    <div class="collapse" id="collapseMoreUpdateLogs{{ $product->id }}">
                        @for ($i = 3; $i < $product->updateLogs->count(); $i++)
                            <li>
                                <b>{{ $product->updateLogs[$i]->updated_at }}</b>
                                {{ $product->updateLogs[$i]->log_description }}
                            </li>
                        @endfor
                    </div>
                @endif

            </td>
            <td><button data-delete-url="{{ route("products.destroy", $product->id) }}" class="btn btn-light btn-remove" role="button">삭제</button></td>
{{--            <td><a href="{{ route("products.destroy", $product->id) }}" class="btn btn-light btn_remove" role="button">삭제</a></td>--}}
            <td><a class="btn btn-light" href="{{ route('products.edit', ['product_id' => $product->id]) }}" role="button">수정</a></td>
        </tr>
    @endforeach
</table>

<button class="btn btn-dark " role="button" id="productMultiDelete">일괄삭제</button>
<button data-checked-or-not="false" data-category-select-url="{{ route('categories.select') }}?type=select" class="btn btn-dark " role="button" id="selectedProductCategoryChange">선택 상품 카테고리 변경</button>
<button data-checked-or-not="false" data-category-select-url="{{ route('categories.select') }}" class="btn btn-dark " role="button" id="searchedProductCategoryChange">검색된 전체 상품 카테고리 변경</button>


<div class="pagination justify-content-center">
{{ $products->appends($parameters)->links()}}
</div>


