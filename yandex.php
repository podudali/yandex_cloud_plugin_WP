<?php

require_once __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

class Yandex_Cloud
{
    private $s3;
    private $folderName;
    private $file;
    private $bucket = '';
    public function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
    }
    public function sendToStorage(): void
    {
        $currentYear = date('Y');
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/wordpress/wp-content/uploads/{$currentYear}/";
        // Получение списка файлов в папке
        $folders = array_diff(scandir($dir), array('.', '..'));
        foreach ($folders as $folder) {
            $folderName = basename($folder);
            $getObject = $this->s3->getObjectUrl(
                $this->bucket,
                $folderName . '/',
            );
            if($getObject === false){
            // Создаем папку в корне бакета
            $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $folderName . '/',
            ]);
        }else{
            $files = array_diff(scandir($dir . $folderName), array('.', '..'));
            // Цикл по всем файлам
            foreach ($files as $file) {
                if (in_array($file, array(".", ".."))) {
                    continue;
                }
                // Пропускаем . и ..
        }
                // Путь к текущей картинке
                $filePath = $dir . $folderName . '/' . $file;
                try {
                    $result = $this->s3->putObject([
                        'Bucket' => $this->bucket,
                        'Key' => "wp-content/uploads/{$currentYear}/" . $folderName . '/' . $file,
                        'SourceFile' => $filePath,
                        'ACL' => 'public-read',
                    ]);
                } catch (Aws\S3\Exception\S3Exception$e) {
                    echo "There was an error uploading the file.\n . {$e}";
                }
            }
        }
        $bucketName = $this->bucket;
        $objectName = $folderName . '/' . $file;

        $objectUrl = $this->s3->getObjectUrl($bucketName, $objectName);
    }
}
