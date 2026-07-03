<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->get();

        return view(
            'template_master',
            compact('templates')
        );
    }

    public function store(Request $request)
    {
        EmailTemplate::create([

            'nama_template' =>
                $request->nama_template,

             'jenis_monitoring' =>
            $request->jenis_monitoring,

            'subject' =>
                $request->subject,

            'body' =>
                $request->body,

        ]);

        return back()->with(
            'success',
            'Template berhasil dibuat'
        );
    }

    public function update(Request $request, $id)
    {
        EmailTemplate::find($id)->update([

            'nama_template' =>
                $request->nama_template,
            
             'jenis_monitoring' =>
            $request->jenis_monitoring,

            'subject' =>
                $request->subject,

            'body' =>
                $request->body,

        ]);

        return back()->with(
            'success',
            'Template berhasil diupdate'
        );
    }
}