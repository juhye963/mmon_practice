@extends('layouts.master')

{{--다른 곳으로 빼기--}}
@section('script_top')
    <script type="text/javascript">
        function checkIfEmailHasBeenTaken(receivedEmail) {
            var re = /\S+@\S+\.\S+/;
            if (!re.test(receivedEmail)) {
                document.getElementById('emailCheckMessage').innerText = '아직 이메일형태가 아닙니다.';
            } else {
                //document.getElementById('emailCheckMessage').innerText = receivedEmail;
                axios({
                    method: 'get',
                    url: '{{ route('sellers.check-if-email-has-been-taken') }}',
                    params: {
                        email: receivedEmail
                    }
                }).then(function (response) {
                    console.log(response.data.message);
                    document.getElementById('emailCheckMessage').innerText = response.data.message;
                }).catch(function (err) {
                    console.log(err);
                }).finally(function () {
                    console.log('good job:)');
                })
            }
        }

        function register() {

            var resgisterSellerFormData = new FormData();

            resgisterSellerFormData.append('seller_name', document.getElementById('sellerName').value);
            resgisterSellerFormData.append('seller_email', document.getElementById('sellerEmail').value);
            resgisterSellerFormData.append('password', document.getElementById('sellerPassword').value);
            resgisterSellerFormData.append('seller_brand_id', document.getElementsByName('brand_id')[0].value);
            resgisterSellerFormData.append('password_confirmation', document.getElementById('sellerPasswordConfirmation').value);

            axios({
                method: 'post',
                url: '{{ route('sellers.store') }}',
                data: resgisterSellerFormData,
            }).then(function (response) {
                console.log(response)
                alert('회원가입 성공')
                window.location = '{{ route('home') }}'

            }).catch(function (error) {
                console.log(error.response.data);
                if (error.response) {
                    if (error.response.status == 422) {
                        alert(Object.values(err.response.data.errors)[0]);
                    } else if (error.response.status == 500) {
                        alert('회원가입 실패')
                    } else {
                        console.log(error.message);
                    }
                } else if (error.request) {
                    console.log(error.request);
                } else {
                    console.log('Error', error.message);
                }
                //console.log(err.config);
            }).finally(function () {
                console.log('finally');
            })
        }

    </script>
@endsection

@section('content')
<h1>이곳은 회원가입폼입니다.</h1>

    @include('errors.validate')

    <input type="text" id="sellerName" name="name" placeholder="이름" value="{{ old('name') }}" autofocus />
    <br>
    <input type="text" id="sellerEmail" name="email" placeholder="이메일" value="{{ old('email') }}"
           onkeyup="checkIfEmailHasBeenTaken(this.value)" autocomplete="off">
    <div id="emailCheckMessage">이메일을 입력해주세요.</div>
    <br>
    <input type="password" id="sellerPassword" name="password" placeholder="비밀번호" >
    <br>
    <input type="password" id="sellerPasswordConfirmation" name="password_confirmation" placeholder="비밀번호 확인" ><br>

    @include('brands.select')
<!--name을 여기서 변수로 넘겨주기-->

<!--1. tns-->
    <button type="submit" onclick="register()">회원가입</button>
@endsection

