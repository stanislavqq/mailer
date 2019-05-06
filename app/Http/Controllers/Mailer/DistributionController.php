<?php

namespace App\Http\Controllers\Mailer;

use App\Events\MailerSend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mailer\CreateDistribution;
use App\Http\Requests\Mailer\UpdateDistribution;
use App\Jobs\SendEmail;
use App\Mail\DistributionSend;
use App\Models\Mailer\ContactList;
use App\Models\Mailer\Distribution;
use App\Models\Mailer\Template;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class DistributionController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $distributions = Distribution::take(100)
            ->with(['contactList' => function ($query) {
                $query->first();
            }])
            ->with(['template' => function ($query) {
                $query->first();
            }])
            ->get();

        return response()->json($distributions->toJson(), 200);
    }

    /**
     * @param CreateDistribution $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(CreateDistribution $request)
    {
        $modelParams = $this->prepareDistributionFormParams($request);
        $savedId = Distribution::create($modelParams);

        if ($savedId) {
            return response()->json([
                'result' => $savedId,
            ], 200);
        }
    }

    /**
     * @param UpdateDistribution $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(UpdateDistribution $request)
    {
        $id = $request->input('id');

        $res = Distribution::where('id', $id)
            ->update($this->prepareDistributionFormParams($request));

        if ($res) {
            return response('ok', 200);
        }

        return response('error', 500);
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function remove($request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        Distribution::where('id', $request->input('id'))->delete();

        return response()->json([
            'message' => 'deleted'
        ]);
    }

    /**
     * @param $request
     * @return array
     */
    protected function prepareDistributionFormParams($request)
    {
        return [
            'name' => $request->input('name'),
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'active' => $request->input('active'),
            'from_email' => $request->input('email_from'),
            'from_name' => $request->input('name_from'),
            'contacts_list_id' => $request->input('contacts_list_id'),
            'template_id' => $request->input('template_id'),
            'send_by_date' => $request->input('send_by_date'),
            'send_date' => $request->has('send_date') ? $request->input('send_date') : Carbon::now(),
            'last_send_date' => $request->input('last_send_date'),
            'user_id' => Auth::user()->id,
        ];
    }

    public function send(Request $request)
    {

        try {
            $fields = $this->validate($request, [
                'id' => 'required',
                'contacts_list_id' => 'required',
                'template_id' => 'required'
            ]);
        } catch (ValidationException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
                'errors' => $ex->errors()
            ]);
        }

        $distribution = Distribution::find($fields['id']);
        $template = Template::find($fields['template_id']);
        $contactList = ContactList::find($fields['contacts_list_id']);

        $emailsCollect = collect();

        $data = [
            'all_count' => $contactList->contacts->count(),
            'current' => 0
        ];

        $body = $template->prepareBody($contactList);

        foreach ($contactList->contacts as $contact) {
            try {
                $this->dispatch(new SendEmail([
                    'to' => 'stanislavqq@yandex.ru',
                    'from' => $distribution->from_email,
                    'subject' => utf8_encode($distribution->subject),
                    'body' => utf8_encode($body)
                ]));
            } catch (\Exception $ex) {
                return response()->json([
                    'message' => $ex->getMessage()
                ], 500);
            }
            ++$data['current'];
            event(new MailerSend($data));
        }

        if (!$template) {
            return response()->json([
                //'message' => trans('messages.template_not_exist'),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Done'
        ], 200);

        $emails = ClientsContact::leftJoin('mailer_contacts_lists_contacts as mclc', 'clients_contact.id', '=', 'mclc.contact_id')
            ->where('mclc.list_id', $fields['contacts_list_id'])
            ->get();

        $testEmails = [
            'bygsmlfgb@emlpro.com',
            'eskvefrfb@emlhub.com',
            'jbvlqgnhc@emltmp.com',
            'qwwaffrfb@emlhub.com',
            'tnebvfwk@yomail.info',
            'tnebxfsd@yomail.info',
            'uavejzzggrjd@dropmail.me',
            'uavelepxwmgo@dropmail.me',
            'ubojbybf@10mail.org',
            'ubokdeyy@10mail.org',
            'ubokffau@10mail.org',
            'ubokgrul@10mail.org',
            'ubokjzzc@10mail.org',
        ];

        $results = [];

        foreach ($testEmails as $emailItem) {
            $results[] = $this->send;
        }

        dd($emails);
    }

    public function testSend(Request $request)
    {

        $fields = $this->validate($request, [
            'email' => 'required|email',
            'template_id' => 'required|numeric'
        ]);

        $email = $fields['email'];
        $templateId = $fields['template_id'];

        $template = Template::where('id', $templateId)->first();

        $body = $template->setVariables([
            'user_email' => $email,
            'user_name' => 'Test_user_name',
            'user_phone' => '8(999)999-99-99'
        ]);

        $res = $this->dispatch(new SendEmail([
            'to' => $email,
            'from' => Config::get('mail.username'),
            'subject' => utf8_encode("Test message"),
            'body' => utf8_encode($body)
        ]), true);

        //$res = self::sendEmail($email, $template, 'test message');

        return response()->json([
            'test' => 'test',
            'status' => 'ok',
            'res' => $res
        ], 200);
//DistributionSend
    }
}

