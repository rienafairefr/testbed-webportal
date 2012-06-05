<?php 
session_start();

if(!$_SESSION['is_auth']) {
    header("location: .");
    exit();
}

include("header.php") ?>

    <div class="container" text-align="top">
    
   
<div class="row">

    <h2>Experiment Details</h2>

    <div id="detailsExp">
        <p id="detailsExpSummary"></p>
        
        <table class="table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th>node</th>
                <th>profile</th>
                <th>firmware</th>
            </tr>
        </thead>
        <tbody id="detailsExpRow">
        </tbody>
        </table>
    </div>
</div>

        
        <?php include('footer.php') ?>


    <script type="text/javascript">
        
        $(document).ready(function(){


        var json_exp = [];
        var withAssoc = false;
        var id = <?php echo $_GET['id']?>

            /* Retrieve experiment details */
            $.ajax({
                url: "/rest/experiment/"+id,
                type: "GET",
                data: {},
                dataType: "json",
                success:function(data){
                    //console.log(data);

                    $("#detailsExpSummary").html("<b>Experiment:</b> " + id + "<br/>");
                    $("#detailsExpSummary").append("<b>Name:</b> " + data.name + "<br/>");
                    $("#detailsExpSummary").append("<b>Duration:</b> " + data.duration + "<br/>");
                    $("#detailsExpSummary").append("<b>Number of Nodes:</b> " + data.nodes.length + "<br/>");
        
                    json_exp = [];
                    withAssoc = false;
                    //build a more simple json for parsing
                    if(data.profileassociations != null)
                    {
                        withAssoc = true;
                        for(i = 0; i < data.profileassociations.length; i++) {
                            for(j = 0; j < data.profileassociations[i].nodes.length;j++){
                                json_exp.push({"node": data.profileassociations[i].nodes[j],"profilename":data.profileassociations[i].profilename});
                            }
                        }
                    }
                    
                    if(data.firmwareassociations != null) {
                        for(i = 0; i < data.firmwareassociations.length; i++) {
                            for(j = 0; j < data.firmwareassociations[i].nodes.length;j++){
                                
                                for(k = 0; k < json_exp.length; k++) {
                                    if(json_exp[k].node == data.firmwareassociations[i].nodes[j])
                                        json_exp[k].firmwarename = data.firmwareassociations[i].firmwarename;
                                }
                            }
                        }
                    }
                    
                    $("#detailsExpRow").html("");
                    for(k = 0; k < json_exp.length; k++) {
                        $("#detailsExpRow").append("<tr><td>"+json_exp[k].node+"</td><td>"+json_exp[k].profilename+"</td><td>"+json_exp[k].firmwarename+"</td></tr>");
                    }
                    
                    if(!withAssoc)
                    {
                        for(k = 0; k < data.nodes.length; k++) {
                            $("#detailsExpRow").append("<tr><td>"+data.nodes[k]+"</td><td></td><td></td></tr>");
                        }
                    }

                    $('#details_modal').modal('show');
                },
                error:function(XMLHttpRequest, textStatus, errorThrows){
                    $("#detailsExpSummary").html("An error occurred while retrieving experiment #" + id + " details");
                    $('#details_modal').modal('show');
                }
            });
    });

    </script>

  </body>
</html>
