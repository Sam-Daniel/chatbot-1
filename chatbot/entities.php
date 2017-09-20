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
                baseUrl = "https://api.api.ai/v1/";

            $(document).ready(function() {
                
                $("#newKeyword").on("click", function(event) {
                    $.post( "ajax/entities/addEntitiesForm.php", function( data ){
                        if( data.length > 0 ) {           
                            $( "#botEntitiesForm" ).html( data );
                        }               
                    });     
                });
                
            });

            function loadEntities() {
                $.ajax({
                    type: "GET",
                    url: baseUrl + "entities",
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
                                            "<button type='button' class='btn btn-info btn-xs' title='View Keyword Details' onclick='getSpecifiedEntity(this)' ><span class='glyphicon glyphicon-folder-open'></span></button>  " + 
                                            "<button type='button' class='btn btn-info btn-xs' title='Delete Keyword' onclick='deleteEntities(this)' ><span class='glyphicon glyphicon-remove'></span></button>";    
                            $('<td></td>').text("@" + data[i].name).appendTo(row); 
                            $('<td></td>').html(actionString).css({"text-align":"right", "padding-right":"5%"}).appendTo(row);
                 
                        }
                        // console.log("TTTTT:" + entTable.html());
                        entTable.appendTo("#resultDiv");
                        $("#resultDivTable tr:first-child").remove();
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
                    if($(this).val() != "") {
                        refValueArray.push($(this).val());
                    }                    
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
                        location.reload();
                    },
                    error: function() {
                        alert(JSON.stringify({name: entitiesName, entries: entriesArray}));                        
                    }
                });
            }


            function getSpecifiedEntity(e) {
                $(e).closest('tr').find("input").each(function() {
                    var id = this.value;
                    $.ajax({
                        type: "GET",
                        url: baseUrl + "entities/" + id,                        
                        headers: {
                            "Authorization": "Bearer " + devAccessToken
                        },
                        success: function(data) {
                            $("#editDiv").html(
                                '<table class="table table-hover" id="editDivTable" style="width: 50%; margin: 0 auto;" >' +
                                    '<tr>' +
                                        '<th>Keyword</th>' +
                                        '<th>Synonyms</th>' +
                                        '<th></th>' +
                                    '</tr>' +
                                '</table>'
                            );
                            var entTable = $("#editDivTable");
                            var res = "";
                            var dataID = data.id;
                            for (var i = 0; i < data.entries.length; i++) {
                                var actionString =  "<input type='hidden' value='" + data.entries[i].value + "' readonly />" +
                                                    "<button type='button' class='btn btn-info btn-xs' title='Delete Keyword' onclick='deleteEntityData(this, \"" + data.id + "\")' ><span class='glyphicon glyphicon-remove'></span></button>";
                                var row = $('<tr></tr>').appendTo(entTable);    
                                $('<td class="entityName"></td>').text(data.entries[i].value).appendTo(row); 
                                for(var j = 0; j < data.entries[i].synonyms.length; j++) {
                                    var synString = data.entries[i].synonyms[j];
                                    res = synString.concat(",", res);                             
                                }
                                $('<td></td>').text(res).appendTo(row);
                                $('<td></td>').html(actionString).appendTo(row);
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

            function deleteEntities(e) {
                $(e).closest('tr').find("input").each(function() {
                    var id = this.value;

                    $.ajax({                        
                        url: "ajax/entities/deleteEntities.php?id=" + id,                        

                        success: function(data) {
                            alert("Deleting entities successful");
                            $(e).closest('tr').remove();
                        },
                        error: function(data) {
                            alert("Something when wrong. Please try again later.\n" + JSON.stringify(data) + "\n" + id);                        
                        }
                    });                    
                });               
            }

            function deleteEntityData(e, id) {
                // alert(id);
                $(e).closest('tr').find("input").each(function() {
                    var value = this.value;
                    // alert(value);
                    $.ajax({                        
                        url: "ajax/entities/deleteEntityData.php?id=" + id + "&name=" + value,                        

                        success: function(data) {
                            alert("Deleting entity data successful");
                            alert(data);
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
            <h3>Keywords <button type="button" class="btn btn-info btn-xs" title="Add new Keywords" data-toggle="modal" data-target="#myModal" id="newKeyword" ><span class="glyphicon glyphicon-plus"></span></button></h3> 
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
                        <h4 class="modal-title">Add new Keyword</h4>
                    </div>
                    <div class="modal-body">
                        <form id="botEntitiesForm"></form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="postEntities()">Save</button>
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
                        <div id="editDiv">
                            
                        </div>
                        
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
    loadEntities();
</script>
