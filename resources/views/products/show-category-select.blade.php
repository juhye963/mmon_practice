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
            } else {
                console.log($(opener.document).find("#selectedCategoryForMultiProductUpdate").val());
                console.log(document.getElementById('subCategoryId').value);
                var selectedSubCategory = parseInt(document.getElementById('subCategoryId').value);
                $(opener.document).find("#selectedCategoryForMultiProductUpdate").val(selectedSubCategory);
                console.log($(opener.document).find("#selectedCategoryForMultiProductUpdate").val());
                opener.parent.hello();
                window.self.close();
            }

          //console.log(document.getElementById('categoryPid').value);
          //console.log(document.getElementById('subCategoryId').value);
        }

        function reloadSelectedCategoryIdData () {

        }
    </script>
@endsection

@include('categories.select')

<button class="btn btn-dark float-right" role="button" id="selectedCategory">카테고리 선택</button>
