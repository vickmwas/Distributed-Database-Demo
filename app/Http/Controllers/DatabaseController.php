<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;

class DatabaseController extends RelationshipsController
{


    public function loadData()
    {
        $data = array(
            array(
                "narrative" => "Get a list of all clients, their details, and all the policies that they have",
                "query" => "SELECT clients.* ,policy_type.policy_name
                            FROM clients
                            INNER JOIN client_policy ON clients.id=client_policy.client_id
                            INNER JOIN policy_details ON client_policy.policy_detail_id=policy_details.id
                            INNER JOIN policy_type ON policy_details.policy_id = policy_type.id;",
                "source" => "query1"
            ),
            array(
                "narrative" => "Get a list of the approved claims between 2016 and 2018, and the contact details of the clients who requested the claims.",
                "query" => "SELECT claims.*
                            FROM claims
                            WHERE claims.date_approved>=DATE('2016-01-01') AND claims.date_approved<=DATE('2018-12-12')
                            AND claims.status = 'APPROVED';",
                "source" => "query2"
            ),
            array(
                "narrative" => "Get a list of all car insurance claims",
                "query" => "SELECT claims.*,client_policy.details,policy_details.policy_id,policy_details.monthly_amount,policy_type.policy_name
                            FROM claims
                            INNER JOIN client_policy ON claims.client_policy_id=client_policy.id
                            INNER JOIN policy_details ON client_policy.policy_detail_id=policy_details.id
                            INNER JOIN policy_type ON policy_details.policy_id = policy_type.id
                            WHERE policy_type.policy_name='CAR INSURANCE' ;",
                "source" => "query3"
            ),
            array(
                "narrative" => "Get a list of all claims worth more than 50,000",
                "query" => "SELECT claims.amount_requested,policy_type.policy_name,clients.name, clients.phone
                            FROM claims
                            INNER JOIN client_policy ON client_policy.id = claims.client_policy_id
                            INNER JOIN clients ON clients.id = client_policy.client_id
                            INNER JOIN policy_details ON client_policy.policy_detail_id=policy_details.id
                            INNER JOIN policy_type ON policy_details.policy_id = policy_type.id
                            WHERE claims.amount_requested >50000;",
                "source" => "query4"
            ),
            array(
                "narrative" => "get a list of all client details with a CAR insurance, with GOLD package",
                "query" => "SELECT  clients.*,client_policy.details,policy_type.policy_name,policy_details.rank
                            FROM clients
                            INNER JOIN client_policy ON clients.id = client_policy.client_id
                            INNER JOIN policy_details ON client_policy.policy_detail_id=policy_details.id
                            INNER JOIN policy_type ON policy_details.policy_id = policy_type.id
                            WHERE policy_type.policy_name='CAR INSURANCE' AND policy_details.rank=GOLD;",
                "source" => "query5"
            ),
            array(
                "narrative" => "get a list of all claims made by people who made payments via MPesa",
                "query" => "SELECT claims.incident_details,payments.payment_type,clients.name,clients.phone
                            FROM claims
                            INNER JOIN client_policy ON claims.client_policy_id=client_policy.id
                            RIGHT JOIN clients ON client_policy.client_id=clients.id
                            INNER JOIN payments ON client_policy.id=payments.client_policy_id
                            WHERE payments.payment_type='MPESA';
",
                "source" => "query6"
            ),
            array(
                "narrative" => "Get a list of all claims whose amount requested was between 50,000 and 100,000",
                "query" => "SELECT *
                            FROM claims
                            WHERE amount_requested BETWEEN 50000 AND 100000;",
                "source" => "query7"
            ), array(
                "narrative" => "Get a list of  payments made for CAR INSURANCE policy",
                "query" => "SELECT clients.name,payments.payment_date, policy_details.monthly_amount ,policy_type.policy_name,policy_details.rank
                            FROM payments 
                            INNER JOIN client_policy ON payments.client_policy_id=client_policy.id
                            INNER JOIN clients ON clients.id = client_policy.client_id
                            INNER JOIN policy_details ON client_policy.policy_detail_id=policy_details.id
                            INNER JOIN policy_type ON policy_details.policy_id = policy_type.id
                            WHERE policy_type.policy_name='CAR INSURANCE'",
                "source" => "query8"
            )

        );

        return $data;

    }

    public function query1()
    {

        $data = $this->get_clients_policies_relationships()->map(function ($client) {
            return collect($client)->only('name', 'phone', 'email', 'gender', 'national_id', 'dob', 'policy_name');
        });
        $queries = $this->loadData();
        $rawQuery = $queries[0]["query"];

//        return get_defined_vars();
        return view('welcome', get_defined_vars());

    }


    public function query2()
    {
        $data = collect($this->get_claims_in_range())->map(function ($claim) {
            return collect($claim)->only('id', 'client_policy_id', 'amount_requested', 'amount_paid', 'incident_details',
                'date_of_incident', 'status', 'date_requested', 'date_approved', 'details', 'monthly_amount', 'policy_name');
        });

        $queries = $this->loadData();
        $rawQuery = $queries[1]["query"];

//        return get_defined_vars();
        return view('welcome', get_defined_vars());

    }

    public function query3()
    {
        $data =  $this->get_claims()->map(function ($claim) {
            return collect($claim)->only('id', 'client_policy_id', 'amount_requested', 'amount_paid', 'incident_details',
                'date_of_incident', 'status', 'date_requested', 'date_approved', 'details', 'monthly_amount', 'policy_name');
        });

        $queries = $this->loadData();
        $rawQuery = $queries[2]["query"];

//        return get_defined_vars();
        return view('welcome', get_defined_vars());
    }

    public function query4()
    {
        $clients = $this->get_claims();
        $client_over_50K = collect(array());
        foreach ($clients as $client) {

            if ($client['amount_requested'] > 50000) {
                $client_over_50K->push($client);
            }
        }
        $data =  $client_over_50K->map(function ($clients) {
            return collect($clients)->only('name', 'phone', 'policy_name', 'amount_requested');
        });

        $queries = $this->loadData();
        $rawQuery = $queries[3]["query"];

//        return get_defined_vars();
        return view('welcome', get_defined_vars());

    }

    public function query5()
    {
        $client_details = $this->get_claims();
        $clients_bronze_car = collect(array());
        foreach ($client_details as $client_detail) {

            if ($client_detail['policy_name'] == "CAR INSURANCE" && $client_detail['rank'] == "GOLD") {
                $clients_bronze_car->push($client_detail);
            }
        }
        $data = $clients_bronze_car->map(function ($clients) {
            return collect($clients)->only('name', 'phone', 'email', 'gender', 'national_id', 'dob', 'details', 'policy_name', 'rank');
        });
        $queries = $this->loadData();
        $rawQuery = $queries[4]["query"];

//        return get_defined_vars();
        return view('welcome', get_defined_vars());

    }


    public function query6()
    {
        $claims = $this->get_claims();
        $payments = $this->get_payments();
        $claim_payments_mpesa = collect(array());
        for ($index = 0; $index < count($payments); $index++) {
            for ($index2 = 0; $index2 < count($claims); $index2++) {
                if (collect($claims[$index2])['client_policy_id'] == collect($payments[$index])['client_policy_id']
                    && collect($payments[$index])['payment_type'] == "MPESA") {
                    $claim_payments_mpesa->push(collect($claims[$index2])->union($payments[$index]));
                    break;
                }
            }
        }

        $data =  $claim_payments_mpesa->map(function ($clients) {
            return collect($clients)->only('name', 'phone', 'payment_type', 'incident_details');
        });

        $queries = $this->loadData();
        $rawQuery = $queries[5]["query"];

//        return get_defined_vars();
        return view('welcome', get_defined_vars());
    }

    public function query7()
    {
        $clients = $this->get_claims();
        $client_over_50K_100k = collect(array());
        foreach ($clients as $client) {

            if ($client['amount_requested'] >= 50000 && $client['amount_requested'] <= 100000) {
                $client_over_50K_100k->push($client);
            }
        }
        $data =  $client_over_50K_100k->map(function ($clients) {
            return collect($clients)->only('id', 'client_policy_id', 'amount_requested', 'amount_paid', 'incident_details',
                'date_of_incident', 'status', 'date_requested', 'date_approved');
        });


        $queries = $this->loadData();
        $rawQuery = $queries[6]["query"];
//        return get_defined_vars();
        return view('welcome', get_defined_vars());
    }


    public function query8()
    {
        $claims = $this->get_claims();
        $payments = $this->get_payments();
        $payments_car_insurance = collect(array());
        for ($index = 0; $index < count($payments); $index++) {
            for ($index2 = 0; $index2 < count($claims); $index2++) {
                if (collect($claims[$index2])['client_policy_id'] == collect($payments[$index])['client_policy_id']
                    && collect($claims[$index2])['policy_name'] == "CAR INSURANCE") {
                    $payments_car_insurance->push(collect($claims[$index2])->union($payments[$index]));
                    break;
                }
            }
        }

        $data = $payments_car_insurance->map(function ($payment) {
            return collect($payment)->only('name', 'payment_date', 'monthly_amount', 'policy_name', 'rank');
        });
        $queries = $this->loadData();
        $rawQuery = $queries[7]["query"];

//        return get_defined_vars();
        return view('welcome', get_defined_vars());

    }
}
