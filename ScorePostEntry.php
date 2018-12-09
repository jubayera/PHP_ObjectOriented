<?php

//Test this in http://www.practical-research.org/prob5.php/LeaderboardGet

class ScorePostEntry
{
  public $UserId;
  public $LeaderboardId;
  public $Offset;
  public $Limit;
}

class ScorePostOutput
{
  public $UserId;
  public $LeaderboardId;
  public $Score;
  public $Rank;
  public $Entries;
}

$splitted = explode('/', $_SERVER['PHP_SELF']);
if( $splitted[2] == "LeaderboardGet") 
{
      echo '<form name="postform" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">
                <table id="postarea">
                <tbody>                    
                    <tr>    <td>User ID:</td><td><input type="text" name="UserId"></td></tr>
                    <tr>    <td>Leaderboard ID:</td><td><input type="text" name="LeaderboardId"></td></tr>
                    <tr>    <td>Offset:</td><td><input type="text" name="Offset"></td></tr>
                    <tr>    <td>Limit:</td><td><input type="text" name="Limit"></td></tr>
                    <tr>    <td></td><td> <input type="submit" value="Submit Entry"> </td>    </tr>
                </tbody>
                </table>
            </form>';

      if(isset($_POST['UserId']) && isset($_POST['LeaderboardId']))
      {

          $myObjScorePost_Encode = new ScorePostEntry;

          $myObjScorePost_Encode->UserId = $_POST['UserId'];
          $myObjScorePost_Encode->LeaderboardId = $_POST['LeaderboardId'];
          $myObjScorePost_Encode->Offset = $_POST['Offset'];
          $myObjScorePost_Encode->Limit = $_POST['Limit'];

          $scorePostJSON = json_encode($myObjScorePost_Encode);
          //echo $scorePostJSON;

          $myObjScorePost_Decode = new ScorePostEntry;
          $myObjScorePost_Decode = json_decode($scorePostJSON);

          //echo $myObjScorePost_Decode;
      }
      else
      {
            echo 'Can accept RAW JSON Input, if not posted through form. This form can accept input through form submission'; 

            //Make sure that it is a POST request.
            if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
                throw new Exception('Request method must be POST!');
            }

            //Make sure that the content type of the POST request has been set to application/json
            $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
            /*if(strcasecmp($contentType, 'application/json') != 0){
                throw new Exception('Content type must be: application/json');
            }*/
             
            //Receive the RAW post data.
            $content = trim(file_get_contents("php://input"));
                  
            //Attempt to decode the incoming RAW post data from JSON.
            $myObjScorePost_Decode = json_decode($content, true);
             
            //If json_decode failed, the JSON is invalid.
            if(!is_array($myObjScorePost_Decode)) {
                throw new Exception('Received content contained invalid JSON!');
            }
        }

              $servername = localhost;
              $db_name = "practic3_transaction";
              $db_user = "practic3_user";
              $db_password = 'test*server*games';
              // Create connection
              $conn = new mysqli($servername, $db_user, $db_password, $db_name);
              // Check connection
              if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
              }               

              $sql = "SELECT UserId, LeaderboardId, Score, Rank FROM ScorePost WHERE UserId = " . $myObjScorePost_Decode->{'UserId'} . " AND LeaderboardId = " . $myObjScorePost_Decode->{'LeaderboardId'};
               
              //echo $sql;

              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                  // output data of each row
                  if($row = $result->fetch_assoc()) {
                      
                      if($row["UserId"] != "")
                      {              
                          $myObjScorePost_Encode = new ScorePostOutput;
                          $myObjScorePost_Encode->UserId = $row["UserId"];
                          $myObjScorePost_Encode->LeaderboardId = $row["LeaderboardId"];
                          $myObjScorePost_Encode->Score = $row["Score"];
                          $myObjScorePost_Encode->Rank = $row["Rank"];      
                      }
                      else
                      {
                        $myObjScorePost_Encode = new ScorePostOutput;
                          $myObjScorePost_Encode->UserId = "";
                          $myObjScorePost_Encode->LeaderboardId = "";
                          $myObjScorePost_Encode->Score = "";
                          $myObjScorePost_Encode->Rank = "";  
                      }
                    }
                  }
                  else
                      {
                        $myObjScorePost_Encode = new ScorePostOutput;
                          $myObjScorePost_Encode->UserId = "";
                          $myObjScorePost_Encode->LeaderboardId = "";
                          $myObjScorePost_Encode->Score = "";
                          $myObjScorePost_Encode->Rank = "";  
                      }
          
              $sql = "SELECT UserId, Score, Rank FROM ScorePost WHERE LeaderboardId = " . $myObjScorePost_Decode->{'LeaderboardId'} . " ORDER BY Rank LIMIT " . $myObjScorePost_Decode->{'Limit'} . " OFFSET " . $myObjScorePost_Decode->{'Offset'};

              //echo $sql;
             
              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                  // output data of each row
                  if($row = $result->fetch_assoc()) {
                      
                      if($row["UserId"] != "")
                      {              
                          $entries[] = array('UserId' => $row["UserId"], 'Score' => $row["Score"], 'Rank' => $row["Rank"]);           
                      }
                      else
                      {
                        $entries[] = array();
                      }

                    }
                  }

                  $myObjScorePost_Encode->Entries = $entries;

                  $scorePostJSON = json_encode($myObjScorePost_Encode);
                  echo $scorePostJSON;

              $conn->close();
          
  }      

?>
