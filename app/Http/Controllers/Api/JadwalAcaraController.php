<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalAcara;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class JadwalAcaraController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');
        $idHari = $request->input('id_hari', null);

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = JadwalAcara::with('hari');

        if ($idHari) {
            $query->where('id_hari', $idHari);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('acara', 'like', '%' . $search . '%')
                  ->orWhere('link', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('jam_awal', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $result = $query->paginate($perPage);

        return response()->json([
            'current_page' => $result->currentPage(),
            'per_page' => $result->perPage(),
            'total' => $result->total(),
            'last_page' => $result->lastPage(),
            'next_page_url' => $result->nextPageUrl(),
            'prev_page_url' => $result->previousPageUrl(),
            'data' => $result->items(),
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hari' => 'required|exists:tb_hari,id',
            'jam_awal' => 'required|date_format:H:i',
            'jam_akhir' => 'required|date_format:H:i|after:jam_awal',
            'acara' => 'required|string|max:255',
            'link' => 'nullable|url|max:1000',
            'uploader' => 'required|exists:users,id',
            'waktu' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $overlap = JadwalAcara::where('id_hari', $request->id_hari)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('jam_awal', '<', $request->jam_akhir)
                    ->where('jam_akhir', '>', $request->jam_awal);
                });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'Jadwal bertabrakan dengan acara lain pada hari dan jam yang sama.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $hariAcara = JadwalAcara::create($request->all());
        $hariAcara->load('hari');

        return response()->json($hariAcara, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $data = JadwalAcara::with('hari')->find($id);

        if (!$data) {
            return response()->json(['message' => 'Acara tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $acara = JadwalAcara::find($id);

        if (!$acara) {
            return response()->json(['message' => 'Acara tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'id_hari' => 'sometimes|required|exists:tb_hari,id',
            'jam_awal' => 'sometimes|required|date_format:H:i',
            'jam_akhir' => 'sometimes|required|date_format:H:i|after:jam_awal',
            'acara' => 'sometimes|required|string|max:255',
            'link' => 'nullable|url|max:1000',
            'uploader' => 'sometimes|required|exists:users,id',
            'waktu' => 'sometimes|required|date_format:Y-m-d H:i:s',
        ]);

        $idHari = $request->input('id_hari', $acara->id_hari);
        $jamAwal = $request->input('jam_awal', $acara->jam_awal);
        $jamAkhir = $request->input('jam_akhir', $acara->jam_akhir);

        $overlap = JadwalAcara::where('id_hari', $idHari)
            ->where('id', '!=', $acara->id)
            ->where(function ($query) use ($jamAwal, $jamAkhir) {
                $query->where('jam_awal', '<', $jamAkhir)
                    ->where('jam_akhir', '>', $jamAwal);
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'Jadwal bertabrakan dengan acara lain pada hari dan jam yang sama.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $acara->update($request->all());
        $acara->load('hari');

        return response()->json($acara, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $acara = JadwalAcara::find($id);
        if (!$acara) {
            return response()->json(['message' => 'Acara tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        $acara->delete();
        return response()->json(['message' => 'Acara berhasil dihapus'], Response::HTTP_OK);
    }
}
