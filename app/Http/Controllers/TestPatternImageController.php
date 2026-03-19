<?php

namespace App\Http\Controllers;

use App\Models\TestPatternImage;

class TestPatternImageController extends Controller
{
    private const BASE_URL = 'https://qubyx.org/images/testpatterns';

    public function index()
    {
        $items = TestPatternImage::orderBy('id')->get();
        $data = $items->map(fn ($item) => [
            'id' => (int) $item->id,
            'name' => $item->name,
            'url' => rtrim(self::BASE_URL, '/') . '/' . ltrim($item->url ?? '', '/'),
        ]);

        return response()->json($data);
    }
}
