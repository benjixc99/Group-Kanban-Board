<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminBaseController extends Controller
{
        /**
         * Show admin home.
         *
         * @return Application|Factory|\Illuminate\Contracts\View\View|View
         */
        public function index()
        {
                $breadcrumbs = [
                        ['link' => "/dashboard", 'name' => __('locale.menu.Dashboard')],
                        ['name' => Auth::user()->displayName()],
                ];



                return view('admin.dashboard', compact('breadcrumbs'));
        }

        protected function redirectResponse(Request $request, $message, $type = 'success')
        {
                if ($request->wantsJson()) {
                        return response()->json([
                                'status'  => $type,
                                'message' => $message,
                        ]);
                }

                return redirect()->back()->with("flash_{$type}", $message);
        }
}
