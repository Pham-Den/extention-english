<?php

namespace Plugins\Audio\Actions;

use App\Plugins\Audio\Services\AudioService;
use Illuminate\Http\Request;

class SplitAudioAction
{
    // public function __invoke($request, $response)
    // {
    //     $audioFile = $request->files['audio'] ?? null;

    //     if (!$audioFile || $audioFile['error'] !== UPLOAD_ERR_OK) {
    //         return $this->error($response, 'Invalid audio upload');
    //     }

    //     // Lưu file tạm
    //     $tempFilePath = storage_path('tmp/' . $audioFile['name']);
    //     move_uploaded_file($audioFile['tmp_name'], $tempFilePath);

    //     // Danh sách đoạn cần giữ
    //     $timestamps = [
    //         ['start' => '00:00:52', 'end' => '00:00:54'],
    //         ['start' => '00:02:28', 'end' => '00:02:39'],
    //         ['start' => '00:03:21', 'end' => '00:03:28'],
    //     ];
    //     // $audioFile = $request->files['audio'] ?? null;

    //     // if (!$audioFile || $audioFile['error'] !== UPLOAD_ERR_OK) {
    //     //     return response($response)->error('Invalid audio upload', 400);
    //     // }

    //     // $tempFilePath = storage_path('tmp/' . $audioFile['name']);
    //     // move_uploaded_file($audioFile['tmp_name'], $tempFilePath);

    //     // Nhận và decode timestamps
    //     $timestampsJson = $request->post['timestamps'] ?? '[]';
    //     $timestamps = json_decode($timestampsJson, true);

    //     if (!is_array($timestamps)) {
    //         return response($response)->error('Invalid timestamps', 400);
    //     }

    //     $audioService = new AudioService();
    //     $cutFiles = [];

    //     try {
    //         foreach ($timestamps as $index => $ts) {
    //             $outputFile = storage_path('tmp/cut_' . $index . '.mp3');
    //             $audioService->cutAudio($tempFilePath, $ts['start_time'], $ts['end_time'], $outputFile);
    //             $cutFiles[] = $outputFile;
    //         }
    //         if (count($cutFiles) < count($timestamps)) {
    //             return response($response)->error('Not all audio segments were created', 400);
    //         }

    //         $finalOutputFile = storage_path('tmp/final_output.mp3');
    //         $audioService->mergeAudio($finalOutputFile, $cutFiles);

    //         response($response)->file($finalOutputFile, 'output.mp3', 'audio/mpeg');
    //         // Trả response
    //         // response($response)->success([
    //         //     'output_file' => $finalOutputFile
    //         // ], 'Audio processed successfully');
            

    //     } catch (\Throwable $e) {
    //         response($response)->error($e->getMessage(), 500);
    //     }
    // }

    public function handle(Request $request)
    {
        return 'asddasads';
        // Lấy file từ request
        $audioFile = $request->file('audio');

        if (!$audioFile || !$audioFile->isValid()) {
            return response()->json(['error' => 'Invalid audio upload'], 400);
        }

        // Lưu file tạm vào storage
        $tempFilePath = storage_path('tmp/' . $audioFile->getClientOriginalName());
        $audioFile->move(storage_path('tmp'), $audioFile->getClientOriginalName());

        // Lấy timestamps từ input (POST body)
        $timestampsJson = $request->input('timestamps', '[]');
        $timestamps = json_decode($timestampsJson, true);

        if (!is_array($timestamps)) {
            return response()->json(['error' => 'Invalid timestamps'], 400);
        }

        $audioService = new AudioService();
        $cutFiles = [];

        try {
            foreach ($timestamps as $index => $ts) {
                if (!isset($ts['start_time'], $ts['end_time'])) {
                    continue;
                }

                $outputFile = storage_path("tmp/cut_{$index}.mp3");
                $audioService->cutAudio($tempFilePath, $ts['start_time'], $ts['end_time'], $outputFile);
                $cutFiles[] = $outputFile;
            }

            if (count($cutFiles) < count($timestamps)) {
                return response()->json(['error' => 'Not all audio segments were created'], 400);
            }

            $finalOutputFile = storage_path('tmp/final_output.mp3');
            $audioService->mergeAudio($finalOutputFile, $cutFiles);

            // Trả file audio
            return response()->download($finalOutputFile, 'output.mp3', ['Content-Type' => 'audio/mpeg']);

        } catch (\Throwable $e) {
            Log::error('SplitAudio error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
