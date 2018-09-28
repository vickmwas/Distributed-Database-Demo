<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/bootstrap-grid.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/bootstrap-reboot.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;

        }

        .full-height {
            height: 100vh;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            padding: 3%;
        }

        .title {
            font-size: 30px;
            text-align: center;
        }

        .data-separator {
            margin: 20px;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">

    {{--<div class="top-right links">--}}
    {{--<a href="#">Home</a>--}}
    {{--<a href="#">Login</a>--}}
    {{--<a href="#">Register</a>--}}
    {{--</div>--}}

    <div class="content">
        <h3 class="title"><b>Insurance</b> Sample Distributed Database</h3>
        Select a query from below
        <div class="row">
            <div class="list-group col-sm-4">

                @foreach($queries as $query)
                    <a href="{{url($query['source'])}}"
                       class="list-group-item list-group-item-action">{{$query["narrative"]}}</a>
                @endforeach
            </div>
            <div class="col-sm-8">
                <h3 class="data-separator">Unfragmented Query</h3>
                <h5><code>{{$rawQuery}}</code></h5>


                <h3 class="data-separator">Data</h3>


                <table class="table table-dark">
                    <thead>
                    <tr>
                        @foreach(array_keys(collect($data[0])->toArray()) as $header)
                            <th scope="col">{{$header}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $row)
                        <tr>
                            @foreach($row as $rowKey => $rowData)
                                <th scope="row">{{$row[$rowKey]}}</th>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>


            </div>
        </div>
    </div>
</div>
</body>

</html>
