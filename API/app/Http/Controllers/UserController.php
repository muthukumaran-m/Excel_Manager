<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'uploaded_file' => 'required|file|mimes:xls,xlsx'
        ]);
        $the_file = $request->file('uploaded_file');

        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet        = $spreadsheet->getActiveSheet();
        $row_limit    = $sheet->getHighestDataRow();
        $column_limit = $sheet->getHighestDataColumn();
        $row_range    = range(2, $row_limit);
        $column_range = range('F', $column_limit);
        $startcount = 2;
        $data = array();

        $drawings = $sheet->getDrawingCollection();
        foreach ($row_range as $key => $row) {
            $drawing = $drawings[$startcount - 2];
            $zipReader = fopen($drawing->getPath(), 'r');
            $imageContents = '';
            while (!feof($zipReader)) {
                $imageContents .= fread($zipReader, 1024);
            }
            fclose($zipReader);
            $extension = $drawing->getExtension();
            $fileName = time() . $drawing->getName() . ".$extension";

            Storage::disk('public')->put($fileName,  $imageContents);

            $data[] = [
                'name' => $sheet->getCell('A' . $row)->getValue(),
                'email_id' => $sheet->getCell('B' . $row)->getValue(),
                'mobile_number' => $sheet->getCell('C' . $row)->getValue(),
                'description' => $sheet->getCell('D' . $row)->getValue(),
                'image' => Storage::url($fileName),
            ];
            $startcount++;
        }
        // return $data;
        DB::table('users')->insert($data);
        return response()->json('Great! Data has been successfully uploaded.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = User::get();
        $data_array[] = array("Name", "Email ID", "Mobile number", "Description", "Image");
        foreach ($data as $data_item) {
            $data_array[] = array(
                'Name' => $data_item->name,
                'Email ID' => $data_item->email_id,
                'Mobile number' => $data_item->mobile_number,
                'Description' => $data_item->description,
            );
        }
        $this->ExportExcel($data_array);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($request->hasFile('image')) {

            $user->image = Storage::url(Storage::put('public', $request->file('image')));
            $user->save();
            return $user;
        } else {

            // return $request->all();
            return  $user->update(
                $request->all()
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function ExportExcel($customer_data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($customer_data);

            $images = User::get('image');
            foreach ($images as $key => $row) {
                Log::debug([$key, public_path() . "/" . $row->image]);
                $drawing = new Drawing();
                $drawing->setPath(public_path() . "/" . $row->image);
                $drawing->setHeight(20);
                $drawing->setWidth(20);
                $drawing->setOffsetY(20);
                $drawing->setCoordinates("E" . ($key + 2));
                $drawing->setWorksheet($spreadSheet->getActiveSheet());
            }
            $Excel_writer = new Xls($spreadSheet);
            header("Access-Control-Allow-Origin: *");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Customer_ExportedData.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
}
