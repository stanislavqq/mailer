<?php

namespace App\Http\Controllers\Mailer;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailerAttachRequest;
use App\Models\Mailer\MailerAttachment;
use App\Models\Mailer\Template;
use Auth;
use Illuminate\Http\Request;

class TemplateController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $template = Template::where('id', $request->input('id'))->first();
        $template->fileList = $template->mailer_attachments ? $template->mailer_attachments : [];

        $template->fileList->filter(function ($item) {
            $item->percentage = 0;
//            if (file_exists(public_path(($item->path)))) {
//                $item->raw = file(public_path($item->path));
//            }

            $item->status = 'ready';
        });

        return response()->json($template);
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return response()->json([
            'templates' => Template::all(),
            'variables' => Template::getVariables()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $id = Template::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'body' => $request->input('body'),
            'author_id' => Auth::user()->id
        ]);

        if ($id) {
            return response()->json([
                'status' => 'ok',
                'createdId' => $id,
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => '123'
        ], 500);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $templateId = $request->input('id');

        $res = Template::where('id', $templateId)
            ->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'body' => $request->input('body'),
            ]);

        if ($res) {
            return response()->json([], 200);
        }

        return response()->json([
            'message' => 'error'
        ], 500);
    }

    /**
     * Remove template
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request)
    {
        $templateId = $request->input('id');

        $res = Template::where('id', $templateId)
            ->delete();

        if ($res) {
            return response()->json([], 200);
        }
    }

    /**
     * @param MailerAttachRequest $request
     */
    public function attachUpload(MailerAttachRequest $request)
    {
        $file = $request->file;
        dd($request->all());

        $attach = new MailerAttachment();
        $attachRes = $attach->saveFile($file);

        return response()->json([
            'status' => 'ok',
            'message' => $attachRes
        ]);
    }

    public function attachRemove(Request $request)
    {
        $fileId = $request->input('id');
        $attach = MailerAttachment::where('id', $fileId)->first();
        dd($attach);
        if ($attach->remove()) {
        return response()->json([
            'status' => 'ok'
        ]);
    }
    }
}
