@extends('layouts.popup')

@section('script_bottom')
    @parent
    <script>
        document.getElementById('selectedCategory').addEventListener('click', sendSelectedCategoryIdToParent)

        function sendSelectedCategoryIdToParent () {
            //console.log(typeof document.getElementById('categoryPid').value);

            if (document.getElementById('categoryPid').value == '') {
                alert('부모 카테고리를 선택해주세요.');
            } else if (document.getElementById('subCategoryId').value == '') {
                alert('서브 카테고리를 선택해주세요.');
            }

            //console.log($(opener.document).find("#selectedCategoryForMultiProductUpdate").val());
            //console.log(document.getElementById('subCategoryId').value);
            var selectedSubCategory = document.getElementById('subCategoryId').value;
            $(opener.document).find("#selectedCategoryForMultiProductUpdate").val(selectedSubCategory);
            //console.log($(opener.document).find("#selectedCategoryForMultiProductUpdate").val());

            //console.log($(opener.document).find("#searchedProductCategoryChange").prop("dataset").checkedOrNot);

            if ($(opener.document).find("#searchedProductCategoryChange").prop("dataset").checkedOrNot == 'true') {
                opener.parent.changeSearchedProductsCategory();
                //console.log($(opener.document).find("#searchedProductCategoryChange").prop("dataset").checkedOrNot);
            } else if ($(opener.document).find("#selectedProductCategoryChange").prop("dataset").checkedOrNot == 'true') {
                opener.parent.changeCheckedProductsCategory();
                //console.log($(opener.document).find("#selectedProductCategoryChange").prop("dataset").checkedOrNot)
            } else {
                alert('잘못된 접근');
            }
            window.self.close();
        }
    </script>
@endsection

@include('categories.select', ['categories' => $categories])

<button class="btn btn-dark float-right" role="button" id="selectedCategory">카테고리 선택</button>
