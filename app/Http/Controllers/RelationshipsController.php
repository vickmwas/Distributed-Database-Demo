<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RelationshipsController extends Controller
{

    public $sqlsrv;
    public $mysql;
    public $pgsql;

    function __construct()
    {
        $this->sqlsrv = DB::connection('sqlsrv');
        $this->mysql = DB::connection('local_mysql');
        $this->pgsql = DB::connection('local_pgsql');
    }


    public function get_policy_type()
    {
        $policyType1 = ($this->sqlsrv->select("SELECT policy_type1.policy_name, policy_type1.id FROM policy_type1"));
        $policyType2 = ($this->pgsql->select("SELECT policy_type2.policy_name, policy_type2.id from policy_type2"));
        $policyType3 = ($this->mysql->select("SELECT policy_type3.policy_name, policy_type3.id from policy_type3"));

        $policyTypes = collect($policyType1)->merge(collect($policyType2))->merge(collect($policyType3));

        return $policyTypes;
    }

    public function get_client_policies()
    {
        //policy details, based on each policy type
        $client_policy1 = ($this->sqlsrv->select("SELECT client_policy1.* from client_policy1"));
        $client_policy2 = ($this->pgsql->select("SELECT client_policy2.* from client_policy2"));

        $client_policies = collect(array())->merge(collect($client_policy1))->merge(collect($client_policy2));

        return $client_policies;
    }


    public function get_policy_details()
    {
        //policy details, based on each policy type
        $policy_details1 = ($this->pgsql->select("SELECT policy_details1.* from policy_details1"));
        $policy_details2 = ($this->mysql->select("SELECT policy_details2.* from policy_details2"));

        $policy_details = collect($policy_details1)->merge(collect($policy_details2));

        return $policy_details;
    }

    public function get_policy_type_policy_details()
    {
        $policy_details = $this->get_policy_details();
        $policy_types = $this->get_policy_type();

        for ($index = 0; $index < count($policy_details); $index++) {
            $policyId = $policy_details[$index]->policy_id;
            for ($index2 = 0; $index2 < count($policy_types); $index2++) {
                if ($policyId == $policy_types[$index2]->id) {

                    $policy_details[$index] = collect($policy_details[$index])->merge(collect(array("policy_id" => $policy_types[$index2]->id, "policy_name" => $policy_types[$index2]->policy_name)));
                }
            }
        }

        return $policy_details;
    }


    public function get_clients_policies_relationships()
    {
        $client_policies = $this->get_client_policies();

        $policy_details = $this->get_policy_type_policy_details();

        $clients = $this->clients();

        $clientPolicyRelationships = collect(array());

        //get relationship between client policy and policy details
        for ($index = 0; $index < count($client_policies); $index++) {
            $newClientPolicy = collect(array());
            $currentClientPolicy = collect($client_policies[$index]);
            for ($j = 0; $j < count($policy_details); $j++) {
                if ((int)$currentClientPolicy["policy_detail_id"] == (int)$policy_details[$j]["id"]) {
                    $newClientPolicy = $newClientPolicy->merge(collect($client_policies[$index])->union($policy_details[$j]));
                    $clientPolicyRelationships->push($newClientPolicy);
                    break;
                }

            }

        }


        //get relationship between
        for ($j = 0; $j < count($clientPolicyRelationships); $j++) {
            for ($k = 0; $k < count($clients); $k++) {
                if ((int)$clientPolicyRelationships[$j]["client_id"] == (int)$clients[$k]["id"]) {
//                    echo $currentClientPolicy["client_id"] . "  ? " . $clients[$j]["id"] . "\n";
                    $clientPolicyRelationships[$j] = $clientPolicyRelationships[$j]->union(collect($clients[$k]));
                    break;
                }
            }
        }

        return $clientPolicyRelationships;
    }


    public function clients()
    {

        $clients1 = ($this->sqlsrv->select("SELECT clients1.* from clients1"));

        $clients2 = ($this->mysql->select("SELECT clients2.* from clients2"));

        $clients = collect(array());
        for ($index = 0; $index < count($clients1); $index++) {

            $clients->push(collect($clients1[$index])->merge(collect($clients2[$index])));
        }
        return $clients;
    }


    public function get_claims()
    {
        $client_policies = $this->get_clients_policies_relationships();

        $claims = collect(array());

        $claims = $claims->merge(collect($this->sqlsrv->select("SELECT claims1.* from claims1")));
        $claims = $claims->merge(collect($this->sqlsrv->select("SELECT claims2.* from claims2")));
        $claims = $claims->merge(collect($this->sqlsrv->select("SELECT claims3.* from claims3")));
        $claims = $claims->merge(collect($this->mysql->select("SELECT claims4.* from claims4")));
        $claims = $claims->merge(collect($this->mysql->select("SELECT claims5.* from claims5")));
        $claims = $claims->merge(collect($this->mysql->select("SELECT claims6.* from claims6")));
        $claims = $claims->merge(collect($this->pgsql->select("SELECT claims7.* from claims7")));
        $claims = $claims->merge(collect($this->pgsql->select("SELECT claims8.* from claims8")));

        for ($index = 0; $index < count($claims); $index++) {
            for ($j = 0; $j < count($client_policies); $j++) {
                if (collect($claims[$index])["client_policy_id"] == collect($client_policies[$j])["id"]) {
                    $claims[$index] = collect($claims[$index])->union($client_policies[$j]);
                    break;
                }
            }
        }
        return $claims;
    }

    public function get_claims_in_range(){
        return $this->sqlsrv->select("SELECT claims1.* FROM claims1");
    }


    public function get_payments()
    {
        $payments = "SELECT * FROM payments";
        return $this->sqlsrv->select($payments);

    }

}
