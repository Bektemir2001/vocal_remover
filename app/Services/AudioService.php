<?php

namespace App\Services;



use App\Jobs\HandleAudioJob;
use App\Models\Audio;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
class AudioService
{
    public function store(array $data, int $user): array
    {
        try {
            $query = Audio::query();
            if(isset($data['audio'])) {
                $name_file = $data['audio']->getClientOriginalName();
                $path = Storage::disk('public')->put('audios', $data['audio']);
                $audio = $query->create([
                    'user_id' => $user,
                    'path' => $path,
                    'origin_name' => $name_file,
                ]);
                HandleAudioJob::dispatch($audio);
            }
            else{
                $audio = $query->create([
                    'user_id' => $user,
                    'youtube_link' => $data['youtube_link']
                ]);
            }


            return ['success' => true];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
