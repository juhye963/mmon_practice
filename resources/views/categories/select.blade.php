@section('script')
    @parent
    {{-- 부모컨텐츠를 겹쳐 쓰지 않고 추가합니다 --}}
    {{-- 상품생성폼 말고도 다른곳에서도 include할 수도 있고, 그곳에서는 script가 이미 있을 수 있음 --}}
    <script type="text/javascript">
        function createSubCategoriesSelect(Data) {
            var categorySelectArea = document.getElementById("categorySelect");
            var subCategorySelect = document.createElement("SELECT");

            subCategorySelect.setAttribute("id", "subCategoryId")
            subCategorySelect.classList.add('form-control');
            subCategorySelect.setAttribute("size", Data.length);
            categorySelectArea.appendChild(subCategorySelect);

            var i;
            var product_sub_category_id = '{{ $product_sub_category_id }}';

            for (i = 0; i < Data.length; i++) {
                var subCategoryOption = document.createElement('OPTION');
                var subCategoryOptionName = document.createTextNode(Data[i].name);
                subCategoryOption.setAttribute("value", Data[i].id);
                if (product_sub_category_id == Data[i].id) {
                    subCategoryOption.setAttribute("selected", true);
                }
                subCategoryOption.appendChild(subCategoryOptionName);

                document.getElementById("subCategoryId").appendChild(subCategoryOption);
            }
        }

        function clearSubCategoriesSelect() {
            var subCategoriesExists = document.getElementById("subCategoryId");
            //console.log(subCategoriesExists); null 혹은 해당 html코드 리턴
            if (subCategoriesExists) {
                var parentElement = document.getElementById("categorySelect");
                parentElement.removeChild(subCategoriesExists);
            }
        }

        function displaySubCategories(selectedParentCategoryId) {
            //alert(selectedParentCategoryId);

            clearSubCategoriesSelect();

            axios({
                method: 'get',
                url: '{{ route('categories.display-sub-categories') }}',
                params: {
                    category_pid: selectedParentCategoryId
                }
            }).then(function (response) {
                //console.log(response);
                //console.log(response.data.sub_categories.length);
                //console.log(selectedParentCategoryId);
                if (response.data.sub_categories) {
                    var subCategoriesData = response.data.sub_categories;
                    //console.log(response.data.sub_categories);

                    createSubCategoriesSelect(subCategoriesData);

                } else {

                }

            }).catch(function (error) {
                console.log(error);
            }).finally(function () {
                console.log('well done');
            })
        }

    </script>

@endsection

<div class="form-group" id="categorySelect">
    <legend>상품 카테고리 셀렉</legend>
    <select class="form-control" id="categoryPid" name="categoryId" size="{{ count($categories) }}" >
        {{--<option value="">카테고리 선택</option>--}}
        @foreach($categories as $category)
            <option value="{{ $category->id }}" onclick="displaySubCategories(this.value)"
                {{ $category->id != $product_parent_category_id ?  '' : 'selected'}}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>


