<?php

namespace App\Http\Controllers\Utm;

use App\Factories\LinkFactory;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UtmController extends Controller
{
    /**
     * @return \Illuminate\View\View|\Laravel\Lumen\Application
     */
    public function importView()
    {
        return view('importFile');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function importExcel(Request $request)
    {
        $validator=\Validator::make($request->all(), [
            'file'=>'required|mimes:xlsx, csv, xls'
        ]);
        if ($validator->fails())
            return response()->json(['status'=>false, 'error'=>$validator->errors()->all()], 401);

        $file=$request->file('file');
        $path=$this->UploadFile($file);

        list($data, $spreadsheet, $sheet)=$this->getLoad($path);

        $addressFile=$this->saveExcel($sheet, $data, $spreadsheet);

        File::delete($path);

        return response()->json(['status'=>true, 'file'=>$addressFile]);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
        private function UploadFile($file)
    {
        $uploadedFile=$file;
        $filename=time() . $uploadedFile->getClientOriginalName();
        $result=$uploadedFile->move(public_path() . '/file/', $filename);

        if ( $result->getRealPath() ) {
            return $result->getRealPath();
        }

        throw new Exception("There was an error uploading");
    }

    /**
     * @param $path
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function getLoad($path)
    {
        $reader=new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        // Read the spreadsheet file.
        $reader=$reader->load($path);
        $data=$reader->getActiveSheet()->toArray(null, true, true, true);

        $spreadsheet=new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        return array($data, $spreadsheet, $sheet);
    }

    /**
     * @param $sheet
     * @param $data
     * @param $spreadsheet
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function saveExcel($sheet, $data, $spreadsheet)
    {
        $sheet->setCellValue('A1', 'Utm');
        $sheet->setCellValue('B1', 'ShotCode ');
        $i=2;
        foreach($data as $key=>$val) {
            if ( $val['A'] != "Link" ) {
                $sheet->setCellValue('A' . $i++, $val["A"] . '?utm_campaign=' . $val["B"] . '&utm_source=' . $val["C"] . '&utm_medium=' . $val["D"]);
                $sheet->setCellValue('B' . ($i - 1), LinkFactory::createLink($val["A"] . '?utm_campaign=' . $val["B"] . '&utm_source=' . $val["C"] . '&utm_medium=' . $val["D"]));
            }
        }
        $addressFile='/file/' . time() . '.xlsx';
        $writer=new Xlsx($spreadsheet);
        $writer->save(public_path() . $addressFile);
        return $addressFile;
    }
}