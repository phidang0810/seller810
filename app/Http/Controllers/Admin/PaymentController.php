<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\PaymentRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


}
