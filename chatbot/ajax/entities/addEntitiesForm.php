<script type="text/javascript">
    var devAccessToken = "76c59f2365aa4cae94cf259635c87dfe",
        baseUrl = "https://api.api.ai/v1/";

    $(document).ready(function() {                
        $newRef = $("#newRef");

        $newRef.on("click", function(event) {
            $("#botEntitiesBody").append(
                "<tr class='botEntitiesTr'>" +
                    "<td><input type='text' name='refValue[]' class='getRefValue' placeholder='Reference value?' size='30%' onkeyup='copyToSyn(this, event)' /></td>" +
                    "<td>" +
                        "<input type='text' name='synonyms[]' placeholder='Separate the synonyms with comma sign'  value='' size='60%' />" +
                    "</td>" + 
                "</tr>"
            );
        });
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

<label for="entitiesName">Group Name:</label>
<input type="text" name="entitiesName" id="entitiesName" placeholder="What's is keyword's name?" size="30%" />
<div class="table-responsive" >
    <table class="table table-hover" >
        <thead>
            <tr>                            
                <th><button type="button" class="btn btn-info btn-xs" id="newRef" title="Add Reference Row" ><span class="glyphicon glyphicon-plus"></span></button> Keywords</th>
                <th>Synonyms</th>
            </tr>
        </thead>
        <tbody id="botEntitiesBody">
            <tr class="botEntitiesTr">
                <td><input type="text" name="refValue[]" class="getRefValue" placeholder="Reference value?" size="30%" onkeyup="copyToSyn(this, event)" /></td>
                <td><input type="text" name="synonyms[]" placeholder="Separate the synonyms with comma sign" size="60%" value="" /></td>
            </tr>                 
        </tbody>
    </table>    
</div>