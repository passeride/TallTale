/*
  MainScript V0.1
  Passeride
  */
$(document).ready(function(){
    start();
});

/// This object will house the player objects and their respective id as key
var players = [];

// This is all the entries in the story
var entries = [];

/// This will keep track of the player on this client
var clientPlayerID;

/// This is the current writer
var currWriter;

var baseAPIURL = "http://www.collabstor.io/php/api.php";

/*
 This is the gamecode, if this is empty game is not started
*/
var CODE = "";

var TITLE = "";

var updateInterval;

var updateInterval_time = 1000;

var max_entries = 20;

/*
 This function will trigger on start
 */
function start(){

    disableInput();
    $("#gameCreateButton").click(function(){
        var username = $("#playerName").val();
        $.getJSON(baseAPIURL + "?method=CreateGame&UserID=" + username, function(data){
            CODE = data.GameCode;
            clientPlayerID = data.UserID;
            $("#joinCreateForm").html("<h2>CODE: " + data.GameCode + "</h2>");
            console.log(data.GameCode);
            updateInterval = setInterval(update, updateInterval_time);
        });
    });

    $("#gameJoinButton").click(function(){
        var gameCode = $("#gameCodeInput").val();
        var username = $("#playerName").val();
        $.getJSON(baseAPIURL + "?method=JoinGame&GameCode=" + gameCode + "&UserID=" + username, function(data){
            if(data.status == "success"){
                CODE = gameCode;
                clientPlayerID = data.UserID;
                $("#joinCreateForm").html("<h2>CODE: " + CODE + "</h2>");
                console.log(data.GameCode);
                updateInterval = setInterval(update, updateInterval_time);
            }
        });
    });

    // Setting up listener
    $("#sendButton").click(function (){
        submitEntry();
    });

    // Setting up button listener on button
    $("#entryText").keypress(function(e){
        if(e.keyCode == 13){
            submitEntry();
        }
    });
}

function disableInput(){
    $("#entryText").attr('disabled','disabled');
    $("#sendButton").attr('disabled','disabled');
}

function enableInput(){
    $("#entryText").removeAttr('disabled');
    $("#sendButton").removeAttr('disabled');
}

/*
This function will be called in the interval updateInterval_time
*/
function update(){
    $.getJSON(baseAPIURL + "?method=GetGameState&GameCode=" + CODE, function(data){

        console.log(data);
        // Game
        currWriter = data.Game[0].CurrentWriter;
        TITLE = data.Game[0].Title;
        $("#storyTitle").html(TITLE + '<br/>' + data.Entries.length + '/' + max_entries);


        // Players
        for(i = 0; i < data.Players.length; i++){
            var p = data.Players[i];
            if(!(p.userID in players)){
                tmpP = new Player(p.userID,  p.color, p.username);
                addPlayer(tmpP);
            }
        }

        // Entries
        for(i = 0
; i < data.Entries.length; i++){
            var e = data.Entries[i];
            if(!(e.EntryNr in entries)){
                addEntry(players[clientPlayerID], e);
            }
        }

        // Setting input enabled if user is writer and story not finished
        console.log("Entries: " + entries.filter(Boolean).length + " Max_entries: " + max_entries);
        console.log("entries.length <= max_entries " + entries.filter(Boolean).length <= max_entries);
        if(entries.filter(Boolean).length <= max_entries && currWriter == clientPlayerID){
            console.log("Client is writer");
            enableInput();
        }else{
            console.log("Client is passive");
            disableInput();
        }
    });

    // This will make everythign appeard
    if(entries.filter(Boolean).length >= max_entries){
        console.log("SHOWING ALL TEXT!");
        $("#story").children('span').each(function(i, obj){
            $( this ).removeClass("hiddenEntry");
            $( this ).addClass("entry");
            $("#storyInput").hide();
        });
    }
}

/*
  this function is for adding a player to the player list on the left hand side of the stuff
  */
function addPlayer(player){
    players[player.ID] = player;
    $("#playerList").append('<div class="col-md-12 player" style="background-color:' + player.Color + '" id="' + player.ID + '"> ' + player.Name + '</div>');
}

function submitEntry(){
    var text = $("#entryText").val();
    $("storyInput").attr('disabled','disabled');
    console.log("Submitting " + text);
    $.getJSON(baseAPIURL + "?method=SubmitEntry&GameCode=" + CODE + "&UserID=" + clientPlayerID + "&Entry=" + text, function(data){
        if(data.status == "success"){
            $("#entryText").val("");
        }else{
            console.log(data.error);
        }
    });

}

/*
 This should be used to add entries to the story
 */
function addEntry(Player, EntryText){
    entries[EntryText.EntryNr] = EntryText;

    console.log("Adding " + EntryText.Text);

    var lastPlayer = Player;
    $("#story>span").last().attr("style", "text-shadow:0 0 25px " + hexToRgbA(lastPlayer.Color));
    $("#story>span").last().addClass("hiddenEntry");
    /// When a new element is added to the list, it will check if the clien should read it or not.
    if(currWriter == clientPlayerID){
        $("#story").append( '<span class="entry" style="text-shadow:0 0 25px ' + hexToRgbA(Player.Color) + ' ">  '  + EntryText.Text + '</span>');
        $("#story>span").last().attr("player", Player.ID);

        $("#story>span").last().attr("entry", EntryText.EntryNr);
    }else{
        $("#story").append( '<span class="hiddenEntry" style="text-shadow:0 0 25px ' + hexToRgbA(Player.Color) + ' ">  '  + EntryText.Text + '</span>');
        $("#story>span").last().attr("player", Player.ID);
        $("#story>span").last().attr("entry", EntryText.EntryNr);
    }

}


/*
 This is a helperfunction to convert hex to rgba
 */
function hexToRgbA(hex){
    var c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+',1)';
    }
    throw new Error('Bad Hex');
}
