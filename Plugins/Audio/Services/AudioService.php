<?php

namespace App\Plugins\Audio\Services;


class AudioService
{
    private $ffmpegPath = '/usr/bin/ffmpeg';  // Đảm bảo rằng bạn đã cài đặt FFmpeg và đường dẫn chính xác

    // Phương thức cắt đoạn audio theo timestamp
    public function cutAudio($inputFile, $startTime, $endTime, $outputFile)
    {
        if (!file_exists($inputFile) || !is_readable($inputFile)) {
            app('log')->error("Cannot access input file: {$inputFile}");
            return false;
        }
        
        // Tính độ dài đoạn cắt (endTime - startTime) bằng giây
        $startTimeInSeconds = strtotime($startTime);
        $endTimeInSeconds = strtotime($endTime);
        $duration = $endTimeInSeconds - $startTimeInSeconds;  // Độ dài đoạn cắt = 50 giây
        
        // Câu lệnh FFmpeg sửa lại
        $cmd = sprintf(
            '%s -i "%s" -ss %s -t %d -c:a libmp3lame -q:a 4 "%s"',
            $this->ffmpegPath,
            $inputFile,
            $startTime,
            $duration, 
            $outputFile
        );
        
        
        
        // $cmd .= ' 2>&1'; // thêm vào cuối lệnh
        exec($cmd, $output, $returnVar);
    
        // Kiểm tra nếu có lỗi, ghi chi tiết vào log
        if ($returnVar !== 0) {
            app('log')->error('Error during audio cutting: ' . implode("\n", $output));
        }
    
        return $outputFile;
    }
    

    // Phương thức ghép các file audio đã cắt lại thành một file duy nhất
    public function mergeAudio($outputFile, $inputFiles)
    {
        // Tạo danh sách file tạm để ghép
        $tempListFile = '/tmp/audio_files.txt';
        $fileListContent = '';

        foreach ($inputFiles as $file) {
            $fileListContent .= "file '$file'\n";
        }

        file_put_contents($tempListFile, $fileListContent);

        // Ghép các file lại với nhau
        $cmd = sprintf('%s -f concat -safe 0 -i %s -c copy %s', $this->ffmpegPath, $tempListFile, $outputFile);
        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            app('log')->error('Error during audio merging: ' . implode("\n", $output));
        }

        // Xóa file tạm
        unlink($tempListFile);

        return $outputFile;
    }

}
