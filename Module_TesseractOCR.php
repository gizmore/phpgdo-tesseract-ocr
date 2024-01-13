<?php
namespace GDO\TesseractOCR;

use GDO\CLI\Process;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Path;
use GDO\FFMpeg\Module_FFMpeg;
use GDO\ImageMagick\Module_ImageMagick;
use GDO\Util\FileUtil;

final class Module_TesseractOCR extends GDO_Module
{

    public function getConfig(): array
    {
        return [
            GDT_Path::make('tesseract_binary')->existingFile(),
        ];
    }

    public function cfgBinaryPath(): ?string
    {
        return $this->getConfigVar('tesseract_binary');
    }

    public function onInstall(): void
    {
        Install::tesseractOCR();
    }

    public function getDependencies(): array
    {
        return [
            'ImageMagick',
        ];
    }

    public function scan(string $path): ?string
    {
        $path2 = $path . '.jpg';
        $im = Module_ImageMagick::instance()->cfgConvertPath();
        $cmd = sprintf('%s %s -bordercolor White -border 120x120 %s', escapeshellarg($im), escapeshellarg($path), escapeshellarg($path2));
        echo "{$cmd}\n";
        $output = null;
        $result = exec($cmd, $output);
        if ($result === false)
        {
            return null;
        }
        FileUtil::removedFile($path);

        $tempPath = $this->tempPath('tess_out');
        $cmd = sprintf('tesseract %s %s -l deu --oem 3 --psm 3 -c thresholding_method=2 -c tessedit_write_images=true', escapeshellarg($path2), escapeshellarg($tempPath));
        echo "{$cmd}\n";
        $output = null;
        $result = exec($cmd, $output);
        if ($result !== false)
        {
            $text = file_get_contents($tempPath . '.txt');
            FileUtil::removedFile($tempPath);
            return $text;
        }
        return null;
    }

}
