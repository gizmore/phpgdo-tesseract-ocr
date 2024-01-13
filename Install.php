<?php
namespace GDO\TesseractOCR;

use GDO\CLI\Process;

final class Install
{

    public static function tesseractOCR(): void
    {
        $module = Module_TesseractOCR::instance();
        if (!$module->cfgBinaryPath())
        {
            if ($path = Process::commandPath('tesseract'))
            {
                $module->saveConfigVar('tesseract_binary', $path);
                $module->message('msg_binary_detected', ['tesseract']);
            }
            else
            {
                $module->error('%s', ['Cannot find tesseract binary!']);
            }
        }
    }

}
