<!doctype html>

<html lang="{{ app()->getLocale() }}" ng-app="minibank">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet" type="text/css">
        <link href="/css/minibank.css" rel="stylesheet" type="text/css">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Scripts -->
        <script src="/node_modules/angular/angular.min.js" type="text/javascript"></script>

        <title>{{ config('app.name') }}</title>
    </head>

    <body ng-controller="controller" ng-init="getClients()">
        <nav class="navbar navbar-default bd-navbar" id="app">
            <div class="container-fluid" style="padding-left: 45%">
                <div class="navbar-header">
                    <a class="navbar-brand">
                        <p>MiniBank</p>
                    </a>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="row">
                <div class="col-md-3 p-md-5 bg-light" ng-class="{'temp-height': showSpinnerClients}">
                    <h4>
                        Clients
                        <span ng-if="clients.length > 0" ng-cloak>([[ clients.length ]])</span>
                        <span class="glyphicon glyphicon-plus pointer" data-toggle="modal" data-target="#add-client"></span>
                    </h4>

                    <span us-spinner spinner-on="showSpinnerClients"></span>
                    
                    <div ng-repeat="client in clients" class="option pointer" ng-click="selectClient(client.id)" ng-class="{'option-selected': client.id == selected_client_id}" ng-cloak>[[client.name | capitalize]]</div>

                    <div ng-if="showNoClients" class="messages" ng-cloak>There are no clients</div>
                </div>

                <div class="col-md-5 p-md-5 border-left border-white bg-light" ng-class="{'temp-height': showSpinnerAccounts}">
                    <h4>
                        Accounts
                        <span ng-if="accounts.length > 0"  ng-cloak>([[ accounts.length ]])</span>
                        <span class="glyphicon glyphicon-plus pointer" data-toggle="modal" data-target="#create-account" ng-if="selected_client_id != undefined" ng-cloak></span>
                    </h4>

                    <input type="email" class="form-control email" placeholder="CFO email" ng-model="cfo_email" maxlength="190" ng-if="selected_client_id != undefined" ng-cloak>

                    <span us-spinner spinner-on="showSpinnerAccounts"></span>

                    <div ng-repeat="account in accounts" class="option pointer account" ng-click="selectAccount(account.id)" ng-class="{'option-selected': account.id == selected_account_id}" ng-cloak>
                        Balance: 
                        <strong ng-if="!(showSpinnerReplay && selected_account_index == $index)">[[account.balance]]</strong>

                        <span us-spinner="spinnerBalanceReplay" spinner-on="showSpinnerReplay && selected_account_index == $index"></span>

                        <span class="float-right">
                            <a class="btn btn-xs btn-bd-purple" data-toggle="modal" data-target="#deposit">Deposit</a>
                            <a class="btn btn-xs btn-bd-purple" data-toggle="modal" data-target="#withdraw">Withdraw</a>
                        </span>
                    </div>

                    <div ng-if="showNoAccounts" class="messages" ng-cloak>This client does not have an account</div>
                    <div ng-if="clients != undefined && clients.length > 0 && selected_client_id == undefined" class="messages" ng-cloak>Select a client</div>
                </div>

                <div class="col-md-4 p-md-5 border-left border-white bg-light" ng-class="{'temp-height': showSpinnerEvents}">
                    <h4>
                        Events
                        <span ng-if="events.length > 0"  ng-cloak>([[ events.length ]])</span>
                    </h4>
                    
                    <span us-spinner spinner-on="showSpinnerEvents"></span>

                    <div ng-repeat="event in events" class="event" ng-class="{'option-selected': event.id == selected_event_id}" ng-cloak>
                        <strong>[[event.type | eventType]]</strong><span ng-if="event.type != 0">: </span> 
                        <span ng-if="event.type != 0" ng-class="{'green_message': event.type == 1, 'red_message': event.type == 2}">[[event | eventData]]</span>

                        <span class="float-right">
                            <a class="btn btn-xs btn-bd-purple" ng-click="replay(selected_account_id, $index + 1)" ng-if="!(showSpinnerReplay && clicked_index == $index) && accounts[selected_account_index].events_applied - 1 != $index">Replay</a>
                            <span us-spinner="spinnerButtonReplay" spinner-on="showSpinnerReplay && clicked_index == $index"></span>
                        </span>
                    </div>

                    <div ng-if="accounts != undefined && accounts.length > 0 && selected_account_id == undefined" class="messages" ng-cloak>Select an account</div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <div class="modal fade bs-example-modal-sm" id="add-client" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add Client</h4>
                    </div>
                    
                    <div class="modal-body">
                        <input type="text" class="form-control" placeholder="Name" ng-model="client_name" maxlength="191">                
                    </div>
              
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-modal-purple" ng-click="addClient()" ng-disabled="client_name == undefined || client_name == ''">Add</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bs-example-modal-sm" id="create-account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create Account</h4>
                    </div>
                    
                    <div class="modal-body">
                        Are you sure you want to create a new account?
                    </div>
              
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                        <button type="button" class="btn btn-modal-purple" ng-click="createAccount(selected_client_id)">Yes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bs-example-modal-sm" id="withdraw" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Withdraw</h4>
                    </div>
                    
                    <div class="modal-body">
                        <input type="text" class="form-control" placeholder="Amount (comma not allowed)" ng-model="withdraw_amount" floating-number-only maxlength="6">
                        <span ng-if="exception_message != undefined" class="red_message">[[exception_message]]</span>               
                    </div>
              
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-modal-purple" ng-disabled="withdraw_amount == undefined || withdraw_amount == ''" ng-click="withdrawMoney(selected_account_id, withdraw_amount)">Withdraw</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bs-example-modal-sm" id="deposit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Deposit</h4>
                    </div>
                    
                    <div class="modal-body">
                        <input type="text" class="form-control" placeholder="Amount (comma not allowed)" ng-model="deposit_amount" floating-number-only maxlength="6">                
                    </div>
              
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-modal-purple" ng-disabled="deposit_amount == undefined || deposit_amount == ''" ng-click="depositMoney(selected_account_id, deposit_amount)">Deposit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="/js/app.js" type="text/javascript"></script>
        <script src="/node_modules/angular-spinner/dist/angular-spinner.min.js"></script>
        <script src="/node_modules/lodash/lodash.min.js"></script>
        <script src="/js/minibank.js" type="text/javascript"></script>
    </body>
</html>
