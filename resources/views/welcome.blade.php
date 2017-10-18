<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet" type="text/css">
        <link href="/css/minibank.css" rel="stylesheet" type="text/css">

        <title>MiniBank</title>
    </head>

    <body>
        <nav class="navbar navbar-default bd-navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand">
                        <p>MiniBank</p>
                    </a>
                </div>
            </div>
        </nav>

        <div class="container bg-light border border-white">
            <div class="row">
                <div class="col-md-3 p3 p-md-5 border border-white">
                    <h4>
                        Clients 
                        <span class="glyphicon glyphicon-plus"></span>
                    </h4>
                    
                    <div>Marcus</div>
                    <div>Vinicius</div>
                </div>

                <div class="col-md-4 p3 p-md-5 border border-white">
                    <h4>
                        Accounts
                        <span class="glyphicon glyphicon-plus"></span>
                    </h4>

                    <div>
                        Conta 1
                        <span class="button-container">
                            <a class="btn btn-xs btn-bd-purple">Deposite</a>
                            <a class="btn btn-xs btn-bd-purple">Withdraw</a>
                        </span>
                    </div>
                    <div>
                        Conta 2
                        <span class="button-container">
                            <a class="btn btn-xs btn-bd-purple">Deposite</a>
                            <a class="btn btn-xs btn-bd-purple">Withdraw</a>
                        </span>
                    </div>
                </div>

                <div class="col-md-5 p3 p-md-5 border border-white">
                    <h4>Events</h4>

                     <div>
                        Event 1
                        <span class="button-container">
                            <a class="btn btn-xs btn-bd-purple">Replay</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="/js/app.js" type="text/javascript"></script>
    </body>
</html>
