  <html>
    <head>
        <title>
            Collabstor.io
        </title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
        <link href="./css/mainStyle.css" rel="stylesheet"/>
        <!-- These are my scripts, there are many like it but these are mine :)  -->
        <script src="./js/jquery-3.2.1.min.js"></script>
        <script src="./js/mainScript.js" ></script>
        <script src="./js/player.js" ></script>
        <!-- Google tracking ur arse -->
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-108014187-1"></script>
        <script>
         window.dataLayer = window.dataLayer || [];
         function gtag(){dataLayer.push(arguments);}
         gtag('js', new Date());

         gtag('config', 'UA-108014187-1');
        </script>

    </head>
    <body>
        <div class="container-fluid">
            <div class="row" style="min-height:100%" >
                <div class="col sidePanel" >
                    <div class="row">
                        <div class="logo">
                            <img alt="" src="./img/logo.png"/>
                        </div>
                    </div>
                    <div class="row">
                        <div id="joinCreateForm">
                            <label for="Name">Name</label>
                            <input id="playerName" name="playerName" type="text" class="form-control" value="" placeholder="UserName" />
                            <label for="gameCode">GameCode:</label>
                            <input id="gameCodeInput" name="gameCode" type="text" class="form-control" value="" placeholder="GameCode" />
                            <div class="btn-group btn-group-lg">
                                <button id="gameJoinButton" class="btn btn-primary" >Join</button>
                                <button id="gameCreateButton" class="btn btn-primary">Create</button>
                            </div>
                        </div>
                    </div>
                    <div id="playerList" class="row playerPlate">
                    </div>
                </div>
                <div class="col-lg-10 justify-md-center" >
                    <div class="storyBox">
                        <h1 id="storyTitle" class="storyTitle">
                            This is the title
                        </h1>
                        <div class="storyPage">
                            <div id="story" class="storyEntry">
                            </div>

                            <div id="storyInput" class="input-group storyInput">
                                <input id="entryText" name="entryText" class="form-control" type="text" value="" placeholder="Write your entry!" />
                                <span class="input-group-btn">
                                    <button id="sendButton" class="btn btn-primary">Send!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
