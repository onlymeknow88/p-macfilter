<?php

namespace App\Http\Controllers;

use App\Models\DhcpList;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    public function index(Request $request)
    {


        $data = DhcpList::all();



        return view('script', compact('data'));
    }

    public function store(Request $request)
    {
        $dhcp = DhcpList::create($request->all());

        return redirect()->route('script.index');
    }

    public function script(Request $request, $id)
    {
        $data = DhcpList::find($id);

        if($data)
        {
            $script = "add-DhcpServerv4Filter -List Allow -MacAddress $data->mac_address -Description $data->computer_name".'_'."".strtoupper($data->lan_wan)."".'_'."".strtoupper($data->username)."";

            $output = shell_exec($script);
            $data->update([
                'status' => 'allow'
            ]);
        }

        return view('v_script',compact('output_script'));
    }
}
