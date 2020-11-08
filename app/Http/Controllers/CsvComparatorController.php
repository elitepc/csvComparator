<?php

namespace App\Http\Controllers;

use App\Exceptions\CsvException;
use App\Http\Requests\CsvRequest;
use App\Http\Helpers\CsvComparator;

class CsvComparatorController extends Controller
{
    public function upload(CsvRequest $request)
    {
        $file = $request->file('file');
        $newFile = $request->file('new_file');

        if (!$file->isValid() || !$newFile->isValid()) {
            abort(400, "Bad request, invalid files");
        }

        $csvComparator = new CsvComparator($file, $newFile, ";");

        try {
            $differences = $csvComparator->getCsvDifferences();

            return view('table_compare', [
                'csvData' => $differences,
                'header' => $csvComparator->getNewCsvHeader()
            ]);
        } catch (CsvException $e) {
            abort(400, "Bad request, CSVs can't be compared");
        }
    }

}
