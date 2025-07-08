<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeputarDinusSlider;
use App\Models\SeputarDinusSlidesTitle;
use App\Models\SeputarDinusSlidesTitleTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class SeputarDinusSlidesTitleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = SeputarDinusSlidesTitle::with('translations.translation');

        if ($search) {
            $query->where('judul', 'like', '%' . $search . '%');
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('judul', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $slidesTitles = $query->paginate($perPage);

        return response()->json([
            'current_page' => $slidesTitles->currentPage(),
            'per_page' => $slidesTitles->perPage(),
            'total' => $slidesTitles->total(),
            'last_page' => $slidesTitles->lastPage(),
            'next_page_url' => $slidesTitles->nextPageUrl(),
            'prev_page_url' => $slidesTitles->previousPageUrl(),
            'data' => $slidesTitles->items(),
        ], Response::HTTP_OK);
    }

    public function getOnlyTranslationData(Request $request)
    {
        $langId = $request->input('translation_id');

        if (!$langId) {
            return response()->json(['message' => 'Translation ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $translation = Translation::find($langId);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], Response::HTTP_NOT_FOUND);
        }

        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('current_page', 1);
        $search = $request->input('search', null);
        $sort = $request->input('sort', 'id_desc');

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $query = SeputarDinusSlidesTitleTranslation::where('translation_id', $translation->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'asc' || $sort === 'desc') {
            $query->orderBy('judul', $sort);
        } elseif ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $paginated = $query->paginate($perPage);

        $data = $paginated->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'judul' => $item->judul,
                'translation_id' => $item->translation_id,
                'spt_dinus_title_id' => $item->spt_dinus_title_id,
            ];
        });

        return response()->json([
            'translation' => [
                'id' => $translation->id,
                'name' => $translation->name,
                'code' => $translation->code,
            ],
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $allLangs = Translation::all();
        foreach ($allLangs as $lang) {
            $request->validate([
                'judul' . $lang->code => 'nullable|string|max:255',
            ]);
        }

        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::create($request->only(['judul', 'urutan']));

        foreach ($allLangs as $lang) {
            $judulKey = 'judul' . $lang->code;

            SeputarDinusSlidesTitleTranslation::create([
                'spt_dinus_title_id' => $seputarDinusSlidesTitle->id,
                'translation_id' => $lang->id,
                'judul' => $request->input($judulKey),
                'urutan' => $seputarDinusSlidesTitle->urutan,
            ]);
        }

        return response()->json(
            $seputarDinusSlidesTitle->load('translations.translation'),
            Response::HTTP_CREATED
        );
    }


    public function show(string $id)
    {
        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::find($id);
        if ($seputarDinusSlidesTitle) {
            return response()->json($seputarDinusSlidesTitle, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $allLangs = Translation::all();
        foreach ($allLangs as $lang) {
            $request->validate([
                'judul' . $lang->code => 'nullable|string|max:255',
            ]);
        }

        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::find($id);
        if (!$seputarDinusSlidesTitle) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }

        $seputarDinusSlidesTitle->update($request->only(['judul', 'urutan']));

        foreach ($allLangs as $lang) {
            $judulKey = 'judul' . $lang->code;

            $translation = SeputarDinusSlidesTitleTranslation::where('spt_dinus_title_id', $seputarDinusSlidesTitle->id)
                ->where('translation_id', $lang->id)
                ->first();

            if ($translation) {
                $translation->update([
                    'judul' => $request->input($judulKey, $seputarDinusSlidesTitle->judul),
                    'urutan' => $seputarDinusSlidesTitle->urutan,
                ]);
            } else {
                SeputarDinusSlidesTitleTranslation::create([
                    'spt_dinus_title_id' => $seputarDinusSlidesTitle->id,
                    'translation_id' => $lang->id,
                    'judul' => $request->input($judulKey, $seputarDinusSlidesTitle->judul),
                    'urutan' => $seputarDinusSlidesTitle->urutan,
                ]);
            }
        }

        return response()->json(
            $seputarDinusSlidesTitle->load('translations.translation'),
            Response::HTTP_OK
        );
    }


    public function destroy(string $id)
    {
        $seputarDinusSlidesTitle = SeputarDinusSlidesTitle::find($id);
        if ($seputarDinusSlidesTitle) {
            $seputarDinusSlidesTitle->delete();
            return response()->json(['message' => 'Data deleted'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
    }

    public function destroyTranslation($id)
    {
        $translation = SeputarDinusSlidesTitleTranslation::find($id);

        if (!$translation) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

        $translation->delete();

        return response()->json(['message' => 'Translation deleted successfully'], 200);
    }
}
