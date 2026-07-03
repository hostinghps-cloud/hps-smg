<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailMaster;
use App\Models\UserMaster;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | EMAIL MASTER
    |--------------------------------------------------------------------------
    */

    public function emailMaster()
    {
        $emails = EmailMaster::orderBy('kode_company', 'asc')->get();

        return view('email_master', compact('emails'));
    }

    public function storeEmailMaster(Request $request)
    {
        $request->validate([

            'kode_company' => 'required',
            'company_name' => 'required',
            'email' => 'required'

        ]);

        EmailMaster::create([

            'kode_company' => $request->kode_company,
            'company_name' => $request->company_name,
            'email' => $request->email

        ]);

        return redirect('/email-master')
            ->with('success', 'Email berhasil ditambahkan');
    }

    public function updateEmailMaster(Request $request, $id)
    {
        $data = EmailMaster::findOrFail($id);

        $data->update([

            'kode_company' => $request->kode_company,
            'company_name' => $request->company_name,
            'email' => $request->email

        ]);

        return redirect('/email-master')
            ->with('success', 'Email berhasil diupdate');
    }

    public function deleteEmailMaster($id)
    {
        EmailMaster::findOrFail($id)->delete();

        return redirect('/email-master')
            ->with('success', 'Email berhasil dihapus');
    }


    /*
|--------------------------------------------------------------------------
| footer MASTER
|--------------------------------------------------------------------------
*/
    public function footerMaster()
    {
        $footers = DB::table('footer_masters')
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'footer_master',
            compact('footers')
        );
    }
    public function storeFooterMaster(Request $request)
    {
        DB::table('footer_masters')->insert([

            'footer_name' => $request->footer_name,

            'footer_html' => $request->footer_html,

            'created_at' => now(),

            'updated_at' => now()

        ]);

        return back()
            ->with('success', 'Footer berhasil ditambahkan');
    }
    public function updateFooterMaster(
        Request $request,
        $id
    ) {

        DB::table('footer_masters')
            ->where('id', $id)
            ->update([
                'footer_name' => $request->footer_name,
                'footer_html' => $request->footer_html,
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Footer berhasil diupdate'
        );
    }
    public function deleteFooterMaster($id)
    {
        DB::table('footer_masters')
            ->where('id', $id)
            ->delete();

        return back()
            ->with('success', 'Footer berhasil dihapus');
    }


    /*
    |--------------------------------------------------------------------------
    | USER MASTER
    |--------------------------------------------------------------------------
    */

    public function userMaster()
    {
        if (auth()->user()->role == 'user') {

            $users = UserMaster::where(
                'id',
                auth()->id()
            )->get();

        } else {

            $users = UserMaster::where(
                'role',
                '!=',
                'master'
            )
                ->latest()
                ->get();

        }

        return view('user_master', compact('users'));
    }
    public function storeUserMaster(Request $request)
    {
        $request->validate([

            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
            'password' => 'required'

        ]);

        UserMaster::create([

            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'smtp_password' => $request->smtp_password,
            'cc' => $request->cc

        ]);

        return redirect('/user-master')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function updateUserMaster(Request $request, $id)
    {
        $user = UserMaster::findOrFail($id);

        // USER hanya boleh edit dirinya sendiri
        if (
            auth()->user()->role == 'user' &&
            auth()->id() != $id
        ) {
            abort(403);
        }

        // USER hanya boleh ganti password
        if (auth()->user()->role == 'user') {

            if ($request->password) {

                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            return redirect('/user-master')
                ->with('success', 'Password berhasil diubah');
        }

        // ADMIN & MASTER
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'smtp_password' => $request->smtp_password,
            'cc' => $request->cc
        ]);

        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect('/user-master')
            ->with('success', 'User berhasil diupdate');
    }
    public function deleteUserMaster($id)
    {
        UserMaster::findOrFail($id)->delete();

        return redirect('/user-master')
            ->with('success', 'User berhasil dihapus');
    }

}