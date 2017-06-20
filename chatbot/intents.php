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
            var devAccessToken = "76c59f2365aa4cae94cf259635c87dfe",
                baseUrl = "https://api.api.ai/v1/",
                entitiesArr = [],
                templatesArr = [],
                templateStr = "",
                userAskArr = [],
                dataArray = [];

            $(document).ready(function() {
                $("#newQuestion").on("click", function(event) {
                    $.post( "ajax/intents/addIntentsForm.php", function( data ){
                        if( data.length > 0 ) {           
                            $( "#botEntitiesForm" ).html( data );
                        }               
                    });     
                });
            });

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

            function phraseChecker(phrase, callback) {
                // var phrase = "I need a short and long bond paper";
                $.ajax({
                    type: "GET",
                    url: baseUrl + "entities",
                    headers: {
                        "Authorization": "Bearer " + devAccessToken
                    },

                    success: function(data) {
                        // $("#testdiv").html(data[2].preview);
                        // var templateStr = "";
                        var templateStr2 = "asdas";
                        var dataCount = data.length;
                        for (var i = 0; i < data.length; i++) {
                            
                            var xhr = $.ajax({
                                type: "GET",
                                url: baseUrl + "entities/" + data[i].id,                        
                                headers: {
                                    "Authorization": "Bearer " + devAccessToken
                                },
                                success: function(entityData) {
                                    var entriesCount = entityData.entries.length;

                                    for(var j = 0; j < entityData.entries.length; j++) {
                                        var synonymsCount = entityData.entries[j].synonyms.length;
                                        for(var k = 0; k < entityData.entries[j].synonyms.length; k++) {
                                            index = phrase.indexOf(entityData.entries[j].synonyms[k]);
                                            var res = "";
                                            if(index != -1) {
                                                // $("#testdiv").append(index + " " + entityData.entries[j].synonyms[k] + "<br><br>");
                                                var replaceString = "|@" + entityData.name + ":" + entityData.name + ":" + entityData.entries[j].synonyms[k] + "|";

                                                phrase = phrase.replace(entityData.entries[j].synonyms[k], replaceString);
                                                storetemplateStr(phrase, callback); 
                                                templateStr2 = phrase;
                                            }
                                             // if( (i === (dataCount-1)) && (j === (entriesCount-1 )) && (k === (synonymsCount-1)) ){
                                                       
                                             //    }


                                        }
                                    }  
                                     
                                    // callback();
                                },
                                error: function(entityData) {
                                    alert("Something when wrong. Please try again later.\n" + JSON.stringify(data) + "\n" + id);
                                }
                            });
                            // alert(JSON.stringify(xhr));
                        }
                        // alert(templateStr2);
                    },
                    error: function() {
                        respond(messageInternalError);
                    }
                });     
                          
            }

            function storetemplateStr(phrase, callback) {
                templateStr = phrase;
                callback(templateStr);
                // var strArr = templateStr.split("|");
                // for(var j = 0; j < strArr.length; j++) {
                //     if(strArr[j].indexOf("@") == -1) {
                //         var dataObj = {'text': strArr[j]};                            
                //     }
                //     else {
                //         var strArr2 = strArr[j].split(":");
                //         var dataObj = {'text': strArr2[2], 'alias': strArr2[1], meta: strArr2[0]};
                //     }
                //     dataArray.push(dataObj);
                // }



// $("#testdiv").append(JSON.stringify(dataArray) + " 1234<br><br>");
                // $("#testdiv").append(templateStr + "<br><br>");
                // alert(templateStr);
            }

            function testcurlGet(){
                $.ajax({                        
                        url: "ajax/intents/getIntent.php" ,                        

                        success: function(data) {
                            $("#testdiv").append(JSON.stringify(data) + " 1234<br><br>");
                        },
                        error: function(data) {
                            alert("Something when wrong. Please try again later.\n" + JSON.stringify(data) + "\n" + id);                        
                        }
                    });   
            }

            function postIntents() {
                var intentName = $("input[name=intentName]").val(),
                    botResponse = $("input[name='response']").val(),
                    entityArray = [],
                    userSaysArray = [],
                    reponsesArray = [],
                    dataArray = [];

                $("input[name='userSays[]']").each(function() {                    
                        phraseChecker($(this).val(), function(data) {
                            alert(data);
                            // var strArr = templateStr.split("|");
                            // for(var j = 0; j < strArr.length; j++) {
                            //     if(strArr[j].indexOf("@") == -1) {
                            //         var dataObj = {'text': strArr[j]};                            
                            //     }
                            //     else {
                            //         var strArr2 = strArr[j].split(":");
                            //         var dataObj = {'text': strArr2[2], 'alias': strArr2[1], meta: strArr2[0]};
                            //     }
                            //     dataArray.push(dataObj);
                            // }
                            // alert(JSON.stringify(dataArray));   
                            // $("#testdiv").html(JSON.stringify(dataArray) + " 1234<br><br>");
                        });  

                        // var strArr = templateStr.split("|");
                        // for(var j = 0; j < strArr.length; j++) {
                        //     if(strArr[j].indexOf("@") == -1) {
                        //         var dataObj = {'text': strArr[j]};                            
                        //     }
                        //     else {
                        //         var strArr2 = strArr[j].split(":");
                        //         var dataObj = {'text': strArr2[2], 'alias': strArr2[1], meta: strArr2[0]};
                        //     }
                        //     dataArray.push(dataObj);

                        // }
                        // alert("asdada");
                        // $("#testdiv").append("asd " + templateStr + "1234<br><br>");
                        // storetemplateStr(phrase);
                    
                    

                    // var dataObj = {'text': $(this).val()};
                    // dataArray.push(dataObj);
                });


                
                

                // var userAskObj = {'data': dataArray, 'isTemplate': false, 'count': 0};
                // userSaysArray.push(userAskObj);
                // var responsesObj = {'resetContext': false, 'action': intentName, 'affectedContext': [], 'parameters':[], 'speech': botResponse};
                // reponsesArray.push(responsesObj);      

                // $.ajax({
                //     type: "POST",
                //     url: baseUrl + "intents",
                //     contentType: "application/json; charset=utf-8",
                //     dataType: "json",
                //     headers: {
                //         "Authorization": "Bearer " + devAccessToken
                //     },
                //     data: JSON.stringify({name: intentName, auto: true, context: [], templates: [], userSays: userSaysArray, responses: reponsesArray, priority: 500000}),
                //     success: function(data) {
                //         alert("Adding intents successful");
                //         location.reload();
                //     },
                //     error: function(data) {
                //         alert("Adding intents failed. Please try again.");
                //     }
                // });                
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

                $("#testdiv").append("asdasdasdasd" + templateStr + "1231<br><br>");
                $(e).closest('tr').find("input").each(function() {
                    // alert(this.value);
                    var id = this.value;
                    $.ajax({
                        type: "GET",
                        url: baseUrl + "intents/" + id,                        
                        headers: {
                            "Authorization": "Bearer " + devAccessToken
                        },
                        success: function(data) {
                            // $("#testdiv").html(JSON.stringify(data));

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
                            for (var h = 0; h < data.userSays.length; h++) {
                                for (var i = 0; i < data.userSays[h].data.length; i++) {
                                    var actionString =  "<input type='hidden' value='" + data.userSays[h].data[i].value + "' readonly />" +
                                                        "<button type='button' class='btn btn-info btn-xs' title='Delete Keyword' onclick='deleteEntityData(this, \"" + data.id + "\")' ><span class='glyphicon glyphicon-remove'></span></button>";
                                    var row = $('<tr></tr>').appendTo(entTable);    
                                    $('<td class="entityName"></td>').text(data.userSays[h].data[i].text).appendTo(row); 
                                    
                                    $('<td></td>').html("").appendTo(row);
                                }
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
   //  var ret = phraseChecker();
   // alert(ret);

testcurlGet();
</script>
