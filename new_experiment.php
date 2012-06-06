<?php 
session_start();

if(!$_SESSION['is_auth']) {
    header("location: .");
    exit();
}
?>

<?php include("header.php") ?>

        <div class="container">
            <h2>New experiment</h2>
            
            <div class="alert" id="txt_notif">
                <button class="close" data-dismiss="alert">×</button>
                <p id="txt_notif_msg"></p>
            </div>
            
            
            <form class="well form-horizontal" id="form_part1">

                <h3>1. Configure your experiment</h3>
                <div class="control-group">
                    <label class="control-label" for="txt_name">Name:</label>
                    <div class="controls">
                        <input id="txt_name" type="text" class="input-large" required="required">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="txt_duration">Duration (minutes):</label>
                    <div class="controls">
                        <input id="txt_duration" type="number" class="input-large" required="required">
                    </div>
                </div>
                
                <h3>2. Select your nodes</h3>
                <div class="control-group">
                    <label class="control-label">Resources</label>
                    <div class="controls">
                        
                            <input type="radio" name="resources_type" id="optionsRadiosType" value="type"
                            checked=""> by type

                            <input type="radio" name="resources_type" id="optionsRadiosMaps" value="physical"> physical   
                            
                            
                        <!-- by type -->
                        <div class="" id="div_resources_type">
                            Fixed:
                                <input id="txt_fixed" type="text" class="input-large">
                        </div>
                        
                        <!-- physical -->
                        <div class="" id="div_resources_map">
                            
                                <a href="#" id="devlille_maps">Devlille Maps</a>
                                <input id="devlille_list" value="" />
                        </div>
                        
                    </div>
                </div>
                <button id="btn_submit" class="btn btn-primary" type="submit">Set my nodes selection</button>
            </form>
            
            <form class="well form-horizontal" id="form_part2">
                <h3>3. Configure your nodes</h3>
                <p>
                    <select id="my_nodes" size="15" multiple></select>
                    <select id="my_profils" size="15">
                        <option value="profile1">profile1</option>
                    </select>
                    <select id="my_firmwares" size="15">
                    </select>
                    <input type="file" id="files" name="files[]" multiple />
                </p>
                <p>
                    <button id="btn_assoc" class="btn">Associate</button>
                    <button id="btn_submit" class="btn btn-primary" type="submit">Submit</button>
                </p>
                <p>
                    <table style="width:500px" class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>node</th>
                                <th>profile</th>
                                <th>firmware</th>
                            </tr>
                        </thead>
                        <tbody id="my_assoc"></tbody>
                    </table>
                </p>
            </form>
            
            
        <?php include('footer.php') ?>
        
        </div>
        
        
        <script type="text/javascript">

            /* ************ */
            /*  global var  */
            /* ************ */

            //json
            var exp_json = {
                "type": "physical",
                "name": "test",
                "duration": 100
            };

            //firmware
            var binary = [];


            /* ************ */
            /*   on ready   */
            /* ************ */
            $(document).ready(function () {

                $("#txt_notif").hide();
                document.getElementById('files').addEventListener('change', handleFileSelect, false);

                //ressources type
                $("#div_resources_map").hide();
                $("input[name=resources_type]").change(function () {
                    if ($(this).val() == "physical") {
                        $("#div_resources_type").hide();
                        $("#div_resources_map").show();
                    } else {
                        $("#div_resources_type").show();
                        $("#div_resources_map").hide();
                    }

                });

                //open popup
                $("#devlille_maps").click(function () {
                    window.open('devlille_maps.php', '', 'resizable=yes, location=no, width=500, height=500, menubar=no, status=no, scrollbars=no, menubar=no');
                });

            });

            /* ************* */
            /* submit part 1 */
            /* ************ */
            $("#form_part1").bind("submit", function () {

                //set main properties
                exp_json.type = $("input[name=resources_type]:checked").val();
                exp_json.name = $("#txt_name").val();
                exp_json.duration = parseInt($("#txt_duration").val());

                //build nodes list
                var devlille_nodes = [];
                if ($("#devlille_list").val() != "") {
                    var devlille_list = parseNodebox($("#devlille_list").val());
                    for (i = 0; i < devlille_list.length; i++) {
                        if(!isNaN(devlille_list[i]))
                            devlille_nodes.push("node"+devlille_list[i]+".devlille.senslab.info");
                    }
                }
                //set nodes list
                exp_json.nodes = devlille_nodes;
                
                $("#my_nodes").empty();
                for (i = 0; i < exp_json.nodes.length; i++) {
                        $("#my_nodes").append(new Option(exp_json.nodes[i], exp_json.nodes[i], false, false));
                }

                //check if selected nodes as already an association, if yes -> remove from the list
                for (i = 0; i < exp_json.nodes.length; i++) {
                    if(exp_json.profileassociations != null) {
                        for (j = 0; j < exp_json.profileassociations.length; j++) {
                            for (k = 0; k < exp_json.profileassociations[j].nodes.length; k++) {
                                if(exp_json.profileassociations[j].nodes[k] == exp_json.nodes[i]) {
                                    $('#my_nodes option[value="'+ exp_json.nodes[i]+'"]').remove();
                                }
                            }
                        }
                    }
                }
                
                
                //TODO: check associations for removed nodes

                //TODO: remove a assoc and re-add the nodes


                //set an association
                $("#btn_assoc").click(function () {

                    //get selected item an remove
                    var nodes_set = $("#my_nodes").val();
                    var profil_set = $("#my_profils").val();
                    var firmware_set = $("#my_firmwares").val();

                    if (nodes_set == null || profil_set == null || firmware_set == null) {
                        return false;
                    }
                    $("#my_nodes option:selected").remove();


                    //init some vars
                    if (exp_json.profiles == null) {
                        exp_json.profileassociations = [];
                        exp_json.firmwareassociations = [];

                        //TODO: default profile, only for debug
                        exp_json.profiles = {};
                        exp_json.profiles.profile1 = {};
                        exp_json.profiles.profile1.power = 'dc';
                        exp_json.profiles.profile1.sensor = {};
                        exp_json.profiles.profile1.sensor.temperature = false;
                        exp_json.profiles.profile1.sensor.luminosity = false;
                        exp_json.profiles.profile1.sensor.frequency = 15;
                        exp_json.profiles.profile1.consemptium = {};
                        exp_json.profiles.profile1.consemptium.current = true;
                        exp_json.profiles.profile1.consemptium.voltage = true;
                        exp_json.profiles.profile1.consemptium.frequency = 60;
                        exp_json.profiles.profile1.radio = {};
                        exp_json.profiles.profile1.radio.rssi = false;
                        exp_json.profiles.profile1.radio.frequency = 11;
                        exp_json.profiles.profile1.profilename = 'profile1';
                    }

                    var find = false;
                    //if profil already exist in the table
                    for (i = 0; i < exp_json.profileassociations.length; i++) {
                        if (exp_json.profileassociations[i].profilename == profil_set) {
                            exp_json.profileassociations[i].nodes = exp_json.profileassociations[i].nodes.concat(nodes_set);
                            find = true;
                        }
                    }

                    if (!find) {
                        exp_json.profileassociations.push({
                            "profilename": profil_set,
                            "nodes": nodes_set
                        });
                    }

                    find = false;
                    //if firmware already exist in the table
                    for (i = 0; i < exp_json.firmwareassociations.length; i++) {
                        if (exp_json.firmwareassociations[i].firmwarename == firmware_set) {
                            exp_json.firmwareassociations[i].nodes = exp_json.firmwareassociations[i].nodes.concat(nodes_set);
                            find = true;
                        }
                    }

                    if (!find) {
                        exp_json.firmwareassociations.push({
                            "firmwarename": firmware_set,
                            "nodes": nodes_set
                        });
                    }
                    
                    
                    displayAssociation();
                    
                    return false;
                });
                
               
                return false;
            })


            /* ************* */
            /* submit part 2 */
            /* ************ */
            $("#form_part2").bind('submit', function () {
                console.log(JSON.stringify(exp_json));

                var mydata = JSON.stringify(exp_json);
                var datab = "";

                
                if (exp_json.profiles != null) {
                    var boundary = "AaB03x";

                    //JSON
                    datab += "--" + boundary + '\r\n';
                    datab += 'Content-Disposition: form-data; name="'+exp_json.name+'.json"; filename="'+exp_json.name+'.json"\r\n';
                    datab += 'Content-Type: application/json\r\n\r\n';
                    datab += mydata + '\r\n\r\n';
                    //datab += "--" + boundary + '\r\n';


                    for (i = 0; i < binary.length; i++) {
                        datab += "--" + boundary + '\r\n';
                        datab += 'Content-Disposition: form-data; name="' + binary[i].name + '"; filename="' + binary[i].name + '"\r\n';
                        datab += 'Content-Type: text/plain\r\n\r\n';
                        datab += binary[i].bin + '\r\n';
                    }

                    //add json
                    datab += "--" + boundary + '--';


                    $.ajax({
                        type: "POST",
                        dataType: "text",
                        
                        data: datab,
                        url: "/rest/experiment",
                        contentType: "multipart/form-data; boundary="+boundary,
                        
                        //data: "data="+datab,
                        //url: "dump.php",
                        success: function (data_server) {
                            $("#txt_notif_msg").html(data_server);
                            $("#txt_notif").show();
                            $("#txt_notif").removeClass("alert-error");
                            $("#txt_notif").addClass("alert-success");
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrows) {
                            $("#txt_notif_msg").html(errorThrows);
                            $("#txt_notif").show();
                            $("#txt_notif").removeClass("alert-success");
                            $("#txt_notif").addClass("alert-error");
                        }
                    });
                }
                else
                {
                    $.ajax({
                        type: "POST",
                        dataType: "text",
                        data: mydata,
                        contentType: "application/json; charset=utf-8",
                        url: "/rest/experiment?body",
                        success: function (data_server) {
                            $("#txt_notif_msg").html(data_server);
                            $("#txt_notif").show();
                            $("#txt_notif").removeClass("alert-error");
                            $("#txt_notif").addClass("alert-success");
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrows) {
                            $("#txt_notif_msg").html(errorThrows);
                            $("#txt_notif").show();
                            $("#txt_notif").removeClass("alert-success");
                            $("#txt_notif").addClass("alert-error");
                        }
                    });
                    
                }



                return false;
            });



            /* ************ */
            /*   function   */
            /* ************ */

            // expand a list of nodes containing dash intervals
            // 1-3,5,9 -> 1,2,3,5,9
            function expand(factExp) {
                exp = [];
                for (i = 0; i < factExp.length; i++) {
                    dashExpression = factExp[i].split("-");
                    if (dashExpression.length == 2) {
                        for (j = parseInt(dashExpression[0]); j < (parseInt(dashExpression[1]) + 1); j++)
                        exp.push(j);
                    } else exp.push(parseInt(factExp[i]));
                }
                exp.sort(sortfunction);
                for (var i = 1; i < exp.length; i++) {
                    if (exp[i] == exp[i - 1]) {
                        exp.splice(i--, 1);
                    }
                }
                return exp;
            }

            function parseNodebox(input) {
                return expand(input.split(","));
            }

            function sortfunction(a, b) {
                return (a - b) //causes an array to be sorted numerically and ascending
            }
            
            function handleFileSelect(evt) {
                var files = evt.target.files; // fileList object

                // loop through the FileList and render image files as thumbnails.
                for (var i = 0, f; f = files[i]; i++) {

                    var reader = new FileReader();

                    // closure to capture the file information.
                    reader.onload = (function (theFile) {
                        return function (e) {

                            binary.push({
                                "name": theFile.name,
                                "bin": e.target.result
                            });

                            $("#my_firmwares").append(new Option(theFile.name, theFile.name, false, false));
                        };
                    })(f);

                    reader.readAsText(f);
                }
            }
            
            
            //display nodes associations
            function displayAssociation() {
                $("#my_assoc").html("");
                
                    json_tmp = [];
    
                    //build a more simple json for parsing
                    if(exp_json.profileassociations != null)
                    {
                        for(i = 0; i < exp_json.profileassociations.length; i++) {
                            for(j = 0; j < exp_json.profileassociations[i].nodes.length;j++){
                                json_tmp.push({"node": exp_json.profileassociations[i].nodes[j],"profilename":exp_json.profileassociations[i].profilename});
                            }
                        }
                    }
                    
                    if(exp_json.firmwareassociations != null) {
                        for(i = 0; i < exp_json.firmwareassociations.length; i++) {
                            for(j = 0; j < exp_json.firmwareassociations[i].nodes.length;j++){
                                
                                for(k = 0; k < json_tmp.length; k++) {
                                    if(json_tmp[k].node == exp_json.firmwareassociations[i].nodes[j])
                                        json_tmp[k].firmwarename = exp_json.firmwareassociations[i].firmwarename;
                                }
                            }
                        }
                    }
                    
                    //display
                    for(k = 0; k < json_tmp.length; k++) {
                        $("#my_assoc").append("<tr><td>"+json_tmp[k].node+"</td><td>"+json_tmp[k].profilename+"</td><td>"+json_tmp[k].firmwarename+"</td></tr>");
                    }
            }
            
        </script>
       
        </body>
</html>
