<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{

    public $sqlsrv;
    public $mysql;
    public $pgsql;

    function __construct()
    {
        $this->sqlsrv = DB::connection('sqlsrv');
        $this->mysql = DB::connection('mysql');
        $this->pgsql = DB::connection('pgsql');
    }

    public function index()
    {
        $query1 = "SELECT claims1.* FROM claims1";
        $query2 = "";

        $users = $this->sqlsrv->select($query1);
        echo json_encode($users);
    }

    public function query2()
    {
        $clients1 = ($this->sqlsrv->select("SELECT clients1.* from clients1"));

        $clients2 = ($this->mysql->select("SELECT clients2.* from clients2"));

        $clients = collect(array());
        for ($index = 0; $index < count($clients1); $index++) {

            $clients->push(collect($clients1[$index])->merge(collect($clients2[$index])));
        }


        $policyType1 = ($this->sqlsrv->select("SELECT policy_type1.policy_name, policy_type1.id FROM policy_type1"));
        $policyType2 = ($this->pgsql->select("SELECT policy_type2.policy_name, policy_type2.id from policy_type2"));
        $policyType3 = ($this->mysql->select("SELECT policy_type3.policy_name, policy_type3.id from policy_type3"));

        $policyTypes = collect($policyType1)->merge(collect($policyType2))->merge(collect($policyType3));



        echo json_encode($policyTypes);





    }


}
