<?php
/*
  API v0.1 Lukas

  API Must have following functions
  USER:
      CreateUser(UserName, GameCode)
        // Will create a user and connect that user to the game
      UpdateUserColor(HexColor)
        // Will update the db user color

  GAME:
      CreateGame(UserName)
        // Will also call CreateUser after creating GameCode, and also return the GameCode to the client
      UpdateTitle(Title)
      GetGameState(GameCode);
  ENTRY:
      CreateEntry(GameCode, UserId, Text);

 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$_POST = $_REQUEST;

switch($_POST['method']){
case "CreateGame":
    createGame();
    break;
case "GetGameState":
    getGameState($_POST['GameCode']);
    break;
case "SubmitEntry":
    commitEntry($_POST['GameCode'], $_POST['UserID'], $_POST['Entry']);
    break;
case "JoinGame":
    joinGame($_POST['GameCode'], $_POST['UserID']);
    break;
case "SetTitle":
    UpdateTitle($_POST['GameCode'], $_POST['Title']);
    break;
}

/*
  This function will return an db object to be used in the api
  phpboi
  r42HNkLa5kFt
 */
function connect(){
    $file = 'local.ini';
    if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
    $dns = $settings['database']['driver'] .
         ':host=' . $settings['database']['host'] .
         ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
         ';dbname=' . $settings['database']['schema'];
    $pdo = new PDO($dns, $settings['database']['username'], $settings['database']['password']);
    return $pdo;
}

function joinGame($CODE, $PLAYERID){
    $id = createPlayer($CODE, $PLAYERID);
    echo '{"status":"success","UserID":"' . $id . '"}';
}
/*
  This function will update the title
 */
function updateTitle($CODE, $TITLE){
    $conn = connect();

    $stmt = $conn->prepare("UPDATE TAH_DB.Game SET Title = :Title WHERE GameCode = :GameCode");

    $stmt->execute(array(
        ':Title' => $TITLE,
        ':GameCode' => $CODE
    ));

    $conn = null;

    echo '{"status":"success"}';
}

/*
  This function will create a game and return gamecode
 */
function createGame(){
    $conn = connect();

    $CODE = generateString(5);
    $TITLE = "Temporary Title";
    $OWNER = "";

    $stmt = $conn->prepare("INSERT INTO TAH_DB.Game (GameCode, Title) VALUES (:GameCode, :Title)");

    $stmt->execute(array(
        ':GameCode' => $CODE,
        ':Title' => $TITLE
    ));

    $conn = null;

    $playerid = createPlayer($CODE, $_POST['UserID']);
    setCurrentWriter($CODE, $playerid);
    setNextWriter($CODE);

    echo '{"status":"success",  "GameCode":"' . $CODE . '", "UserID":"' . $playerid . '"}';
}

/*
  This function will set the current writer
 */
function setCurrentWriter($CODE, $PLAYERID){
    $conn = connect();

    $stmt = $conn->prepare("UPDATE TAH_DB.Game SET CurrentWriter = :currentWriter WHERE GameCode = :GameCode");

    $stmt->execute(array(
        ':currentWriter' => $PLAYERID,
        ':GameCode' => $CODE
    ));

    $conn = null;
}


/*
  This funciont will allow a player to submit
 */
function commitEntry($CODE, $PLAYERID, $TEXT){
    // check if user is current writer
    if(getCurrentWriter($CODE) != $PLAYERID){
        echo '{"status":"failed","error":"not current writer"}';
    }else{
        $conn = connect();

        $stmt = $conn->prepare("INSERT INTO TAH_DB.Entry (Game_GameCode, user_userID, Text, create_time) VALUES (:GameCode, :userID, :Text, NOW())");

        $stmt->execute(array(
            ':GameCode' => $CODE,
            ':userID' => $PLAYERID,
            ':Text' => $TEXT
        ));

        setNextWriter($CODE);
        echo '{"status":"success"}';
    }

}


/*
  This will return the currentwriter of the game corresponding to CODE
 */
function getCurrentWriter($CODE){
    $conn = connect();

    $stmt = $conn->prepare("SELECT * FROM TAH_DB.Game WHERE GameCode = :GameCode");

    $stmt->execute(array(
        ':GameCode' => $CODE
    ));


    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $row = end($all);

    return $row['CurrentWriter'];
}

/*
  This function will set the next writer of this game
 */
function setNextWriter($CODE){
    // Players
    $players = [];

    // Getting players
    $conn = connect();

    // Important to order by create_time to get consistent result
    $stmt = $conn->prepare("SELECT * FROM TAH_DB.User WHERE Game_GameCode = :GameCode ORDER BY create_time DESC");

    $stmt->execute(array(
        ':GameCode' => $CODE
    ));

    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    for($i = 0; $i < count($all); $i++){
        $players[] = $all[$i]['userID'];
    }

    $conn = null;
    // Get current writer
    $currentWriter = getCurrentWriter($CODE);
    //    echo json_encode($players);
    $nextWriter = "";

    for($i = 0; $i < count($players); $i++){
        if($players[$i] == $currentWriter){
            if($i == count($players) - 1){
                $nextWriter = $players[0];
            }else{
                $nextWriter = $players[$i + 1];
            }
        }
    }

    // setting nextWriter
    setCurrentWriter($CODE, $nextWriter);
}

/*
  this function will test if a playername is taken
 */
function nameTaken($CODE, $PLAYERID){

}

/*

  This function will create a player and take a code as paramter
*/
function createPlayer($CODE, $PLAYERID){
    $conn = connect();

    $User = $PLAYERID;

    $nameHash = hash('md5', $User);

    $color = '#'. substr(base_convert($nameHash, 32, 16), 0, 6);

    $stmt = $conn->prepare("INSERT INTO TAH_DB.User (Game_GameCode, username, create_time, checkin, color) VALUES (:GameCode, :username, NOW(), NOW(), :Color)");

    $stmt->execute(array(
        ':username' => $User,
        ':GameCode' => $CODE,
        ':Color' => $color
    ));

    $userID = $conn->lastInsertId();
    $conn = null;

    return $userID;
}

/*
  This function will be called to create a random string to be used in CODE creating
 */
function generateString($length)
{
    $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    $key = "";
    for($i=0; $i<$length; $i++)
        $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];

    return $key;
}

/*
  This function will return a json containing the current state of the game. With info such as.
  - GameCode
  - Entries
  - CurrentWriter
  - Players
    [
      -PlayerId
      -PlayerName
      -PlayerColor
    ]
 */
function getGameState($CODE){
    // Output
    $output = [];

    // Getting game info
    $conn = connect();

    $stmt = $conn->prepare("SELECT * FROM TAH_DB.Game WHERE GameCode = :GameCode");

    $stmt->execute(array(
        ':GameCode' => $CODE
    ));

    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output['Game'] = $all;

    $conn = null;

    // get player info
    $conn = connect();

    $stmt = $conn->prepare("SELECT * FROM TAH_DB.User WHERE Game_GameCode = :GameCode");

    $stmt->execute(array(
        ':GameCode' => $CODE
    ));

    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output['Players'] = $all;

    $conn = null;
    // Get entries
    $conn = connect();

    $stmt = $conn->prepare("SELECT * FROM TAH_DB.Entry WHERE Game_GameCode = :GameCode");

    $stmt->execute(array(
        ':GameCode' => $CODE
    ));

    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output['Entries'] = $all;

    $conn = null;

    echo json_encode($output);

}

?>