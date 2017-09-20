<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Make ChatBot</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            var devAccessToken = "e14e1c6b19b74fac95c1bdf52689f6b7",
                baseUrl = "https://api.api.ai/v1/",
                entitiesArr = [];


            $( function() {
                // $( "#loading" ).on("load", function() {         
                //     alert("wahahaha");
                // });  


                $("#newQuestion").on("click", function(event) {
                    $.post( "ajax/intents/addIntentsForm.php", function( data ){
                        if( data.length > 0 ) {           
                            $( "#botEntitiesForm" ).html( data );
                        }               
                    });     
                });
            });

            function initialize() {
                const processSynonyms = function (data) {
                    for (var dataCtr = 0; dataCtr < data.length; dataCtr++) {                            
                        $.ajax({
                            type: "GET",
                            url: baseUrl + "entities/" + data[dataCtr].id,                        
                            headers: {
                                "Authorization": "Bearer " + devAccessToken
                            },
                            success: function(entityData) {
                                entitiesArr.push(entityData);
                            },
                            error: function(entityData) {
                                alert("Something when wrong. Please try again later.\n");
                            }
                        });
                    }
                }

                getEntities(processSynonyms);
            }

            function getEntities(cbFunc) {
                $.ajax({
                    type: "GET",
                    url: baseUrl + "entities",
                    headers: {
                        "Authorization": "Bearer " + devAccessToken
                    },
                    success: function(data) {
                        cbFunc(data);                     
                    },
                    error: function() {
                        respond(messageInternalError);
                    }
                });
            }



            function loadIntents() {
                $.ajax({
                    type: "GET",
                    url: baseUrl + "intents",
                    headers: {
                        "Authorization": "Bearer " + devAccessToken
                    },

                    success: function(data) {
                        var entTable = $("#resultDivTable");
                        var tr = [];
                        var actionString = "";

                        for (var i = 0; i < data.length; i++) {
                            var row = $('<tr></tr>').appendTo(entTable);    
                            actionString =  "<input type='hidden' value='" + data[i].id + "' readonly />" +
                                            "<button type='button' class='btn btn-info btn-xs' title='View Question Details' onclick='getSpecifiedIntent(this)' ><span class='glyphicon glyphicon-folder-open'></span></button>  " + 
                                            "<button type='button' class='btn btn-info btn-xs' title='Delete Keyword' onclick='deleteIntents(this)' ><span class='glyphicon glyphicon-remove'></span></button>";    
                            $('<td></td>').text(data[i].name).appendTo(row); 
                            $('<td></td>').html(actionString).css({"text-align":"right", "padding-right":"5%"}).appendTo(row);
                 
                        }
                        entTable.appendTo("#resultDiv");
                        $("#resultDivTable tr:first-child").remove();
                    },
                    error: function() {
                        respond(messageInternalError);
                    }
                });
            }        





            function phraseChecker(phrase) {
                for(var entityDataCtr = 0; entityDataCtr < entitiesArr.length; entityDataCtr++) {
                    for(var entriesCtr = 0; entriesCtr < entitiesArr[entityDataCtr].entries.length; entriesCtr++) {
                        for(var synonymsCtr = 0; synonymsCtr < entitiesArr[entityDataCtr].entries[entriesCtr].synonyms.length; synonymsCtr++) {
                            var index = phrase.indexOf(entitiesArr[entityDataCtr].entries[entriesCtr].synonyms[synonymsCtr]);
                            if(index != -1) {
                                var replaceString = "|@" + entitiesArr[entityDataCtr].name + ":" + entitiesArr[entityDataCtr].name + ":" + entitiesArr[entityDataCtr].entries[entriesCtr].synonyms[synonymsCtr] + "|";
                                phrase = phrase.replace(entitiesArr[entityDataCtr].entries[entriesCtr].synonyms[synonymsCtr], replaceString);
                            }
                        }    
                    }                    
                }                 
                return phrase;
            }

            function postIntents() {
                var intentName = $("input[name=intentName]").val(),
                    botResponse = $("input[name='response']").val(),
                    userSaysArray = [],
                    reponsesArray = [],
                    parametersArray = [],
                    templateArray = [],

                    dataArray = [];

                $("input[name='userSays[]']").each(function() {  
                    var template = phraseChecker($(this).val());
                    var strArr = template.split("|");
                    for(var templatePartCtr = 0; templatePartCtr < strArr.length; templatePartCtr++) {
                        if(strArr[templatePartCtr].indexOf("@") == -1) {
                            var dataObj = {'text': strArr[templatePartCtr]};                            
                        }
                        else {
                            var strArr2 = strArr[templatePartCtr].split(":");
                            var dataObj = {'text': strArr2[2], 'alias': strArr2[1], meta: strArr2[0]};
                            var parValVar = "\$" + strArr2[1];
                            var parameterObj = {'dataType': strArr2[0], 'name':  strArr2[1], 'value': parValVar};
                            parametersArray.push(dataObj);
                            strArr2.pop();
                            strArr[templatePartCtr] = strArr2.join(":");
                        }
                        dataArray.push(dataObj);                        
                    }
                    var newTemplate = strArr.join("");
                    templateArray.push(newTemplate);
                });   

                var parametersArray = parametersArray.filter(function(elem, index, self) {
                    return index == self.indexOf(elem);
                });
                var templateArray = templateArray.filter(function(elem, index, self) {
                    return index == self.indexOf(elem);
                });
                var dataArray = dataArray.filter(function(elem, index, self) {
                    return index == self.indexOf(elem);
                });


                var userAskObj = {'data': dataArray, 'isTemplate': false, 'count': 0};
                userSaysArray.push(userAskObj);

                var responsesObj = {'resetContext': false, 'action': intentName, 'affectedContext': [], 'parameters':parametersArray, 'speech': botResponse};
                reponsesArray.push(responsesObj);

                $.ajax({
                    type: "POST",
                    url: baseUrl + "intents",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + devAccessToken
                    },
                    data: JSON.stringify({name: intentName, auto: true, context: [], templates: [], userSays: userSaysArray, responses: reponsesArray, priority: 500000}),
                    success: function(data) {
                        alert("Adding intents successful");
                    },
                    error: function() {
                        alert("Adding intents failed. Please try again.");

                    }
                });

            }

            function getIntent() {
                $.ajax({
                    type: "GET",
                    url: baseUrl + "intents",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + devAccessToken
                    },

                    success: function(data) {
                        var debugJSON = JSON.stringify(data);
                        $("#resultDiv").html(debugJSON);
                    },
                    error: function() {
                        respond(messageInternalError);
                    }
                });
            }

            function getSpecifiedIntent(e) {
                $(e).closest('tr').find("input").each(function() {
                    var id = this.value;
                    $.ajax({
                        type: "GET",
                        url: baseUrl + "intents/" + id,                        
                        headers: {
                            "Authorization": "Bearer " + devAccessToken
                        },
                        success: function(data) {

                            $("#editDiv").html(
                                '<table class="table table-hover" id="editDivTable" style="width: 50%; margin: 0 auto;" >' +
                                    '<tr>' +
                                        '<th>User Ask</th>' +
                                    '</tr>' +  
                                    '<tbody id="editDivTableBody"></tbody>' +
                                '</table>'
                            );

                            $("#editDiv2").html(
                                '<table class="table table-hover" id="editDivTable2" style="width: 50%; margin: 0 auto;" >' +
                                    '<tr>' +
                                        '<th>Response</th>' +
                                    '</tr>' +    
                                '</table>'
                            );


                            var entTable = $("#editDivTable");
                            var res = "";
                            var dataID = data.id;
                            var usertext = "";
                            for (var h = 0; h < data.userSays.length; h++) {
                                for (var i = 0; i < data.userSays[h].data.length; i++) {
                                    usertext += data.userSays[h].data[i].text
                                }
                                // var actionString =  "<input type='hidden' value='" + data.userSays[h].data[i].value + "' readonly />" +
                                //                     "<button type='button' class='btn btn-info btn-xs' title='Delete Keyword' onclick='deleteEntityData(this, \"" + data.id + "\")' ><span class='glyphicon glyphicon-remove'></span></button>";
                                var row = $('<tr></tr>').appendTo(entTable);    
                                $('<td class="entityName"></td>').text(usertext).appendTo(row);
                                $('<td></td>').html("").appendTo(row);
                            }
                            entTable.appendTo("#editDiv");
                            var entTable = $("#editDivTable2");
                            var res = "";
                            for (var h = 0; h < data.responses.length; h++) {
                                for (var i = 0; i < data.responses[h].messages.length; i++) {
                                    var actionString =  "<input type='hidden' value='" + data.responses[h].messages[i].value + "' readonly />" +
                                                        "<button type='button' class='btn btn-info btn-xs' title='Delete Keyword' onclick='deleteEntityData(this, \"" + data.id + "\")' ><span class='glyphicon glyphicon-remove'></span></button>";
                                    var row = $('<tr></tr>').appendTo(entTable);    
                                    $('<td class="entityName"></td>').text(data.responses[h].messages[i].speech).appendTo(row); 
                                    
                                    $('<td></td>').html("").appendTo(row);
                                }
                            }
                            entTable.appendTo("#editDiv");
                            $('#editmyModal').modal('show');
                        },
                        error: function(data) {
                            alert("Something when wrong. Please try again later.\n" + JSON.stringify(data) + "\n" + id);                        
                        }
                    });

                    
                });
            }

            function deleteIntents(e) {
                $(e).closest('tr').find("input").each(function() {
                    var id = this.value;

                    $.ajax({                        
                        url: "ajax/intents/deleteIntent.php?id=" + id,                        

                        success: function(data) {
                            alert("Deleting questions successful");
                            $(e).closest('tr').remove();
                        },
                        error: function(data) {
                            alert("Something when wrong. Please try again later.\n" + JSON.stringify(data) + "\n" + id);                        
                        }
                    });                    
                });               
            }
           
        </script>
    </head>
    <body>
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>                        
                    </button>
                    <a class="navbar-brand" href="#">Make - ChatBot</a>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav">
                        <li><a href="index.php">Home</a></li>      
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Train Me <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="entities.php">Keywords</a></li>
                                <li><a href="intents.php">Questions</a></li>
                            </ul>
                        </li>    
                    </ul>      
                </div>
            </div>
        </nav>      
        <div class="container">           
            <div id="testdiv"></div>
            <h3>Questions <button type="button" class="btn btn-info btn-xs" title="Add new Questions" data-toggle="modal" data-target="#myModal" id="newQuestion" ><span class="glyphicon glyphicon-plus"></span></button></h3> 
            <hr/>
            <div id="resultDiv" >
                <table class="table table-hover" id="resultDivTable" style="width: 50%; margin: 0 auto;" >
                    <tr><td><img id="loading" src='images/loadingcircle2.gif' /></td></tr>    
                </table>
            </div>
        </div>
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add new Question</h4>
                    </div>
                    <div class="modal-body">
                        <form id="botEntitiesForm"></form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="postIntents()">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="editmyModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">View Keyword</h4>
                    </div>
                    <div class="modal-body">
                        <div id="editDiv"></div>
                        <div id="editDiv2"></div>                        
                        <form id="boteditEntitiesForm"></form>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal" onclick="editEntities()">Save</button> -->
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script type="text/javascript">
    loadIntents();
    initialize();
</script>
