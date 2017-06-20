<script type="text/javascript">
    var devAccessToken = "76c59f2365aa4cae94cf259635c87dfe",
        baseUrl = "https://api.api.ai/v1/";

    $(document).ready(function() {                
        $newBotResponse = $("#newBotResponse");
        $newUserSays = $("#newUserSays");

        $newUserSays.on("click", function(event) {
            $("#QuestionTableBody").append(
                "<tr>" +
                    "<td ><input type='text' name='userSays[]' placeholder='User says?' size='100%' /></td>" +
                "</tr>"
            );
        });

        // $newBotResponse.on("click", function(event) {
        //     $("#responseTableBody").append(
        //         "<tr>" +
        //             "<td ><input type='text' name='response[]' placeholder='response?'' size='100%'' /></td>" +
        //         "</tr>"
        //     );
        // });
    });
    
    function copyToSyn(e, event) {
        if (event.which == 13) {
            event.preventDefault();
            $inputVal = e.value;
            $(e).closest('tr').find("input[name='synonyms[]']").each(function() {
                $currentVal = this.value;
                this.value = $inputVal + ',' + $currentVal;
            });
        }
    }
</script>

<h3>Questions</h3>
<hr/>
<form id="botIntentsForm">
    <label for="intentName">Question Group Name:</label>
    <input type="text" name="intentName" id="intentName" placeholder="What's is intent's name?" size="30%" />
    <br/>
    <table class="table table-bordered" id="QuestionTable" >                   
        <tbody id="QuestionTableBody">
            <tr>
                <td ><span><button type="button" class="btn btn-info btn-xs" id="newUserSays" ><span class="glyphicon glyphicon-plus"></span></button> User Questions</span></td>
            </tr>
            <tr>                            
                <td ><input type="text" name="userSays[]" placeholder="User says?" size="100%" /></td>
            </tr> 
        </tbody>
    </table>   
    <table class="table table-bordered" id="responseTable" >                   
        <tbody id="responseTableBody">            
            <tr>
                <td ><span><button type="button" class="btn btn-info btn-xs" id="newBotResponse" ><span class="glyphicon glyphicon-plus"></span></button> Bot Responses</span></td>
            </tr>
            <tr>                            
                <td ><input type="text" name="response" placeholder="response?" size="100%" /></td>
            </tr> 
        </tbody>
    </table>        
</form>