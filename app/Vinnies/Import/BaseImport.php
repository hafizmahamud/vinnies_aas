<?php

namespace App\Vinnies\Import;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Csv\Reader;

abstract class BaseImport
{
    protected $csv;
    protected $headers;

    public function __construct(UploadedFile $csv)
    {
        $this->csv = Reader::createFromPath($csv->path());
        $this->csv->setHeaderOffset(0);

        $this->headers = $this->csv->getHeader();
        $this->data    = collect($this->csv->getRecords());
    }

    public function validHeaders()
    {
        return empty(array_diff($this->columns, $this->headers));
    }

    public function index($columnName)
    {
        return array_search($columnName, $this->headers);
    }

    abstract public function import($file_id);
}
