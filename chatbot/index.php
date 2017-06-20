<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

?>



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
            var accessToken = "3c7e280edc174111bc93c7ccf45f1ae7",
                baseUrl = "https://api.api.ai/v1/",
                $speechInput,
                $recBtn,
                recognition,
                messageRecording = "Recording...",
                messageCouldntHear = "I couldn't hear you, could you say that again?",
                messageInternalError = "Oh no, there has been an internal server error",
                messageSorry = "I'm sorry, I don't have the answer to that yet.";

            $(document).ready(function() {
                $speechInput = $("#speech");
                $recBtn = $("#rec");

                $speechInput.keypress(function(event) {
                    if (event.which == 13) {
                        event.preventDefault();
                        send();
                    }
                });
                $recBtn.on("click", function(event) {
                    switchRecognition();
                });
                $(".debug__btn").on("click", function() {
                    $(this).next().toggleClass("is-active");
                    return false;
                });
            });

            function startRecognition() {
                recognition = new webkitSpeechRecognition();
                recognition.continuous = false;
                recognition.interimResults = false;

                recognition.onstart = function(event) {
                    respond(messageRecording);
                    updateRec();
                };
                recognition.onresult = function(event) {
                    recognition.onend = null;
            
                    var text = "";
                    for (var i = event.resultIndex; i < event.results.length; ++i) {
                        text += event.results[i][0].transcript;
                    }
                    setInput(text);
                    stopRecognition();
                };
                recognition.onend = function() {
                    respond(messageCouldntHear);
                    stopRecognition();
                };
                recognition.lang = "en-US";
                recognition.start();
            }

            function stopRecognition() {
                if (recognition) {
                    recognition.stop();
                    recognition = null;
                }
                updateRec();
            }

            function switchRecognition() {
                if (recognition) {
                    stopRecognition();
                } else {
                    startRecognition();
                }
            }

            function setInput(text) {
                $speechInput.val(text);
                send();
            }

            function updateRec() {
                $recBtn.text(recognition ? "Stop" : "Speak");
            }

            function send() {
                var text = $speechInput.val();
                $.ajax({
                    type: "POST",
                    url: baseUrl + "query",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + accessToken
                    },
                    data: JSON.stringify({query: text, lang: "en", sessionId: "yaydevdiner"}),

                    success: function(data) {
                        prepareResponse(data);
                    },
                    error: function() {
                        respond(messageInternalError);
                    }
                });
            }

            function prepareResponse(val) {
                var debugJSON = JSON.stringify(val, undefined, 2),
                    spokenResponse = val.result.speech;

                respond(spokenResponse);
                debugRespond(debugJSON);
            }

            function debugRespond(val) {
                $("#response").text(val);
            }

            function respond(val) {
                if (val == "") {
                    val = messageSorry;
                }

                if (val !== messageRecording) {
                    var msg = new SpeechSynthesisUtterance();
                    msg.voiceURI = "native";
                    msg.text = val;
                    msg.lang = "en-US";
                    window.speechSynthesis.speak(msg);
                }

                $("#spokenResponse").addClass("is-active").find(".spoken-response__text").html(val);
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
            <br/><br/>
            <div id="spokenResponse" class="spoken-response">
                <div class="spoken-response__text"></div>
            </div>
            <div class="form-group input-group">
                <input type="text" class="form-control" placeholder="what's your question?" id="speech" />
                <span class="input-group-btn"><button class="btn btn-default" type="button" id="rec"><i class="fa fa-search">Speak</i></button></span>
            </div>
        </div>
    </body>
</html>
