<?php
$dir = "files/";
if (isset($_POST["ip"])) {
    echo gethostbyname(gethostname());
    return;
} else if (file_exists($dir)) {
    if (isset($_POST["counter"])) {
        for ($i = 1; $i <= intval($_POST["counter"]); $i++) {
            $fname = $_FILES["f".$i]["name"];
            $fsize = $_FILES["f".$i]["size"];
            $ftmp = $_FILES["f".$i]["tmp_name"];

            $res = move_uploaded_file($ftmp, $dir.$fname);

            if ($res) {
                echo "<script>console.log('Uploaded $fname ($fsize B)');</script>";
            } else {
                echo "<script>console.log($res);</script>";
            }
        }
    }
    $files = scandir($dir);
    $script = "<script>window.setTimeout(function(){\n";
    foreach ($files as $file) {
        if ($file == "." || $file == ".." || $file == "index.html") {
            continue;
        }
        $fs = filesize($dir.$file);
        $script .= "addFileDownload('$file', $fs);\n";
    }
    $script .= "}, 1000);</script>";
    echo $script;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Moon IO</title>
        <link rel = "stylesheet" type = "text/css" href = "index.css">
        <script src="/lib/jquery.js"></script>
    </head>
    <body>
        <header onclick = "window.location=''">
            <span id="ip">localhost</span>Moon IO
        </header>
        <div id="wrapper">
            <fieldset>
                <legend>Upload</legend>
                <form action="" method="POST" enctype="multipart/form-data" id="uploadform">
                    <input type="file" name="f1" id="f1">
                    <input type="button" id="add" value="More..." onclick="addFileInput()">
                    <input type="submit" value="Submit">
                    <input type="hidden" name= "counter" id="counter" value=1>
                </form>
            </fieldset>
            <fieldset>
                <legend>Chat</legend>
                <div id="namebox">
                    <input type="text" maxlength="10" name="name" id="inputName" placeholder="Username">
                    <button onclick="registerUser()" id="register">Register</button>
                </div>
                <div id="chatbox">
                    <div class="chatrow">
                        <div class="content">Sample Text</div>
                        <div class="time">1:00</div>
                    </div>
                    <div class="chatrow">
                        <div class="content">Lorem ipsum dolor sit amet</div>
                        <div class="time">1:10</div>
                    </div>
                </div>
                <div id="inputbox">
                    <textarea id="input" rows="1" placeholder="Enter a message"></textarea>
                    <input type="button" onclick="submitData()" id="enter" value="Submit">
                </div>
            </fieldset>
            <fieldset>
                <legend>Download</legend>
                <table id="dltable">
                    <tr>
                        <td>
                            <strong class="title">File Name</strong>
                        </td>
                        <td>
                            <strong class="title">File Size</strong>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </body>
    <script>
        n=1
        // AJAX
        $.post("index.php", {
            ip: true
        },
        function(data, status) {
            $("#ip").html(data);
        });
        $("#input").keypress(function(e) {
           if (e.keyCode == 13 && !e.ctrlKey) {
               submitData();
           } 
            return true;
        });
        var lastDate = new Date();
        setInterval(retrieveData(), 50);
        function addFileInput() {
            var child1 = document.getElementById("f" + n);
            var child2 = document.createElement("input");
            child2.setAttribute("type", "file");
            n++;
            child2.setAttribute("name", "f" + n);
            child2.setAttribute("id", "f" + n);
            document.getElementById("counter").setAttribute("value", n);
            child1.parentNode.insertBefore(child2, child1.nextSibling);
        }
        function addFileDownload(fn, fs) {
            var units = ["B", "KB", "MB", "GB", "TB", "PB"];
            var ind = 0;
            while (fs > 1024) {
                fs /= 1024;
                ind++;
            }
            fs = Math.round(fs);
            var unit = units[ind];
            var table = document.getElementById("dltable");
            var row = document.createElement("tr");
            var td = document.createElement("td");
            var tdsize = document.createElement("td");
            var file = document.createElement("a");
            file.setAttribute("href", "/files/" + fn);
            file.setAttribute("target", "_blank");
            file.innerHTML = fn;
            td.appendChild(file);
            tdsize.innerHTML = "<span class='size'>" + fs + "</span> " + unit;
            row.appendChild(td);
            row.appendChild(tdsize);
            table.appendChild(row);
        }
        function submitData() {
            var txt = $("#input").value;
        }
        function retrieveData() {
            var date = lastDate;
            $.post("chatsql.php", {
                date: date.getTime()
            }, function(data, status) {
                var json = JSON.parse(data);
            });
        }
    </script>
</html>