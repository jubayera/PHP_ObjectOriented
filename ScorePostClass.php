<?php


//Test this in http://www.practical-research.org/prob4.php/ScorePost

class ScorePostClass
{
  public $UserId;
  public $LeaderboardId;
  public $Score;
  //public $Rank;
}

$splitted = explode('/', $_SERVER['PHP_SELF']);
if( $splitted[2] == "ScorePost") 
{
      echo '<form name="postform" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">
                <table id="postarea">
                <tbody>                    
                    <tr>    <td>User ID:</td><td><input type="text" name="UserId"></td></tr>
                    <tr>    <td>Leaderboard ID:</td><td><input type="text" name="LeaderboardId"></td></tr>
                    <tr>    <td>Score:</td><td><input type="text" name="Score"></td></tr>
                    <tr>    <td></td><td> <input type="submit" value="Submit Entry"> </td>    </tr>
                </tbody>
                </table>
            </form>';

      if(isset($_POST['UserId']) && isset($_POST['LeaderboardId']) && isset($_POST['Score']))
      {
          $myObjScorePost_Encode = new ScorePostClass;

          $myObjScorePost_Encode->UserId = $_POST['UserId'];
          $myObjScorePost_Encode->LeaderboardId = $_POST['LeaderboardId'];
          $myObjScorePost_Encode->Score = $_POST['Score'];

          $scorePostJSON = json_encode($myObjScorePost_Encode);
          //echo $scorePostJSON;

          $myObjScorePost_Decode = new ScorePostClass;
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
              $db_name = "--";
              $db_user = "--";
              $db_password = '--';
              // Create connection
              $conn = new mysqli($servername, $db_user, $db_password, $db_name);
              // Check connection
              if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
              } 

              $sql = "SELECT MAX(Score) as score, Rank as rank FROM ScorePost WHERE UserId = " . $myObjScorePost_Decode->{'UserId'} . " AND LeaderboardId = " . $myObjScorePost_Decode->{'LeaderboardId'};
              $result = $conn->query($sql);

              //echo "result->num_rows: " . $result->num_rows . "<br/>";

              if ($result->num_rows > 0) {
                  // output data of each row
                  if($row = $result->fetch_assoc()) {
                      //echo "Score: " . $row["score"] . "<br/>";
                      if($row["score"] == "")
                      {                         
                              $myObjScorePost_Encode = new ScorePostClass;

                              $myObjScorePost_Encode->UserId = $myObjScorePost_Decode->{'UserId'};
                              $myObjScorePost_Encode->LeaderboardId = $myObjScorePost_Decode->{'LeaderboardId'};
                              $myObjScorePost_Encode->Score = $myObjScorePost_Decode->{'Score'};
                              $myObjScorePost_Encode->Rank = $row["rank"];

                              //get rank from scores of all users
                              $sql = "SELECT Score, Rank FROM ScorePost ORDER BY Score DESC";
                              $result2 = $conn->query($sql);     

                              $max = 0;                         

                              if ($result2->num_rows > 0) {
                                  // output data of each row
                                  while($row = $result2->fetch_assoc()) {

                                  //echo "result->Score :: rank: " . $row['Score'] . " " . $row['Rank'] . "<br/>";
                                      
                                  if($myObjScorePost_Decode->{'Score'} > $row["Score"])
                                  {
                                    if($row["Rank"] + 1 > $max)
                                    {
                                      $myObjScorePost_Encode->Rank = $row["Rank"] + 1;
                                    }
                                    //echo $myObjScorePost_Decode->{'Score'} . " " . $row["Score"] . " " . $myObjScorePost_Encode->Rank . "\n";   
                                    $max = $myObjScorePost_Encode->Rank;             
                                  }
                                  /*else if($myObjScorePost_Decode->{'Score'} < $row["Score"])
                                  {
                                    $myObjScorePost_Encode->Rank = $row["Rank"] - 1;
                                  }*/
                                  else if($row["Score"] == $myObjScorePost_Decode->{'Score'})
                                  {
                                    $myObjScorePost_Encode->Rank = $row["Rank"];
                                  }

                                }
                              }
                              else
                              {
                                $myObjScorePost_Encode->Rank = 1;
                              }

                               $sql = "INSERT INTO ScorePost (UserId, LeaderboardId, Score, Rank)
                                      VALUES (" . $myObjScorePost_Decode->{'UserId'} . ", " . $myObjScorePost_Decode->{'LeaderboardId'} . ", " . $myObjScorePost_Decode->{'Score'} . 
                                      ", " . $myObjScorePost_Encode->Rank . ")";

                                if ($conn->query($sql) === TRUE) {
                                  echo "New record CREATED successfully.<br/><br/>";

                                  $scorePostJSON = json_encode($myObjScorePost_Encode);
                                  echo $scorePostJSON;

                              } else {
                                  echo "Error: " . $sql . "<br>" . $conn->error;                      
                              }
                      }
                      else if($myObjScorePost_Decode->{'Score'} > $row["score"])
                      {
                          $myObjScorePost_Encode = new ScorePostClass;

                          //get rank from scores of all users
                          $sql = "SELECT Score, Rank FROM ScorePost ORDER BY Score DESC";
                          $result2 = $conn->query($sql);                          

                              $max = 0;                         

                              if ($result2->num_rows > 0) {
                                  // output data of each row
                                  while($row = $result2->fetch_assoc()) {

                                  //echo "result->Score :: rank: " . $row['Score'] . " " . $row['Rank'] . "<br/>";
                                      
                                  if($myObjScorePost_Decode->{'Score'} > $row["Score"])
                                  {
                                    if($row["Rank"] + 1 > $max)
                                    {
                                      $myObjScorePost_Encode->Rank = $row["Rank"] + 1;
                                    }
                                    //echo $myObjScorePost_Decode->{'Score'} . " " . $row["Score"] . " " . $myObjScorePost_Encode->Rank . "\n";   
                                    $max = $myObjScorePost_Encode->Rank;             
                                  }                                  
                                  /*else if($myObjScorePost_Decode->{'Score'} < $row["Score"])
                                  {
                                    $myObjScorePost_Encode->Rank = $row["Rank"] - 1;
                                  }*/
                                  else if($row["Score"] == $myObjScorePost_Decode->{'Score'})
                                  {
                                    $myObjScorePost_Encode->Rank = $row["Rank"];
                                  } 

                                }
                              }

                          $sql = "UPDATE ScorePost SET Score = " . $myObjScorePost_Decode->{'Score'} . ", Rank = " . $myObjScorePost_Encode->Rank .  " WHERE UserId = " . $myObjScorePost_Decode->{'UserId'} . " AND LeaderboardId = " . $myObjScorePost_Decode->{'LeaderboardId'};

                          if ($conn->query($sql) === TRUE) {
                              echo "New record UPDATED successfully.<br/><br/>";                              

                              $myObjScorePost_Encode->UserId = $myObjScorePost_Decode->{'UserId'};
                              $myObjScorePost_Encode->LeaderboardId = $myObjScorePost_Decode->{'LeaderboardId'};
                              $myObjScorePost_Encode->Score = $myObjScorePost_Decode->{'Score'};
                              //$myObjScorePost_Encode->Rank = $myObjScorePost_Encode->Rank;

                              $scorePostJSON = json_encode($myObjScorePost_Encode);
                              echo $scorePostJSON;

                          } else {
                              echo "Error: " . $sql . "<br>" . $conn->error;                      
                          }
                      }
                      else
                      {
                              $myObjScorePost_Encode = new ScorePostClass;
                              $myObjScorePost_Encode->UserId = $myObjScorePost_Decode->{'UserId'};
                              $myObjScorePost_Encode->LeaderboardId = $myObjScorePost_Decode->{'LeaderboardId'};
                              $myObjScorePost_Encode->Score = $myObjScorePost_Decode->{'Score'};
                              $myObjScorePost_Encode->Rank = $row["rank"];
                              $scorePostJSON = json_encode($myObjScorePost_Encode);
                              echo $scorePostJSON;
                      }
                  }
              } 
              else {
                  echo "0 results";
                  
              }              

              $conn->close();
          
  }      

?>
