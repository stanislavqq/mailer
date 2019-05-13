<?php

namespace App\Http\Controllers\Mailer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mailer\CreateContact;
use App\Http\Requests\Mailer\UpdateContact;
use App\Models\ClientsContact;
use App\Models\Mailer\ClientsContactsLists;
use App\Models\Mailer\ContactList;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    private $filterFieldsToDataRequest = [
        "client_name" => 'c.client_name',
        "client_director" => 'clients_contact.name',
        "client_email" => 'clients_contact.email',
        "client_phone" => 'clients_contact.phone',
        "client_city" => 'lc.city_name',
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClientsList(Request $request)
    {
        $limit = $request->input('limit', 50);
        $offset = $request->input('offset', 50);

        $filter = $request->input('filter');

        $clientsQuery = $this->getClientContacts();

        $clients = $clientsQuery;

        foreach ($this->filterFieldsToDataRequest as $index => $value) {
            if (isset($filter[$index]) && !empty($filter[$index])) {
                $clients = $clients->where($value, 'like', "%" . $filter[$index] . "%");
            }
        }

        $clientsTotal = $clientsQuery->count();

        $clients = $clientsQuery->orderByDesc('clients_contact.client_id');

        if ($clientsTotal > $limit) {
            $clients->take($limit)
                ->offset($offset);
        }

        $clients = $clients->get();

        if ($clients) {
            return response()->json([
                'clients' => $clients->toJson(),
                'count' => $clients->count(),
                'clients_total' => $clientsTotal
            ], 200);
        }

        return response()->json([
            'clients' => [],
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContactList()
    {
        $userId = Auth::user()->id;
        $contactLists = ContactList::where('user_id', $userId)
            ->with('contacts')
            ->get();

        foreach ($contactLists as $list) {
            $list->contactCount = $list->contacts->count();
        }

        return response()->json($contactLists);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $id = $request->input('id');

        $contactListItem = ContactList::where('id', $id)->first();

        $contactListIds = ClientsContactsLists::select('contact_id')->where('list_id', $id)->pluck('contact_id');
        $contactList = $this->getClientContacts($contactListIds)->get();

        return response()->json([
            'id' => $contactListItem->id,
            'name' => $contactListItem->name,
            'description' => $contactListItem->description,
            'list' => $contactList
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListItem(Request $request)
    {
        $id = $request->input('id');

        $contactListItem = ContactList::where('id', $id)->first();

        $contactListIds = ClientsContactsLists::select('contact_id')
            ->where('list_id', $id)
            ->pluck('contact_id');

        $contactList = $this->getClientContacts($contactListIds)->get();//ClientsContact::whereIn('id', $contactListIds)->get();

        return response()->json([
            'id' => $contactListItem->id,
            'name' => $contactListItem->name,
            'description' => $contactListItem->description,
            'list' => $contactList
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(CreateContact $request)
    {
        $ids = [];

        DB::beginTransaction();

        try {
            $contactListId = ContactList::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'user_id' => Auth::user()->id
            ]);

            foreach ($request->input('contacts_list') as $item) {
                $ids[] = [
                    'contact_id' => $item['id'],
                    'list_id' => $contactListId->id
                ];
            }

            ClientsContactsLists::insert($ids);

            DB::commit();
            return response()->json([
                'status' => 'ok',
                'id' => $contactListId
            ], 200);

        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'error_message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateContact $request)
    {
        $ids = [];
        $listId = $request->input('id');

        DB::beginTransaction();

        try {
            ContactList::where('id', $listId)
                ->update([
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                ]);

            ClientsContactsLists::where('list_id', $listId)
                ->delete();

            foreach ($request->input('list') as $item) {
                $ids[] = [
                    'contact_id' => $item['id'],
                    'list_id' => $listId
                ];
            }

            $inserted = ClientsContactsLists::insert($ids);

            DB::commit();
            return response()->json([
                'status' => 'ok',
                'id' => $listId
            ], 200);

        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'error_message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param Request $request
     */
    public function remove(Request $request)
    {
        $id = (int)$request->input('id');

        ContactList::find($id)->delete();
    }

    /**
     * @param array $contactListIds
     * @return mixed
     */
    public function getClientContacts($contactListIds = [])
    {
        $query = ClientsContact::select('id', 'c.client_name as client_name', 'name', 'phone', 'c.client_id as cl_id', 'email', 'birthday', 'lc.city_name as city')
            ->leftJoin('clients as c', 'clients_contact.client_id', '=', 'c.client_id')
            ->leftJoin('location_city as lc', 'c.client_city', '=', 'lc.city_id');

        if ($contactListIds) {
            $query->whereIn('id', $contactListIds);
        }

        return $query;
    }
}
