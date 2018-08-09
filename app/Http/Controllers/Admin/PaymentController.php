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

        $this->_pushBreadCrumbs('Danh thu', route('admin.payments.index'));
    }

    public function getPaymentChart(PaymentRepository $payment)
    {
        $data = $payment->getLineChartData($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getTopProductSell(PaymentRepository $payment)
    {
        $data = $payment->getTopProductSell($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getTopPlatformSell(PaymentRepository $payment)
    {
        $data = $payment->getTopPlatformSell($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getTopCategorySell(PaymentRepository $payment)
    {
        $data = $payment->getTopCategorySell($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

}
