<?php

namespace App\Http\Controllers\Admin;

class DashboardController extends AdminController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->_data['title'] = 'Dashboard';
        return view($this->_view . 'dashboard.index', $this->_data);
    }
}
