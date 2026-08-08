<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller cơ sở của ứng dụng. Mọi controller khác kế thừa lớp này để dùng
 * các tính năng phân quyền (AuthorizesRequests) và kiểm tra dữ liệu (ValidatesRequests).
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
