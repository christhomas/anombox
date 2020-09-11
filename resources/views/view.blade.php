<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style type="text/css">
        html, body, header, article{
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
        }

        header {
            background-color: white;
            height: 100px;
            z-index: 2;
            text-align: center;
        }

        header h1 {
            font-family: monospace;
            line-height: 100px;
        }

        article {
            z-index: 1;
            background-size: cover;
            background-image: url("https://source.unsplash.com/collection/190727/1600x900");
        }

        article .outer {
            position: fixed;
            width: 100vw;
            height: 100vh;
            padding-top: 100px;
        }

        article .frame {
            position: relative;
            width: 90%;
            height: 90%;
            background-color: white;
            opacity: 0.8;
            border-radius: 2rem;
        }

        article .frame-padding {
            position: relative;
            padding: 2rem;
            width: 100%;
            height: 100%;
        }

        article .content {
            position: relative;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
        }

        article .content .list-group {
            position: relative;
            overflow-y: auto;
            height: 100%;
        }

        .empty, .loading {
            width: 100%;
            height: 100%;
            font-size: 4rem;
            font-family: monospace;
        }

        table .filename {
            width: 80%;
        }

        table .size,
        table .date,
        table .actions {
            width: 10%;
            white-space: nowrap;
            padding-left: 2rem;
        }
    </style>

    <title>Anombox: View Files</title>
</head>
<body>
<header>
    <h1>Anombox</h1>
</header>
<article class="container-fluid">
    <div class="outer row align-items-center justify-content-center">
        <div class="frame">
            <div class="frame-padding">
                <div class="content">
                    <div class="list-group d-none">
                        <table>
                            <thead>
                                <tr>
                                    <th class="filename">Filename</th>
                                    <th class="size">Size</th>
                                    <th class="date">Upload Date</th>
                                    <th class="actions">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody class="file-list">
                            </tbody>
                        </table>
                    </div>
                    <div class="empty d-none row align-items-center justify-content-center">
                        <div>There are no files ...</div>
                    </div>
                    <div class="loading row align-items-center justify-content-center">
                        <div>Loading ...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script defer type="text/javascript">
    $("document").ready(function(){
        var token = localStorage.getItem('token');

        var commonResponses = {
            403: function(){
                $(window).attr('location', '/anombox/v1/admin');
            },
            401: function(){
                $(window).attr('location', '/anombox/v1/admin');
            },
            500: function(){
                console.log("Something has gone wrong, received a 500 response");
            },
        };

        var commonFields = {
            xhrFields: {
                withCredentials: true
            },
            headers: {
                Authorization: token
            }
        };

        var bytesToSize = function(bytes) {
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            if (bytes == 0) return '0 Byte';
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        }

        var loadList = function(){
            $.ajax(Object.assign({},{
                url: '/anombox/v1/list',
                method: 'GET',
                statusCode: commonResponses
            }, commonFields)).done(function(data){
                console.log(data);

                $(".loading").addClass("d-none");

                var fileList = $(".file-list");
                fileList.empty();

                if(data.length == 0){
                    $(".empty").removeClass("d-none");
                    $(".list-group").addClass("d-none");
                }else{
                    $(".empty").addClass("d-none");
                    $(".list-group").removeClass("d-none");

                    for(var f in data){
                        var file = data[f];

                        var row = $("<tr/>");
                        row.append($("<td/>").addClass("filename").text(file.name));
                        row.append($("<td/>").addClass("size").text(bytesToSize(file.size)));
                        row.append($("<td/>").addClass("date").text(new Date(file.date * 1000).toLocaleString()));

                        var download = $("<a>Download</a>").addClass("mr-2 btn btn-primary");
                        download.attr('href', '/anombox/v1/download/'+file.name+'?token='+token);
                        download.attr('download', file.name);
                        download.on("click", function(){
                            var filename = $(this).closest("tr").find(".filename").text();
                            console.log("Downloading file: " + filename);
                        });

                        var remove = $("<button>Delete</button>").addClass("mr-2 btn btn-danger");
                        remove.on("click", function(){
                            if(confirm("Are you sure you want to delete this file?")){
                                var filename = $(this).closest("tr").find(".filename").text();

                                $.ajax(Object.assign({},{
                                    url: "/anombox/v1/delete/"+filename,
                                    method: "DELETE",
                                    statusCode: Object.assign({}, {
                                        200: function(data){
                                            console.log({
                                                message: "file was deleted",
                                                data,
                                            });

                                            loadList();
                                        }
                                    }, commonResponses)
                                }, commonFields));
                            }
                        });

                        var actions = $("<td/>").addClass("actions");
                        actions.append(download);
                        actions.append(remove);

                        row.append(actions);

                        fileList.append(row);
                    }
                }
            });
        }

        loadList();
    });
</script>
</body>
