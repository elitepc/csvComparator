<?php


namespace App\Http\Helpers;


use App\Exceptions\CsvException;

class CsvComparator
{
    private $oldCsvFile;
    private $newCsvFile;

    private $oldCsvArray;
    private $newCsvArray;

    private $oldCsvHeader;
    private $newCsvHeader;

    private $comparisonResult;

    private $delimiter;

    /**
     * CsvComparator constructor.
     * @param $oldCsvFile
     * @param $newCsvFile
     * @param  string  $delimiter
     */
    public function __construct($oldCsvFile, $newCsvFile, $delimiter = ';')
    {
        $this->oldCsvFile = $oldCsvFile;
        $this->newCsvFile = $newCsvFile;
        $this->delimiter = $delimiter;

        $this->convertCsvsToArray();
    }

    /**
     * Compares and returns the CSVs
     * @return array All lines from the newer file marked as new, equal or different
     * @throws CsvException
     */
    public function getCsvDifferences()
    {

        if ($this->checkCsvCannotBeCompared()) {
            throw new CsvException("Csv files can't be compared");
        }

        $oldCsvArrayBody = array_slice($this->oldCsvArray, 1);
        $newCsvArrayBody = array_slice($this->newCsvArray, 1);

        // remove header from array
        $this->oldCsvHeader = $this->oldCsvArray[0];
        $this->newCsvHeader = $this->newCsvArray[0];

        // make it searchable by unique columns
        $csvHashMap = self::convertUniqueValuesToKeys($oldCsvArrayBody);
        $newCsvHashMap = self::convertUniqueValuesToKeys($newCsvArrayBody);

        $this->compareCsvValues($newCsvHashMap, $csvHashMap);

        return $this->comparisonResult;
    }

    /**
     * @return mixed
     */
    public function getNewCsvHeader()
    {
        return $this->newCsvHeader;
    }

    /**
     * Returns true if CSV files can't be compared
     * @return bool
     */
    private function checkCsvCannotBeCompared()
    {
        return !$this->oldCsvArray
            || !$this->newCsvArray
            || !is_array($this->oldCsvArray[0])
            || !is_array($this->newCsvArray[0])
            || array_diff($this->oldCsvArray[0], $this->newCsvArray[0]);
    }

    /**
     * Converts both files to array
     */
    private function convertCsvsToArray()
    {
        $this->oldCsvArray = self::csvToArray($this->oldCsvFile, $this->delimiter);
        $this->newCsvArray = self::csvToArray($this->newCsvFile, $this->delimiter);
    }

    /**
     * Converts a file to array
     * @param $file
     * @param $delimiter
     * @return array
     */
    private static function csvToArray($file, $delimiter)
    {
        return array_map(
            function ($v) use ($delimiter) {
                return str_getcsv($v, $delimiter);
            }, file($file->getRealPath()));
    }

    /**
     * Converts line into an hashmap with key:
     * "{$cnpj}_{$pdf_file_name}_{$balance_date}_{$balance_refers_to_date}"
     * TODO: make more dynamic if needed
     * @param $csv
     * @return array
     */
    private static function convertUniqueValuesToKeys($csv)
    {
        $csvHashMap = [];
        foreach ($csv as $csvLine) {
            $csvHashMap[$csvLine[0] . '_' . $csvLine[1] . '_' . $csvLine[2] . '_'. $csvLine[3]] = $csvLine;
        }
        return $csvHashMap;
    }

    /**
     * Compares based on the hashmaps provided
     * @param $newCsvHashMap
     * @param $csvHashMap
     */
    private function compareCsvValues($newCsvHashMap, $csvHashMap)
    {
        $this->comparisonResult = [];

        foreach ($newCsvHashMap as $key => &$line) {
            $this->comparisonResult[$key] = $line;

            if (!array_key_exists($key, $csvHashMap)) {
                $this->comparisonResult[$key]['compare'] = 'new';
                continue;
            }
            $difference = array_diff($line, $csvHashMap[$key]);

            if ($difference) {
                $this->comparisonResult[$key]['compare'] = 'different';
                $this->comparisonResult[$key]['difference'] = $difference;
            } else {
                $this->comparisonResult[$key]['compare'] = 'equal';
            }
        }
    }


}
