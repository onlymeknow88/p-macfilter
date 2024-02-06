<?php

namespace App\Http\Controllers;

use App\Models\DhcpList;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ScriptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = DhcpList::query();

            return DataTables::of($query)
                ->addColumn('lan_wan', function ($item) {
                    if ($item->lan_wan == 'L') {
                        $lan_wan = 'LAN';
                    } else {
                        $lan_wan = 'WIFI';
                    }
                    return $lan_wan;
                })
                ->addColumn('update_script', function ($item) {
                    return '<div class="d-flex justify-content-start gap-2">
                    <button type="button" onclick="allowData(`' . route('script.run', $item->id) . '`)" class="btn btn-warning btn-sm">Allow MacADD</button>
                    <button type="button" onclick="blockData(`' . route('script.runBlock', $item->id) . '`)" class="btn btn-danger btn-sm">Block MacADD</button>
                    </div>';
                })
                ->addColumn('aksi', function ($item) {
                    return '
                    <div class="d-flex justify-content-start gap-2">
                    <button class="btn btn-sm btn-primary btn-icon text-white" onclick="editForm(`' . route('script.show', $item->id) . '`)"> Edit</button>
                        <button type="button" class="btn btn-sm btn-danger mr-6 px-2" title="Delete" onclick="deleteData(`' . route('script.destroy', $item->id) . '`)">
                            Hapus
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['lan_wan', 'aksi', 'update_script'])
                ->make(true);
        }

        return view('scriptMac.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mac_address' => ['required', 'string', 'max:255'],
                'computer_name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],

                'lan_wan' => ['required'],
            ],
            [
                'mac_address.required' => 'Silahkan isi Mac Address',
                'computer_name.required' => 'Silahkan isi computer name',
                'username.required' => 'Silahkan isi username',

                'lan_wan.required' => 'Silahkan isi LAN/WAN',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $dhcp = DhcpList::create($data);

        return ResponseFormatter::success([
            $dhcp
        ], 'Create Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DhcpList::find($id);
        return response()->json(['data' => $data]);
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mac_address' => ['required', 'string', 'max:255'],
                'computer_name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],

                'lan_wan' => ['required'],
            ],
            [
                'mac_address.required' => 'Silahkan isi Mac Address',
                'computer_name.required' => 'Silahkan isi computer name',
                'username.required' => 'Silahkan isi username',

                'lan_wan.required' => 'Silahkan isi koneksi type',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $dhcp = DhcpList::find($id);

        $macAddress = $dhcp->mac_address;
        $computerName = $dhcp->computer_name;
        $lanWan = strtoupper($dhcp->lan_wan);
        $username = strtoupper($dhcp->username);

        $script1 = "Remove-DhcpServerv4Filter -MacAddress '$macAddress'";

        $del = shell_exec("powershell.exe $script1");

        $dhcp->update($data);

        if ($dhcp) {
            // Add a new DHCP filter with the updated MAC address
            $newMacAddress = $data['mac_address'];
            $addScript = "Add-DhcpServerv4Filter -List Allow -MacAddress '$newMacAddress' -Description '$computerName" . "_$lanWan" . "_$username'";
            $addResult = shell_exec("powershell.exe $addScript");
        }



        return ResponseFormatter::success([
            $dhcp
        ], 'Create Success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = DhcpList::find($id);

        $macAddress = $data->mac_address;

        $script = "Remove-DhcpServerv4Filter -MacAddress '$macAddress'";

        $out = shell_exec("powershell.exe $script");

        $data->delete();

        return ResponseFormatter::success([
            null
        ], 'Deleted');
    }



    public function runScript($id)
    {
        $data = DhcpList::find($id);

        if ($data) {
            $macAddress = $data->mac_address;
            $computerName = $data->computer_name;
            $lanWan = strtoupper($data->lan_wan);
            $username = strtoupper($data->username);

            $script = "add-DhcpServerv4Filter -List Allow -MacAddress '$macAddress' -Description '$computerName" . "_$lanWan" . "_$username'";

            // Execute the PowerShell script
            $out = shell_exec("powershell.exe $script");

            $data->update([
                'status' => 'Allow'
            ]);

            // return view('v_script', compact('out'));
        }

        // return redirect()->route('script.index');
        return ResponseFormatter::success([
            $data
        ], 'Allow Success');
    }

    public function runBlock($id)
    {
        $data = DhcpList::find($id);

        if ($data) {
            $macAddress = $data->mac_address;
            $computerName = $data->computer_name;
            $lanWan = strtoupper($data->lan_wan);
            $username = strtoupper($data->username);

            $script = "add-DhcpServerv4Filter -List Deny -MacAddress '$macAddress' -Description '$computerName" . "_$lanWan" . "_$username'";

            // Execute the PowerShell script
            $out = shell_exec("powershell.exe $script");

            $data->update([
                'status' => 'Block'
            ]);
        }

        // return redirect()->route('script.index');
        return ResponseFormatter::success([
            $data
        ], 'Block Success');
    }
}
