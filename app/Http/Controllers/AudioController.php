<?php

namespace App\Http\Controllers;

use App\Http\Requests\AudioStoreRequest;
use App\Models\Audio;
use App\Services\AudioService;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AudioController extends Controller
{
    protected AudioService $audioService;

    public function __construct(AudioService $audioService)
    {
        $this->audioService = $audioService;
    }

    public function index(): View
    {
        $audios = Audio::query()->orderBy('created_at', 'desc')->get();
        return view('audio.index', compact('audios'));
    }

    public function create(): View
    {
        return view('audio.create');
    }

    public function store(AudioStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->audioService->store($data, auth()->id());
        return redirect()->route('audios.index');
    }

    public function show(Audio $audio): View
    {
        return view('audio.show', compact('audio'));
    }
}
