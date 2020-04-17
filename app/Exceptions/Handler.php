<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * render() 메서드의 리턴값은 Response 객체여야함을 알려줌
     * Response 객체 어떻게 쓰는가?
     * 라라벨 책 133p참고
     * https://laravel.com/docs/6.x/responses response객체 섹션 참고
     * https://www.tutorialspoint.com/laravel/laravel_response.htm
     * return response($content,$status)
     * 여기서 status 란 response 클래스 정의된 곳에 있는 public static $statusTexts 들을 말하는듯
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if (app()->environment('production')) {
            if ($exception instanceof QueryException) {
                return response(view('errors.notice',[
                    'title' => '회원가입 실패',
                    'description' => '회원정보를 데이터베이스에 저장하는 데에 실패하였습니다.'
                ]),404);
            }
        }

        return parent::render($request, $exception);
    }
}
