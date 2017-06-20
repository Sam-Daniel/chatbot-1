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
                baseUrl = "https://api.api.ai/v1/";

            $(document).ready(function() {
                $synonymGet = $(".synonymGet");
                $newRef = $("#newRef");
                
                $synonymGet.on("keypress", function(event) {
                    if (event.which == 13) {
                        event.preventDefault();
                        $inputVal = this.value;
                        this.value = "";
                        $(this).closest('tr').find("input[name='synonyms[]']").each(function() {
                            $currentVal = this.value;
                            this.value = $inputVal + ',' + $currentVal;
                        });
                    }
                });

                $newRef.on("click", function(event) {
                    $("#botEntitiesBody").append(
                        "<tr class='botEntitiesTr'>" +
                            "<td><input type='text' name='refValue[]' placeholder='Reference value?' size='30%' /></td>" +
                            "<td>" +
                                "<input type='text' class='synonymGet' size='20%' /> " +
                                "<input type='text' name='synonyms[]' placeholder='Synonym'  value='' size='60%' readonly />" +
                            "</td>" + 
                        "</tr>"
                    );
                    event.preventDefault();
                });
            });

            function getEntities() {
                $.ajax({
                    type: "GET",
                    url: baseUrl + "entities",
                    headers: {
                        "Authorization": "Bearer " + devAccessToken
                    },

                    success: function(data) {
                        var debugJSON = JSON.stringify(data);
                        $("#resultDiv").html(debugJSON);

                        for(var x = 0; x<data.length; x++) {
                            $("#entityList").append("<option value='" + data[x].name + "'>" + data[x].name + "</option>");
                        }

                    },
                    error: function() {
                        respond(messageInternalError);
                    }
                });
            }

            function postEntities() {
                var entitiesName = $("input[name=entitiesName]").val(),
                    refValueArray = [],
                    synonymsArray = [],
                    entriesArray = [];

                $("input[name='refValue[]']").each(function() {
                    refValueArray.push($(this).val());
                });

                $("input[name='synonyms[]']").each(function() {
                    var synString = $(this).val();
                    var synArr = synString.split(",");
                    synonymsArray.push(synArr);
                });

                for(var x = 0; x < refValueArray.length; x++) {
                    var entriesObj = {'value': refValueArray[x], 'synonyms': synonymsArray[x]};
                    entriesArray.push(entriesObj);
                }
                               

                $.ajax({
                    type: "POST",
                    url: baseUrl + "entities",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + devAccessToken
                    },
                    data: JSON.stringify({name: entitiesName, entries: entriesArray}),

                    success: function(data) {
                        alert("Adding entities successful");
                    },
                    error: function() {
                        alert("Adding entities failed. Please try again.");
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

            function postIntent() {
                var intentName = $("input[name=intentName]").val(),
                    userAsk = $("input[name=userSays]").val(),
                    parname = $("input[name='parname[]']").val(),
                    entity = $("input[name='entity[]']").val(),
                    resolveVal = $("input[name='resolveVal[]']").val(),
                    botResponse = $("input[name='response']").val(),
                    entityArray = [],
                    userSaysArray = [],
                    reponsesArray = [],
                    dataArray = [];

                $("select[name='entity[]']").each(function() {
                    entityArray.push($(this).val());
                });

                var meta = "@" + entityArray[0];
                var dataObj = {'text': userAsk, 'alias': entityArray[0], meta: meta};
                // var dataObj = {'text': userAsk};
                dataArray.push(dataObj);
                var userAskObj = {'data': dataArray, 'isTemplate': false, 'count': 0};
                userSaysArray.push(userAskObj);
                var responsesObj = {'resetContext': false, 'action': intentName, 'affectedContext': [], 'parameters':[], 'speech': botResponse};
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
                        alert(SON.stringify({name: intentName, auto: true, context: [], templates: [], userSays: userSaysArray, responses: reponsesArray, priority: 500000}));
                    },
                    error: function() {
                        alert("Adding intents failed. Please try again.");

                    }
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
                        <li class="active"><a href="chatbot_trainer.php">Train Me</a></li>
                    </ul>      
                </div>
            </div>
        </nav>      
        <div class="container">
            <h3>Keywords</h3>
            <hr/>
            <form id="botEntitiesForm">
                <label for="entitiesName">Keyword:</label>
                <input type="text" name="entitiesName" id="entitiesName" placeholder="What's is entities' name?" size="30%" />
                <table class="table table-bordered">
                    <thead>
                        <tr>                            
                            <th>Reference Value</th>
                            <th>Synonyms</th>
                        </tr>
                    </thead>
                    <tbody id="botEntitiesBody">
                        <tr class="botEntitiesTr">
                            <td><input type="text" name="refValue[]" placeholder="What's is your reference value?" size="30%" /></td>
                            <td>
                                <input type="text" class="synonymGet" size="20%"  />
                                <input type="text" name="synonyms[]" placeholder="Synonym" size="60%" value="" readonly />
                            </td>        
                        </tr> 
                        <tr class="botEntitiesTr">
                            <td><input type="text" name="refValue[]" placeholder="What's is your reference value?" size="30%" /></td>
                            <td>
                                <input type="text" class="synonymGet" size="20%"  />
                                <input type="text" name="synonyms[]" placeholder="Synonym" size="60%" value="" readonly />
                            </td>        
                        </tr> 
                        <tr class="botEntitiesTr">
                            <td><input type="text" name="refValue[]" placeholder="What's is your reference value?" size="30%" /></td>
                            <td>
                                <input type="text" class="synonymGet" size="20%"  />
                                <input type="text" name="synonyms[]" placeholder="Synonym" size="60%" value="" readonly />
                            </td>        
                        </tr> 
                                       
                    </tbody>
                </table>
                <button type="button" class="btn btn-info" id="newRef" ><span class="glyphicon glyphicon-plus"></span></button>
                <input type="button" class="btn btn-info" name="submitBotData" id="submitBotData" value="SAVE" onclick="postEntities()" />
                <!-- <input type="button" class="btn btn-info" name="submitBotData" id="submitBotData" value="GET entities" onclick="getEntities()" /> -->
            </form>
            
            <hr/>
            <br/>
            <h3>Intents</h3>
            <hr/>
            <form id="botIntentsForm">
                <label for="intentName">Intent Name:</label>
                <input type="text" name="intentName" id="intentName" placeholder="What's is intent's name?" size="30%" />
                <table class="table table-bordered">                   
                    <tbody>
                        <tr>                            
                            <td colspan="3"><input type="text" name="userSays" placeholder="User says?" size="100%" /></td>
                        </tr> 
                        <tr>
                            <td>Parameter Name</td>
                            <td>Entity</td>
                            <td>Resolved Value</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="parname[]" placeholder="Parameter Name" size="35%" readonly/></td>
                            <td><select id="entityList" name="entity[]"></select></td>
                            <td><input type="text" name="resolveVal[]" placeholder="Resolve Value" size="35%" readonly /></td>
                        </tr>
                        <tr>
                            <td colspan="3"><h4>Responses</h4></td>
                        </tr>
                        <tr>                            
                            <td colspan="3"><input type="text" name="response" placeholder="response?" size="100%" /></td>
                        </tr> 
                    </tbody>
                </table>
                <input type="button" class="btn btn-info" name="newUserSays" id="newUserSays" value="New User Says" />
                <input type="button" class="btn btn-info" name="newBotResponse" id="newBotResponse" value="New Response" />
                <input type="button" name="submitBotData" class="btn btn-info" id="submitBotData" value="SAVE" onclick="postIntent()" />
                <!-- <input type="button" name="submitBotData" id="submitBotData" value="GET intents" onclick="getIntent()" /> -->
            </form>

            
            <div id="resultDiv"></div>
        </div>
    </body>
</html>
