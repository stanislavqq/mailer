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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClientsList()
    {
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
        $contactList = $this->getClientContacts($contactListIds);

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
    public function getListItem(Request $request) {
        $id = $request->input('id');

        $contactListItem = ContactList::where('id', $id)->first();

        $contactListIds = ClientsContactsLists::select('contact_id')
            ->where('list_id', $id)
            ->pluck('contact_id');

        $contactList = $this->getClientContacts($contactListIds);//ClientsContact::whereIn('id', $contactListIds)->get();

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

        return $query->limit(1000)
            ->orderByDesc('clients_contact.client_id')
            ->get();
    }
}
