<?php

namespace App\Http\Controllers\Mailer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mailer\CreateDistribution;
use App\Http\Requests\Mailer\UpdateDistribution;
use App\Jobs\SendEmail;
use App\Models\ClientsContact;
use App\Models\Mailer\ClientsContactsLists;
use App\Models\Mailer\ContactList;
use App\Models\Mailer\Distribution;
use App\Models\Mailer\Mail;
use App\Models\Mailer\MailerDriver;
use App\Models\Mailer\Template;
use App\Models\Mailer\UserMailerDrivers;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class MailerController extends Controller
{

    public function index() {
        return view('mailer.index');
    }

    public function getUser() {
        return Auth::user()->toJson();
    }

    public function getClients(Request $request)
    {
//        $clients = DB::table('clients_contact')
//            ->select([
//                'clients.client_id', 'clients.client_name', 'clients.client_director',
//                'cc.id', 'cc.name', 'clients.client_phone', 'clients.client_email', 'cc.birthday'
//            ])
//            ->leftJoin('clients_contact as cc', 'cc.client_id', '=', 'clients.client_id')
//            ->orderBy('clients.client_id', 'DESC')
//            ->take(200)
//            ->get();
        $clients = $this->getClientContacts();

        if ($clients) {
            return response()->json([
                'clients' => $clients->toJson(),
            ], 200);
        }

        return response()->json([
            'clients' => [],
        ]);

    }

    public function getClientContacts($contactListIds = [])
    {
        $query = ClientsContact::select('id', 'c.client_name as client_name', 'name', 'phone', 'c.client_id as cl_id', 'email')
            ->leftJoin('clients as c', 'clients_contact.client_id', '=', 'c.client_id');


        if ($contactListIds) {
            $query->whereIn('id', $contactListIds);
        }

        return $query->limit(100)
            ->get();
    }

    public function getContactLists()
    {
        //$userId = Auth::user()->id;
        $contactLists = ContactList::with('contacts')
            ->get();

        foreach ($contactLists as $list) {
            $list->contactCount = $list->contacts->count();
        }

        return response()->json($contactLists);
    }

    public function getContactListItem(Request $request)
    {
        $id = $request->input('id');
//        $contactList = ContactList::where('id', $id)
//            ->leftJoin('mailer_contacts_lists_contacts as mclc', 'mailer_contacts.id', '=', 'mclc.list_id')
//            ->leftJoin('clients_contact as cc', 'mclc.contact_id', '=', 'cc.id')
//            ->select('mclc.id as mclc_id', 'cc.id as cc_id', 'mailer_contacts.id as mc_id')
//            ->first();

        $contactListItem = ContactList::where('id', $id)->first();

        $contactListIds = ClientsContactsLists::select('contact_id')->where('list_id', $id)->pluck('contact_id');
        $contactList = $this->getClientContacts($contactListIds);//ClientsContact::whereIn('id', $contactListIds)->get();
//        $contactList = ContactList::select('mailer_contacts.id as id, mailer_contacts.name as name, mailer_contacts.description as description')
//            ->where('mailer_contacts.id', $id)
//            ->leftJoin('mailer_contacts_lists_contacts as mclc', function ($query) {
//                $query->on('mailer_contacts.id', '=', 'mclc.list_id');
//            })
//            ->leftJoin('clients_contact as cc', function ($query) {
//                $query->on('mclc.contact_id', '=', 'cc.id');
//            })
//            ->first();

        return response()->json([
            'id' => $contactListItem->id,
            'name' => $contactListItem->name,
            'description' => $contactListItem->description,
            'list' => $contactList
        ]);
    }

    public function templateSave(Request $request)
    {

        $id = Template::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'body' => $request->input('template'),
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
        ]);
    }

    public function getTemplates()
    {
        return response()->json([
            'templates' => Template::all(),
            'variables' => Template::getVariables()
        ]);
    }

    public function getTemplateItem(Request $request)
    {
        return response()->json(Template::where('id', $request->input('id'))->first());
    }

    public function updateTemplate(Request $request)
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

    public function removeTemplate(Request $request)
    {
        $templateId = $request->input('id');

        $res = Template::where('id', $templateId)
            ->delete();

        if ($res) {
            return response()->json([], 200);
        }
    }

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    //protected function distribution

    public function saveDistribution(CreateDistribution $request)
    {
        $modelParams = $this->prepareDistributionFormParams($request);
        //dump($modelParams);
        $savedId = Distribution::create($modelParams);

        if ($savedId) {
            return response()->json([
                'result' => $savedId,
            ], 200);
        }
    }

    public function updateDistribution(UpdateDistribution $request)
    {
        $id = $request->input('id');

        $res = Distribution::where('id', $id)
            ->update($this->prepareDistributionFormParams($request));

        if ($res) {
            return response('ok', 200);
        }

        return response('error', 500);
    }

    public function getDistributions()
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

    public static function sendEmail(string $to, $message, string $subject = 'Новое письмо')
    {

        $headers = '';
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        try {
            return mail($to, $subject, $message, $headers);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function removeDistribution(Request $request)
    {

        $this->validate($request, [
            'id' => 'required'
        ]);

        Distribution::where('id', $request->input('id'))->delete();

        return response()->json([
            'message' => 'deleted'
        ]);
    }

    public function sendingEmails(Request $request)
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

        $variables = [];

        foreach ($template->variables as $variable) {
            $variables[$variable->name] = $contactList->{$variable->column};
        }

        foreach ($contactList->contacts as $contact) {

            $emailSender = new Mail();
            $emailSender->to('stanislavqq@yandex.ru')
                ->from($distribution->from_email)
                ->fromName($distribution->from_name)
                ->subject($distribution->subject)
                ->message($template->body, $variables);

            $emailsCollect->push($emailSender);
        }

        $res = SendEmail::dispatchNow($emailsCollect);

        if (!$template) {
            return response()->json([
                'message' => trans('messages.template_not_exist'),
            ], 500);
        }

        return;

        $emails = [];
        //mailer_distributions.id as md_id, mailer_distributions.template_id, mailer_distributions.contacts_list_id, mclc.contact_id
//        $emails = ClientsContact::select('*')
//            ->leftJoin('mailer_contacts_lists_contacts as mclc', 'clients_contact.id', '=', 'mclc.contact_id')
//            ->leftJoin('mailer_contacts as mc', 'mclc.list_id', '=', 'mc.id')
//            ->leftJoin('mailer_distributions', 'mc.id', '=', 'mailer_distributions.contacts_list_id')
//            ->leftJoin('mailer_templates', 'mailer_distributions.template_id', '=', 'mailer_templates.id')
//            ->where('mailer_distributions.id', $fields['contacts_list_id'])
//            ->where('mc.id', $fields['contacts_list_id'])
//            ->where('mailer_templates.id', $fields['template_id'])
//            ->get();

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

    public function deleteContactList(Request $request)
    {
        $id = (int)$request->input('id');

        ContactList::find($id)->delete();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMailerDrivers()
    {
        $userDriver = UserMailerDrivers::where('user_id', Auth::user()->id)->get(['driver_id', 'login', 'password'])->first();

        return response()->json([
            'drivers' => MailerDriver::get(['id', 'name'])->toArray(),
            'user_driver' => $userDriver ? $userDriver->toArray() : null
        ]);
    }

    public function saveUserMailerDriver(Request $request)
    {
        $requestCollect = new Collection($request->input());
        $requestCollect['user_id'] = Auth::user()->id;

        $model = UserMailerDrivers::where('user_id', Auth::user()->id)->first();

        if ($model) {
            $model->login = $requestCollect['login'];
            $model->password = $requestCollect['password'];
            $model->driver_id = $requestCollect['driver_id'];
            $res = $model->update();
        } else {
            $res = UserMailerDrivers::create($requestCollect->toArray());
        }

        if ($res) {
            return response()->json([
                'status' => 'ok'
            ], 200);
        }

        return response()->json([
            'status' => 'error'
        ], 500);

    }
}
