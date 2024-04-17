<?php

namespace App\Jobs;

use App\Models\Audio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use GuzzleHttp\Client;

class HandleAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Audio $audio;
    public function __construct(Audio $audio)
    {
        $this->audio = $audio;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $audio = $this->audio;
        $file_path = public_path('storage/'.$audio->path);

        $client = new Client();
        if($audio->path)
        {
            $response = $client->request('POST', env('VOCAL_REMOVER_API'), [
                'multipart' => [
                    [
                        'name'     => 'audio_file',
                        'contents' => fopen($file_path, 'r'),
                        'filename' => $audio->origin_name
                    ]
                ],
                'stream' => true,
                'timeout' => 500
            ]);
        }
        else{
            $response = $client->request('POST', env('VOCAL_REMOVER_YOUTUBE_API'), [
                'form_params' => [
                    'youtube' => $audio->youtube_link
                ],
                'stream' => true,
                'timeout' => 500
            ]);
        }


        if ($response->getStatusCode() == 200) {
            $stream = $response->getBody(); // Получаем поток ответа
            $zipContent = $stream->getContents(); // Читаем содержимое потока

            $zip = new ZipArchive;
            $tmpFile = tmpfile();
            fwrite($tmpFile, $zipContent);
            fseek($tmpFile, 0);

            if ($zip->open(stream_get_meta_data($tmpFile)['uri']) === TRUE) {
                $zip->extractTo(storage_path('app/separated_files')); // Извлечение файлов
                $zip->close(); // Закрываем архив
                fclose($tmpFile); // Закрываем и удаляем временный файл
                $instrumentsPath = storage_path('app/separated_files/instruments.wav');
                $vocalsPath = storage_path('app/separated_files/vocals.wav');

                $instrumentsSavedPath = Storage::disk('public')->putFile('audios', new \Illuminate\Http\File($instrumentsPath));
                $vocalsSavedPath = Storage::disk('public')->putFile('audios', new \Illuminate\Http\File($vocalsPath));
                $audio->update([
                    'audio_voice' => $instrumentsSavedPath,
                    'audio_noise' => $vocalsSavedPath,
                    'status' => 1
                ]);
                echo "Файл инструментов сохранен: " . $instrumentsSavedPath . "\n";
                echo "Файл вокала сохранен: " . $vocalsSavedPath . "\n";
                $this->getText();
            } else {
                echo 'Ошибка при разархивации файлов';
                fclose($tmpFile); // Закрываем и удаляем временный файл
            }
        }
        else {
            echo "Ошибка: " . $response->getStatusCode();
        }
    }


    public function getText()
    {
        $audio = $this->audio;
        $file_path = public_path('storage/'.$audio->audio_noise);

        $client = new Client();
        $response = $client->request('POST', env('WHISPER_API'), [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($file_path, 'r'),
                    'filename' => $audio->audio_noise
                ]
            ]
        ]);

        if ($response->getStatusCode() == 200)
        {
            $jsonResponse = $response->getBody()->getContents();
            $audio->update([
                'text' => $jsonResponse,
                'status' => 2
            ]);
        }
    }

}
